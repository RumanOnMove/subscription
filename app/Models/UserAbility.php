<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MoveOn\Common\Traits\Filterable;

class UserAbility extends Model
{
    use Filterable;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ["id"];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function model()
    {
        return $this->morphTo("model");
    }
}
