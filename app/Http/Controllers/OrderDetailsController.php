<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Order_Details;
use App\Models\OrderDetailsHistory;
use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderDetailsController extends Controller
{

    public function edit($id)
    {

        $order_detalie=Order_Details::find($id);
        if (!$order_detalie)
        {
            return response()->json([
                'message'=>'هذه الطلب غير موجود او قد يكون محذوف'
            ]);

        }
        if(isset($order_detalie)){

            return response()->json(['order_detaile' =>$order_detalie]);
        }

    }

    public function update($id,Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'sku_id' => 'required|exists:skus,id',
                'quantity'=>'required|numeric|min:1',
            ]);

            if($validator->fails()){

                return response()->json(['message' =>$validator->errors()]);

            }

            $order_detaile=Order_Details::with('order')->find($id);
            if (!$order_detaile)
            {
                return response()->json([
                    'message'=>'هذه الطلب غير موجود او قد يكون محذوف'
                ]);

            }
            $sku = Sku::with('product','discount')->find($request->sku_id);
            $price= $sku->price * $request->quantity;
            $discount= isset($sku->discount->discounted_price)?($sku->price-$sku->discount->discounted_price)*$request->quantity:0;
            if ($order_detaile->order->order__status_id==1){
                $order_detaile->update([
                    'sku_id' => $request->sku_id,
                    'quantity' => $request->quantity,
                    'price' => $price,
                    'discount' =>$discount,
                    'total_price' => $price -$discount,
                ]);
                return response()->json([
                    'message'=>'تم تعديل الطلب بنجاح'
                ]);

            }
            return response()->json([
                'message'=>'عذرا لايمكنك تحديث هذا الطلب'
            ]);

        }catch (\Exception $exception){
            return response()->json(['message'=>$exception->getMessage()]);
        }


    }


    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $order_detaile=Order_Details::with('order')->find($id);
            if (!$order_detaile)
            {
                return response()->json([
                    'message'=>'هذه الطلب غير موجود او قد يكون محذوف'
                ]);

            }
            if ($order_detaile->order->order__status_id==1) {

                $order_detaile->delete();
                DB::commit();
                return response()->json([
                    'message' => 'تم حذف  الطلب بنجاح'
                ]);
            }
            return response()->json([
                'message'=>'عذرا لايمكنك حذف هذا الطلب'
            ]);


        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['message'=>$exception->getMessage()]);
        }



    }
}
