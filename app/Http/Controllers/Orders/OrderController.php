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
        $cartItems = $user->cart_items();

        $order = new Order([
            'id_user' => $user->id_user,
            'id_status' => 1, // Статус "Новый"
            'total_price' => $cartItems->sum('total_price'), // Добавляем общую стоимость заказа
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

        $order->id_address = $address->id_address;

        // Переносим товары из корзины в заказ
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->id_product);

            $typeProduct = '';

            if ($product instanceof Sushi) {
                $typeProduct = 'sushi';
            } elseif ($product instanceof Drink) {
                $typeProduct = 'drink';
            } elseif ($product instanceof Dessert) {
                $typeProduct = 'dessert';
            }

            $orderItem = new CartOrder([
                'id_user' => $user->id_user,
                'id_order' => $order->id_order,
                'type_product' => $typeProduct,
                'id_product' => $cartItem->id_product,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);
            $orderItem->save();

            // Удаляем товар из корзины
            $cartItem->delete();
        }

        // Удаляем все товары из корзины пользователя
        $user->cart_items()->delete();

        return response()->json([
            'message' => 'Заказ успешно оформлен!',
            'order_number' => $order->id_order,
            'total_price' => $order->total_price,
            'status' => $order->status->first()->name_status,
            'delivery_address' => $address,
            'ordered_items' => $order->items,
        ]);
    }
    public function showOrders()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('address', 'status', 'items')->get();

        if ($orders->count() > 0) {
            $ordersDetails = [];

            foreach ($orders as $order) {
                $orderItems = [];
                $orderTotalPrice = $order->total_price;

                foreach ($order->items()->get() as $cartOrder) {
                    $orderedItems = [
                        'id_product' => $cartOrder->product->id_product,
                        'name' => $cartOrder->product->name,
                        'quantity' => $cartOrder->quantity,
                        'discounted_price' => $cartOrder->discounted_price,
                        'total_price' => $cartOrder->total_price,
                    ];
                    $orderItems[] = $orderedItems;
                }

                $deliveryAddress = $order->address;
                $deliveryAddressDetails = [
                    'city' => $deliveryAddress->address_city ?? '',
                    'street' => $deliveryAddress->address_street ?? '',
                    'entrance' => $deliveryAddress->address_entrance ?? '',
                    'floor' => $deliveryAddress->address_floor ?? '',
                    'apartment' => $deliveryAddress->address_apartment ?? '',
                ];

                $ordersDetails[] = [
                    'order_number' => $order->id_order,
                    'total_price' => $orderTotalPrice,
                    'status' => $order->status->first()->name_status,
                    'delivery_address' => $deliveryAddressDetails,
                    'ordered_items' => $orderItems,
                ];
            }

            return response()->json([
                'data' => $ordersDetails,
            ]);
        } else {
            return response()->json([
                'message' => 'У вас пока нет оформленных заказов.'
            ]);
        }
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
