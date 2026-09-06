<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientQueues extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'service_id', 'queue_number', 'status', 'priority'];

    protected $casts = [
        'priority' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope a query to a given status (waiting, serving, finish, skipped).
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to a given service.
     */
    public function scopeForService(Builder $query, int $serviceId): Builder
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Order waiting tickets the way a counter should call them: priority
     * lane (senior/PWD/pregnant) ahead of the regular line, oldest ticket
     * first within each.
     */
    public function scopeNextInLine(Builder $query): Builder
    {
        return $query->orderByDesc('priority')->orderBy('queue_number');
    }

    /**
     * Filter by the topbar's "Search for anything" box — matches a name or
     * an exact ticket number, whichever the term looks like.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%");

            if (is_numeric($term)) {
                $q->orWhere('queue_number', (int) $term);
            }
        });
    }

    /**
     * Order by a table-header column, restricted to a known-safe allowlist
     * so `sort=`/`dir=` from the query string can never reach raw SQL.
     */
    public function scopeSortBy(Builder $query, ?string $column, ?string $direction): Builder
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return match ($column) {
            'name' => $query->orderBy('name', $direction),
            'status' => $query->orderBy('status', $direction),
            'created_at' => $query->orderBy('created_at', $direction),
            'deleted_at' => $query->orderBy('deleted_at', $direction),
            default => $query->orderBy('queue_number', $direction),
        };
    }

    /**
     * The ticket number shown to clients and staff, e.g. "GEN-007".
     * Falls back to a generic prefix if the related service is unavailable.
     */
    public function formattedNumber(): string
    {
        return static::formatNumber($this->queue_number, $this->service->prefix ?? null);
    }

    /**
     * Build a formatted ticket number from a raw queue number and prefix,
     * for the few call sites that already have the prefix on hand.
     */
    public static function formatNumber(int $queueNumber, ?string $prefix): string
    {
        return ($prefix ?: 'Q') . '-' . str_pad((string) $queueNumber, 3, '0', STR_PAD_LEFT);
    }
}
