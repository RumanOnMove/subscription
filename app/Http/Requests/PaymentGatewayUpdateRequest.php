<?php

namespace App\Http\Requests;


use App\Enums\PaymentGatewaySlugEnum;
use Illuminate\Validation\Rule;
use MoveOn\Common\Traits\Validatable;
use MoveOn\Subscription\Models\PaymentGateway;

class PaymentGatewayUpdateRequest extends PaymentGatewayStoreRequest
{
    use Validatable;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $paymentGateway = $this->route('payment_gateway');
        $rules = parent::rules();
        $rules['name'] = [
            'required',
            'string',
            'max:255',
        ];
        $rules["slug"] = [
            'required',
            Rule::in(PaymentGatewaySlugEnum::values()),
            Rule::unique((new PaymentGateway())->getTable())->ignore($paymentGateway->id),
        ];
        return $rules;
    }
}
