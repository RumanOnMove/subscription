<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MoveOn\Common\Traits\Filterable;
use MoveOn\Subscription\Models\Plan;

class PlanGroup extends Model
{
    use HasFactory, SoftDeletes, Filterable;
    protected $guarded = [];

    # Plans
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, (new PlanGroupPlan())->getTable(), "plan_group_id", "plan_id")
            ->withPivot("is_primary", "is_trial");
    }

    # Trialled Plans
    public function trialledPlans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, (new PlanGroupPlan())->getTable(), "plan_group_id", "plan_id")
                    ->wherePivot('is_trial', true)
                    ->withPivot("is_primary", "is_trial")
                    ->with("gateway");
    }

    # Regular Plans
    public function regularPlans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, (new PlanGroupPlan())->getTable(), "plan_group_id", "plan_id")
                    ->wherePivot('is_trial', false)
                    ->withPivot("is_primary", "is_trial")
                    ->with("gateway");
    }
}
