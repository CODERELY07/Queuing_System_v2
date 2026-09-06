<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Delete Old Queues" used to hard-delete rows outright, which quietly
     * erased the history the admin analytics cards need (finished/skipped
     * counts, daily trend). Soft deleting keeps every row on record while
     * still hiding it from the active queue list and today's counters.
     */
    public function up(): void
    {
        Schema::table('client_queues', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_queues', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
