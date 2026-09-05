<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the two states the design spec calls for that the original
     * enum didn't have: a priority lane (senior/PWD/pregnant, called out
     * of turn) and a no-show state for tickets that were called and never
     * showed up. `status` stays a plain enum column rather than moving to
     * doctrine/dbal-backed ->change(), since the package isn't installed.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE client_queues MODIFY status ENUM('waiting', 'serving', 'finish', 'skipped') NOT NULL DEFAULT 'waiting'");

        Schema::table('client_queues', function (Blueprint $table) {
            $table->boolean('priority')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fold skipped tickets back into waiting before the enum shrinks,
        // so a stray 'skipped' row doesn't get truncated by MySQL.
        DB::table('client_queues')->where('status', 'skipped')->update(['status' => 'waiting']);

        Schema::table('client_queues', function (Blueprint $table) {
            $table->dropColumn('priority');
        });

        DB::statement("ALTER TABLE client_queues MODIFY status ENUM('waiting', 'finish', 'serving') NOT NULL DEFAULT 'waiting'");
    }
};
