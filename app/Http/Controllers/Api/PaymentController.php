<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    use ApiResponse;
    public function createStripeCheckout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        // Stuart price (GBP)
        $amount = $request->input('amount') + 5; // price_tax_included

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => 'Stuart Delivery Payment',
                    ],
                    'unit_amount' => $amount * 100, // Stripe uses cents
                ],
                'quantity' => 1,
            ]],
            'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('checkout.cancel') . '?redirect_url=' . $request->get('cancel_redirect_url'),
            'metadata' => [
                'amount'               => $amount,
                'success_redirect_url' => '/payment/success',
                'cancel_redirect_url'  => '/payment/cancel',
            ],
        ]);

        return response()->json([
            'checkout_url' => $session->url
        ]);
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->error([], 'Session ID is required', 400);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = Session::retrieve($sessionId);

            $success_redirect_url = $metadata->success_redirect_url ?? null;

            return redirect($success_redirect_url);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function checkoutCancel(Request $request)
    {
        $cancel_redirect_url = $request->query('redirect_url');

        return redirect($cancel_redirect_url);
    }
}
