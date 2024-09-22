<?php

namespace App\Http\Controllers;

use App\Models\Customer_Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerAddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

    }
   public function create(Request $request)
   {
       try {
           $validator = Validator::make($request->all(), [
               'firstname' => 'required|string|max:20',
               'lastname' => 'required|string|max:20',
               'phone' => 'required|numeric|digits:9',
               'city' => 'required|string|max:50',
               'country' => 'required|string|max:50',
               'address' => 'required|string|max:50',
               'neighborhood'=>'required|string|max:50',
           ]);

           if($validator->fails()){

               return response()->json(['message' =>$validator->errors()]);

           }

           $address = Customer_Address::create([
               'firstname' => $request->firstname,
               'lastname' => $request->lastname,
               'customer_id' => auth('api')->id(),
               'phone' => $request->phone,
               'city' => $request->city,
               'country' => $request->country,
               'address' => $request->address,
               'neighborhood' => $request->neighborhood,
           ]);
           if($address){
               return response()->json([
                   'status' => 'success',
                   'message' => 'create address successfully',
               ]);
           }
       }catch (\Exception $exception){
           return response()->json(['message' => $exception->getMessage()], 500);
       }
   }

    public function edit($id)
    {
        try {
            $address=Customer_Address::find($id);
            if (!$address)
            {
                return response()->json([
                    'message'=>'هذه العنوان غير موجود او قد يكون محذوف'
                ]);

            }
            if(isset($address)){

                return response()->json(['address' =>$address]);

            }

        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 500);
        }



    }



    public function update($id,Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:20',
                'lastname' => 'required|string|max:20',
                'phone' => 'required|numeric|digits:9',
                'city' => 'required|string|max:50',
                'country' => 'required|string|max:50',
                'address' => 'required|string|max:50',
                'neighborhood'=>'required|string|max:50',
            ]);

            if($validator->fails()){

                return response()->json(['message' =>$validator->errors()]);

            }

            $address=Customer_Address::find($id);
            if (!$address)
            {
                return response()->json([
                    'message'=>'هذه العنوان غير موجود او قد يكون محذوف'
                ]);

            }
            $address->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'customer_id' => auth('api')->id(),
                'phone' => $request->phone,
                'city' => $request->city,
                'country' => $request->country,
                'address' => $request->address,
                'neighborhood' => $request->neighborhood,
            ]);

            return response()->json([
                'message'=>'تم تعديل العنوان بنجاح'
            ]);

        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 500);
        }


    }

   public function delete($id)
   {
       try {
           $address=Customer_Address::find($id);
           if (!$address)
           {
               return response()->json([
                   'message'=>'هذه العنوان غير موجود او قد يكون محذوف'
               ]);

           }
           $address->delete();
           return response()->json([
               'message'=>'تم حذف العنوان بنجاح'
           ]);

       }catch (\Exception $exception){
           return response()->json(['message' => $exception->getMessage()], 500);
       }



   }

}
