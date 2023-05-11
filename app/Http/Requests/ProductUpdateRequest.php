<?php

namespace App\Http\Requests;

use App\Enums\PaymentGatewaySlugEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MoveOn\Common\Traits\Validatable;
use MoveOn\Subscription\Models\PaymentGateway;

class ProductUpdateRequest extends FormRequest
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
        $rules = [
            "gateway_id"  => ["required", Rule::exists((new PaymentGateway)->getTable(), "id")],
            "name"        => ["string", "max:200"],
            "image_url"   => ["string", "max:200"],
            "home_url"    => ["string", "max:200"],
            "description" => ["required", "string", "max:200"],
            "category"    => ["required", "string", "max:100"],
        ];

        $gateway = PaymentGateway::find(request("gateway_id"));

        if(empty($gateway)) return $rules;

        if ($gateway->slug == PaymentGatewaySlugEnum::STRIPE()) {
            $rules['description'][] = "required";
            $rules['category'][]    = "required";
            $rules['name'][]        = "required";
        }

        return $rules;
    }
}
