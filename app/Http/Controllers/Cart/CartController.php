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

        if ($cartItems->count() > 0) {
            $cartDetails = [];
            $totalPrice = 0;

            foreach ($cartItems as $cartItem) {
                $product = Product::find($cartItem->id_product);

                if ($product) {
                    $itemDetails = [
                        'id_product' => $product->id_product,
                        'name' => $product->name,
                        'price' => $product->price,
                        'discounted_price' => $product->discounted_price,
                        'quantity' => $cartItem->quantity,
                        'grams' => $product->grams,
                        'img' => $product->img,
                        'total_price' => $cartItem->quantity * $product->discounted_price,
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
                'data' => $cartDetails,
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
        // Определение типа продукта на основе id
        $product = Product::findOrFail($id);
        $typeProduct = $product->type_product;

        $productId = $product->id_product;

        $user = Auth::user();

        // Проверяем, есть ли уже этот продукт в корзине
        $orderItem = $user->cart_items()->where('id_product', $productId)->first();

        if ($orderItem) {
            // Если продукт уже есть, увеличиваем количество
            $orderItem->quantity += $request->input('quantity', 1);
            $orderItem->save();
            $message = 'Количество товаров в корзине увеличено!';
        } else {
            // Если продукта нет, создаем новую запись в корзине с указанием type_product и price
            $orderItem = new CartOrder([
                'id_user' => $user->id_user,
                'id_product' => $productId,
                'quantity' => $request->input('quantity', 1),
                'discounted_price' => $product->discounted_price,
                'type_product' => $typeProduct,
                'total_price' => $request->input('quantity', 1) * $product->discounted_price,
            ]);
            $orderItem->save();
            $message = 'Товар добавлен в корзину!';
        }

        return response()->json([
            'message' => $message,
            'id_product' => $productId,
            'name' => $product->name,
            'discounted_price' => $product->discounted_price,
            'quantity' => $orderItem->quantity,
            'type_product' => $typeProduct,
            'total_price' => $orderItem->total_price,
        ]);
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

    public function increaseQuantity(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        // Проверяем, есть ли этот товар в корзине
        $orderItem = $user->cart_items()->where('id_product', $product->id_product)->first();

        if ($orderItem) {
            // Если товар есть, увеличиваем количество
            $orderItem->quantity += $request->input('quantity', 1);
            $orderItem->save();

            return response()->json([
                'message' => 'Количество товаров в корзине увеличено!',
            ]);
        }
    }

    public function decreaseQuantity(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        // Проверяем, есть ли этот товар в корзине
        $orderItem = $user->cart_items()->where('id_product', $product->id_product)->first();

        if ($orderItem) {
            if ($orderItem->quantity > 1) {
                $orderItem->quantity -= $request->input('quantity', 1);
                $orderItem->save();

                return response()->json([
                    'message' => 'Количество товаров в корзине уменьшено!',
                ]);
            } else {
                // Если количество товаров равно 1, то нельзя уменьшать
                return response()->json([
                    'message' => 'Количество товаров равно 1, уменьшение невозможно!',
                ]);
            }
        }
    }
}

