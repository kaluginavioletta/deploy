<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\CartOrder;
use App\Models\Order;
use App\Models\Sushi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class SushiController extends Controller
{
    public function index()
    {
        $sushi = Sushi::all();
        return response()->json(['data' => $sushi], 200);
    }

    // *READ (Получить одно суши по ID)*
    public function show($id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Sushi not found'], 404);
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

    public function addToCart(Request $request, $id)
    {
        $sushi = Sushi::findOrFail($id);
        $user = Auth::user();

        $order = $this->getOrCreateOrder($user);

        $order->id_address = null;

        if ($request->has('checkout')) { // Проверка на оформление заказа
            // Получение id_address от пользователя
            $idAddress = $request->input('id_address');

            // Установка id_address в модель Order
            $order->id_address = $idAddress;

            // Сохранение заказа с id_address
            $order->save();

            // ... ваш код для оформления заказа ...
        } else {
            // Сохранение заказа без id_address
            $order->save();
        }

        $orderItem = $order->items()->where('type_product', Sushi::class)
            ->where('id_product', $sushi->id_sushi)
            ->first();

        if ($orderItem) {
            $orderItem->quantity += $request->input('quantity', 1);
            $orderItem->save();
        } else {
            $orderItem = new CartOrder([
                'id_order' => $order->id_order,
                'type_product' => Sushi::class,
                'id_product' => $sushi->id_sushi,
                'quantity' => $request->input('quantity', 1),
                'price' => $sushi->price_sushi,
            ]);
            $order->items()->save($orderItem);
        }

        $order->calculateTotalPrice();

        return response()->json([
            'message' => 'Товар добавлен в корзину!',
            'id_order' => $order->id_order,
            'total_price' => $order->total_price,
        ]);
    }

    protected function getOrCreateOrder($user)
    {
        $order = $user->orders()->where('id_status', 1)->first(); // Assuming status 1 is for pending orders

        if (!$order) {
            $order = new Order([
                'id_user' => $user->id_user,
                'id_status' => 1,
                'total_price' => 0, // Set total_price to default value
            ]);
            $order->save();
        }

        return $order;
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
