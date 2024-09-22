<?php

namespace App\Http\Controllers;

use App\Models\Customer_Address;
use App\Models\Order;
use App\Models\Order_Details;
use App\Models\OrderHistory;
use App\Models\PaymentType;
use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

    }
    public function payment_types()
    {
        $payment_type = PaymentType::get()->select('id','name');


        if(isset($payment_type)){

            return response()->json(['payment_types' =>$payment_type]);

        }

    }
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_address_id' => 'required|exists:customer__addresses,id',
                'payment_type_id' => 'required|exists:payment_types,id',
                'order_date' => 'required|date|after_or_equal:now',
                'details' => 'required|array',
                'details.*.sku_id' => 'required|exists:skus,id',
                'details.*.quantity' => 'required|numeric|min:1',
                'details.*.attributes.*.id' => 'exists:attribute_options,id'

            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 400);
            }

            $customer_address = Customer_Address::with('customer')->find($request->customer_address_id);
            if ($customer_address->customer->id != auth('api')->id()) {
                return response()->json(['message' => 'Customer address not found'], 403);
            }

            $order = Order::create([
                'customer_id' => auth('api')->id(),
                'customer_address_id' => $request->customer_address_id,
                'order_date' => $request->order_date,
                'payment_type_id' => $request->payment_type_id,
            ]);

            $orderDetails = [];
            $totalPrice = 0;
            $totalDiscount = 0;

            foreach ($request->details as $detail) {
                $sku = Sku::with('product')->find($detail['sku_id']);
                $price = $sku->price * $detail['quantity'];
                $discount = isset($sku->discount->discounted_price) ? ($sku->price - $sku->discount->discounted_price) * $detail['quantity'] : 0;
                $totalPrice += $price;
                $totalDiscount += $discount;

                $orderDetail = Order_Details::create([
                    'order_id' => $order->id,
                    'sku_id' => $detail['sku_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $price,
                    'discount' => $discount,
                    'total_price' => $price - $discount,
                ]);
                if (isset($detail['attributes'])) {
                    $attributeOptions = collect($detail['attributes'])->pluck('id');
                    $orderDetail->attributes()->attach($attributeOptions);
                }

                $orderDetails[] = [
                    'id' => $orderDetail->id,
                    'sku_id' => $orderDetail->sku_id,
                    'quantity' => $orderDetail->quantity,
                    'price' => $orderDetail->price,
                    'discount' => $orderDetail->discount,
                    'total_price' => $orderDetail->total_price,
                    'attributes' => isset($orderDetail->attributes)?$orderDetail->attributes->map(function ($attribute) {
                        return [
                            'id' => $attribute->id,
                            'value' => $attribute->value
                        ];
                    }):null
                ];            }

            $orderSummary = [
                'order_id' => $order->id,
                'order_date' => $order->order_date,
                'order_number' => $order->order_number,
                'order_details' => $orderDetails,
                'total_price' => $totalPrice,
                'total_discount' => $totalDiscount,
                'final_price' => $totalPrice - $totalDiscount,
            ];

            OrderHistory::create([
                'order_id' => $order->id,
                'order_data' => json_encode($orderSummary),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'order' => $orderSummary,
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function edit($id)
    {
        try {
            $order=Order::find($id);
            if (!$order)
            {
                return response()->json([
                    'message'=>'هذه الطلب غير موجود او قد يكون محذوف'
                ]);

            }
            if(isset($order)){

                return response()->json(['order' =>$order]);

            }

        }catch (\Exception $exception){
            return response()->json(['error' => $exception->getMessage()]);
        }



    }
    public function update($id,Request $request)
    {
        try {


            $validator = Validator::make($request->all(), [
                'customer_address_id' => 'required|exists:customer__addresses,id',
                'payment_type_id' => 'required|exists:payment_types,id',
                'order_date' => 'required',
            ]);

            if($validator->fails()){

                return response()->json(['message' =>$validator->errors()]);

            }

            $order=Order::find($id);
            if (!$order)
            {
                return response()->json([
                    'message'=>'هذه الطلب غير موجود او قد يكون محذوف'
                ]);

            }
            if ($order->order__status_id==1)
            {
                $order->update([
                    'customer_address_id' => $request->customer_address_id,
                    'order_date' => $request->order_date,
                    'payment_type_id' => $request->payment_type_id,
                ]);

                return response()->json([
                    'message'=>'تم تعديل الطلب بنجاح'
                ]);

            }
            return response()->json([
                'message'=>'عذرا لايمكنك تعديل هذا الطلب'
            ]);



        }catch (\Exception $exception){
            return response()->json(['error' => $exception->getMessage()]);
        }

    }



    public function delete($id)
    {
        try {
            $order=Order::find($id);
            if (!$order)
            {
                return response()->json([
                    'message'=>'هذه الطلب غير موجود او قد يكون محذوف'
                ]);

            }
            if ($order->order__status_id==1)
            {
                $order->delete();
                return response()->json([
                    'message'=>'تم حذف  الطلب بنجاح'
                ]);

            }
            return response()->json([
                'message'=>'عذا لايمكنك حذف هذا الطلب'
            ]);


        }catch (\Exception $exception){
            return response()->json(['error' => $exception->getMessage()]);
        }

    }
}
