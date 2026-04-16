<?php

class Router {
    private $routes = [];
    private $params = [];

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
        $url = str_replace($scriptName, '', $url);
        $url = trim($url, '/');

        // Debug: log details
        error_log('Router DEBUG - URI: ' . $_SERVER['REQUEST_URI'] . ', Script: ' . $scriptName . ', URL: ' . $url);
        error_log('Router DEBUG - Registered routes: ' . print_r(array_keys($this->routes), true));

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
