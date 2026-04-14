<?php

class Controller {
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
