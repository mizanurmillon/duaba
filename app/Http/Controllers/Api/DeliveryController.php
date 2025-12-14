<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryJob;
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

        $user = auth()->user();

        if (!$user) {
            return $this->error('Unauthorized', 401);
        }

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
                        'comment'        => $data['delivery_instructions'] ?? null,
                        'package_type'   => $data['type'],
                        'client_reference' => $user->id . '-' . now()->timestamp,
                        'dimensions'    => [
                            'height' => $data['package_height'],
                            'width'  => $data['package_width'],
                            'length'  => $data['package_depth'],
                            'weight' => $data['package_weight'],
                        ],

                    ]
                ],
            ];

            if ($data['scheduled_time'] === 'scheduled') {
                $jobData['schedule'] = [
                    'pickup_at' => $data['date'] . 'T' . $data['time'] . ':00Z'
                ];
            }

            Log::info('Stuart API Request:', $jobData);

            // Call Stuart API
            $response = $this->stuart->createJob($jobData);

            Log::info('Stuart API Response:', ['response' => $response]);

            // Extract Stuart Job ID
            $stuartJobId = $response['id'] ?? null;

            // Save into database
            $job = DeliveryJob::create([
                'user_id' => $user->id,
                'pickup_address' => $data['pickup_address'],
                'sender_name' => $data['sender_name'],
                'sender_phone' => $data['sender_phone'],

                'dropoff_address' => $data['dropoff_address'],
                'receiver_name' => $data['receiver_name'],
                'receiver_phone' => $data['receiver_phone'],

                'house_number' => $data['house_number'] ?? null,
                'house_name' => $data['house_name'] ?? null,
                'delivery_instructions' => $data['delivery_instructions'] ?? null,

                'package_height' => $data['package_height'],
                'package_width' => $data['package_width'],
                'package_depth' => $data['package_depth'],
                'package_weight' => $data['package_weight'],
                'package_type' => $data['type'],

                'schedule_type' => $data['scheduled_time'],
                'schedule_date' => $data['date'] ?? null,
                'schedule_time' => $data['time'] ?? null,

                'stuart_job_id' => $stuartJobId,
                'stuart_response' => $response,
            ]);

            return $this->success($job, 'Delivery job created & stored successfully.', 200);
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

    public function getJobs(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return $this->error('Unauthorized', 401);
            }

            // Fetch all jobs from Stuart API
            $jobs = $this->stuart->getJobs();

            // Optional status filter from request
            $filterStatus = strtolower($request->status ?? '');

            // Filter jobs based on user & optional status
            $filteredJobs = collect($jobs)->filter(function ($job) use ($user, $filterStatus) {

                // Skip if no deliveries
                if (empty($job['deliveries'])) {
                    return false;
                }

                $matchUser = false;

                // Check all deliveries in a job
                foreach ($job['deliveries'] as $delivery) {
                    if (!isset($delivery['client_reference'])) continue;

                    // client_reference format: userId-uniqueId
                    $parts = explode('-', $delivery['client_reference']);
                    $deliveryUserId = $parts[0] ?? null;

                    if ($deliveryUserId == $user->id) {
                        $matchUser = true;
                        break; // matched, no need to check other deliveries
                    }
                }

                if (!$matchUser) return false;

                // Status filter (optional)
                if ($filterStatus) {
                    // Job main status
                    $jobStatus = strtolower($job['status'] ?? '');

                    // Collect all delivery statuses
                    $deliveryStatuses = collect($job['deliveries'])->pluck('status')->map(fn($s) => strtolower($s));

                    // Match if either job status or any delivery status matches
                    if ($jobStatus === $filterStatus || $deliveryStatuses->contains($filterStatus)) {
                        return true;
                    }

                    return false;
                }

                return true;
            })->values();

            Log::info('Filtered Stuart Jobs', [
                'user_id' => $user->id,
                'filter_status' => $filterStatus ?: 'all',
                'total_jobs' => $filteredJobs->count(),
            ]);

            return $this->success($filteredJobs, 'Filtered jobs fetched successfully.', 200);
        } catch (\Exception $e) {

            Log::error('Stuart API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->error($e->getMessage());
        }
    }
}
