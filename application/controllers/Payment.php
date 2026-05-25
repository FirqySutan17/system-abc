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

    public function load_data()
    {
        /*
        |--------------------------------------------------------------------------
        | ALLOWED ORDER
        |--------------------------------------------------------------------------
        */

        $allowedOrder = [
            'PAYMENT',
            'PAYMENT_DATE',
            'PEMBAYARAN',
            'SLIP_NO',
            'PLANT',
            'CREATED_AT'
        ];

        /*
        |--------------------------------------------------------------------------
        | PARAMETER
        |--------------------------------------------------------------------------
        */

        $page = max(
            1,
            (int)$this->input->get('page')
        );

        $limit = max(
            1,
            (int)$this->input->get('limit')
        );

        $search = trim(
            $this->input->get('search', true)
        );

        $pembayaran = trim(
            $this->input->get('pembayaran', true)
        );

        $dateFrom = trim(
            $this->input->get('date_from', true)
        );

        $dateTo = trim(
            $this->input->get('date_to', true)
        );

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $orderInput = trim(
            $this->input->get('order', true)
        );

        $order = in_array(
            $orderInput,
            $allowedOrder
        )
            ? $orderInput
            : 'PAYMENT_DATE';

        $dir = strtoupper(
            $this->input->get('dir', true)
        );

        if(
            $dir !== 'ASC' &&
            $dir !== 'DESC'
        ){

            $dir = 'DESC';

        }

        /*
        |--------------------------------------------------------------------------
        | OFFSET
        |--------------------------------------------------------------------------
        */

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | SESSION
        |--------------------------------------------------------------------------
        */

        $username = $this->session
            ->userdata('username');

        $role_id = (int)$this->session
            ->userdata('role_id');

        /*
        |--------------------------------------------------------------------------
        | PLANT FILTER
        |--------------------------------------------------------------------------
        */

        $plants = ($role_id === 1)
            ? []
            : $this->Payment_model
                ->get_user_plants($username);

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->Payment_model->get_data(
            $limit,
            $start,
            $role_id,
            $plants,
            $search,
            $order,
            $dir,
            $pembayaran,
            $dateFrom,
            $dateTo
        );

        $total = $this->Payment_model->count_data(
            $role_id,
            $plants,
            $search,
            $pembayaran,
            $dateFrom,
            $dateTo
        );

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $pages = $total > 0
            ? ceil($total / $limit)
            : 1;

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'total' => (int)$total,

            'page' => (int)$page,

            'pages' => (int)$pages,

            'pagination' => $this->build_pagination(
                $pages,
                $page
            )

        ]);
    }

    private function build_pagination($pages, $current)
    {
        if ($pages <= 1) return '';

        $html = '<ul class="pagination pagination-sm">';

        for ($i=1;$i<=$pages;$i++){

            $active = ($i==$current)
                ? 'active'
                : '';

            $html .= '

                <li class="page-item '.$active.'">

                    <a href="javascript:void(0)"
                    class="page-link"
                    onclick="loadPage('.$i.')">

                        '.$i.'

                    </a>

                </li>

            ';
        }

        return $html.'</ul>';
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

    public function load_po_picker()
    {
        $plant = trim(
            $this->input->get('plant', true)
        );

        $supplier = trim(
            $this->input->get('supplier', true)
        );

        $search = trim(
            $this->input->get('search', true)
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if(
            empty($plant) ||
            empty($supplier)
        ){

            echo json_encode([]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->Payment_model->get_po_picker(
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

            $total = $berat * $harga;

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

                'PO_NO' => $row['PO_NO'],

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

                ->where('PO', $row['PO_NO'])

                ->where('MATERIAL', $row['MATERIAL'])

                ->update(
                    'abc_mst_po',
                    [
                        'STATUS' => 'PAID'
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
            $key = $d['PLANT'].'|'.$d['PO_NO'].'|'.$d['MATERIAL'];
            if (isset($map[$key])) {
                return true;
            }
            $map[$key] = true;
        }
        return false;
    }

    public function edit()
    {
        $payment = trim(
            $this->input->get('payment', true)
        );

        $plant = trim(
            $this->input->get('plant', true)
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if(
            empty($payment) ||
            empty($plant)
        ){

            echo json_encode([
                'status' => false,
                'message' => 'Payment / Plant tidak lengkap'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $header = $this->Payment_model
            ->get_header($payment, $plant);

        $detail = $this->Payment_model
            ->get_detail($payment, $plant);

        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if(!$header){

            echo json_encode([
                'status' => false,
                'message' => 'Payment tidak ditemukan'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        echo json_encode([

            'status' => true,

            'data' => [

                'header' => $header,

                'detail' => $detail

            ]

        ]);
    }

    public function update()
    {
        $data = $this->input->post(NULL, TRUE);

        $payment = $data['PAYMENT'] ?? null;

        $plant = $data['PLANT'] ?? null;

        $username = $this->session
            ->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if(
            empty($payment) ||
            empty($plant)
        ){

            echo json_encode([
                'status' => false,
                'message' => 'Payment / Plant tidak lengkap'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DETAIL
        |--------------------------------------------------------------------------
        */

        if(
            empty($data['DETAIL']) ||
            !is_array($data['DETAIL'])
        ){

            echo json_encode([
                'status' => false,
                'message' => 'Detail payment kosong'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_begin();

        /*
        |--------------------------------------------------------------------------
        | RESET STATUS PO LAMA
        |--------------------------------------------------------------------------
        */

        $oldDetail = $this->Payment_model
            ->get_detail($payment, $plant);

        foreach($oldDetail as $old){

            $this->db
                ->where('PLANT', $plant)

                ->where('PO', $old['PO_NO'])

                ->where('MATERIAL', $old['MATERIAL'])

                ->update(
                    'abc_mst_po',
                    [
                        'STATUS' => 'OPEN'
                    ]
                );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE HEADER
        |--------------------------------------------------------------------------
        */

        $header = [

            'PAYMENT_DATE' => date(
                'Y-m-d H:i:s',
                strtotime($data['PAYMENT_DATE'])
            ),

            'PEMBAYARAN' => $data['PEMBAYARAN'],

            'SUPPLIER' => $data['SUPPLIER'] ?? null,

            'REMARK' => $data['REMARK'] ?? null,

            'UPDATED_AT' => date('Y-m-d H:i:s'),

            'UPDATED_BY' => $username

        ];

        $this->Payment_model
            ->update_header_by_key(
                $payment,
                $plant,
                $header
            );

        /*
        |--------------------------------------------------------------------------
        | DELETE DETAIL LAMA
        |--------------------------------------------------------------------------
        */

        $this->db
            ->where('PAYMENT', $payment)

            ->where('PLANT', $plant)

            ->delete('abc_mst_payment_detail');

        /*
        |--------------------------------------------------------------------------
        | INSERT DETAIL BARU
        |--------------------------------------------------------------------------
        */

        $grandTotal = 0;

        $seqNo = 1;

        foreach($data['DETAIL'] as $row){

            $qty = (float)$row['JUMLAH'];

            $berat = (float)$row['BERAT'];

            $harga = (float)$row['HARGA'];

            $total = $berat * $harga;

            /*
            |--------------------------------------------------------------------------
            | SKIP INVALID
            |--------------------------------------------------------------------------
            */

            if(
                $qty <= 0 ||
                $harga <= 0
            ){

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT DETAIL
            |--------------------------------------------------------------------------
            */

            $detail = [

                'PLANT' => $plant,

                'PAYMENT' => $payment,

                'SEQ_NO' => $seqNo,

                'PO_NO' => $row['PO_NO'],

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
            | UPDATE STATUS PO
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where('PLANT', $plant)

                ->where('PO', $row['PO_NO'])

                ->where('MATERIAL', $row['MATERIAL'])

                ->update(
                    'abc_mst_po',
                    [
                        'STATUS' => 'PAID'
                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | GRAND TOTAL
            |--------------------------------------------------------------------------
            */

            $grandTotal += $total;

            $seqNo++;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE TOTAL
        |--------------------------------------------------------------------------
        */

        $this->Payment_model
            ->update_header_total_by_key(
                $payment,
                $plant,
                $grandTotal,
                $username
            );

        /*
        |--------------------------------------------------------------------------
        | CHECK TRANSACTION
        |--------------------------------------------------------------------------
        */

        if(
            $this->db->trans_status() === FALSE
        ){

            $this->db->trans_rollback();

            echo json_encode([
                'status' => false,
                'message' => 'Gagal update payment'
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

            'message' => 'Payment berhasil diperbarui'

        ]);
    }

    public function remove()
    {
        $payment = $this->input
            ->post('payment', TRUE);

        $plant = $this->input
            ->post('plant', TRUE);

        $username = $this->session
            ->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | USER PLANT
        |--------------------------------------------------------------------------
        */

        $userPlants = json_decode(
            $this->session->userdata('plant'),
            true
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if(
            empty($payment) ||
            empty($plant)
        ){

            echo json_encode([
                'status'  => false,
                'message' => 'Payment / Plant tidak valid'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->Payment_model
            ->get_header($payment, $plant);

        if(!$header){

            echo json_encode([
                'status'  => false,
                'message' => 'Payment tidak ditemukan'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $details = $this->Payment_model
            ->get_detail($payment, $plant);

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_begin();

        /*
        |--------------------------------------------------------------------------
        | ROLLBACK PO STATUS
        |--------------------------------------------------------------------------
        */

        foreach($details as $row){

            $this->db
                ->where('PO', $row['PO_NO'])

                ->where('PLANT', $plant)

                ->update(
                    'abc_mst_po',
                    [
                        'STATUS' => 'RECEIVED'
                    ]
                );

        }

        /*
        |--------------------------------------------------------------------------
        | DELETE DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db

            ->where('PAYMENT', $payment)

            ->where('PLANT', $plant)

            ->delete('abc_mst_payment_detail');

        /*
        |--------------------------------------------------------------------------
        | DELETE HEADER
        |--------------------------------------------------------------------------
        */

        $this->db

            ->where('PAYMENT', $payment)

            ->where('PLANT', $plant)

            ->delete('abc_mst_payment');

        /*
        |--------------------------------------------------------------------------
        | CHECK
        |--------------------------------------------------------------------------
        */

        if($this->db->trans_status() === FALSE){

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => 'Gagal menghapus payment'
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

            'status'  => true,

            'message' => 'Payment berhasil dihapus'

        ]);
    }

    public function print_pdf()
    {
        $payment = $this->input->get('payment');
        $plant   = $this->input->get('plant');

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if(
            !$payment ||
            !$plant
        ){

            show_error(
                'Parameter payment / plant tidak lengkap'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db

            ->select("
                p.*,

                plant.CODE_NAME AS PLANT_NAME,

                supplier.FULL_NAME AS SUPPLIER_NAME
            ", false)

            ->from('abc_mst_payment p')

            ->join(
                'abc_cd_code plant',
                "
                    plant.CODE COLLATE utf8mb4_unicode_ci =
                    p.PLANT COLLATE utf8mb4_unicode_ci

                    AND plant.HEAD_CODE = 'PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer supplier',
                "
                    supplier.CUST COLLATE utf8mb4_unicode_ci =
                    p.SUPPLIER COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            )

            ->where('p.PAYMENT', $payment)

            ->where('p.PLANT', $plant)

            ->get()

            ->row();

        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if(!$header){

            show_error(
                'Data payment tidak ditemukan'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detail = $this->db

            ->select("
                d.*,

                material.MATERIAL_NAME
            ", false)

            ->from('abc_mst_payment_detail d')

            ->join(
                'abc_cd_material material',
                "
                    material.MATERIAL COLLATE utf8mb4_unicode_ci =
                    d.MATERIAL COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            )

            ->where('d.PAYMENT', $payment)

            ->where('d.PLANT', $plant)

            ->order_by('d.SEQ_NO', 'ASC')

            ->get()

            ->result();

        /*
        |--------------------------------------------------------------------------
        | VIEW DATA
        |--------------------------------------------------------------------------
        */

        $data = [

            'header' => $header,

            'detail' => $detail

        ];

        /*
        |--------------------------------------------------------------------------
        | HTML
        |--------------------------------------------------------------------------
        */

        $html = $this->load->view(
            'admin/payment/pdf_template',
            $data,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $this->load->library('pdf');

        $this->pdf->loadHtml($html);

        $this->pdf->setPaper(
            'A4',
            'portrait'
        );

        $this->pdf->render();

        /*
        |--------------------------------------------------------------------------
        | STREAM
        |--------------------------------------------------------------------------
        */

        $this->pdf->stream(

            'PAYMENT_' . $payment . '.pdf',

            [
                'Attachment' => false
            ]

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
