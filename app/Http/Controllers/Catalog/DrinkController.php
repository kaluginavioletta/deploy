<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Drink;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DrinkController extends Controller
{
    public function index()
    {
        $drink = Product::where('type_product', 'drink')->get();
        return response()->json(['data' => $drink]);
    }
    public function show($id)
    {
        $drink = Drink::find($id);
        if (!$drink) {
            return response()->json(['message' => 'Напитки закончились'], 404);
        }
        return $drink;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'compound' => 'required|string',
            'id_view_drink' => 'nullable|exists:view_drinkables,id_view_drink',
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
        $drink = Drink::create(array_merge($request->all(), ['img' => $imagePath]));

        return $drink;
    }

    public function update(Request $request, $id)
    {
        $drink = Drink::find($id);
        if (!$drink) {
            return response()->json(['message' => 'Напитки закончились'], 404);
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

        $drink->update([
            'name' => $request->filled('name') ? $request->input('name') : $drink->name,
            'compound' => $request->filled('compound') ? $request->input('compound') : $drink->compound,
            'id_view_drink' => $request->filled('id_view_drink') ? $request->input('id_view_drink') : $drink->id_view_drink,
            'price' => $request->filled('price') ? $request->input('price') : $drink->price,
            'percent_discount' => $request->filled('percent_discount') ? $request->input('percent_discount') : $drink->percent_discount,
            'discounted_price' => $request->filled('discounted_price') ? $request->input('discounted_price') : $drink->discounted_price,
            'img' => $request->filled('img') ? $request->input('img') : $drink->img,
        ]);

        return $drink;
    }

    public function destroy($id){
        $drink = Drink::find($id);
        if (!$drink) {
            return response()->json(['message' => 'Напитки с данным id не существует'], 404);
        }

        // Удаляем запись в таблице "sushi"
        $drink->delete();

        // Удаляем соответствующую запись в таблице "products"
        DB::table('products')
            ->where('type_product', 'drink')
            ->where('id_drink', $drink->id_drink)
            ->delete();

        return response()->json(['message' => 'Определённый напиток удалён'], 204);
    }
}
