<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            
            $table->string('pickup_address');
            $table->string('sender_name');
            $table->string('sender_phone');
            
            $table->string('dropoff_address');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            
            $table->string('house_number')->nullable();
            $table->string('house_name')->nullable();

            $table->string('delivery_instructions')->nullable();

            $table->float('package_height');
            $table->float('package_width');
            $table->float('package_depth');
            $table->float('package_weight');
            $table->string('package_type');

            $table->string('schedule_type'); // instant or scheduled
            $table->string('schedule_date')->nullable();
            $table->string('schedule_time')->nullable();
            
            $table->string('stuart_job_id')->nullable();
            $table->json('stuart_response')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_jobs');
    }
};
