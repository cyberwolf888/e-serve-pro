<?php

// FR-GR-14 / FR-SW-07 / §9 / M7.8

namespace App\Http\Requests;

use App\Models\DiscussionComment;
use Illuminate\Foundation\Http\FormRequest;

class StoreDiscussionCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [DiscussionComment::class, $this->route('discussion')]);
    }

    public function rules(): array
    {
        return ['body' => ['required', 'string', 'max:10000']];
    }
}
