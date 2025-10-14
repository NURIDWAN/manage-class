<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agreement_number',
        'agreement_date',
        'amount',
        'due_date',
        'status',
        'notes',
        'signature_path',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
