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
$result = Authentica::verifyFace(
    'user_123',
    Authentica::fileToBase64(storage_path('app/faces/reference.jpg')),
    Authentica::fileToBase64(storage_path('app/faces/capture.jpg'))
);
if ($result->successful()) {
    // Face match successful
} else {
    // Handle failed verification
    $error = $result->message();
}
```

### Voice Verification

```php
$result = Authentica::verifyVoice(
    'user_123',
    Authentica::fileToBase64(storage_path('app/voice/reference.wav')),
    Authentica::fileToBase64(storage_path('app/voice/capture.wav'))
);
if ($result->successful()) {
    // Voice match successful
} else {
    // Handle failed verification
    $error = $result->message();
}
```

---

## Practical Scenario: Face & Voice Verification

### Example: User Login with Face or Voice

1. **User uploads or captures a reference image/audio during registration.**
   - Store the reference file securely (e.g., in `storage/app/faces/` or `storage/app/voice/`).
2. **User attempts to log in and provides a real-time image or audio.**
3. **Convert both files to Base64:**
   ```php
   $referenceBase64 = Authentica::fileToBase64($referencePath); // e.g., reference.jpg or reference.wav
   $queryBase64 = Authentica::fileToBase64($queryPath); // e.g., capture.jpg or capture.wav
   ```
4. **Call the verification method:**
   - Face:
     ```php
     $result = Authentica::verifyFace($userId, $referenceBase64, $queryBase64);
     ```
   - Voice:
     ```php
     $result = Authentica::verifyVoice($userId, $referenceBase64, $queryBase64);
     ```
5. **Check the result:**
   ```php
   if ($result->successful()) {
       // Allow login or next step
   } else {
       // Show error message to user
       $error = $result->message();
   }
   ```

### Notes

- Both methods validate input and handle errors (invalid Base64, missing user ID, file too large, etc.).
- The response object provides helpers: `successful()`, `data()`, `message()`, etc.
- Always handle possible errors and inform the user accordingly.

---

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
