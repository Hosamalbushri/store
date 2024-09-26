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


}
