<?php

namespace App\Http\Controllers\Catalog;

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
use Illuminate\Support\Facades\Session;

class SushiController extends Controller
{
    public function index()
    {
        $sushi = Sushi::paginate(8);
        return $sushi->items();
    }

    // *READ (Получить одно суши по ID)*
    public function show($id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Суши закончились'], 404);
        }
        return response()->json(['data' => $sushi], 200);
    }

    // *CREATE (Создать новое суши)*
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_sushi' => 'required|string|max:255',
            'compound_sushi' => 'required|string',
            'id_view_sushi' => 'nullable|exists:view_sushi,id_view_sushi',
            'price_sushi' => 'required|integer',
            'percent_discount_sushi' => 'integer|default:0',
            'discounted_price_sushi' => 'integer',
            'img_sushi' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sushi = Sushi::create($request->all());
        return response()->json(['data' => $sushi], 201);
    }

    public function createOrder(Request $request)
    {
        $user = Auth::user();
        $cartItems = $user->cart_items()->get();

        // Проверяем наличие товаров в корзине
        if ($cartItems->isEmpty()) {
            return response()->json([
                'error' => 'Ваша корзина пуста. Нечего добавлять в заказ.'
            ]);
        }

        $totalPrice = $cartItems->sum('price');

        // Создаем новый заказ
        $order = new Order([
            'id_user' => $user->id_user,
            'id_status' => 1, // Предположим, что статус "В обработке" имеет id = 1
            'total_price' => $totalPrice // Фиксируем общую стоимость заказа
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

        // Пересчитываем общую стоимость заказа
        $order->calculateTotalPrice(); // Предположим, что у вас есть метод calculateTotalPrice для расчета общей стоимости

        // Удаляем все товары из корзины пользователя
        $user->cart_items()->delete();

        // Получаем связанный статус заказа
        $status = $order->status;

        return response()->json([
            'message' => 'Заказ успешно оформлен!',
            'order_number' => $order->id_order,
            'total_price' => $order->total_price,
            'status' => $status->name_status,
            'delivery_address' => $address,
            'ordered_items' => $order->items,
        ]);
    }


    // Просмотр заказов не отображается
    public function showOrders()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('cart_orders', 'address')->get();

        if ($orders->isNotEmpty()) {
            $ordersDetails = [];

            foreach ($orders as $order) {
                $orderItems = [];
                $orderTotalPrice = 0;

                if ($order->cart_orders->isNotEmpty()) {
                    foreach ($order->cart_orders as $cartOrder) {
                        $product = $cartOrder->product;

                        $itemDetails = [
                            'product_name' => $product->name,
                            'price' => $product->price,
                            'quantity' => $cartOrder->quantity,
                            'total_price' => $cartOrder->quantity * $product->price,
                        ];
                        $orderItems[] = $itemDetails;
                        $orderTotalPrice += $itemDetails['total_price'];
                    }
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
                    'status' => $order->id_status->name_status,
                    'delivery_address' => $deliveryAddressDetails,
                    'ordered_items' => $orderItems,
                ];
            }

            return response()->json([
                'orders' => $ordersDetails,
            ]);
        } else {
            return response()->json([
                'message' => 'У вас пока нет оформленных заказов.'
            ]);
        }
    }

    // *UPDATE (Обновить суши)*
    public function update(Request $request, $id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Sushi not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_sushi' => 'sometimes|string|max:255',
            'compound_sushi' => 'sometimes|string',
            'id_view_sushi' => 'sometimes|nullable|exists:view_sushi,id_view_sushi',
            'price_sushi' => 'sometimes|integer',
            'percent_discount_sushi' => 'sometimes|integer|default:0',
            'discounted_price_sushi' => 'sometimes|integer',
            'img_sushi' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sushi->update($request->all());
        return response()->json(['data' => $sushi], 200);
    }

    // *DELETE (Удалить суши)*
    public function destroy($id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Sushi not found'], 404);
        }
        $sushi->delete();
        return response()->json(['message' => 'Sushi deleted'], 204);
    }
}
