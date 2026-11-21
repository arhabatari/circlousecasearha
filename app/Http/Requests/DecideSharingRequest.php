<?php

namespace App\Http\Requests;

use App\Models\Sharing\DemoActor;
use App\Models\Sharing\SharingRequest;
use Illuminate\Foundation\Http\FormRequest;

class DecideSharingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actor = $this->attributes->get('sharing_actor');
        $sharingRequest = $this->route('sharingRequest');

        return $actor instanceof DemoActor && $sharingRequest instanceof SharingRequest
            && $sharingRequest->owner_id === $actor->id;
    }

    public function rules(): array
    {
        return ['rejection_reason' => ['nullable', 'string', 'max:1000']];
    }
}
