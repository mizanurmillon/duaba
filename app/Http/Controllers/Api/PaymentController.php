<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryJob;
use App\Models\Payment;
use App\Models\SystemSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Checkout\Session;
use Stripe\Stripe;

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

        $platformFee = SystemSetting::first();

        $deliveryJob = DeliveryJob::find($request->input('deliver_job_id'));

        $priceTaxIncluded = $deliveryJob->stuart_response['pricing']['price_tax_included'];
        $priceTaxExcluded = $deliveryJob->stuart_response['pricing']['price_tax_excluded'];
        $tax_amount = $deliveryJob->stuart_response['pricing']['tax_amount'];

        // Stuart price (GBP)
        $amount = $priceTaxIncluded + $platformFee->platform_fee;

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => 'Stuart Delivery Payment',
                        'description' => "PriceTaxExcluded: £{$priceTaxExcluded}\n Tax: £{$tax_amount} Subtotal: £{$priceTaxIncluded}\n Platform Fee: £{$platformFee->platform_fee}",
                    ],
                    'unit_amount' => $amount * 100,
                ],
                'quantity' => 1,
            ]],
            'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('checkout.cancel') . '?redirect_url=' . $request->get('cancel_redirect_url'),
            'metadata' => [
                'user_id'              => $user->id,
                'deliver_job_id'       => $deliveryJob->id,
                'sub_total'            => $priceTaxIncluded,
                'amount'               => $amount,
                'platform_fee'         => $platformFee->platform_fee,
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

            //metadata
            $success_redirect_url = $session->metadata->success_redirect_url ?? '/';
            $user = $session->metadata->user_id;
            $deliver_job_id = $session->metadata->deliver_job_id;
            $sub_total = $session->metadata->sub_total;
            $amount = $session->metadata->amount;
            $platform_fee = $session->metadata->platform_fee;

            if ($session->payment_status === 'paid') {
                Payment::create([
                    'user_id'        => $user,
                    'deliver_job_id' => $deliver_job_id,
                    'sub_total'     => $sub_total,
                    'amount'        => $amount,
                    'platform_fee'  => $platform_fee,
                    'status'        => 'success',
                    'payment_method' => 'stripe',
                ]);
            } else if ($session->payment_status === 'unpaid') {
                Payment::create([
                    'user_id'        => $user,
                    'deliver_job_id' => $deliver_job_id,
                    'sub_total'     => $sub_total,
                    'amount'        => $amount,
                    'platform_fee'  => $platform_fee,
                    'status'        => 'failed',
                    'payment_method' => 'stripe',
                ]);
            }

            return redirect($success_redirect_url);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function checkoutCancel(Request $request)
    {
        return redirect('/payment/cancel');
    }
}


