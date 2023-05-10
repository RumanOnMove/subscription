<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MoveOn\Common\Traits\Filterable;

class Shop extends Model
{
    use Filterable;
    use HasFactory;

    protected $guarded = ["id"];

    public function websites()
    {
        return $this->hasMany(Website::class, "shop_id");
    }
}
