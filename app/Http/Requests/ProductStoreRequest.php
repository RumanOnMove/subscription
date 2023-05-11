<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MoveOn\Common\Traits\Validatable;
use MoveOn\Subscription\Models\PaymentGateway;

class ProductStoreRequest extends FormRequest
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
            "name"        => ["required", "string", "max:200"],
            "category"    => ["string", "max:100"],
            "description" => ["string", "max:200"],
            "image_url"   => ["string", "max:200"],
            "home_url"    => ["string", "max:200"],
            "gateway_id"  => ["required", Rule::exists((new PaymentGateway)->getTable(), "id")],
        ];
    }
}
