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
        Schema::create('client_queues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['waiting', 'finish', 'serving'])->default('waiting');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->unsignedInteger('queue_number');
            $table->timestamps();
            $table->unique(['service_id', 'queue_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_queues');
    }
};
