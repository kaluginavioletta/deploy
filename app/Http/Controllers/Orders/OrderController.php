<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartOrder;
use App\Models\Dessert;
use App\Models\Drink;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sushi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $user = Auth::user();

        // Получаем товары из корзины пользователя
        $cartItems = $user->cart_items;

        // Рассчитываем общую стоимость всех товаров в корзине
        $totalOrderPrice = $cartItems->sum(function($cartItem) {
            $product = Product::find($cartItem->id_product);
            return $cartItem->quantity * $product->discounted_price; // Суммируем стоимость каждого товара
        });

        // Создаем новый заказ
        $order = new Order([
            'id_user' => $user->id_user,
            'id_status' => 1, // Статус "Новый"
            'total_price' => $totalOrderPrice, // Устанавливаем общую стоимость заказа
        ]);
        $order->save();

        // Привязываем адрес доставки к заказу
        $address = new Address([
            'address_city' => $request->address_city,
            'address_street' => $request->address_street,
            'address_entrance' => $request->address_entrance,
            'address_floor' => $request->address_floor,
            'address_apartment' => $request->address_apartment,
        ]);
        $address->save();

        // Сохраняем id_address в заказе
        $order->id_address = $address->id_address;
        $order->save(); // Сохраняем изменения в заказе

        $orderedItems = []; // Массив для хранения заказанных товаров

        // Переносим товары из корзины в заказ
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->id_product);

            // Определяем тип продукта
            $typeProduct = '';
            if ($product instanceof Sushi) {
                $typeProduct = 'sushi';
            } elseif ($product instanceof Drink) {
                $typeProduct = 'drink';
            } elseif ($product instanceof Dessert) {
                $typeProduct = 'dessert';
            }

            $itemTotalPrice = $cartItem->quantity * $product->discounted_price; // Рассчитываем стоимость товара

            // Создаем запись в cart_orders
            $orderItem = new CartOrder([
                'id_user' => $user->id_user,
                'id_order' => $order->id_order,
                'id_product' => $cartItem->id_product,
                'quantity' => $cartItem->quantity,
                'discounted_price' => $product->discounted_price,
                'total_price' => $itemTotalPrice, // Сохраняем стоимость товара
                'type_product' => $typeProduct, // Устанавливаем тип продукта
            ]);
            $orderItem->save();

            // Добавляем только нужные данные о продукте в массив orderedItems
            $orderedItems[] = [
                'id_product' => $product->id_product,
                'name' => $product->name,
                'quantity' => $cartItem->quantity,
                'discounted_price' => $product->discounted_price,
                'total_price' => $itemTotalPrice, // Сохраняем стоимость товара
            ];
        }

        // Удаляем все товары из корзины пользователя
        $user->cart_items()->delete();

        return response()->json([
            'message' => 'Заказ успешно оформлен!',
            'order_number' => $order->id_order,
            'total_price' => $order->total_price, // Общая стоимость заказа
            'status' => $order->status->first()->name_status,
            'delivery_address' => [
                'address_city' => $address->address_city,
                'address_street' => $address->address_street,
                'address_entrance' => $address->address_entrance,
                'address_floor' => $address->address_floor,
                'address_apartment' => $address->address_apartment,
            ], // Возвращаем адрес
            'ordered_items' => $orderedItems, // Возвращаем только нужные данные о товарах
        ]);
    }
    public function showOrders()
    {
        $user = Auth::user(); // Получаем текущего аутентифицированного пользователя

        // Загружаем заказы с адресами, статусами и товарами
        $orders = $user->orders()->with('address', 'status', 'items.product')->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'У вас пока нет оформленных заказов.'
            ]);
        }

        $ordersDetails = [];

        foreach ($orders as $order) {
            // Получаем адрес для текущего заказа
            $deliveryAddress = $order->address;

            // Формируем массив с деталями заказанных товаров
            $orderedItems = $order->items->map(function ($item) {
                return [
                    'id_product' => $item->id_product,
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'discounted_price' => $item->discounted_price,
                    'total_price' => $item->total_price,
                ];
            });

            // Проверяем, существует ли адрес
            $deliveryAddressDetails = $deliveryAddress ? [
                'address_city' => $deliveryAddress->address_city ?? '',
                'address_street' => $deliveryAddress->address_street ?? '',
                'address_entrance' => $deliveryAddress->address_entrance ?? '',
                'address_floor' => $deliveryAddress->address_floor ?? '',
                'address_apartment' => $deliveryAddress->address_apartment ?? '',
            ] : null; // Если адреса нет, устанавливаем null

            // Добавляем детали заказа в массив
            $ordersDetails[] = [
                'order_number' => $order->id_order,
                'total_price' => $order->total_price,
                'status' => $order->status->first()->name_status,
                'delivery_address' => $deliveryAddressDetails, // Добавляем адрес заказа
                'ordered_items' => $orderedItems, // Добавляем товары заказа
            ];
        }

        return response()->json([
            'data' => $ordersDetails,
        ]);
    }
    public function updateStatus(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Заказ не найден'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->update([
            'id_status' => $request->input('id_status'),
        ]);

        $order->save();

        return response()->json(['message' => 'Статус заказа успешно обновлен', 'id_status' => $order->id_status], 200);
    }
}
