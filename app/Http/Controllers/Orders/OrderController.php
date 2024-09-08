<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartOrder;
use App\Models\Dessert;
use App\Models\Drink;
use App\Models\Order;
use App\Models\OrderItem;
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

        // Получаем все товары из корзины текущего пользователя
        $cartItems = CartOrder::where('id_user', $user->id_user)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Корзина пуста'], 400);
        }

        // Вычисляем общую стоимость заказа
        $totalPrice = $cartItems->sum('total_price');

        // Создаем новый заказ
        $order = new Order();
        $order->id_user = $user->id_user;
        $order->id_status = 1; // Устанавливаем статус "Новый"
        $order->total_price = $totalPrice;
        $order->save();

        // Создаем записи для каждого товара в заказе
        foreach ($cartItems as $item) {
            OrderItem::create([
                'id_order' => $order->id_order,
                'id_product' => $item->id_product,
                'quantity' => $item->quantity,
                'total_price' => $item->total_price,
            ]);
        }

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

        // Очищаем корзину только после успешного создания заказа и всех связанных записей
        CartOrder::where('id_user', $user->id_user)->delete();

        return response()->json([
            'message' => 'Заказ успешно создан!',
            'id_order' => $order->id_order,
            'total_price' => $totalPrice,
            'status' => $order->status->first()->name_status, // Возвращаем статус заказа
            'delivery_address' => [
                'address_city' => $address->address_city,
                'address_street' => $address->address_street,
                'address_entrance' => $address->address_entrance,
                'address_floor' => $address->address_floor,
                'address_apartment' => $address->address_apartment,
            ],
        ]);
    }
    public function showOrders()
    {
        $user = Auth::user(); // Получаем текущего аутентифицированного пользователя

        // Загружаем заказы с адресами, статусами и товарами
        $orders = $user->orders()->with('address', 'status', 'items.product')->get(); // Изменено на 'items.product'

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'У вас пока нет оформленных заказов.'
            ]);
        }

        $ordersDetails = [];

        foreach ($orders as $order) {
            // Получаем адрес для текущего заказа
            $deliveryAddress = $order->address;

            // Формируем массив с деталями заказанных товаров из items
            $orderedItems = $order->items->map(function ($item) {
                return [
                    'id_product' => $item->id_product,
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'discounted_price' => $item->total_price / $item->quantity, // Предполагаем, что у вас есть total_price
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
