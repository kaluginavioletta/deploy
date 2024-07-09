<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Dessert;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DessertController extends Controller
{
    public function index()
    {
        $dessert = Product::where('type_product', 'dessert')->get();
        return response()->json(['data' => $dessert]);
    }
    public function show($id)
    {
        $dessert = Dessert::find($id);
        if (!$dessert) {
            return response()->json(['message' => 'Десерты закончились'], 404);
        }
        return response()->json(['data' => $dessert], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'compound' => 'required|string',
            'id_view_dessert' => 'nullable|exists:view_dessert,id_view_dessert',
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
        $dessert = Dessert::create(array_merge($request->all(), ['img' => $imagePath]));

        return $dessert;
    }

    public function update(Request $request, $id)
    {
        $dessert = Dessert::find($id);
        if (!$dessert) {
            return response()->json(['message' => 'Дессерты закончились'], 404);
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

        $dessert->update([
            'name' => $request->filled('name') ? $request->input('name') : $dessert->name,
            'compound' => $request->filled('compound') ? $request->input('compound') : $dessert->compound,
            'id_view_drink' => $request->filled('id_view_drink') ? $request->input('id_view_dessert') : $dessert->id_view_dessert,
            'price' => $request->filled('price') ? $request->input('price') : $dessert->price,
            'percent_discount' => $request->filled('percent_discount') ? $request->input('percent_discount') : $dessert->percent_discount,
            'discounted_price' => $request->filled('discounted_price') ? $request->input('discounted_price') : $dessert->discounted_price,
            'img' => $request->filled('img') ? $request->input('img') : $dessert->img,
        ]);

        return $dessert;
    }

    public function destroy($id){
        $dessert = Dessert::find($id);
        if (!$dessert) {
            return response()->json(['message' => 'Дессерт с данным id не существует'], 404);
        }

        $dessert->delete();

        // Удаляем соответствующую запись в таблице "products"
        DB::table('products')
            ->where('type_product', 'dessert')
            ->where('id_dessert', $dessert->id_dessert)
            ->delete();

        return response()->json(['message' => 'Определённый десеерт удалён'], 204);
    }
}
