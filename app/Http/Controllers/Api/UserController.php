<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;

    public function setName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
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
            $user->save();

            return $this->success($user, 'User Onboarding', 200);
        } catch (\Exception $e) {

            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function setAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

            $user = User::updateOrCreate(
                ['id' => $user->id],
                [
                    'address' => $request->input('address'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                ]
            );

            return $this->success($user, 'User Address Set', 200);
        } catch (\Exception $e) {

            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function profile()
    {
        $user = auth()->user();

        if ($user->name == null) {
            // User needs to complete onboarding
            $user->setAttribute('setname', false);
        } else {
            $user->setAttribute('setname', true);
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

    public function changePassword(Request $request) {}

    public function deleteAccount(Request $request) {

       try {
            // Get the authenticated user
            $user = auth()->user();

            // Delete the user's avatar if it exists
            if ($user->avatar) {
                $previousImagePath = public_path($user->avatar);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }

            // Delete the user
            $user->delete();

            return $this->success([], 'User deleted successfully', '200');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
        
    }
}
