<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientQueues extends Model
{
    protected $fillable = ['name', 'service_id', 'queue_number', 'status'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
