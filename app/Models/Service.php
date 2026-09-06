<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    // Deliberately excludes is_internal — it's a fixed system concept (one
    // seeded row) rather than something the admin Services CRUD form
    // should ever be able to set, so it can't be mass-assigned even by
    // accident.
    protected $fillable = ['name', 'slug', 'prefix'];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Auto-slugged from the name so admins never have to think about
        // it when adding a service — "Doctor Consultation" just becomes
        // /display/doctor-consultation on its own.
        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = static::uniqueSlug($service->name);
            }
        });

        // Now that services are admin-editable, a rename ("Doctor
        // Consultation" -> "Consultation") needs its /display/{slug} URL
        // to follow along — otherwise the old link keeps working under a
        // name that no longer matches anything in the UI.
        static::updating(function (Service $service) {
            if ($service->isDirty('name') && !$service->isDirty('slug')) {
                $service->slug = static::uniqueSlug($service->name, $service->id);
            }
        });
    }

    /**
     * A slug from the given name, deduplicated against every other
     * service (the column has a unique index) by appending -2, -3, etc.
     */
    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'service';
        $slug = $base;
        $suffix = 2;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * Real, client-facing departments — excludes the internal "Admin"
     * bucket that only exists to satisfy users.service_id for admin
     * accounts. Used anywhere a list of services is shown to the public
     * (kiosk homepage, display boards) — a real flag rather than matching
     * on the editable `name`, since admins can rename any service now.
     */
    public function scopePublicFacing(Builder $query): Builder
    {
        return $query->where('is_internal', false);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function clientQueues()
    {
        return $this->hasMany(ClientQueues::class);
    }

    /**
     * Waiting tickets for this service, priority lane first — the same
     * order a counter calls them in. A plain constrained hasMany rather
     * than an aliased eager load, so it can be `with()`-loaded alongside
     * `clientQueues` without the two constraints colliding.
     */
    public function waitingQueues()
    {
        return $this->hasMany(ClientQueues::class)
            ->where('status', 'waiting')
            ->orderByDesc('priority')
            ->orderBy('queue_number');
    }

}
