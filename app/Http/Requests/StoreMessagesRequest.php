<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'message' => ['required', 'string'],
            'conversation_id' => [
                'int',
                'exists:conversations,id',
                'required_without:user_id'
            ],
            'user_id' => [
                'int',
                'exists:users,id',
                'required_without:conversation_id'
            ]
        ];
    }
    public function messages()
    {
        return [
            'message.required' => __('Message is required'),
            'conversation_id.required_if' => __('Conversation is required'),
            'user_id.required_if' => __('User is required'),
        ];
    }
}
