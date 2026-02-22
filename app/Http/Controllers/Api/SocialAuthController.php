<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use ApiResponse;

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider'    => 'required|in:google,apple',
            'provider_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation failed', 422);
        }

        $googleIdToken = $request->input('provider_id');

        try {
            // 1. VERIFY GOOGLE ID TOKEN
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $googleIdToken,
            ]);

            if (!$response->successful()) {
                return $this->error([], 'Invalid Google ID token', 401);
            }

            $payload = $response->json();

            // 2. VALIDATE AUDIENCE (SECURITY CRITICAL)
            if ($payload['aud'] !== config('services.google.client_id')) {
                return $this->error([], 'Invalid token audience', 401);
            }

            $email     = $payload['email'];
            $name      = $payload['name'] ?? 'Unknown';
            $avatarUrl = $payload['picture'] ?? null;
            $googleUid = $payload['sub'];
        } catch (\Throwable $e) {
            return $this->error(['error' => $e->getMessage()], 'Google authentication failed', 401);
        }

        // 3. FIND OR CREATE USER
        $user = User::where('email', $email)->first();

        $avatarPath = null;

        if ($avatarUrl) {


            try {
                if ($user->avatar) {
                    $previousImagePath = public_path($user->avatar);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }

                $image = Http::timeout(10)->get($avatarUrl);

                // return $image;

                if ($image->successful()) {
                    $imageName = time() . '.jpg';
                    $folder    = '/uploads/profileImages';
                    $path      = public_path($folder);

                    if (! file_exists($path)) {
                        mkdir($path, 0755, true);
                    }

                    file_put_contents($path . '/' . $imageName, $image->body());
                    $avatarPath = $folder . '/' . $imageName;
                }
            } catch (Exception $e) {
            }
        }

        if ($user) {
            $user->update([
                'provider'    => $request->input('provider'),
                'provider_id' => $googleUid,
                'name'        => $name,
                'avatar' => $avatarPath ? $avatarPath : $user->avatar,
            ]);
        } else {
            $user = User::create([
                'name'              => $name,
                'email'             => $email,
                'password'          => bcrypt(Str::random(32)),
                'provider'          => $request->input('provider'),
                'provider_id'       => $googleUid,
                'avatar'       => $avatarPath,
                'email_verified_at' => now(),
            ]);
        }

        // 4. CREATE SANCTUM TOKEN
        $user->setAttribute('token', $user->createToken($user->email)->plainTextToken);


        return $this->success($user, 'Login successful', 200);
    }


    public function appleLogin(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'token'    => 'required',
            'provider' => 'required|in: google ,apple',
        ]);

        $provider = $validated['provider'];
        $token = $validated['token'];

        $socialiteUser = Socialite::driver($provider)->stateless()->userFromToken($token);


        if (!$socialiteUser || !$socialiteUser->getEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid social token or missing email.',
            ], 422);
        }

        if ($socialiteUser->getAvatar()) {
            try {
                $image = Http::timeout(10)->get($socialiteUser->getAvatar());

                if ($image->successful()) {
                    $imageName = time() . '.jpg';
                    $folder    = '/uploads/profileImages';
                    $path      = public_path($folder);

                    if (! file_exists($path)) {
                        mkdir($path, 0755, true);
                    }

                    file_put_contents($path . '/' . $imageName, $image->body());
                    $avatarPath = $folder . '/' . $imageName;
                }
            } catch (Exception $e) {
                $avatarPath = null;
            }
        } else {
            $avatarPath = null;
        }

        $user = User::where('email', $socialiteUser->getEmail())
            ->where('provider', $provider)
            ->where('provider_id', $socialiteUser->getId())
            ->first();

        if (!$user) {
            $password = Str::random(16);

            $user = User::create(
                [
                    'email' => $socialiteUser->getEmail(),
                    'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname() ?? 'Apple User',
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'avatar' => $avatarPath,
                    'provider' => $provider,
                    'provider_id' => $socialiteUser->getId()
                ],

            );
        }

         $user->setAttribute('token', $user->createToken($user->email)->plainTextToken);

        return $this->success($user, 'Login successful', 200);
    }
}
