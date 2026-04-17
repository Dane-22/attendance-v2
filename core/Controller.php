<?php

require_once __DIR__ . '/JWT.php';

class Controller {

    /**
     * Get Authorization header from request
     * @return string|null The Authorization header value or null
     */
    protected function getAuthorizationHeader() {
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
        return $headers;
    }

    /**
     * Extract Bearer token from Authorization header
     * @return string|null The Bearer token or null
     */
    protected function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s+(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Validate API token - checks Bearer token from header or api_token from query/post
     * @return bool True if token is valid, false otherwise
     */
    protected function validateApiToken() {
        $token = $this->getBearerToken();

        if (!$token) {
            $token = $_GET['api_token'] ?? $_POST['api_token'] ?? null;
        }

        if (!$token) {
            return false;
        }

        $validTokens = $this->getValidApiTokens();
        return in_array($token, $validTokens, true);
    }

    /**
     * Get valid API tokens (can be extended to use database)
     * @return array List of valid API tokens
     */
    protected function getValidApiTokens() {
        return [
            'jajr-attendance-system-api-key-2024',
            defined('API_SECRET_KEY') ? API_SECRET_KEY : null
        ];
    }

    /**
     * Require valid API token - returns 401 if token is invalid
     * Use at the start of API endpoint methods
     */
    protected function requireApiToken() {
        if (!$this->validateApiToken()) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Unauthorized. Valid API token required.',
                'message' => 'Provide token via Authorization: Bearer <token> header or api_token parameter'
            ], 401);
        }
    }

    /**
     * Validate and decode JWT token from Authorization header
     * @return array|false Decoded JWT payload or false if invalid
     */
    protected function validateJWT() {
        $token = JWT::getBearerToken();

        if (!$token && isset($_SESSION['jwt_token'])) {
            $token = $_SESSION['jwt_token'];
        }

        if (!$token) {
            return false;
        }

        return JWT::validate($token);
    }

    /**
     * Require valid JWT token - returns 401 if token is missing or invalid
     * Use at the start of API endpoint methods that require user authentication
     */
    protected function requireJWT() {
        $payload = $this->validateJWT();

        if (!$payload) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Unauthorized. Valid JWT token required.',
                'message' => 'Provide token via Authorization: Bearer <token> header or include jwt_token in session'
            ], 401);
        }

        return $payload;
    }

    /**
     * Get current authenticated user from JWT token
     * @return array|null User data from JWT or null if not authenticated
     */
    protected function getCurrentUser() {
        return $this->validateJWT();
    }

    protected function view($view, $data = []) {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            extract($data);
            require_once $viewFile;
        } else {
            die('View ' . $view . ' does not exist');
        }
    }

    protected function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die('Model ' . $model . ' does not exist');
        }
    }

    protected function redirect($url) {
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        if ($url[0] === '/') {
            $url = $baseUrl . $url;
        }
        header('Location: ' . $url);
        exit();
    }

    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
