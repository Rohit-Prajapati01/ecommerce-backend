<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer'
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'You have registered Successfully...',
            'user' => $user
        ], 200);
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = User::find(Auth::user()->id);

            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'token' => $token,
                'id' => $user->id,
                'name' => $user->name,
                'message' => 'You are Loggined Successfully..!'
            ], 200);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Either email/password does not match'
            ], 401);
        }
    }

    public function getOrderDetails(int $id, Request $request)
    {
        $order = Order::where(['user_id' => $request->user()->id, 'id' => $id])
            ->with('items', 'items.product')
            ->first();

        if ($order == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found',
                'data' => []
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'data' => $order
            ], 200);
        }
    }

    public function getOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)->get();
        return response()->json([
            'status' => 200,
            'data' => $orders
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find($request->user()->id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
                'data' => []
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'sometimes|string|max:255',
            'email'   => 'sometimes|email|unique:users,email,' . $user->id,
            'city'    => 'sometimes|string|max:100',
            'state'   => 'sometimes|string|max:100',
            'zip'     => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'mobile'  => 'sometimes|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }


        $user->fill($request->only([
            'name',
            'email',
            'city',
            'state',
            'zip',
            'address',
            'mobile'
        ]));

        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Profile updated successfully',
            'data' => $user
        ], 200);
    }

    public function getAccountDetails(Request $request)
    {
        $user = User::find($request->user()->id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
                'data' => []
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'data' => $user
            ], 200);
        }
    }
}
