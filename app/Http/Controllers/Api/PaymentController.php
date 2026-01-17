<?php

namespace App\Http\Controllers\Api;

use Stripe\Stripe;
use App\Models\Payment;
use Stripe\PaymentIntent;
use App\Models\DeliveryJob;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Models\SystemSetting;
use App\Services\StuartService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PaymentNotification;
use Illuminate\Container\Attributes\Log;

class PaymentController extends Controller
{
    use ApiResponse;

    protected $stuart;


    public function __construct(StuartService $stuart)
    {
        $this->stuart = $stuart;
    }

    public function createStripeCheckout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $platformFee = SystemSetting::first();

        $deliveryJob = $this->stuart->getJob($request->input('deliver_job_id'));

        $pricing = $deliveryJob['pricing'];

        $priceTaxIncluded = $pricing['price_tax_included'];
        $priceTaxExcluded = $pricing['price_tax_excluded'];
        $tax_amount = $pricing['tax_amount'];

        // Stuart price (GBP)
        $amount = $priceTaxIncluded + $platformFee->platform_fee;

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'payment_intent_data' => [
                'capture_method' => 'manual',
            ],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => 'Stuart Delivery Payment',
                        'description' => "PriceTaxExcluded: £{$priceTaxExcluded}\n Tax: £{$tax_amount} Subtotal: £{$priceTaxIncluded}\n Platform Fee: £{$platformFee->platform_fee}",
                    ],
                    'unit_amount' => (int) round($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('checkout.cancel') . '?redirect_url=' . $request->get('cancel_redirect_url'),
            'metadata' => [
                'user_id'              => $user->id,
                'deliver_job_id'       => $deliveryJob['id'],
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
        Stripe::setApiKey(config('services.stripe.secret'));

        try {

            $session = Session::retrieve([
                'id' => $sessionId,
                'expand' => ['payment_intent']
            ]);

            $metadata = $session->metadata;
            $paymentIntent = $session->payment_intent;


            if ($session->payment_status === 'paid' || $paymentIntent->status === 'requires_capture') {
                $payment = Payment::updateOrCreate(
                    ['payment_intent_id' => $paymentIntent->id],
                    [
                        'user_id'        => $metadata->user_id,
                        'deliver_job_id' => $metadata->deliver_job_id,
                        'sub_total'      => $metadata->sub_total,
                        'amount'         => $metadata->amount,
                        'platform_fee'   => $metadata->platform_fee,
                        'status'         => 'payment_hold', // Authorized successfully
                        'payment_method' => 'stripe',
                    ]
                );

                // return redirect($metadata->success_redirect_url);

                return $this->success($payment, 'Payment created successfully', 200);
            }

            // return redirect($metadata->cancel_redirect_url);

            return $this->error([], 'Payment failed', 400);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function checkoutCancel(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect($request->redirect_url ?? null);
        }

        $checkoutSession = Session::retrieve($sessionId);
        $metadata        = $checkoutSession->metadata;

        $cancel_redirect_url = $metadata->cancel_redirect_url ?? null;

        return redirect($cancel_redirect_url);
    }

    public function deliveryCompleted($deliver_job_id)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $deliveryJob = $this->stuart->getJob($deliver_job_id);

        if (!$deliveryJob) {
            return $this->error([], 'Delivery job not found', 404);
        }

        $validStatuses = ['delivered', 'completed'];

        if (!in_array($deliveryJob['status'], $validStatuses)) {
            return $this->error([], 'Delivery job is not completed yet. Current status: ' . $deliveryJob['status'], 200);
        }

        $payment = Payment::where('deliver_job_id', $deliver_job_id)
            ->where('status', 'payment_hold')
            ->first();

        if ($payment && $payment->payment_intent_id) {
            try {
                $intent = PaymentIntent::retrieve($payment->payment_intent_id);

                if ($intent->status === 'requires_capture') {
                    $intent->capture();

                    $payment->update([
                        'status' => 'success',
                    ]);

                    return $this->success($payment, 'Payment captured successfully', 200);
                }

                return $this->error([], 'Payment is not in a capturable state: ' . $intent->status, 400);
            } catch (\Exception $e) {
                return $this->error([], 'Stripe Error: ' . $e->getMessage(), 500);
            }
        }

        return $this->error([], 'No hold payment found for this job', 404);
    }
}
