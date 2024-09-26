<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Customer_Address;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use mysql_xdevapi\Exception;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(CustomerRequest $request)
    {

        $credentials = request(['email', 'password']);
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token); # If all credentials are correct - we are going to generate a new access token and send it back on response
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => 'required|confirmed|string|min:6',
        ]);

        if($validator->fails()){

            return response()->json($validator->errors());

        }

        DB::beginTransaction();
        try {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            DB::commit();

            if ($customer)
            {
                $token = request(['email','password']);
                if (! $token = auth('api')->attempt($token)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return $this->respondWithToken($token);

            }


        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'message' => 'Customer creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
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

}
