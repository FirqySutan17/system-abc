<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('productions_sales')) {
            show_404();
        }
        $this->load->model('Sales_model');
        $this->load->model('CashIn_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        $this->load->driver('cache', [
            'adapter' => 'file',
            'backup'  => 'file'
        ]);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Sales']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/sales/list');
        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);

        /* 🔒 ORDER WHITELIST */
        $allowedOrder = [
            'SALES',
            'SALES_DATE',
            'CUSTOMER',
            'PEMBAYARAN',
            'JENIS_PAY',
            'PLANT',
            'CREATED_AT'
        ];

        $orderInput = $this->input->get('order', TRUE);
        $order = in_array($orderInput, $allowedOrder)
            ? 's.' . $orderInput
            : 's.SALES_DATE';

        $dir = strtoupper($this->input->get('dir', TRUE)) === 'ASC'
            ? 'ASC'
            : 'DESC';

        $start = ($page - 1) * $limit;

        /* 🔐 SESSION */
        $role_id = (int)$this->session->userdata('role_id');
        $username = $this->session->userdata('username');

        /* 🔥 AMBIL PLANT USER (ARRAY) */
        $plants = ($role_id === 1)
            ? []
            : $this->Sales_model->get_user_plants($username);

        $rows = $this->Sales_model->get_data(
            $limit,
            $start,
            $role_id,
            $plants,
            $search,
            $order,
            $dir
        );

        $total = $this->Sales_model->count_data(
            $role_id,
            $plants,
            $search
        );

        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page);

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'pagination' => $pagination,
            'page'       => $page
        ]);
    }

    private function build_pagination($pages, $current)
    {
        if ($pages <= 1) return '';

        $html = '<ul class="pagination pagination-sm mb-0">';

        $range = 2; // jumlah halaman kiri & kanan dari halaman aktif
        $start = max(1, $current - $range);
        $end   = min($pages, $current + $range);

        // ===== PREV BUTTON =====
        if ($current > 1) {
            $html .= '<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="loadPage('.($current-1).')">«</a>
                    </li>';
        }

        // ===== FIRST PAGE + DOTS =====
        if ($start > 1) {
            $html .= '<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="loadPage(1)">1</a>
                    </li>';

            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }
        }

        // ===== MIDDLE PAGES =====
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $current) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'">
                        <a class="page-link" href="javascript:void(0)" onclick="loadPage('.$i.')">'.$i.'</a>
                    </li>';
        }

        // ===== LAST PAGE + DOTS =====
        if ($end < $pages) {
            if ($end < $pages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }

            $html .= '<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="loadPage('.$pages.')">'.$pages.'</a>
                    </li>';
        }

        // ===== NEXT BUTTON =====
        if ($current < $pages) {
            $html .= '<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="loadPage('.($current+1).')">»</a>
                    </li>';
        }

        $html .= '</ul>';
        return $html;
    }

    public function get_customer()
    {
        $term = $this->input->get('q');
        $data = $this->Sales_model->search_customer($term);
        echo json_encode($data);
    }

    public function get_customer_default()
    {
        $cust = 'CC000001';

        $row = $this->Sales_model->get_customer_by_id($cust);

        if ($row) {
            echo json_encode([
                'id'   => $row['CUST'],
                'text' => $row['CUST'].' - '.$row['FULL_NAME']
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /**
     * Select2: item (material)
     */
    public function get_material()
    {
        $term = $this->input->get('q');
        $data = $this->Sales_model->search_material($term);
        echo json_encode($data);
    }

    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');

        $data = $this->Sales_model->get_plant_select2($username);

        echo json_encode($data);
    }

    public function create()
    {
        ob_clean();

        header('Content-Type: application/json');

        $data = $this->input->post(NULL, TRUE);

        $username = $this->session->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDATION HEADER
        |--------------------------------------------------------------------------
        */

        if (
            empty($data['PLANT']) ||
            empty($data['CUSTOMER']) ||
            empty($data['SALES_DATE'])
        ) {

            echo json_encode([
                'status'  => false,
                'message' => 'Plant, Customer dan Tanggal wajib diisi'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detailRows = json_decode(
            $data['DETAIL'] ?? '[]',
            true
        );

        if (empty($detailRows)) {

            echo json_encode([
                'status'  => false,
                'message' => 'Detail item tidak boleh kosong'
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
        | GENERATE SALES
        |--------------------------------------------------------------------------
        */

        $plant = trim($data['PLANT']);

        $salesNo = $this->Sales_model
            ->generate_sales_no($plant);

        /*
        |--------------------------------------------------------------------------
        | DETAIL LOOP
        |--------------------------------------------------------------------------
        */

        $rows  = [];

        $seq   = 1;

        $grand = 0;

        foreach ($detailRows as $row) {

            /*
            |--------------------------------------------------------------------------
            | MATERIAL
            |--------------------------------------------------------------------------
            */

            $material = trim(
                $row['MATERIAL'] ?? ''
            );

            if ($material == '') {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | VALUE
            |--------------------------------------------------------------------------
            */

            $jumlah = (float) str_replace(
                ',',
                '',
                $row['JUMLAH'] ?? 0
            );

            $berat = (float) str_replace(
                ',',
                '',
                $row['BERAT'] ?? 0
            );

            $harga = (float) str_replace(
                ',',
                '',
                $row['HARGA'] ?? 0
            );

            $discount = (float) str_replace(
                ',',
                '',
                $row['DISCOUNT'] ?? 0
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDATION DETAIL
            |--------------------------------------------------------------------------
            */

            if ($jumlah <= 0 && $berat <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            | PRIORITAS:
            | BERAT jika ada
            | selain itu JUMLAH
            */

            $basis = $berat > 0
                ? $berat
                : $jumlah;

            $amount = ($basis * $harga) - $discount;

            if ($amount < 0) {
                $amount = 0;
            }

            $grand += $amount;

            /*
            |--------------------------------------------------------------------------
            | DETAIL ARRAY
            |--------------------------------------------------------------------------
            */

            $rows[] = [

                'PLANT'      => $plant,

                'SALES'      => $salesNo,

                'SEQ_NO'     => $seq++,

                'CUSTOMER'   => trim($data['CUSTOMER']),

                'MATERIAL'   => $material,

                'JUMLAH'     => $jumlah,

                'BERAT'      => $berat,

                'HARGA'      => $harga,

                'DISCOUNT'   => $discount,

                'TOTAL'      => $amount,

                'CREATED_AT' => date('Y-m-d H:i:s'),

                'CREATED_BY' => $username
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATION DETAIL FINAL
        |--------------------------------------------------------------------------
        */

        if (empty($rows)) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => 'Detail sales tidak valid'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        $dp = (float) str_replace(
            ',',
            '',
            $data['DP_AMOUNT'] ?? 0
        );

        $jenisPay = strtoupper(
            trim($data['JENIS_PAY'])
        );

        if ($jenisPay === 'TEMPO') {

            $remain = $grand - $dp;

            if ($remain < 0) {
                $remain = 0;
            }

            $status = $dp > 0
                ? 'PARTIAL'
                : 'OPEN';

        } else {

            /*
            |--------------------------------------------------------------------------
            | LUNAS
            |--------------------------------------------------------------------------
            */

            $dp     = $grand;

            $remain = 0;

            $status = 'PAID';
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = [

            'PLANT'      => $plant,

            'SALES'      => $salesNo,

            'CUSTOMER'   => trim($data['CUSTOMER']),

            'SALES_DATE' => date(
                'Y-m-d H:i:s',
                strtotime($data['SALES_DATE'])
            ),

            'PEMBAYARAN' => trim(
                $data['PEMBAYARAN']
            ),

            'JENIS_PAY'  => $jenisPay,

            'NOTA'       => trim(
                $data['NOTA'] ?? ''
            ),

            'REMARK'     => trim(
                $data['REMARK'] ?? ''
            ),

            'AMOUNT'     => $grand,

            'DP_AMOUNT'  => $dp,

            'REMAIN'     => $remain,

            'STATUS'     => $status,

            'CREATED_AT' => date('Y-m-d H:i:s'),

            'CREATED_BY' => $username
        ];

        $this->Sales_model
            ->insert_sales_header($header);

        /*
        |--------------------------------------------------------------------------
        | ATTACHMENT
        |--------------------------------------------------------------------------
        */

        if (
            isset($_FILES['ATTACHMENT']) &&
            !empty($_FILES['ATTACHMENT']['name'])
        ) {

            $uploadPath =
                FCPATH .
                'uploads/sales/' .
                date('Y') . '/' .
                $plant;

            if (!is_dir($uploadPath)) {

                mkdir(
                    $uploadPath,
                    0777,
                    true
                );
            }

            $config = [

                'upload_path' => $uploadPath,

                'allowed_types' =>
                    'jpg|jpeg|png|pdf|doc|docx|xls|xlsx',

                'max_size' => 5120,

                'file_name' =>
                    $salesNo . '_' . time(),

                'overwrite' => false
            ];

            $this->load->library(
                'upload',
                $config
            );

            if (
                !$this->upload
                    ->do_upload('ATTACHMENT')
            ) {

                $this->db->trans_rollback();

                echo json_encode([

                    'status' => false,

                    'message' => strip_tags(
                        $this->upload->display_errors()
                    )
                ]);

                return;
            }

            $file = $this->upload->data();

            $this->Sales_model
                ->update_sales_header(
                    $plant,
                    $salesNo,
                    [

                        'ATTACHMENT_NAME' =>
                            $file['client_name'],

                        'ATTACHMENT_PATH' =>
                            'uploads/sales/' .
                            date('Y') . '/' .
                            $plant . '/' .
                            $file['file_name'],

                        'ATTACHMENT_TYPE' =>
                            $file['file_type']
                    ]
                );
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT DETAIL
        |--------------------------------------------------------------------------
        */

        $this->Sales_model
            ->insert_sales_detail_batch($rows);

        /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

        if (
            $this->db->trans_status() === FALSE
        ) {

            $this->db->trans_rollback();

            echo json_encode([

                'status'  => false,

                'message' => 'Gagal menyimpan sales'
            ]);

            return;
        }

        $this->db->trans_commit();

        echo json_encode([

            'status'  => true,

            'message' => 'Sales berhasil dibuat',

            'sales'   => $salesNo
        ]);
    }

    private function parseDecimalID($value)
    {
        if ($value === null || $value === '') return 0;

        if (is_numeric($value)) return (float) $value;

        return (float) str_replace(',', '.', str_replace('.', '', $value));
    }

    private function parse_rupiah($value)
    {
        if ($value === null || $value === '') return 0;

        // hapus semua selain angka
        $value = preg_replace('/[^0-9]/', '', $value);

        return (float) $value;
    }

    public function edit()
    {
        $sales = $this->input->get('sales', TRUE);
        $plant = $this->input->get('plant', TRUE);

        if (!$sales || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid parameter'
            ]);
            return;
        }

        $role_id  = (int)$this->session->userdata('role_id');
        $username = $this->session->userdata('username');

        /* 🔐 VALIDASI PLANT */
        if ($role_id !== 1) {
            if (!$this->Sales_model->user_has_plant($username, $plant)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Akses ditolak'
                ]);
                return;
            }
        }

        $header = $this->Sales_model->get_sales_header($sales, $plant);
        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
            return;
        }

        $detail = $this->Sales_model->get_sales_detail($sales, $plant);

        echo json_encode([
            'status' => true,
            'header' => $header,
            'detail' => $detail
        ]);
    }

    public function update()
    {
        $data  = $this->input->post(NULL, TRUE);
        $sales = $data['SALES'] ?? null;

        if (!$sales) {
            echo json_encode([
                'status'  => false,
                'message' => 'SALES required'
            ]);
            return;
        }

        $role_id  = (int)$this->session->userdata('role_id');
        $username = $this->session->userdata('username');

        $plant = $data['PLANT'] ?? null;

        if (!$plant) {
            echo json_encode(['status'=>false,'message'=>'Plant tidak ditemukan']);
            return;
        }

        /* validasi akses plant */
        if ($role_id !== 1 && !$this->Sales_model->user_has_plant($username, $plant)) {
            echo json_encode(['status'=>false,'message'=>'Akses plant ditolak']);
            return;
        }

        /* ambil header SPESIFIK */
        $header = $this->Sales_model->get_sales_header($sales, $plant);

        if (!$header) {
            echo json_encode([
                'status'=>false,
                'message'=>'Data tidak ditemukan'
            ]);
            return;
        }

        /* 🚫 JIKA SUDAH LUNAS TOTAL → TIDAK BOLEH EDIT */
        if ($header['STATUS'] === 'PAID') {
            echo json_encode([
                'status'  => false,
                'message' => 'Sales sudah LUNAS dan tidak bisa diedit'
            ]);
            return;
        }

        /* 🚫 CEK ADA PEMBAYARAN LAIN DI CASH IN (selain DP otomatis) */
        if ($this->CashIn_model->sales_has_payment($sales, $plant)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Sales sudah memiliki pembayaran Cash In, tidak bisa diedit'
            ]);
            return;
        }

        /* =========================
        DECODE DETAIL
        ========================= */
        $detailRows = json_decode($data['DETAIL'] ?? '[]', true);
        if (empty($detailRows)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Detail tidak boleh kosong'
            ]);
            return;
        }

        $this->db->trans_start();

        /* =========================
        UPDATE HEADER DASAR
        ========================= */
        $headerUpdate = [
            'CUSTOMER'   => $data['CUSTOMER'],
            'SALES_DATE' => date('Y-m-d H:i:s', strtotime($data['SALES_DATE'])),
            'PEMBAYARAN' => $data['PEMBAYARAN_EDIT'],
            'JENIS_PAY'  => $data['JENIS_PAY_EDIT'],
            'REMARK'     => $data['REMARK'] ?? null,
            'NOTA'       => $data['NOTA'] ?? null,
            'UPDATED_AT' => date('Y-m-d H:i:s'),
            'UPDATED_BY' => $username
        ];

        /* =========================
        UPDATE ATTACHMENT (JIKA ADA)
        ========================= */
        if (!empty($_FILES['ATTACHMENT']['name'])) {

            $uploadPath = FCPATH . 'uploads/sales/' . date('Y') . '/' . $plant;
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            $config = [
                'upload_path'   => $uploadPath,
                'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx',
                'max_size'      => 5120,
                'file_name'     => $sales . '_' . time(),
                'overwrite'     => false
            ];

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('ATTACHMENT')) {
                $this->db->trans_rollback();
                echo json_encode([
                    'status'  => false,
                    'message' => $this->upload->display_errors('', '')
                ]);
                return;
            }

            if (!empty($header['ATTACHMENT_PATH'])) {
                $oldFile = FCPATH . $header['ATTACHMENT_PATH'];
                if (file_exists($oldFile)) unlink($oldFile);
            }

            $file = $this->upload->data();

            $headerUpdate['ATTACHMENT_NAME'] = $file['client_name'];
            $headerUpdate['ATTACHMENT_PATH'] = 'uploads/sales/' . date('Y') . '/' . $plant . '/' . $file['file_name'];
            $headerUpdate['ATTACHMENT_TYPE'] = $file['file_type'];
        }

        $this->Sales_model->update_sales_header($plant, $sales, $headerUpdate);

        /* =========================
        RESET DETAIL LAMA
        ========================= */
        $this->Sales_model->delete_sales_detail($plant, $sales);

        $rows  = [];
        $seq   = 1;
        $total = 0;

        foreach ($detailRows as $row) {

            if (empty($row['ITEM'])) continue;

            $method   = $row['METHOD'] ?? 'QTY';
            $qty      = $this->parseDecimalID($row['QTY'] ?? 0);
            $berat    = $this->parseDecimalID($row['BERAT'] ?? 0);
            $harga    = $this->parseDecimalID($row['HARGA'] ?? 0);
            $discount = $this->parseDecimalID($row['DISCOUNT'] ?? 0);

            if ($method === 'BW' && $berat <= 0) continue;
            if ($method === 'QTY' && $qty <= 0) continue;

            $base   = ($method === 'BW') ? ($berat * $harga) : ($qty * $harga);
            $amount = max(0, $base - $discount);
            $total += $amount;

            $rows[] = [
                'PLANT'      => $plant,
                'SALES'      => $sales,
                'SEQ_NO'     => $seq++,
                'CUSTOMER'   => $data['CUSTOMER'],
                'ITEM'       => $row['ITEM'],
                'QTY'        => $qty,
                'BERAT'      => $berat,
                'HARGA'      => $harga,
                'DISCOUNT'   => $discount,
                'AMOUNT'     => $amount,
                'CREATED_AT' => date('Y-m-d H:i:s'),
                'CREATED_BY' => $username
            ];
        }

        if (empty($rows)) {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => false,
                'message' => 'Detail tidak boleh kosong'
            ]);
            return;
        }

        $this->Sales_model->insert_sales_detail_batch($rows);

        /* =========================
        HITUNG DP & STATUS
        ========================= */
        $dp = $this->parse_rupiah($data['BAYAR_AWAL'] ?? 0);
        $jenisPay = $data['JENIS_PAY_EDIT'];

        if ($jenisPay === 'LUNAS') {
            $dp     = $total;
            $remain = 0;
            $status = 'PAID';
        } else {
            $remain = max(0, $total - $dp);
            $status = ($dp > 0) ? 'PARTIAL' : 'OPEN';
        }

        $this->Sales_model->update_sales_amount_full(
            $plant,
            $sales,
            $total,
            $dp,
            $remain,
            $status
        );

        /* =========================
        🔥 SINKRONISASI CASH IN (FINAL & AMAN)
        ========================= */

        // Hapus semua Cash In DP lama
        $this->CashIn_model->delete_dp_by_sales($sales, $plant);

        // Buat ulang sesuai kondisi baru
        if ($jenisPay === 'LUNAS') {

            $this->Sales_model->sync_dp_cashin(
                $sales,
                $plant,
                $data['CUSTOMER'],
                $total,
                $username
            );

        } elseif ($jenisPay === 'TEMPO' && $dp > 0) {

            $this->Sales_model->sync_dp_cashin(
                $sales,
                $plant,
                $data['CUSTOMER'],
                $dp,
                $username
            );
        }

        $this->db->trans_complete();

        echo json_encode([
            'status'  => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'Sales berhasil diupdate'
                : 'Gagal update Sales'
        ]);
    }

    public function remove()
    {
        $sales = $this->input->post('sales', TRUE);
        $plant = $this->input->post('plant', TRUE);

        if (!$sales || !$plant) {
            echo json_encode(['status'=>false,'message'=>'Parameter tidak lengkap']);
            return;
        }

        $role      = $this->session->userdata('role_id');
        $username  = $this->session->userdata('username');

        if ($role != 1 && !$this->Sales_model->user_has_plant($username, $plant)) {
            echo json_encode(['status'=>false,'message'=>'Akses ditolak']);
            return;
        }

        $this->db->trans_begin();

        // 🔥 HAPUS CASH IN DP
        $this->CashIn_model->delete_dp_by_sales($sales, $plant);

        // Hapus detail
        $this->Sales_model->delete_sales_detail($plant, $sales);

        // Hapus header
        $this->Sales_model->delete_sales_header($plant, $sales);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['status'=>false,'message'=>'Gagal menghapus Sales']);
        } else {
            $this->db->trans_commit();
            echo json_encode(['status'=>true,'message'=>'Sales berhasil dihapus']);
        }
    }

    public function print_pdf()
    {
        $sales = $this->input->get('sales');
        $plant = $this->input->get('plant');

        if (!$sales || !$plant) {
            show_error('Parameter SALES atau PLANT tidak lengkap');
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                s.SALES,
                s.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                s.SALES_DATE,
                s.CUSTOMER,
                c.FULL_NAME AS CUSTOMER_NAME,
                s.SLIP_NO,
                s.JENIS_PAY,
                s.PEMBAYARAN,
                s.NOTA,
                s.AMOUNT,
                s.REMAIN,
                s.STATUS,
                s.REMARK
            ')
            ->from('abc_mst_sales s')
            ->join('abc_cd_code aj', "aj.CODE = s.PLANT AND aj.HEAD_CODE = 'AJ'", 'left')
            ->join('abc_cd_customer c', 'c.CUST = s.CUSTOMER', 'left')
            ->where('s.SALES', $sales)
            ->where('s.PLANT', $plant)
            ->get()
            ->row();

        if (!$header) {
            show_error('Data SALES tidak ditemukan');
        }

        /* ================= DETAIL ================= */
        $detail = $this->db
            ->select('
                d.SEQ_NO,
                d.ITEM,
                f.FULL_NAME,
                d.QTY,
                d.BERAT,
                d.HARGA,
                d.DISCOUNT,
                d.AMOUNT
            ')
            ->from('abc_mst_sales_detail d')
            ->join('abc_cd_item f', 'f.ITEM = d.ITEM', 'left')
            ->where('d.SALES', $sales)
            ->where('d.PLANT', $plant)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        $data = compact('header', 'detail');

        /* ================= PDF ================= */
        $html = $this->load->view('admin/sales/pdf_template_thermal', $data, true);

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);

        // 🔥 THERMAL 58mm
        $this->pdf->setPaper([0, 0, 165, 800], 'portrait');
        // 165pt ≈ 58mm

        $this->pdf->render();

        $this->pdf->stream(
            "SALES_{$sales}.pdf",
            ['Attachment' => false]
        );
    }

    public function print_invoice_pdf()
    {
        $sales = $this->input->get('sales');
        $plant = $this->input->get('plant');

        if (!$sales || !$plant) {
            show_error('Parameter SALES atau PLANT tidak lengkap');
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                s.SALES,
                s.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                s.SALES_DATE,
                s.CUSTOMER,
                c.FULL_NAME AS CUSTOMER_NAME,
                s.SLIP_NO,
                s.JENIS_PAY,
                s.PEMBAYARAN,
                s.NOTA,
                s.REMARK,
                s.AMOUNT,
                s.REMAIN,
                s.STATUS
            ')
            ->from('abc_mst_sales s')
            ->join('abc_cd_code aj', "aj.CODE = s.PLANT AND aj.HEAD_CODE = 'AJ'", 'left')
            ->join('abc_cd_customer c', "c.CUST = s.CUSTOMER", 'left')
            ->where('s.SALES', $sales)
            ->where('s.PLANT', $plant)
            ->get()
            ->row();

        if (!$header) {
            show_error('Sales invoice tidak ditemukan');
        }

        /* ================= HANDLE PAYMENT INFO ================= */
        $header->PAYMENT_INFO = empty($header->JENIS_PAY)
            ? 'Belum ditentukan'
            : $header->JENIS_PAY . ' - ' . $header->PEMBAYARAN;

        /* ================= DETAIL ================= */
        $detail = $this->db
            ->select('
                d.SEQ_NO,
                d.ITEM,
                d.QTY,
                d.BERAT,
                d.HARGA,
                d.DISCOUNT,
                d.AMOUNT,
                i.FULL_NAME
            ')
            ->from('abc_mst_sales_detail d')
            ->join('abc_cd_item i', "i.ITEM = d.ITEM", 'left')
            ->where('d.SALES', $sales)
            ->where('d.PLANT', $plant)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        /* ================= SUMMARY ================= */
        $summary = [
            'total_qty'     => 0,
            'total_berat'   => 0,
            'subtotal'      => 0,
            'total_diskon'  => 0,
            'grand_total'   => 0,
            'sisa_tagihan'  => 0,
        ];

        foreach ($detail as $row) {
            $summary['total_qty']    += (float)$row->QTY;
            $summary['total_berat']  += (float)$row->BERAT;
            $summary['subtotal']     += ((float)$row->QTY * $this->normalize_number($row->HARGA));
            $summary['total_diskon'] += $this->normalize_number($row->DISCOUNT);
            $summary['grand_total']  += $this->normalize_number($row->AMOUNT);
        }

        $summary['sisa_tagihan'] = $this->normalize_number($header->REMAIN);

        $data = compact('header', 'detail', 'summary');

        /* ================= PDF ================= */
        $html = $this->load->view('admin/sales/pdf_invoice_template', $data, true);

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            "INVOICE_{$sales}.pdf",
            ['Attachment' => false]
        );
    }

    /* ================= HELPER ================= */

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

        return (float) str_replace(['.', ','], ['', '.'], $value);
    }
}
