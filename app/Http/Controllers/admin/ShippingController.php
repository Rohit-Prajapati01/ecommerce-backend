<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{

    public function getShippingCharge()
    {
        $shipping = ShippingCharge::first();
        return response()->json(
            [
                'status' => 200,
                'data' => $shipping
            ],
            200
        );
    }

    public function updateShippingCharge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_charge' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $shipping = ShippingCharge::updateOrCreate(
            ['id' => 1],
            ['shipping_charge' => $request->shipping_charge]
        );

        return response()->json([
            'status' => 200,
            'message' => 'Shipping charge updated successfully',
            'data' => $shipping
        ], 200);
    }
}
