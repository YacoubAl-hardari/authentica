
![alt text](authentica.png)

# Authentica Laravel Package

Easily integrate Authentica's secure authentication (OTP, Face, Voice, SMS) into your Laravel app.

## Installation

```bash
composer require authentica/authentica
```

## Publish Configuration

```bash
php artisan vendor:publish --tag=authentica-config
```

Or publish all vendor configs:

```bash
php artisan vendor:publish --tag=config
```

## Environment Setup

Add the following to your `.env` file (see `.env.example` for all options):

```
AUTHENTICA_API_KEY=your_api_key_here
AUTHENTICA_BASE_URL=https://api.authentica.sa/api/v2
AUTHENTICA_DEFAULT_CHANNEL=sms # sms, whatsapp, email 
AUTHENTICA_FALLBACK_CHANNEL=email # optional
AUTHENTICA_DEFAULT_TEMPLATE_ID=31 # optional, default: 1
AUTHENTICA_FALLBACK_TEMPLATE_ID=2 # optional, default: 2
AUTHENTICA_DEFAULT_SENDER_NAME=MyBrand # optional
AUTHENTICA_DEFAULT_EMAIL=your@email.com # optional
```

## Usage

Import the facade at the top of your file:

```php
use Authentica\LaravelAuthentica\Facades\Authentica;
```

### Send OTP with Fallback

```php
Authentica::sendOtp([
    'phone' => '+966551234567',
    'method' => 'sms',
    'otp' => '123456', // Optional custom OTP
    'fallback_email' => 'user@example.com', // Optional fallback
]);
```

### Verify OTP

```php
$result = Authentica::verifyOtp('123456', '+966551234567');
if ($result->successful()) {
    // Authentication successful
}
```

### Face Verification

```php
Authentica::verifyFace(
    'user_123',
    Authentica::fileToBase64(storage_path('app/faces/reference.jpg')),
    Authentica::fileToBase64(storage_path('app/faces/capture.jpg'))
);
```

### Voice Verification

```php
Authentica::verifyVoice(
    'user_123',
    Authentica::fileToBase64(storage_path('app/voice/reference.wav')),
    Authentica::fileToBase64(storage_path('app/voice/capture.wav'))
);
```

### Send Custom SMS

```php
Authentica::sendSms(
    '+966551234567',
    'Your order #12345 has shipped!',
    'MyBrand' // Registered sender name
);
```

### Get Balance

```php
$balance = Authentica::getBalance()->credits;
```

## Advanced

- You can inject `AuthenticaClient` directly if you prefer dependency injection.
- All methods return an `AuthenticaResponse` object with helpers like `successful()`, `data()`, `message()`, etc.

## Troubleshooting

- Ensure you have published the config and set all required `.env` variables.
- For more details, see the [Authentica API documentation](https://portal.authentica.sa/docs/).

---
## Support

For technical inquiries: yacoub@yacoubalhaidari.com
