<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'due_date',
        'is_public',
    ];

    protected $casts = [
        'due_date'  => 'date',
        'is_public' => 'boolean',
    ];

    // Relasi: Task dimiliki oleh 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}