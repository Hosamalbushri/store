<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Cart_Detailes;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

    }
    public function add(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'sku_id' => 'required|exists:skus,id',
                'quantity' => 'required|integer'
            ]);

            if($validator->fails()){

                return response()->json(['message' =>$validator->errors()]);

            }
            $cart=Customer::with('cart')->find(auth('api')->id());
            if (!$cart)
            {
                return response()->json(['message'=>'غير قادر على ايجاد رقم السلة']);

            }
            $sku=Sku::with('product')->find($request->sku_id);
            if ($sku->product->id != $request->product_id)
            {
                return response()->json(['message'=>'the sku_id does not belong to this product']);
            }

//            return response($cart->id);
            $cart_detailes = Cart_Detailes::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'sku_id' => $request->sku_id,
                'quantity' => $request->quantity,
            ]);
            if($cart_detailes){
                return response()->json([
                    'status' => 'success',
                    'message' => 'cart created successfully',
                ]);
            }

        }catch (\Exception $exception){
            return response()->json(['message'=>'Failed to add to cart','error' => $exception->getMessage()],500);
        }

    }


//    public function show()
//    {
//        try {
//            $custome=Customer::with('cart')->find(auth('api')->id());
//
//            $cart=Cart::with('cart_detailes')->find($custome->cart->id);
//            if (!$cart)
//            {
//                return response()->json([
//                    'message'=>'هذه السلة غير موجود او قد تكون محذوفة'
//                ]);
//
//            }
//            if(isset($cart)){
//
//                return response()->json([$cart]);
//
//            }
//
//        }catch (\Exception $exception){
//            return response()->json(['message'=>'Failed to get the cart','error' => $exception->getMessage()],500);
//        }
//
//
//    }

    public function update(Request $request,$product_id,$sku_id)
    {
        try {
            $cart=Customer::with('cart')->find(auth('api')->id());
            if (!$cart)
            {
                return response()->json(['message'=>'غير قادر على ايجاد رقم السلة']);

            }
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer'
            ]);

            if($validator->fails()){

                return response()->json(['message' =>$validator->errors()]);

            }



            $cart = Cart_Detailes::where('cart_id','=', $cart->cart->id)
                ->where('product_id','=', $product_id)
                ->where('sku_id','=', $sku_id)->first();

            if ($cart)
            {
                $cart->update([
                    'quantity'=>$request->quantity
                ]);

                return response()->json([
                    'message'=>'تم تعديل السلة بنجاح'
                ]);


            }
            return response()->json([
                'message'=>'هذا المنتج غير موجود في السلة او قد يكون تم حذفة'
            ]);

        }catch (\Exception $exception){
            return response()->json(['message'=>'Failed to update to cart','error' => $exception->getMessage()],500);
        }


    }

    public function delete(Request $request,$product_id,$sku_id)
    {
        try {
            $cart=Customer::with('cart')->find(auth('api')->id());
            if (!$cart)
            {
                return response()->json(['message'=>'غير قادر على ايجاد رقم السلة']);

            }
            $cart = Cart_Detailes::where('cart_id','=', $cart->cart->id)
                ->where('product_id','=', $product_id)
                ->where('sku_id','=', $sku_id)->first();

            if (!$cart)
            {
                return response()->json([
                    'message'=>'هذه السلة غير موجود او قد تكون محذوفة'
                ]);

            }
            $cart->delete();
            return response()->json([
                'message'=>'تم حذف السلة بنجاح'
            ]);


        }catch (\Exception $exception){
            return response()->json(['message'=>'Failed to delete to cart','error' => $exception->getMessage()],500);
        }


    }


    public  function  get_all_products_in_cart()
    {
        try {
            $customer=Customer::with('cart')->find(auth('api')->id());
            $cart=Cart::find($customer->cart->id);
            if (!$cart)
            {
                return response()->json([
                    'message'=>'هذه السلة غير موجود او قد تكون محذوفة'
                ]);

            }
            if(isset($cart)){

                return response()->json([$cart->cart_detailes]);
            }

        }catch (\Exception $exception){
            return response()->json(['message'=>'Failed to get products in cart','error' => $exception->getMessage()],500);
        }
    }

}
