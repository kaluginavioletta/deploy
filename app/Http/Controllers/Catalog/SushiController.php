<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartOrder;
use App\Models\Dessert;
use App\Models\Drink;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sushi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class SushiController extends Controller
{
    public function index()
    {
        $sushi = Product::where('type_product', 'sushi')->get();
        return response()->json(['data' => $sushi]);
    }

    // READ (Получить одно суши по ID)
    public function show($id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Суши закончились'], 404);
        }
        return $sushi;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'compound' => 'required|string',
            'id_view_sushi' => 'nullable|exists:view_sushi,id_view_sushi',
            'price' => 'required|integer',
            'percent_discount' => 'nullable|integer',
            'discounted_price' => 'integer',
            'grams' => 'integer',
            'img' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $request->file('img')->store('images', 'public');
        $sushi = Sushi::create(array_merge($request->all(), ['img' => $imagePath]));

        return $sushi;
    }
    public function update(Request $request, $id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Суши закончились'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'compound' => 'sometimes|string',
            'id_view_sushi' => 'sometimes|nullable|exists:view_sushi,id_view_sushi',
            'price' => 'sometimes|integer',
            'percent_discount' => 'sometimes|integer|default:0',
            'discounted_price' => 'sometimes|integer',
            'img' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sushi->update([
            'name' => $request->filled('name') ? $request->input('name') : $sushi->name,
            'compound' => $request->filled('compound') ? $request->input('compound') : $sushi->compound,
            'id_view_sushi' => $request->filled('id_view_sushi') ? $request->input('id_view_sushi') : $sushi->id_view_sushi,
            'price' => $request->filled('price') ? $request->input('price') : $sushi->price,
            'percent_discount' => $request->filled('percent_discount') ? $request->input('percent_discount') : $sushi->percent_discount,
            'discounted_price' => $request->filled('discounted_price') ? $request->input('discounted_price') : $sushi->discounted_price,
            'img' => $request->filled('img') ? $request->input('img') : $sushi->img,
        ]);

        return $sushi;
    }

    public function destroy($id){
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Суши с данным id не существует'], 404);
        }

        $sushi->delete();

        // Удаляем соответствующую запись в таблице "products"
        DB::table('products')
            ->where('type_product', 'sushi')
            ->where('id_sushi', $sushi->id_sushi)
            ->delete();

        return response()->json(['message' => 'Определённый суши удалён'], 204);
    }
}
