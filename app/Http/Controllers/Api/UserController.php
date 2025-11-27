<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;

    public function onbodding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|string|max:50',
            'longitude' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            $user = auth()->user();

            if (!$user) {
                return $this->error([], 'User not found', 404);
            }

            $user->name = $request->input('name');
            $user->address = $request->input('address');
            $user->latitude = $request->input('latitude');
            $user->longitude = $request->input('longitude');
            $user->save();

            return $this->success($user, 'User Onboarding', 200);
        } catch (\Exception $e) {

            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function profile()
    {
        $user = auth()->user();

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

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        return $this->success($user, 'User Profile', 200);
    }

    public function updateProfile(Request $request) {}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'Logout successful', 200);
    }
}
