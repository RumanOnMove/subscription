<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MoveOn\Common\Traits\Validatable;

class UpdateSubscriptionRequest extends FormRequest
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
            "action" => ["required", Rule::in(["cancel"])],
        ];
    }

    public function messages(): array
    {
        return [
            "action.in" => "Invalid action given. Valid actions: {cancel}"
        ];
    }
}
