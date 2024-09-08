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
use Illuminate\Support\Str;
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
        $productId = $product->id_product; // Предполагаем, что id_product теперь просто id

        $user = Auth::user();
        $userId = $user->id_user; // Предполагаем, что id_user теперь просто id

        // Проверяем, есть ли уже этот продукт в корзине
        $cartOrder = CartOrder::where('id_user', $userId)
            ->where('id_product', $productId)
            ->first();

        // Определяем тип продукта
        $typeProduct = '';
        if ($product instanceof Sushi) {
            $typeProduct = 'sushi';
        } elseif ($product instanceof Drink) {
            $typeProduct = 'drink';
        } elseif ($product instanceof Dessert) {
            $typeProduct = 'dessert';
        }

        if ($cartOrder) {
            // Если продукт уже есть, увеличиваем количество
            $cartOrder->quantity += $request->input('quantity', 1);
            $cartOrder->total_price = $cartOrder->quantity * $product->discounted_price; // Обновляем общую стоимость
            $cartOrder->save();
            $message = 'Количество товаров в корзине увеличено!';
        } else {
            // Если продукта нет, создаем новую запись в корзине
            $cartOrder = new CartOrder([
                'id_user' => $userId,
                'id_product' => $productId,
                'quantity' => $request->input('quantity', 1),
                'total_price' => $request->input('quantity', 1) * $product->discounted_price,
                'type_product' => $typeProduct, // Устанавливаем тип продукта
            ]);
            $cartOrder->save();
            $message = 'Товар добавлен в корзину!';
        }

        return response()->json([
            'message' => $message,
            'id_product' => $productId,
            'name' => $product->name,
            'price' => $product->price,
            'percent_discount' => $product->percent_discount,
            'discounted_price' => $product->discounted_price,
            'quantity' => $cartOrder->quantity,
            'total_price' => $cartOrder->total_price,
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
                    'message' => 'Количество товаров равно 1, уменьшение невозможно!'
                ]);
            }
        }
    }
}

