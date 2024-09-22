<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FavoriteController extends Controller
{
    public function add(Request $request){
        try {
            $validator = Validator::make($request->all(), [

                'product_id' => 'required|exists:products,id',

            ]);

            if($validator->fails()){

                return response()->json(['message' =>$validator->errors()]);

            }
            $customer_id = auth('api')->id();
            $product_id = request('product_id');

            // Check if the favorite already exists
            $existingFavorite = Favorite::where('customer_id', $customer_id)
                ->where('product_id', $product_id)
                ->first();
            if ($existingFavorite) {
                return response()->json(['message' => 'You have already favorited this item.'], 409); // 409 Conflict
            }
            $favorite = Favorite::create([
                'product_id' => $request->product_id,
                'customer_id' => auth('api')->id(),
            ]);
            if($favorite){
                return response()->json([
                    'status' => 'success',
                    'message' => 'cart created successfully',
                ]);
            }

        }catch (\Exception $exception){
            return response()->json(['error' => $exception->getMessage()], 500);
        }



    }

    public function delete($id)
    {
        try {
            $cart=Favorite::find($id);
            if (!$cart)
            {
                return response()->json([
                    'message'=>'هذه المنتج غير موجود او قد تكون محذوفة'
                ]);

            }
            $cart->delete();
            return response()->json([
                'message'=>'تم ازالة المنتج من المفضلة  بنجاح'
            ]);

        }catch (\Exception $exception){
            return response()->json(['error' => $exception->getMessage()], 500);
        }



    }
}
