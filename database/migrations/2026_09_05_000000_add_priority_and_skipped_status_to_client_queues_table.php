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
     *
     * Widening the enum isn't portable SQL at all: MySQL does it with
     * `ALTER ... MODIFY`; Postgres has no native ENUM here — the original
     * `$table->enum()` call compiled to a varchar with an inline, unnamed
     * CHECK constraint, which Postgres names `client_queues_status_check`
     * by its own default-naming convention; and SQLite (used by the test
     * suite — see phpunit.xml) can't ALTER a CHECK constraint at all, so
     * it needs the table rebuilt instead. Each driver gets its own path.
     */
    public function up(): void
    {
        $allowed = ['waiting', 'serving', 'finish', 'skipped'];

        match (DB::connection()->getDriverName()) {
            'pgsql' => $this->setPostgresStatusCheck($allowed),
            'sqlite' => $this->rebuildSqliteStatusColumn($allowed),
            default => DB::statement("ALTER TABLE client_queues MODIFY status ENUM('waiting', 'serving', 'finish', 'skipped') NOT NULL DEFAULT 'waiting'"),
        };

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
        // so a stray 'skipped' row doesn't get rejected/truncated below.
        DB::table('client_queues')->where('status', 'skipped')->update(['status' => 'waiting']);

        Schema::table('client_queues', function (Blueprint $table) {
            $table->dropColumn('priority');
        });

        $allowed = ['waiting', 'finish', 'serving'];

        match (DB::connection()->getDriverName()) {
            'pgsql' => $this->setPostgresStatusCheck($allowed),
            'sqlite' => $this->rebuildSqliteStatusColumn($allowed),
            default => DB::statement("ALTER TABLE client_queues MODIFY status ENUM('waiting', 'finish', 'serving') NOT NULL DEFAULT 'waiting'"),
        };
    }

    /**
     * Postgres: swap out the column's CHECK constraint for one allowing a
     * different set of values. `IF EXISTS` on the drop makes this safe to
     * call even if the constraint name ever drifts from Postgres's default.
     */
    private function setPostgresStatusCheck(array $allowedStatuses): void
    {
        $values = collect($allowedStatuses)->map(fn ($status) => "'{$status}'")->implode(', ');

        DB::statement('ALTER TABLE client_queues DROP CONSTRAINT IF EXISTS client_queues_status_check');
        DB::statement("ALTER TABLE client_queues ADD CONSTRAINT client_queues_status_check CHECK (status IN ({$values}))");
    }

    /**
     * SQLite has no ALTER CHECK CONSTRAINT of any kind, so the only way to
     * change the allowed values is to rebuild the table: create one with
     * the new constraint, copy every row across, then swap it in. Safe to
     * do unconditionally here since this only ever runs against the
     * test suite's fresh, empty SQLite database — never against real data.
     */
    private function rebuildSqliteStatusColumn(array $allowedStatuses): void
    {
        Schema::create('client_queues_tmp', function (Blueprint $table) use ($allowedStatuses) {
            $table->id();
            $table->string('name');
            $table->enum('status', $allowedStatuses)->default('waiting');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->unsignedInteger('queue_number');
            $table->timestamps();
            $table->unique(['service_id', 'queue_number']);
        });

        DB::statement('
            INSERT INTO client_queues_tmp (id, name, status, service_id, queue_number, created_at, updated_at)
            SELECT id, name, status, service_id, queue_number, created_at, updated_at FROM client_queues
        ');

        Schema::drop('client_queues');
        Schema::rename('client_queues_tmp', 'client_queues');
    }
};
