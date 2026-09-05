<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected static function booted(): void
    {
        // Auto-slugged from the name so admins never have to think about
        // it when adding a service — "Doctor Consultation" just becomes
        // /display/doctor-consultation on its own.
        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
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
