<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MoveOn\Subscription\Models\Subscription;
use Zoha\Metable;

class User extends Model
{
    use HasFactory, HasUuids, Metable;

    protected $fillable = [
        'name',
        'email'
    ];

    /**
     * Subscriptions
     * @return MorphMany
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'owner');
    }

    public function userAbilities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserAbility::class, "user_id");
    }
}
