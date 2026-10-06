<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\ShippingCharge;

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
}
