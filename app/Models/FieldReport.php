<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldReport extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'submitted_at' => 'datetime:Y-m-d H:i',
            'approved_at' => 'datetime:Y-m-d H:i',
        ];
    }
}
