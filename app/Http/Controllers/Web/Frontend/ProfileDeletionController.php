<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileDeletionController extends Controller
{
    /**
     * Show the profile deletion confirmation page for authenticated users
     */
    public function showAuthenticated()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('frontend.layouts.pages.profile-delete-authenticated', compact('user'));
    }

    /**
     * Delete the authenticated user's profile
     */
    public function destroyAuthenticated()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        } elseif ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin accounts cannot be deleted'
            ], 403);
        }

        try {
            if ($user->avatar) {
                $imagePath = public_path($user->avatar);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Store user data for response
            $userName = $user->name;
            $userEmail = $user->email;

            Auth::logout();

            // Delete the user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Profile deleted successfully',
                'deleted_user' => [
                    'name' => $userName,
                    'email' => $userEmail
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
