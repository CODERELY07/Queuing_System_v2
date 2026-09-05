<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Lets a department's display URL read as /display/doctor-consultation
     * instead of /display/2. Nullable at the column level only so this
     * migration can add it and backfill in one step without doctrine/dbal
     * (not installed) — the Service model fills it in on every create from
     * here on, so in practice it's never actually null.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        DB::table('services')->get()->each(function ($service) {
            DB::table('services')->where('id', $service->id)->update([
                'slug' => Str::slug($service->name),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
