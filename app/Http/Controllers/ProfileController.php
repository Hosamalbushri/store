<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:255',
            'gender' => 'required|string',
            'birthday' => 'required|date',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        if($validator->fails()){

            return response()->json(['message' =>$validator->errors()]);

        }
        $customer=Customer::find(auth('api')->id());
        if(!$customer){
            return response()->json(['message'=>'هذا المستخدم غير موجود او قد يكون محذوفا']);
        }
        $customer->update([
            'phone'=>$request->phone,
            'gender'=>$request->gender,
            'birthday'=>$request->birthday,

        ]);

        $media = $customer->getMedia('images')->map(fn($media)=>$media->getUrl());
        if ($request->hasFile('photo')) {
            $media->media()->delete();
            $customer->addMedia($request->file('photo'))->toMediaCollection('images');
        }


        return response()->json(['message'=>'تم تحديث ملفك الشخصي
        ',
            auth()->user(),
            $media
        ]);


    }

}
