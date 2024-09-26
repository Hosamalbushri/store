<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Customer_Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

    }

    protected function respondWithToken($token)
    {
        $user =Customer::active()->find(auth('api')->id());
        if ($user){
            $cart=Customer::with('cart')->find(auth('api')->id());
            # This function is used to make JSON response with new
            # access token of current user
            return response()->json(data: [
                'access_token' => $token,
                'user_id' => auth('api')->id(),
                'name'=>$user->name,
                'phone'=>$user->phone,
                'birthday'=>$user->birthday,
                'gender'=>$user->gender,
                'email'=>$user->email,
                'cart_id'=> $cart->cart->id,
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ]);
        }
        return  response()->json(['message'=>'هذا الحساب موقف']);
    }
    public function refresh()
    {
        # When access token will be expired, we are going to generate a new one wit this function
        # and return it here in response
        return $this->respondWithToken(auth('api')->refresh());
    }



    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $customer=Customer::find(auth('api')->id());
            if (!$customer)
            {
                return response()->json(['message'=>'هذا المستخدم غير موجود او قد يكون محذوفا']);
            }
            $customer=[
                'id'=>$customer->id,
                'name'=>$customer->name,
                'email'=>$customer->email,
                'phone'=>$customer->phone,
                'gender'=>$customer->gender,
                'birthday'=>$customer->birthday,
                'photo'=>$customer->getFirstMediaUrl('images')!==''?$customer->getFirstMediaUrl('images'):('https://ui-avatars.com/api/?name='.urlencode($customer->name).'&color=fff&background=#000000'),



            ];
            return response()->json([$customer]);

        }catch (\Exception $ex)
        {
            return response()->json(['error' => $ex], 401);
        }

    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout()
    {
        auth('api')->logout(); # This is just logout function that will destroy access token of current user

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */




    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function profile(Request $request)
    {
        $customer=Customer::find(auth('api')->id());
        if(!$customer){
            return response()->json(['message'=>'هذا المستخدم غير موجود او قد يكون محذوفا']);
        }
        $validator = Validator::make($request->all(), [
            'phone' => [
                'max:9',
                'string',
                'required',
                Rule::unique('customers')->ignore($customer->id),
            ],
            'name'=>'required|string|max:255',
            'gender' => ['required',
                'string',
            ],
            'birthday' => ['required',
                'date',
            ],
            'photo' => [
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],
        ]);


        if($validator->fails()){

            return response()->json(['message' =>$validator->errors()]);

        }



        $customer->update([
            'phone'=>$request->phone,
            'name'=>request()->name,
            'gender'=>$request->gender,
            'birthday'=>$request->birthday,
        ]);

        if ($request->hasFile('photo')) {
            $customer->clearMediaCollection('images');
            $customer->addMedia($request->file('photo'))->toMediaCollection('images');
        }
        $customer=[
            'id'=>$customer->id,
            'name'=>$customer->name,
            'email'=>$customer->email,
            'phone'=>$customer->phone,
            'gender'=>$customer->gender,
            'birthday'=>$customer->birthday,
            'photo'=>$customer->getMedia('images')->map(fn($media)=>$media->getUrl()),

        ];


        return response()->json(['message'=>'تم تحديث ملفك الشخصي
        ',
            $customer,

        ]);


    }
    public function changePassword(Request $request)
    {
        $customer=Customer::find(auth('api')->id());
        if(!$customer){
            return response()->json(['message'=>'هذا المستخدم غير موجود او قد يكون محذوفا']);
        }
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
        if($validator->fails()){

            return response()->json(['message' =>$validator->errors()]);
        }
        if (!Hash::check($request->current_password, $customer->password)) {
            return response()->json(['current_password' => 'The current password is incorrect.']);
        }
        $customer->update([
            'password' => Hash::make($request->password),
        ]);


        return response()->json(['message'=>'تم تغيير كلمة السر بنجاح']);




    }



    public function myorders()
    {
        $myorders=Customer::with('orders')->find(auth('api')->id());
        if (!$myorders)
        {
            return  response()->json(['message'=>'هذا المستخدم غير موجود او قد يكون محذوفا']);
        }

        $myorders=$myorders->orders->map(function ($order){
            return
                [
                    'order_id'=>$order->id,
                    'order_number'=>$order->order_number,
                    'order_date'=>$order->order_date,
                    'payment_type'=>['name'=>$order->payment_type->name],
                    'order_status'=>['status'=>$order->status->status_name],
                    'order_address'=>
                        [
                            'firstname'=>$order->customer_address->firstname,
                            'lastname'=>$order->customer_address->lastname,
                            'phone'=>$order->customer_address->phone,
                            'country'=>$order->customer_address->country,
                            'city'=>$order->customer_address->city,
                            'address'=>$order->customer_address->address,
                            'neighborhood'=>$order->customer_address->neighborhood,
                        ]
                    ,
                    'order_detail'=>$order->orderDetail->map(function ($detail){
                        return
                            [
                                'product'=>[
                                    'product_name'=>$detail->sku->product->name,
                                    'price'=>$detail->sku->price,
                                    'quantity'=>$detail->quantity,
                                    'price_before_discount'=>$detail->price,
                                    'discount'=>$detail->discount,
                                    'price_after_discount'=>$detail->total_price
                                ]
                            ];
                    }),
                    'order_total_price'=>$order->total_price





                ];

        });

        return  response() ->json($myorders);

    }
    public function myfavorite()
    {
        $myfavorite=Customer::with('favorite')->find(auth('api')->id());
        if (!$myfavorite)
        {
            return  response()->json(['message'=>'هذا المستخدم غير موجود او قد يكون محذوفا']);
        }
        $myfavorite=$myfavorite->favorite->map(function ($favorite){
            return [
                'id'=>$favorite->id,
                'product_id'=> $favorite->product->id,
                'product_name'=> $favorite->product->name,
            ];
        });

        return  response() ->json($myfavorite);

    }


    public function myaddress()
    {
        $address=Customer_Address::where('customer_id',auth('api')->id())->get();
        if (!$address)
        {
            return  response()->json(['message'=>'هذا المستخدم غير موجود او قد يكون محذوفا']);
        }
        $address=$address->map(function ($address){
            return [
                'id'=>$address->id,
                'firstname'=> $address->firstname,
                'lastname'=> $address->lastname,
                'phone'=> $address->phone,
                'address'=> $address->address,
                'city'=>$address->city,
                'country'=>$address->country,
                'neighborhood'=>$address->neighborhood
            ];
        });

        return  response() ->json($address);

    }

}
