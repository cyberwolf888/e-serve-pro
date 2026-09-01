<?php

// FR-GR-14 / §9 / M7.8

namespace App\Http\Requests;

use App\Models\DiscussionTopic;
use Illuminate\Foundation\Http\FormRequest;

class StoreDiscussionTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [DiscussionTopic::class, $this->route('class')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
        ];
    }
}
