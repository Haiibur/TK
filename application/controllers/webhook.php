<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class webhook extends CI_Controller {

    public function github() {
        // Menerima data JSON dari GitHub
        $payload = file_get_contents('php://input');

        // Catat payload ke file log atau lakukan apa pun yang diperlukan
        $this->logPayload($payload);

        // Tentukan respons HTTP 200 OK untuk GitHub
        header('HTTP/1.1 200 OK');
    }

    private function logPayload($payload) {
        // Implementasi penyimpanan payload ke file log atau database
        // Misalnya, simpan ke file log
        $logFile = APPPATH . 'logs/github_webhook.log';
        file_put_contents($logFile, $payload . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}