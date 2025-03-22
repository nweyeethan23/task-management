<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'priority', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
