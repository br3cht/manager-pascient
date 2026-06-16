<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function Address()
    {
        return $this->belongsTo(Address::class);
    }
}
