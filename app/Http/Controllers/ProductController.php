<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class ProductController extends Controller
{
    public function getProduct(Request $request)
    {


        if (Product::count() == 0) {



            $response = [
                'status' => 204,
                'message' => 'No products found.',
            ];
        } else {
            $query = Product::query();

            // Sorting
            if ($request->has('sort_by')) {
                $query->orderBy($request->sort_by, $request->has('desc') && $request->desc === 'true' ? 'desc' : 'asc');
            }

            // Filtering
            if ($request->has('name')) {
                $query->where('name', $request->name);
            }

            if ($request->has('description')) {
                $query->where('description', $request->description);
            }

            // Pagination
            $products = $query->paginate($request->per_page ?? 10);

            $response = [
                'status' => 200,
                'data' => $products,
            ];
        }




        return response()->json($response);
    }
}
