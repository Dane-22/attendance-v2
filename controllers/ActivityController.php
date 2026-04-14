<?php

require_once __DIR__ . '/../core/Controller.php';

class ActivityController extends Controller {

    public function logs() {
        $this->view('activity/logs', [
            'title' => 'Activity Logs'
        ]);
    }
}
