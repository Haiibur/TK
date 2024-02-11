<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class RealisasiS extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        $params = array('server_key' => 'SB-Mid-server-3XqDS86A6xIG0SDj7UzWa_BY', 'production' => false);
        $this->load->library('Midtrans');
        $this->midtrans->config($params);
        $this->load->library('veritrans');
        $this->veritrans->config($params);
        $this->load->helper('url');
        $this->load->model('RealisasiP', 'RP');
        $this->load->library('form_validation');
        $this->is_logged();
    }

    function is_logged()
    {
        $this->load->library('Session/session');
        if ($this->session->userdata('ses_statuslogin') != TRUE) {
            redirect('login', 'refresh');
        }
    }

    public function index()
    {
        $cekemail = $this->session->userdata('ses_username');
        $data = array(
            "base"               => base_url(),
            'page'               => "Siswa/page/RealisasiS",
            'site'             => site_url('SiswaTK/RealisasiS/token'),
            'finish'         => site_url('SiswaTK/RealisasiS/finish'),
            'email' => $cekemail
        );

        $data["GetALL"] = $this->RP->tblrealisasi($cekemail);
        $this->load->view('Siswa/template/halaman', $data);
    }

    public function token()
    {

        $Angsuran   = $this->input->post('Angsuran');
        $NISs   = $this->input->post('NISs');
        $NamaSiswa   = $this->input->post('NamaSiswa');
        $NoTelphone   = $this->input->post('NoTelphone');
        $Alamat   = $this->input->post('Alamat');
        $email   = $this->input->post('email');
        $JumlahTagihan        = $this->input->post('JumlahTagihan');

        $transaction_details = array(
            'order_id' => rand(),
            'gross_amount' => $JumlahTagihan, // no decimal allowed for creditcard
        );

        // Optional
        $item1_details = array(
            'id' => 'a1',
            'price' => $JumlahTagihan,
            'quantity' => 1,
            'name' => "Pembayaran Angsuran " . $Angsuran
        );

        // Optional
        // $item2_details = array(
        // 	'id' => 'a2',
        // 	'price' => 20000,
        // 	'quantity' => 2,
        // 	'name' => "Orange"
        // );

        // Optional
        $item_details = array($item1_details);

        // Optional
        $billing_address = array(
            'first_name'    => "Andri66",
            'last_name'     => "Litani66",
            'address'       => "Mangga 20",
            'city'          => "Jakarta",
            'postal_code'   => "16602",
            'phone'         => "081122334455",
            'country_code'  => 'IDN'
        );

        // Optional
        $shipping_address = array(
            'first_name'    => "Obet",
            'last_name'     => "Supriadi",
            'address'       => $Alamat,
            'phone'         => "08113366345",
            'country_code'  => 'IDN'
        );

        // Optional
        $customer_details = array(
            'first_name'    => $NISs . ' ' . $NamaSiswa,
            'email'         => $email,
            'phone'         => $NoTelphone,
            'billing_address'  => $billing_address,
            'shipping_address' => $shipping_address
        );

        // Data yang akan dikirim untuk request redirect_url.
        $credit_card['secure'] = true;
        //ser save_card true to enable oneclick or 2click
        //$credit_card['save_card'] = true;

        $time = time();
        $custom_expiry = array(
            'start_time' => date("Y-m-d H:i:s O", $time),
            'unit' => 'day',
            'duration'  => 3
        );

        $transaction_data = array(

            'payment_type'             => 'vtweb',
            'vtweb'                         => array(
                //'enabled_payments' 	=> ['credit_card'],
                'credit_card_3d_secure' => true
            ),
            'transaction_details' => $transaction_details,
            'item_details'       => $item_details,
            'customer_details'   => $customer_details,
            'credit_card'        => $credit_card,
            'expiry'             => $custom_expiry
        );

        error_log(json_encode($transaction_data));
        $snapToken = $this->midtrans->getSnapToken($transaction_data);
        error_log($snapToken);
        echo $snapToken;
    }

    public function finish()
    {
        $result = json_decode($this->input->post('result_data'), true);
        echo $result['status_code'];
        echo 'RESULT <br><pre>';
        var_dump($result);
        die();
        echo '</pre>';

        $order_id             = $result['order_id'];
        $NoTagihan             = $this->input->post('NoTagihan');
        $Angsuran             = $this->input->post('Angsuran');
        $NISs                 = $this->input->post('NISs');
        $gross_amount         = $result['gross_amount'];
        $status_code          = $result['status_code'];
        $transaction_time     = $result['transaction_time'];

        $data = array(
            'NoBukti'            => $order_id,
            'TanggalBayar'        => $transaction_time,
            'JenisPembayaran'   => $Angsuran,
            'JumlahPembayaran'        => $gross_amount,
            'NIS'    => $NISs,
            'NoTagihan' => $NoTagihan,
            'Status' => $status_code
        );

        $cek = $this->RP->Insertpembayaran($data);
        $table = 'rencanapembayaran';
        $idfield = 'NoTagihan';

        $dataupdate = array(
            'status' => 1
        );
        $this->RP->update_data($table, $dataupdate, $NoTagihan, $idfield);
        if ($cek > 0) {
            $redir = site_url('SiswaTK/RealisasiS');
            $message = "Pembayaran Telah Ditranfer,Terimaksh";
            echo "<script>alert('$message');</script>";

            echo "<script>location='$redir';</script>";
        } else {
            $redir = site_url('SiswaTK/RealisasiS');
            $message = "Pembayaran Ditolak";
            echo "<script>alert('$message');</script>";
            echo "<script>location='$redir';</script>";
        }
    }
}
