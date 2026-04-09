<?php

namespace Authentica\LaravelAuthentica\Traits;

use Illuminate\Support\Facades\Validator;
use Authentica\LaravelAuthentica\Exceptions\ValidationException;

trait ValidatesRequests
{
    protected function validate(array $rules, array $data): void
    {
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->first(), $validator->errors());
        }
    }

    protected function validateOtpRequest(array $data): void
    {
        $rules = [
            'method' => 'sometimes|in:sms,whatsapp,email',
            'phone' => 'required_if:method,sms,whatsapp|nullable|phone:SA',
            'email' => 'required_if:method,email|nullable|email',
            'otp' => 'nullable|numeric|digits_between:4,10',
            'template_id' => 'nullable|integer|min:1',
            'fallback_phone' => 'nullable|phone:SA',
            'fallback_email' => 'nullable|email',
        ];

        // Conditional requirements
        if (isset($data['method'])) {
            if (in_array($data['method'], ['sms', 'whatsapp']) && empty($data['phone'])) {
                throw new ValidationException('Phone number is required for SMS/WhatsApp methods');
            }
            if ($data['method'] === 'email' && empty($data['email'])) {
                throw new ValidationException('Email address is required for email method');
            }
        }

        $this->validate($rules, $data);
    }

    protected function validateFaceRequest(string $userId, string $registered, string $query): void
    {
        $this->validateMedia($userId, $registered, $query, 'face');
    }

    protected function validateVoiceRequest(string $userId, string $registered, string $query): void
    {
        $this->validateMedia($userId, $registered, $query, 'voice');
    }
}