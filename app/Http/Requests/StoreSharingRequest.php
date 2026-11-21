<?php

namespace App\Http\Requests;

use App\Models\Sharing\DemoActor;
use Illuminate\Foundation\Http\FormRequest;

class StoreSharingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->attributes->get('sharing_actor') instanceof DemoActor;
    }

    public function rules(): array
    {
        return [
            'starts_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.today(config('circlo-sharing.timezone'))->toDateString()],
            'ends_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return ['starts_on' => 'start date', 'ends_on' => 'end date'];
    }
}
