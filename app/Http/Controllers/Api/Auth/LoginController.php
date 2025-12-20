<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\EmailOtp;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Mail\VerificationOtp;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use ApiResponse;

    private function sendOtp($user)
    {
        $code = rand(100000, 999999);

        // Store verification code in the database
        $verification = EmailOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'verification_code' => $code,
                'expires_at'        => Carbon::now()->addMinutes(3),
            ]
        );

        Mail::to($user->email)->send(new VerificationOtp($user, $code));

        return $code;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            // Check if user already exists
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Existing user → send OTP only
                $otp = $this->sendOtp($user);
            } else {
                // New user → create first, then send OTP
                $user = User::create([
                    'email' => $request->email,
                ]);

                $otp = $this->sendOtp($user);
            }
            return $this->success(
                ['otp' => $otp],
                'OTP sent to your email. Please verify to complete login.',
                200
            );
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function otpResend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->email)->first();

        $otp = $this->sendOtp($user);

        return $this->success(['otp' => $otp], 'OTP resent to your email.', 200);
    }

    public function otpVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            // Retrieve the user by email
            $user = User::where('email', $request->input('email'))->first();

            $verification = EmailOtp::where('user_id', $user->id)
                ->where('verification_code', $request->input('otp'))
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if ($verification) {
                $user->email_verified_at = Carbon::now();
                $user->save();

                $verification->delete();

                if (
                    $user->name == null || $user->address == null ||
                    $user->latitude == null ||
                    $user->longitude == null
                ) {
                    // User needs to complete onboarding
                    $user->setAttribute('onboarding', false);
                } else {
                    $user->setAttribute('onboarding', true);
                }

                // Generate API token
                $user->setAttribute('token', $user->createToken($user->email)->plainTextToken);

                return $this->success($user, 'OTP Verified Successfully', 200);
            } else {

                return $this->error([], 'Invalid or expired OTP', 400);
            }
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
