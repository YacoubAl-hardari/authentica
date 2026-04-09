<?php

namespace Authentica\LaravelAuthentica;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Authentica\LaravelAuthentica\Exceptions\AuthenticaException;
use Authentica\LaravelAuthentica\Traits\ValidatesRequests;
use Authentica\LaravelAuthentica\Traits\HandlesMedia;
use Authentica\LaravelAuthentica\Support\Response as AuthenticaResponse;

class AuthenticaClient
{
    use ValidatesRequests, HandlesMedia;

    protected Client $httpClient;
    protected array $config;
    protected array $headers;

    public function __construct(array $config = [])
    {
        $this->config = array_merge(config('authentica', []), $config);
        $this->validateConfig();

        $this->headers = [
            'X-Authorization' => $this->config['api_key'],
            'Accept' => 'application/json',
        ];

        $this->httpClient = new Client([
            'base_uri' => $this->config['base_url'],
            'timeout' => $this->config['timeout'],
            'http_errors' => false,
        ]);
    }

    /**
     * Get current account balance
     */
    public function getBalance(): AuthenticaResponse
    {
        return $this->makeRequest('GET', '/balance');
    }

    /**
     * Send OTP via configured channel
     */
    public function sendOtp(array $data): AuthenticaResponse
    {
        $this->validateOtpRequest($data);
        
        $payload = array_merge([
            'template_id' => $this->config['templates']['otp'],
            'method' => $this->config['default_channel'],
        ], $data);

        // Auto-add fallback if configured
        if (!isset($payload['fallback_phone']) && $this->config['fallback_channel'] === 'sms') {
            $payload['fallback_phone'] = $payload['phone'] ?? null;
        }
        if (!isset($payload['fallback_email']) && $this->config['fallback_channel'] === 'email') {
            $payload['fallback_email'] = $payload['email'] ?? null;
        }

        return $this->makeRequest('POST', '/send-otp', [
            'json' => $this->filterNullValues($payload)
        ]);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $otp, ?string $phone = null, ?string $email = null): AuthenticaResponse
    {
        $this->validate([
            'otp' => 'required|string',
            'phone' => 'required_without:email|nullable|phone:SA',
            'email' => 'required_without:phone|nullable|email',
        ], compact('otp', 'phone', 'email'));

        return $this->makeRequest('POST', '/verify-otp', [
            'json' => $this->filterNullValues([
                'otp' => $otp,
                'phone' => $phone,
                'email' => $email,
            ])
        ]);
    }

    /**
     * Verify face match
     */
    public function verifyFace(string $userId, string $registeredImage, string $queryImage): AuthenticaResponse
    {
        $this->validateFaceRequest($userId, $registeredImage, $queryImage);

        return $this->makeRequest('POST', '/verify-by-face', [
            'multipart' => [
                ['name' => 'user_id', 'contents' => $userId],
                ['name' => 'registered_face_image', 'contents' => $registeredImage],
                ['name' => 'query_face_image', 'contents' => $queryImage],
            ],
            'headers' => ['Content-Type' => 'multipart/form-data']
        ]);
    }

    /**
     * Verify voice match
     */
    public function verifyVoice(string $userId, string $registeredAudio, string $queryAudio): AuthenticaResponse
    {
        $this->validateVoiceRequest($userId, $registeredAudio, $queryAudio);

        return $this->makeRequest('POST', '/verify-by-voice', [
            'json' => [
                'user_id' => $userId,
                'registered_audio' => $registeredAudio,
                'query_audio' => $queryAudio,
            ]
        ]);
    }

    /**
     * Send custom SMS
     */
    public function sendSms(string $phone, string $message, ?string $senderName = null): AuthenticaResponse
    {
        $this->validate([
            'phone' => 'required|phone:SA',
            'message' => 'required|string|max:1600',
            'sender_name' => 'nullable|string',
        ], compact('phone', 'message', 'senderName'));

        return $this->makeRequest('POST', '/send-sms', [
            'json' => [
                'phone' => $phone,
                'message' => $message,
                'sender_name' => $senderName ?? $this->config['sender']['name']
            ]
        ]);
    }

    /**
     * Internal request handler
     */
    protected function makeRequest(string $method, string $uri, array $options = []): AuthenticaResponse
    {
        try {
            $response = $this->httpClient->request($method, $uri, array_merge([
                'headers' => $this->headers
            ], $options));

            $body = json_decode($response->getBody()->getContents(), true);
            return new AuthenticaResponse($body, $response->getStatusCode());
        } catch (RequestException $e) {
            $this->handleApiError($e);
        } catch (\Exception $e) {
            throw new AuthenticaException("Request failed: {$e->getMessage()}", 0, $e);
        }
        // This line is unreachable, but added to satisfy static analysis
        throw new \RuntimeException('Unreachable code after try-catch in makeRequest');
    }

    /**
     * Handle API errors
     */
    protected function handleApiError(RequestException $e): void
    {
        $response = $e->getResponse();
        $message = 'Authentica API request failed';
        
        if ($response) {
            $body = json_decode($response->getBody()->getContents(), true);
            $message = $body['message'] ?? $body['error'] ?? $response->getReasonPhrase();
            $statusCode = $response->getStatusCode();
        } else {
            $statusCode = $e->getCode();
            $message = $e->getMessage();
        }

        throw new AuthenticaException($message, $statusCode, $e);
    }

    /**
     * Validate configuration
     */
    protected function validateConfig(): void
    {
        if (empty($this->config['api_key'])) {
            throw new \RuntimeException('Authentica API key is not configured. Set AUTHENTICA_API_KEY in your .env file');
        }
    }

    /**
     * Remove null values from array
     */
    protected function filterNullValues(array $data): array
    {
        return array_filter($data, fn($value) => $value !== null && $value !== '');
    }
}