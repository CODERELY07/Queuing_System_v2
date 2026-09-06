<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every place that hides the internal "Admin" service from the kiosk
     * homepage and the public display boards did it by matching the
     * literal string "Admin" — fine while services were seed-only, but
     * now that admins can rename any service through the CRUD UI, renaming
     * that one row would make it reappear as a bookable department. A real
     * flag survives a rename; a name comparison doesn't.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_internal')->default(false)->after('prefix');
        });

        // Backfill: whichever row is currently named "Admin" (case
        //-insensitive, matching the checks this replaces) is the one
        // that's actually internal.
        \App\Models\Service::whereRaw('LOWER(name) = ?', ['admin'])->update(['is_internal' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('is_internal');
        });
    }
};
