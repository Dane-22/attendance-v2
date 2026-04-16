<?php

/**
 * JWT (JSON Web Token) Handler Class
 * Implements JWT encoding/decoding for stateless authentication
 */
class JWT {

    private static $secretKey;
    private static $algorithm = 'HS256';

    /**
     * Initialize JWT with secret key from environment or config
     */
    public static function init() {
        if (defined('JWT_SECRET_KEY')) {
            self::$secretKey = JWT_SECRET_KEY;
        } elseif (getenv('JWT_SECRET_KEY')) {
            self::$secretKey = getenv('JWT_SECRET_KEY');
        } else {
            // Fallback secret - should be changed in production
            self::$secretKey = 'jajr-attendance-jwt-secret-key-change-in-production';
        }
    }

    /**
     * Generate a new JWT token
     *
     * @param array $payload Token payload (user data)
     * @param int $expiry Expiration time in seconds (default: 24 hours)
     * @return string The generated JWT token
     */
    public static function generate($payload, $expiry = 86400) {
        self::init();

        $issuedAt = time();
        $expiration = $issuedAt + $expiry;

        // JWT Header
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ];

        // JWT Payload (Claims)
        $claims = [
            'iat' => $issuedAt,      // Issued at
            'exp' => $expiration,    // Expiration time
            'iss' => 'jajr-attendance-system', // Issuer
            'sub' => $payload['user_id'] ?? null, // Subject (user ID)
        ];

        // Merge custom payload with claims
        $claims = array_merge($claims, $payload);

        // Encode header and payload
        $base64Header = self::base64UrlEncode(json_encode($header));
        $base64Payload = self::base64UrlEncode(json_encode($claims));

        // Create signature
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, self::$secretKey, true);
        $base64Signature = self::base64UrlEncode($signature);

        // Combine into JWT token
        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }

    /**
     * Validate and decode a JWT token
     *
     * @param string $token JWT token
     * @return array|false Decoded payload or false if invalid
     */
    public static function validate($token) {
        self::init();

        // Split token into parts
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        // Verify signature
        $signature = self::base64UrlDecode($base64Signature);
        $expectedSignature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, self::$secretKey, true);

        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }

        // Decode payload
        $payload = json_decode(self::base64UrlDecode($base64Payload), true);

        if (!$payload) {
            return false;
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false; // Token expired
        }

        return $payload;
    }

    /**
     * Extract Bearer token from Authorization header
     *
     * @return string|null The Bearer token or null
     */
    public static function getBearerToken() {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        if (!empty($headers) && preg_match('/Bearer\s+(\S+)/', $headers, $matches)) {
            return $matches[1];
        }

        // Also check query/body parameters as fallback
        return $_GET['token'] ?? $_POST['token'] ?? null;
    }

    /**
     * Base64 URL-safe encoding
     *
     * @param string $data
     * @return string
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL-safe decoding
     *
     * @param string $data
     * @return string
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}
