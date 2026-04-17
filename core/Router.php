<?php

class Router {
    private $routes = [];
    private $params = [];
    private static $baseUrl = null;

    /**
     * Get the base URL of the application (handles subdirectory installs)
     * @return string Base URL path (e.g., '/jajr-v2' or '')
     */
    public static function getBaseUrl() {
        if (self::$baseUrl === null) {
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            self::$baseUrl = ($scriptName === '/' || $scriptName === '\\') ? '' : $scriptName;
        }
        return self::$baseUrl;
    }

    public function add($route, $params = []) {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-zA-Z0-9-]+)', $route);
        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }

    public function match($url) {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function getParams() {
        return $this->params;
    }

    public function dispatch() {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);

        // Remove script path from URL properly (only at beginning)
        if ($scriptName !== '/' && strpos($url, $scriptName) === 0) {
            $url = substr($url, strlen($scriptName));
        }
        $url = trim($url, '/');

        // Debug: log details
        error_log('Router DEBUG - URI: ' . $_SERVER['REQUEST_URI'] . ', Script: ' . $scriptName . ', URL: ' . $url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $action = $this->params['action'];

            $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerObj = new $controller();

                if (is_callable([$controllerObj, $action])) {
                    if (isset($this->params['id'])) {
                        $controllerObj->$action($this->params['id']);
                    } else {
                        $controllerObj->$action();
                    }
                } else {
                    http_response_code(404);
                    echo 'Action not found';
                }
            } else {
                http_response_code(404);
                echo 'Controller not found';
            }
        } else {
            http_response_code(404);
            echo 'Page not found';
        }
    }
}
