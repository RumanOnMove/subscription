<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MoveOn\Common\Traits\Validatable;
use MoveOn\Subscription\Models\Plan;
use MoveOn\Subscription\Models\Subscription;

class UpDownSubscriptionRequest extends FormRequest
{
    use Validatable;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "new_plan_id"     => ["required", Rule::exists((new Plan())->getTable(), "id")],
            "subscription_id" => ["required", Rule::exists((new Subscription())->getTable(), "id")],
        ];
    }
}
