<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\CartOrder;
use App\Models\Dessert;
use App\Models\Drink;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sushi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function showCart()
    {
        $user = Auth::user();
        $cartItems = $user->cart_items;

        if ($cartItems->isNotEmpty()) {
            $cartDetails = [];
            $totalPrice = 0;

            foreach ($cartItems as $cartItem) {
                $product = Product::find($cartItem->id_product);

                if ($product) {
                    $itemDetails = [
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $cartItem->quantity,
                        'grams' => $cartItem->grams,
                        'img' => $cartItem->img,
                        'total_price' => $cartItem->quantity * $product->price,
                    ];

                    // Добавляем дополнительные детали в зависимости от типа продукта
                    if ($product->type === 'sushi') {
                        $itemDetails['compound'] = $product->compound;
                        $itemDetails['grams'] = $product->grams;
                    } elseif ($product->type === 'drink') {
                        $itemDetails['compound'] = $product->compound;
                    } elseif ($product->type === 'dessert') {
                        $itemDetails['compound'] = $product->compound;
                    }

                    $cartDetails[] = $itemDetails;
                    $totalPrice += $itemDetails['total_price'];
                }
            }

            return response()->json([
                'cart' => $cartDetails,
                'total_price' => $totalPrice,
            ]);
        } else {
            return response()->json([
                'message' => 'Корзина пользователя пуста.'
            ]);
        }
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        // Определение type_product на основе типа продукта
        $typeProduct = '';

        if ($product instanceof Sushi) {
            $typeProduct = 'sushi';
        } elseif ($product instanceof Drink) {
            $typeProduct = 'drink';
        } elseif ($product instanceof Dessert) {
            $typeProduct = 'dessert';
        }

        // Проверяем, есть ли уже этот товар в корзине
        $orderItem = $user->cart_items()->where('id_product', $product->id_product)->first();

        if ($orderItem) {
            // Если товар уже есть, увеличиваем количество
            $orderItem->quantity += $request->input('quantity', 1);
            $orderItem->save();
            $message = 'Количество товаров в корзине увеличено!';
        } else {
            // Если товара нет, создаем новую запись в корзине с указанием type_product и price
            $orderItem = new CartOrder([
                'id_user' => $user->id_user,
                'id_product' => $product->id_product,
                'quantity' => $request->input('quantity', 1),
                'type_product' => $typeProduct,
                'price' => $product->price,
            ]);
            $orderItem->save();
            $message = 'Товар добавлен в корзину!';
        }

        return response()->json([
            'message' => $message,
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
    public function removeFromCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        // Проверяем, есть ли этот товар в корзине
        $orderItem = $user->cart_items()->where('id_product', $product->id_product)->first();

        if ($orderItem) {
            // Если товар есть, удаляем его из корзины
            $orderItem->delete();

            return response()->json([
                'message' => 'Товар удален из корзины!',
            ]);
        } else {
            return response()->json([
                'message' => 'Товар отсутствует в корзине!',
            ]);
        }
    }
}

