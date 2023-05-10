<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MoveOn\Subscription\Models\Discount;
use MoveOn\Subscription\Models\Plan;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "plan_id"     => ["required", Rule::exists((new Plan())->getTable(), "id")],
            "coupon"      => ["string", "max:200", Rule::exists((new Discount())->getTable(), "coupon_code")],
        ];
    }
}
