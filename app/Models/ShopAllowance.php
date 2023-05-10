<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MoveOn\Common\Traits\Filterable;
use Zoha\Metable;

class ShopAllowance extends Model
{
    use Filterable;
    use HasFactory;
    use SoftDeletes;
    use Metable;

    protected $guarded = ["id"];

    protected $metaTable = 'shop_allowance_meta';

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
