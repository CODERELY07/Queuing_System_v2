<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function clientQueues()
    {
        return $this->hasMany(ClientQueues::class);
    }
}
