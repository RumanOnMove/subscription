<?php

namespace App\Http\Requests;

use App\Enums\PaymentGatewaySlugEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MoveOn\Common\Traits\Validatable;
use MoveOn\Subscription\Enums\FeeType;
use MoveOn\Subscription\Enums\GatewayType;
use MoveOn\Subscription\Models\PaymentGateway;

class PaymentGatewayStoreRequest extends FormRequest
{
    use Validatable;
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
            'name'             => ['required', 'string', 'max:255'],
            'slug'             => ['required', Rule::in(PaymentGatewaySlugEnum::values()), Rule::unique((new PaymentGateway())->getTable())],
            'gateway_type'     => ['required', 'string', Rule::in(GatewayType::values())],
            'logo'             => ['required', 'string', 'max:255'],
            'url'              => ['required', 'string', 'max:255'],
            'fee'              => ['required', 'numeric', 'between:0,9999.99'],
            'fee_type'         => ['required', 'string', Rule::in(FeeType::values())],
            'is_active'        => ['boolean'],
            'customer_visible' => ['boolean'],
            ];
    }
}
