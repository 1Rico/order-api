<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_date',
        'to_date',
        'discount_amount',
        'voucher_code',
    ];

    protected $dates = [
        'from_date',
        'to_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return Carbon::parse($this->attributes['to_date'])->gte(Carbon::now()->toDateTimeString())
            && $this->attributes['times_used']  < 1;
    }
}
