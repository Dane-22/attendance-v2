<?php

require_once __DIR__ . '/../core/Controller.php';

class PagesController extends Controller {

    public function notifications() {
        $this->view('notifications/index', [
            'title' => 'Notifications'
        ]);
    }

    public function documents() {
        $this->view('documents/index', [
            'title' => 'Documents'
        ]);
    }

    public function finance() {
        $this->view('finance/index', [
            'title' => 'Finance'
        ]);
    }

    public function payroll() {
        $this->view('finance/payroll', [
            'title' => 'Payroll'
        ]);
    }

    public function overtime() {
        $this->view('finance/overtime', [
            'title' => 'Overtime'
        ]);
    }

    public function cashAdvance() {
        $this->view('finance/cash_advance', [
            'title' => 'Cash Advance'
        ]);
    }

    public function billing() {
        $this->view('finance/billing', [
            'title' => 'Billing'
        ]);
    }

    public function procurement() {
        $this->view('procurement/index', [
            'title' => 'Procurement'
        ]);
    }

    public function settings() {
        $this->view('settings/index', [
            'title' => 'Settings'
        ]);
    }
}
