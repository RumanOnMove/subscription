<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ForceFail implements Rule
{
    public function __construct(private string $message){

    }

    /**
     * Determine if the validation rule passes.
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return false;
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }
}
