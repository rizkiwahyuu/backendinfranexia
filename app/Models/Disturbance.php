<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disturbance extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime:Y-m-d H:i',
            'resolved_at' => 'datetime:Y-m-d H:i',
        ];
    }
}
