<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class HandlerNotif extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $params = array('server_key' => 'SB-Mid-server-3XqDS86A6xIG0SDj7UzWa_BY', 'production' => false);
        $this->load->library('veritrans');
        $this->veritrans->config($params);
        $this->load->model('RealisasiP', 'RP');
        $this->load->helper('url');
    }

    public function index()
    {
        $json_result = file_get_contents('php://input');
        $result = json_decode($json_result, "true");

        var_dump($result);
        die();

        if ($result->transaction_status == 'capture') {

        $order_id = $result['order_id'];
        $data = [
            'status' => $result['status_code'],
        ];

        if ($result['status_code'] == 200) {

            $update = $this->db->update('realisasipembayaran', $data, array('NoBukti' => $order_id));
            if ($update) {
            }
            $id = $result['order_id'];

            $this->RP->updatedata($id)->row();
        }
        }
    }

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