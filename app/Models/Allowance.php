<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Allowance extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ["id"];

    protected $casts = [
        "meta" => "json"
    ];

    public function allowances()
    {
        return $this->hasMany(AllowanceDetails::class);
    }

    public function storeRelatedAllowances()
    {
        return $this->hasMany(AllowanceDetails::class)->where("link_of_type", Store::class);
    }

    public function unlinkedAllowance()
    {
        return $this->hasOne(AllowanceDetails::class)
            ->whereNull("link_of_type")
            ->whereNull("link_of_id");
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
