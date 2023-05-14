<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MoveOn\Common\Traits\Validatable;
use MoveOn\Subscription\Models\GatewayAssociatedCustomer;
use MoveOn\Subscription\Models\Discount;
use MoveOn\Subscription\Models\PaymentGateway;
use MoveOn\Subscription\Models\Plan;

class SubscriptionStoreRequest extends FormRequest
{
    use Validatable;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
       return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "plan_id"     => ["required", Rule::exists((new Plan())->getTable(), "id")],
            "coupon"      => ["string", "max:200", Rule::exists((new Discount())->getTable(), "coupon_code")],
        ];
    }

}
