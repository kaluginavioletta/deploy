<?php

namespace App\Http\Controllers\Favorite;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function showFavorites()
    {
        $user = Auth::user();

        $favorites = $user->favorites()->with('product')->get();

        return response()->json([
            'data' => $favorites,
        ]);
    }

    public function addToFavorites(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        $productId = $product->id_product;

        // Определение типа продукта
        $typeProduct = $product->type_product;

        // Проверяем, добавлен ли уже этот товар в избранное
        $favoriteItem = $user->favorites()->where('id_product', $productId)->where('type_product', $typeProduct)->first();

        if ($favoriteItem) {
            $message = 'Этот товар уже добавлен в избранное!';
        } else {
            // Добавляем товар в избранное
            $favoriteItem = new Favorite([
                'id_user' => $user->id_user,
                'id_product' => $productId,
                'type_product' => $typeProduct,
            ]);
            $favoriteItem->save();
            $message = 'Товар добавлен в избранное!';
        }

        return response()->json([
            'message' => $message,
            'id_product' => $productId,
            'name' => $product->name,
            'discounted_price' => $product->discounted_price,
            'type_product' => $typeProduct,
        ]);
    }


    public function removeFromFavorites(Request $request, $id)
    {
        $user = Auth::user();
        $favoriteItem = $user->favorites()->where('id_product', $id);

        if ($favoriteItem) {
            $favoriteItem->delete();
            $message = 'Товар удален из избранного!';
        } else {
            $message = 'Этот товар не был добавлен в избранное!';
        }

        return response()->json([
            'message' => $message,
        ]);
    }
}
