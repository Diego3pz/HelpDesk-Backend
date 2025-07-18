<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'category',
        'attachments',
        'status',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    protected $casts = [
        'attachments' => 'array',
    ];
}
