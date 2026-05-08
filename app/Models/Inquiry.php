<?php

namespace App\Models;

use App\Enums\InquiryPreferredContactMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'recipient_user_id',
        'name',
        'email',
        'phone',
        'message',
        'preferred_contact_method',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_contact_method' => InquiryPreferredContactMethod::class,
            'submitted_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }
}
