<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Cart;

use App\Models\Order;

use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {

        $user = Auth::user();


        $cart = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity

            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully'], 200);
    }

    public function getCart()
    {
        $user = Auth::user();


        $cart = Cart::with('products')->where('user_id', $user->id)->get();

        if (count($cart) > 0) {

            $response = [
                'status' => 200,
                'data' => $cart,
            ];
        } else {

            $response = [
                'status' => 204,
                'message' => 'No items found.',
            ];
        }

        return response()->json(['cart' => $response]);
    }
    public function updateCart(Request $request)
    {

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->first();

        if (!$cart) {

            $response = [
                'status' => 404,
                'message' => 'Cart not found.',
            ];
        } else {


            $cart->update(
                [
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,

                ]
            );

            $response = [
                'status' => 200,
                'message' => 'Cart updated successfully.',
            ];
        }



        return response()->json(['message' => 'Cart item updated successfully'], 200);
    }

    public function deleteCartItem(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->first();

        if (!$cart) {

            $response = [
                'status' => 404,
                'message' => 'Cart not found.',
            ];
        } else {
            $cart->delete();
            $response = [
                'status' => 200,
                'message' => 'Item deleted successfully.',
            ];
        }



        return response()->json(['message' => 'Cart item removed successfully'], 200);
    }

    public function clearCart(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id);

        if (!$cart->exists()) {
            $response = [
                'status' => 404,
                'message' => 'Cart not found.',
            ];
        } else {
            $cart->delete();
            $response = [
                'status' => 200,
                'message' => 'Cart deleted successfully.',
            ];
        }

        return response()->json($response);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->get();

        if (count($cart)==0) {
            
            $response = [
                'status' => 404,
                'message' => 'Cart not found.',
            ];
        } else {


            //$totalAmount = $cart->products->sum('price');
            $totalAmount = 0;
            foreach ($cart as $cartItem) {
                $totalAmount += $cartItem->price * $cartItem->quantity;

                $product = Product::find($cartItem->product_id);

                $balance_quantity = $product->quantity - $cartItem->quantity;

                $totalAmount += $cartItem->quantity * $product->price;


                $product->update(
                    [
                        'quantity' => $balance_quantity
                    ]
                );
            }




            $taxes = 0.1 * $totalAmount;
            $shipping = 5.00;

            $totalOrderAmount = $totalAmount + $taxes + $shipping;




            $cart = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalOrderAmount,
                'status' => 1

            ]);

            $response = [
                'status' => 200,
                'message' => 'Order placed successfully.',
            ];
        }
        return response()->json($response);
    }
    public function orderHistory()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if (count($orders) == 0) {
            $response = [
                'status' => 404,
                'message' => 'Oder not found.',
            ];
        } else {

            $response = [
                'status' => 200,
                'data' => $orders,
                'message' => 'Data found',
            ];
        }
        return response()->json($response);
    }
}
