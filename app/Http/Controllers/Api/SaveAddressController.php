<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SaveAddress;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaveAddressController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $addresses = SaveAddress::where('user_id', $user->id)->get();

        return $this->success($addresses, 'Saved addresses retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'label' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $data = SaveAddress::create([
            'user_id' => $user->id,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'label' => $request->label,
        ]);

        if (!$data) {
            return $this->error([], 'Failed to save address', 500);
        }

        return $this->success($data, 'Address saved successfully', 201);
    }

    public function show($addressId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $address = SaveAddress::where('id', $addressId)->where('user_id', $user->id)->first();

        if (!$address) {
            return $this->error([], 'Address not found', 404);
        }

        return $this->success($address, 'Address retrieved successfully');
    }

    public function update(Request $request, $addressId)
    {
        $validator = Validator::make($request->all(), [
            'address_line1' => 'sometimes|required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:100',
            'postal_code' => 'sometimes|required|string|max:20',
            'country' => 'sometimes|required|string|max:100',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'label' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $address = SaveAddress::where('id', $addressId)->where('user_id', $user->id)->first();

        if (!$address) {
            return $this->error([], 'Address not found', 404);
        }

        $address->update($request->only([
            'address_line1',
            'address_line2',
            'city',
            'state',
            'postal_code',
            'country',
            'latitude',
            'longitude',
            'label',
        ]));

        return $this->success($address, 'Address updated successfully');
    }

    public function destroy($addressId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $address = SaveAddress::where('id', $addressId)->where('user_id', $user->id)->first();

        if (!$address) {
            return $this->error([], 'Address not found', 404);
        }

        $address->delete();

        return $this->success(null, 'Address deleted successfully');
    }
}
