<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StuartService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    use ApiResponse;

    protected $stuart;

    public function __construct(StuartService $stuart)
    {
        $this->stuart = $stuart;
    }

    public function createJob(Request $request)
    {
        $data = $request->validate([
            'pickup_address' => 'required|string',
            'sender_name' => 'required|string',
            'sender_phone' => 'required|string',
            'dropoff_address' => 'required|string',
            'receiver_name' => 'required|string',
            'receiver_phone' => 'required|string',
            'house_number' => 'nullable|string',
            'house_name' => 'nullable|string',
            'delivery_instructions' => 'nullable|string',
            'package_height' => 'required|numeric',
            'package_width' => 'required|numeric',
            'package_depth' => 'required|numeric',
            'package_weight' => 'required|numeric',
            'type' => 'required|string',
            'scheduled_time' => 'required|string|in:instant,scheduled',
            'date' => 'nullable|date',
            'time' => 'nullable|string',
        ]);

        try {
            $jobData = [
                'pickups' => [
                    [
                        'address' => $data['pickup_address'],
                        'contact' => [
                            'firstname' => $data['sender_name'],
                            'phone' => $data['sender_phone'],
                        ],
                        'comment' => 'Pickup location',
                    ],
                ],

                'dropoffs' => [
                    [
                        'address' => $data['dropoff_address'],
                        'contact' => [
                            'firstname' => $data['receiver_name'],
                            'phone' => $data['receiver_phone'],
                            'house_number' => $data['house_number'] ?? null,
                            'house_name' => $data['house_name'] ?? null,
                        ],

                        'comment' => $data['delivery_instructions'] ?? null,
                        'package_type' => $data['type'],
                        'client_reference' => json_encode([
                            'height' => $data['package_height'],
                            'width'  => $data['package_width'],
                            'depth'  => $data['package_depth'],
                            'weight' => $data['package_weight'],
                        ]),
                    ]
                ],
            ];

            // Scheduled delivery support
            if ($data['scheduled_time'] === 'scheduled') {
                $jobData['schedule'] = [
                    'pickup_at' => $data['date'] . 'T' . $data['time'] . ':00Z'
                ];
            }

            Log::info('Stuart API Request:', $jobData);

            $response = $this->stuart->createJob($jobData);

            Log::info('Stuart API Response:', ['response' => $response]);

            return $this->success($response, 'Delivery job created successfully.', 200);
        } catch (\Exception $e) {

            Log::error('Stuart API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->error($e->getMessage());
        }
    }

    public function getJob($jobId)
    {
        try {
            $response = $this->stuart->getJob($jobId);

            Log::info('Stuart Job Details:', ['job_id' => $jobId, 'response' => $response]);

            return $this->success($response, 'Job details fetched successfully.', 200);
        } catch (\Exception $e) {

            Log::error('Stuart API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->error($e->getMessage());
        }
    }
}
