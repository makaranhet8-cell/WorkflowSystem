<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'destination',
        'purpose',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
{
    // ត្រូវប្រាកដថាប្រើ user_id ជា foreign key
    return $this->belongsTo(User::class, 'user_id');
}
}
