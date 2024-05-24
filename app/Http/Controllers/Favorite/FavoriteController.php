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
            'favorites' => $favorites,
        ]);
    }

    public function addToFavorites(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        // Определение типа продукта
        $typeProduct = get_class($product);

        // Проверяем, добавлен ли уже этот товар в избранное
        $favoriteItem = $user->favorites()->where('id_product', $product->id_product)->first();

        if ($favoriteItem) {
            $message = 'Этот товар уже добавлен в избранное!';
        } else {
            // Добавляем товар в избранное
            $favoriteItem = new Favorite([
                'id_user' => $user->id_user,
                'id_product' => $product->id_product,
                'type_product' => $typeProduct,
            ]);
            $favoriteItem->save();
            $message = 'Товар добавлен в избранное!';
        }

        return response()->json([
            'message' => $message,
        ]);
    }

    public function removeFromFavorites(Request $request, $id)
    {
        $user = Auth::user();
        $favoriteItem = $user->favorites()->where('id_product', $id)->first();

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
