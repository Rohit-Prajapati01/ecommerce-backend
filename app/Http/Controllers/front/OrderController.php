<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class OrderController extends Controller
{
    public function saveOrder(Request $request)
    {


        if (!empty($request->cart)) {
            //save order in fb
            $order = Order::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'mobile' => $request->mobile,
                'state' => $request->state,
                'zip' => $request->zip,
                'city' => $request->city,
                'grandtotal' => $request->grand_total,
                'subtotal' => $request->sub_total,
                'discount' => $request->discount,
                'shipping' => $request->shipping,
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'user_id' => $request->user()->id
            ]);

            //save order items

            foreach ($request->cart as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'name' => $item['title'],
                    'price' => $item['qty'] * $item['price'],
                    'unit_price' => $item['price'],
                    'qty' => $item['qty'],
                    'product_id' => $item['product_id'],
                    'size' => $item['size'],
                ]);
            }
            return response()->json([
                'status' => 200,
                'id' => $order->id,
                'message' => 'You have successfully placed your order'
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Your cart is empty'
            ], 400);
        }
    }

    // public function createPaymentIntent(Request $request)
    // {
    //     try {

    //         if ($request->grand_total > 0) {

    //             Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    //             $paymentIntent = PaymentIntent::create([
    //                 'amount' => $request->grand_total * 100, // Amount in cents
    //                 'currency' => 'USD',
    //                 'payment_method_types' => ['card'],
    //             ]);
    //             $clientSecret = $paymentIntent->client_secret;
    //             return response()->json([
    //                 'status' => 200,
    //                 'client_secret' => $clientSecret,
    //                 'message' => 'Payment intent created successfully'
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'Invalid amount'
    //             ], 400);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => $e->getMessage()
    //         ], 400);
    //     }
    // }

    public function createPaymentIntent(Request $request)
    {
        try {

            if ($request->amount > 0) {

                Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

                $paymentIntent = PaymentIntent::create([
                    'amount' => $request->amount,
                    'currency' => 'usd',
                    'payment_method_types' => ['card'],
                ]);

                return response()->json([
                    'status' => 200,
                    'clientSecret' => $paymentIntent->client_secret,
                ]);
            }

            return response()->json([
                'status' => 400,
                'message' => 'Invalid amount'
            ], 400);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
