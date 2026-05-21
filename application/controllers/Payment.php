<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('accounting_payment_entry')) {
            show_404();
        }
        $this->load->model('Payment_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Payment']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/payment/list');
        $this->load->view('templates/footer');
    }

    /**
     * Load data table (ajax)
     */
    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'PAYMENT_DATE';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $start = ($page - 1) * $limit;

        $userPlants = json_decode($this->session->userdata('plant'), true);
        $role_id    = $this->session->userdata('role_id');

        $rows  = $this->Payment_model->get_data($limit, $start, $search, $order, $dir, $userPlants, $role_id);
        $total = $this->Payment_model->count_data($search, $userPlants, $role_id);

        echo json_encode([
            'rows'  => $rows,
            'total' => $total,
            'page'  => $page
        ]);
    }

    public function get_supplier()
    {
        $term = $this->input->get('q');
        $data = $this->Payment_model->search_supplier($term);
        echo json_encode($data);
    }

    public function get_receive_by_supplier()
    {
        $supplier = $this->input->get('supplier');
        $q        = $this->input->get('q');

        if(!$supplier){
            echo json_encode([]);
            return;
        }

        $this->db->select('r.RECEIVE as id, r.RECEIVE as text, r.SUPPLIER, c.FULL_NAME as supplier_name');
        $this->db->from('abc_mst_receive r');
        $this->db->join('abc_cd_customer c', 'r.SUPPLIER = c.CUST', 'left');
        $this->db->where('r.SUPPLIER', $supplier);
        $this->db->where('r.DELETED IS NULL', null, false);

        if($q){
            $this->db->like('RECEIVE', $q);
        }

        $this->db->order_by('RECEIVE','DESC');
        $this->db->limit(50);
        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'] . ' - ' . '(' . $r['SUPPLIER'] . ')' .  ' ' . $r['supplier_name']
            ];
        }

        echo json_encode($out);
    }

    public function load_receive_modal()
    {
        $supplier = $this->input->get('supplier');
        $plant    = $this->input->get('plant');
        $type     = $this->input->get('type');

        $userPlants = json_decode($this->session->userdata('plant'), true);

        if (!$supplier || !$plant || !in_array($plant, $userPlants)) {
            echo json_encode([]);
            return;
        }

        if ($type === 'RECEIVE_LB') {

            $rows = $this->db
                ->select("
                    r.PLANT,
                    aj.CODE_NAME AS PLANT_NAME,
                    r.RECEIVE,
                    r.RECEIVE_DATE,
                    r.SUPPLIER,
                    s.FULL_NAME AS SUPPLIER_NAME,

                    (IFNULL(r.QTY,0) - IFNULL(r.DEAD,0)) AS TOTAL_QTY,
                    r.RECEIVE_AMOUNT AS TOTAL_BERAT,

                    REPLACE(r.PRICE,'.','') AS HARGA,

                    (
                        (IFNULL(r.QTY,0) - IFNULL(r.DEAD,0)) 
                        * REPLACE(r.PRICE,'.','')
                    ) AS TOTAL
                ")
                ->from('abc_mst_receive_lb r')

                /* 🔹 JOIN PLANT */
                ->join(
                    'abc_cd_code aj',
                    "aj.CODE = r.PLANT AND aj.HEAD_CODE = 'PLANT'",
                    'left',
                    false
                )

                /* 🔹 JOIN SUPPLIER */
                ->join(
                    'abc_cd_customer s',
                    's.CUST = r.SUPPLIER',
                    'left'
                )

                ->where('r.SUPPLIER', $supplier)
                ->where('r.PLANT', $plant)
                ->where('r.DELETED IS NULL', null, false)
                ->where('r.PAYMENT_STATUS', 'UNPAID')

                ->order_by('r.RECEIVE_DATE','DESC')
                ->get()
                ->result();

            $result = [];

            foreach ($rows as $r) {
                $result[] = [
                    'PLANT'         => $r->PLANT,
                    'PLANT_NAME'    => $r->PLANT_NAME,
                    'RECEIVE'       => $r->RECEIVE,
                    'RECEIVE_DATE'  => $r->RECEIVE_DATE,
                    'SUPPLIER'      => $r->SUPPLIER,
                    'SUPPLIER_NAME' => $r->SUPPLIER_NAME,
                    'MATERIAL'      => 'LB',
                    'MATERIAL_NAME' => 'LIVE BIRD',
                    'TOTAL_QTY'     => (float)$r->TOTAL_QTY,
                    'TOTAL_BERAT'   => (float)$r->TOTAL_BERAT,
                    'HARGA'         => (float)$r->HARGA,
                    'TOTAL'         => (float)$r->TOTAL
                ];
            }

            echo json_encode($result);
            return;
        }

        // DEFAULT RECEIVE
        $data = $this->Payment_model->get_receive_for_modal($supplier, $plant);
        echo json_encode($data);
    }

    public function load_receive_picker()
    {
        $search = trim(
            $this->input->get('search', true)
        );

        $plant = trim(
            $this->input->get('plant', true)
        );

        $supplier = trim(
            $this->input->get('supplier', true)
        );

        if (
            empty($plant) ||
            empty($supplier)
        ) {

            echo json_encode([]);

            return;
        }

        $rows = $this->Payment_model
            ->get_receive_picker(
                $plant,
                $supplier,
                $search
            );

        echo json_encode($rows);
    }

    public function get_plant()
    {
        echo json_encode(
            $this->Payment_model->get_plant_select2()
        );
    }

    public function get_user_plants()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);

        if(empty($userPlants)) return;

        $plants = $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->from('abc_cd_code')
            ->where('HEAD_CODE','PLANT')
            ->where_in('CODE', $userPlants)
            ->order_by('CODE_NAME','ASC')
            ->get()
            ->result();

        echo json_encode($plants);
    }

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);

        $plant = $data['PLANT'] ?? null;

        $username = $this->session
            ->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDASI HEADER
        |--------------------------------------------------------------------------
        */

        if (
            empty($plant) ||
            empty($data['PAYMENT_DATE']) ||
            empty($data['PEMBAYARAN'])
        ) {

            echo json_encode([
                'status' => false,
                'message' => 'Header payment belum lengkap'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DETAIL
        |--------------------------------------------------------------------------
        */

        if (
            empty($data['DETAIL']) ||
            !is_array($data['DETAIL'])
        ) {

            echo json_encode([
                'status' => false,
                'message' => 'Detail payment kosong'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE NUMBER
        |--------------------------------------------------------------------------
        */

        $paymentNo = $this->Payment_model
            ->generate_payment_no($plant);

        $slipNo = $this->Payment_model
            ->generate_slip_no($plant);

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_begin();

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = [

            'PLANT' => $plant,

            'PAYMENT' => $paymentNo,

            'PAYMENT_DATE' => date(
                'Y-m-d H:i:s',
                strtotime($data['PAYMENT_DATE'])
            ),

            'PEMBAYARAN' => $data['PEMBAYARAN'],

            'SLIP_NO' => $slipNo,

            'SUPPLIER' => $data['SUPPLIER'] ?? null,

            'REMARK' => $data['REMARK'] ?? null,

            'TOTAL' => 0,

            'CREATED_AT' => date('Y-m-d H:i:s'),

            'CREATED_BY' => $username
        ];

        $this->Payment_model
            ->insert_header($header);

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $grandTotal = 0;

        $seqNo = 1;

        foreach ($data['DETAIL'] as $row) {

            $qty = (float)$row['JUMLAH'];

            $berat = (float)$row['BERAT'];

            $harga = (float)$row['HARGA'];

            $total = $qty * $harga;

            /*
            |--------------------------------------------------------------------------
            | SKIP INVALID
            |--------------------------------------------------------------------------
            */

            if (
                $qty <= 0 ||
                $harga <= 0
            ) {

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT DETAIL
            |--------------------------------------------------------------------------
            */

            $detail = [

                'PLANT' => $plant,

                'PAYMENT' => $paymentNo,

                'SEQ_NO' => $seqNo,

                'RECEIVE_NO' => $row['RECEIVE_NO'],

                'MATERIAL' => $row['MATERIAL'],

                'BERAT' => $berat,

                'JUMLAH' => $qty,

                'HARGA' => $harga,

                'TOTAL' => $total,

                'REMARK' => $row['REMARK'] ?? null,

                'CREATED_AT' => date('Y-m-d H:i:s'),

                'CREATED_BY' => $username
            ];

            $this->Payment_model
                ->insert_detail($detail);

            /*
            |--------------------------------------------------------------------------
            | UPDATE RECEIVE DETAIL
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where('PLANT', $plant)

                ->where('RECEIVE', $row['RECEIVE_NO'])

                ->where('MATERIAL', $row['MATERIAL'])

                ->update(
                    'abc_mst_receive_detail',
                    [
                        'STATUS' => 'Y'
                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */

            $grandTotal += $total;

            $seqNo++;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE HEADER TOTAL
        |--------------------------------------------------------------------------
        */

        $this->Payment_model
            ->update_header_total_by_key(
                $paymentNo,
                $plant,
                $grandTotal,
                $username
            );

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION CHECK
        |--------------------------------------------------------------------------
        */

        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();

            echo json_encode([
                'status' => false,
                'message' => 'Gagal menyimpan payment'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

        $this->db->trans_commit();

        echo json_encode([

            'status' => true,

            'payment' => $paymentNo,

            'message' => 'Payment berhasil disimpan'
        ]);
    }

    private function isDuplicateDetail($details)
    {
        $map = [];
        foreach ($details as $d) {
            $key = $d['PLANT'].'|'.$d['RECEIVE_NO'].'|'.$d['MATERIAL'];
            if (isset($map[$key])) {
                return true;
            }
            $map[$key] = true;
        }
        return false;
    }

    public function edit()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);

        if (!in_array($plant, $userPlants)) {
            echo json_encode([
                'status'=>false,
                'message'=>'Unauthorized'
            ]);
            return;
        }

        $payment = $this->input->get('payment');
        $plant   = $this->input->get('plant');

        if(!$payment || !$plant){
            echo json_encode([
                'status'=>false,
                'message'=>'Payment / Plant tidak lengkap'
            ]);
            return;
        }

        $header = $this->Payment_model->get_header($payment, $plant);
        $detail = $this->Payment_model->get_detail($payment, $plant);

        echo json_encode([
            'status'=>true,
            'data'=>[
                'header'=>$header,
                'detail'=>$detail
            ]
        ]);
    }

    public function update()
    {
        $payment = $this->input->post('PAYMENT');
        $plant   = $this->input->post('PLANT');
        $detail  = $this->input->post('DETAIL');
        $newType = $this->input->post('PAYMENT_TYPE');

        $userPlants = json_decode($this->session->userdata('plant'), true);
        $username   = $this->session->userdata('username');

        if (!$payment || !$plant || !in_array($plant,$userPlants)) {
            echo json_encode(['status'=>false,'message'=>'Unauthorized']);
            return;
        }

        $this->db->trans_begin();

        $this->Payment_model->update_header_by_key($payment,$plant,[
            'PAYMENT_DATE'=>$this->input->post('PAYMENT_DATE'),
            'SLIP_NO'=>$this->input->post('SLIP_NO'),
            'REMARK'=>$this->input->post('REMARK'),
            'PEMBAYARAN'=>$this->input->post('PEMBAYARAN'),
            'SUPPLIER'=>$this->input->post('SUPPLIER'),
            'PAYMENT_TYPE'=>$newType,
            'UPDATED_AT'=>date('Y-m-d H:i:s'),
            'UPDATED_BY'=>$username
        ]);

        /* HAPUS DETAIL LAMA */
        $this->Payment_model->soft_delete_detail_by_key($payment,$plant,[
            'DELETED'=>'Y'
        ]);

        $grandTotal = 0;
        $seqNo = 1;

        foreach ($detail as $row) {

            $qty   = (float)$row['JUMLAH'];
            $harga = (float)$row['HARGA'];
            $berat = (float)$row['BERAT'];
            $total = $qty * $harga;

            $this->Payment_model->insert_detail([
                'PLANT'=>$plant,
                'PAYMENT'=>$payment,
                'SEQ_NO'=>$seqNo,
                'RECEIVE_NO'=>$row['RECEIVE_NO'],
                'MATERIAL'=>$row['MATERIAL'],
                'BERAT'=>$berat,
                'JUMLAH'=>$qty,
                'HARGA'=>$harga,
                'TOTAL'=>$total,
                'REMARK'=>$row['REMARK'] ?? null,
                'CREATED_AT'=>date('Y-m-d H:i:s'),
                'CREATED_BY'=>$username
            ]);

            if ($newType === 'RECEIVE_LB') {
                $this->db->where('PLANT',$plant)
                         ->where('RECEIVE',$row['RECEIVE_NO'])
                         ->update('abc_mst_receive_lb',[
                             'PAYMENT_STATUS'=>'PAID',
                             'PAYMENT_NO'=>$payment
                         ]);
            } else {
                $this->db->where('PLANT',$plant)
                         ->where('RECEIVE',$row['RECEIVE_NO'])
                         ->where('MATERIAL',$row['MATERIAL'])
                         ->update('abc_mst_receive_detail',['STATUS'=>'Y']);
            }

            $grandTotal += $total;
            $seqNo++;
        }

        $this->Payment_model->update_header_total_by_key($payment,$plant,$grandTotal);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['status'=>false,'message'=>'Gagal update payment']);
            return;
        }

        $this->db->trans_commit();

        echo json_encode([
            'status'=>true,
            'message'=>'Payment berhasil diperbarui'
        ]);
    }

    /**
     * Soft delete Payment (header + detail)
     */
    public function remove()
    {
        $payment = $this->input->post('payment', TRUE);
        $plant   = $this->input->post('plant', TRUE);
        $user    = $this->session->userdata('username');

        $userPlants = json_decode($this->session->userdata('plant'), true);

        // 🔐 VALIDASI PLANT USER
        if (!$plant || !in_array($plant, $userPlants)) {
            echo json_encode([
                'status' => false,
                'message' => 'Unauthorized plant access'
            ]);
            return;
        }

        // VALIDASI INPUT
        if (!$payment) {
            echo json_encode([
                'status'  => false,
                'message' => 'Payment tidak valid'
            ]);
            return;
        }

        // CEK DATA MASIH ADA
        $exists = $this->db
            ->from('abc_mst_payment')
            ->where('PAYMENT', $payment)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->count_all_results();

        if ($exists == 0) {
            echo json_encode([
                'status'  => false,
                'message' => 'Payment sudah dihapus atau tidak ditemukan'
            ]);
            return;
        }

        $this->db->trans_begin();

        try {

            $data = [
                'DELETED'    => 'Y',
                'UPDATED_AT' => date('Y-m-d H:i:s'),
                'UPDATED_BY' => $user
            ];

            $header  = $this->Payment_model->get_header($payment, $plant);
            $details = $this->Payment_model->get_detail($payment, $plant);

            if ($header['PAYMENT_TYPE'] === 'RECEIVE_LB') {

                foreach ($details as $d) {
                    $this->db->where('PLANT', $d['PLANT']);
                    $this->db->where('RECEIVE', $d['RECEIVE_NO']);
                    $this->db->update('abc_mst_receive_lb', [
                        'PAYMENT_STATUS' => 'UNPAID',
                        'PAYMENT_NO'     => null
                    ]);
                }

            } else {

                foreach ($details as $d) {
                    $this->db->where('PLANT', $d['PLANT']);
                    $this->db->where('RECEIVE', $d['RECEIVE_NO']);
                    $this->db->where('MATERIAL', $d['MATERIAL']);
                    $this->db->update('abc_mst_receive_detail', [
                        'STATUS' => null
                    ]);
                }
            }

            $this->Payment_model->update_header_by_key($payment, $plant, $data);
            $this->Payment_model->soft_delete_detail_by_key($payment, $plant, $data);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menghapus payment');
            }

            $this->db->trans_commit();

            echo json_encode([
                'status'  => true,
                'message' => 'Payment berhasil dihapus'
            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function print_pdf()
    {
        $payment = $this->input->get('payment');
        $plant   = $this->input->get('plant');

        if (!$payment || !$plant) {
            show_error('Parameter PAYMENT atau PLANT tidak lengkap');
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                p.PAYMENT,
                p.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                c.FULL_NAME,
                p.PAYMENT_DATE,
                p.PEMBAYARAN,
                p.SLIP_NO,
                p.SUPPLIER,
                p.REMARK,
                p.TOTAL
            ')
            ->from('abc_mst_payment p')
            ->join('abc_cd_code aj', "aj.CODE = p.PLANT AND aj.HEAD_CODE = 'PLANT'", 'left')
            ->join('abc_cd_customer c', "c.CUST = p.SUPPLIER", 'left')
            ->where('p.PAYMENT', $payment)
            ->where('p.PLANT', $plant)
            ->where('p.DELETED IS NULL')
            ->get()
            ->row();

        if (!$header) {
            show_error('Data PAYMENT tidak ditemukan');
        }

        /* ================= DETAIL ================= */
        $detail = $this->db
            ->select('
                d.SEQ_NO,
                d.RECEIVE_NO,
                d.MATERIAL,
                m.material_name,
                d.JUMLAH,
                d.BERAT,
                d.HARGA,
                d.TOTAL,
                d.REMARK
            ')
            ->from('abc_mst_payment_detail d')
            ->join('abc_cd_material m', "m.material = d.MATERIAL", 'left')
            ->where('d.PAYMENT', $payment)
            ->where('d.PLANT', $plant)
            ->where('d.DELETED IS NULL')
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        $data = compact('header', 'detail');

        /* ================= PDF ================= */
        $html = $this->load->view('admin/payment/pdf_template', $data, true);

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            "PAYMENT_{$payment}.pdf",
            ['Attachment' => false]
        );
    }

    function format_decimal_id($number, $dec = 2)
    {
        return number_format((float)$number, $dec, ',', '.');
    }

    function format_rupiah($number)
    {
        return number_format((float)$number, 0, ',', '.');
    }

    private function normalize_number($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        // hapus titik ribuan, ganti koma desimal (jika ada)
        return (float) str_replace(['.', ','], ['', '.'], $value);
    }

}
