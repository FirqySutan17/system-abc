<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Mpdf\Mpdf;

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
        ob_clean();

        header('Content-Type: application/json');

        $page  = (int)$this->input->get('page') ?: 1;

        $limit = (int)$this->input->get('limit') ?: 10;

        $order = $this->input->get('order', true)
            ?: 'SALES_DATE';

        $dirInput = $this->input->get('dir', true);

        $dir = strtoupper($dirInput) === 'ASC'
            ? 'ASC'
            : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filters = [

            'search' => $this->input->get(
                'search',
                true
            ),

            'plant' => $this->input->get(
                'plant',
                true
            ),

            'customer' => $this->input->get(
                'customer',
                true
            ),

            'status' => $this->input->get(
                'status',
                true
            ),

            'date_from' => $this->input->get(
                'date_from',
                true
            ),

            'date_to' => $this->input->get(
                'date_to',
                true
            )
        ];

        /*
        |--------------------------------------------------------------------------
        | SESSION
        |--------------------------------------------------------------------------
        */

        $role_id = (int)$this->session
            ->userdata('role_id');

        $username = $this->session
            ->userdata('username');

        $plants = ($role_id === 1)
            ? []
            : $this->Sales_model
                ->get_user_plants($username);

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->Sales_model->get_data(
            $limit,
            $start,
            $role_id,
            $plants,
            $filters,
            $order,
            $dir
        );

        $total = $this->Sales_model->count_data(
            $role_id,
            $plants,
            $filters
        );

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $pages = ceil($total / $limit);

        $pagination = $this->build_pagination(
            $pages,
            $page
        );

        echo json_encode([

            'rows' => $rows,

            'total' => $total,

            'pagination' => $pagination,

            'page' => $page
        ]);

        exit;
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
        $cust = 'CS000002';

        $row = $this->Sales_model
            ->get_customer_by_id($cust);

        if ($row) {

            echo json_encode([

                'id'   => $row['CUST'],

                'text' => $row['CUST']
                    .' - '.
                    $row['FULL_NAME']
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

            $material = trim(
                $row['MATERIAL'] ?? ''
            );

            if ($material == '') {
                continue;
            }

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

            if ($berat <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */

            $amount = $berat * $harga;

            $grand += $amount;

            $rows[] = [

                'PLANT'      => $plant,

                'SALES'      => $salesNo,

                'SEQ_NO'     => $seq++,

                'CUSTOMER'   => trim($data['CUSTOMER']),

                'MATERIAL'   => $material,

                'JUMLAH'     => $jumlah,

                'BERAT'      => $berat,

                'HARGA'      => $harga,

                'TOTAL'      => $amount,

                'CREATED_AT' => date('Y-m-d H:i:s'),

                'CREATED_BY' => $username
            ];
        }

        if (empty($rows)) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => 'Detail sales tidak valid'
            ]);

            return;
        }

        $jenisPay = strtoupper(
            trim($data['JENIS_PAY'] ?? 'LUNAS')
        );

        if ($jenisPay === 'TEMPO') {

            $status = 'OPEN';

            $dp = 0;

            $remain = $grand;

        } else {

            // LUNAS

            $status = 'PAID';

            $dp = $grand;

            $remain = 0;
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
        ob_clean();

        header('Content-Type: application/json');

        $sales = trim(
            $this->input->get(
                'sales',
                TRUE
            )
        );

        $plant = trim(
            $this->input->get(
                'plant',
                TRUE
            )
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            $sales == '' ||
            $plant == ''
        ) {

            echo json_encode([

                'status' => false,

                'message' => 'Parameter tidak valid'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SESSION
        |--------------------------------------------------------------------------
        */

        $role_id = (int) $this->session
            ->userdata('role_id');

        $username = $this->session
            ->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDATE PLANT ACCESS
        |--------------------------------------------------------------------------
        */

        if ($role_id !== 1) {

            $hasPlant = $this->Sales_model
                ->user_has_plant(
                    $username,
                    $plant
                );

            if (!$hasPlant) {

                echo json_encode([

                    'status' => false,

                    'message' => 'Akses ditolak'
                ]);

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->Sales_model
            ->get_sales_header(
                $sales,
                $plant
            );

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' => 'Data sales tidak ditemukan'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detail = $this->Sales_model
            ->get_sales_detail(
                $sales,
                $plant
            );

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        echo json_encode([

            'status' => true,

            'header' => $header,

            'detail' => $detail
        ]);
    }

    public function update()
    {
        ob_clean();

        header('Content-Type: application/json');

        $data = $this->input->post(NULL, TRUE);

        $sales = trim(
            $data['SALES'] ?? ''
        );

        $plant = trim(
            $data['PLANT'] ?? ''
        );

        $username = $this->session
            ->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            $sales == '' ||
            $plant == ''
        ) {

            echo json_encode([

                'status' => false,

                'message' => 'Sales / Plant tidak valid'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATION HEADER
        |--------------------------------------------------------------------------
        */

        if (
            empty($data['CUSTOMER']) ||
            empty($data['SALES_DATE'])
        ) {

            echo json_encode([

                'status' => false,

                'message' => 'Customer dan Tanggal wajib diisi'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GET HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->Sales_model
            ->get_sales_header(
                $sales,
                $plant
            );

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' => 'Data sales tidak ditemukan'
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

                'status' => false,

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
        | DETAIL LOOP
        |--------------------------------------------------------------------------
        */

        $rows  = [];

        $seq   = 1;

        $grand = 0;

        foreach ($detailRows as $row) {

            $material = trim(
                $row['MATERIAL'] ?? ''
            );

            if ($material == '') {
                continue;
            }

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

            /*
            |--------------------------------------------------------------------------
            | VALIDATION DETAIL
            |--------------------------------------------------------------------------
            */

            if ($berat <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */

            $total = $berat * $harga;

            $grand += $total;

            /*
            |--------------------------------------------------------------------------
            | DETAIL ARRAY
            |--------------------------------------------------------------------------
            */

            $rows[] = [

                'PLANT'      => $plant,

                'SALES'      => $sales,

                'SEQ_NO'     => $seq++,

                'CUSTOMER'   => trim(
                    $data['CUSTOMER']
                ),

                'MATERIAL'   => $material,

                'JUMLAH'     => $jumlah,

                'BERAT'      => $berat,

                'HARGA'      => $harga,

                'TOTAL'      => $total,

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

                'status' => false,

                'message' => 'Detail sales tidak valid'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        $jenisPay = strtoupper(
            trim(
                $data['JENIS_PAY_EDIT'] ?? 'LUNAS'
            )
        );

        if ($jenisPay === 'TEMPO') {

            $status = 'OPEN';

            $dp = 0;

            $remain = $grand;

        } else {

            /*
            |--------------------------------------------------------------------------
            | LUNAS
            |--------------------------------------------------------------------------
            */

            $status = 'PAID';

            $dp = $grand;

            $remain = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER UPDATE
        |--------------------------------------------------------------------------
        */

        $headerUpdate = [

            'CUSTOMER' => trim(
                $data['CUSTOMER']
            ),

            'SALES_DATE' => date(
                'Y-m-d H:i:s',
                strtotime(
                    $data['SALES_DATE']
                )
            ),

            'PEMBAYARAN' => trim(
                $data['PEMBAYARAN_EDIT']
            ),

            'JENIS_PAY' => $jenisPay,

            'NOTA' => trim(
                $data['NOTA'] ?? ''
            ),

            'REMARK' => trim(
                $data['REMARK'] ?? ''
            ),

            'AMOUNT' => $grand,

            'DP_AMOUNT' => $dp,

            'REMAIN' => $remain,

            'STATUS' => $status,

            'UPDATED_AT' => date('Y-m-d H:i:s'),

            'UPDATED_BY' => $username
        ];

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
                    $sales . '_' . time(),

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

            /*
            |--------------------------------------------------------------------------
            | DELETE OLD FILE
            |--------------------------------------------------------------------------
            */

            if (
                !empty(
                    $header['ATTACHMENT_PATH']
                )
            ) {

                $oldFile =
                    FCPATH .
                    $header['ATTACHMENT_PATH'];

                if (
                    file_exists($oldFile)
                ) {

                    unlink($oldFile);
                }
            }

            $file = $this->upload->data();

            $headerUpdate['ATTACHMENT_NAME']
                = $file['client_name'];

            $headerUpdate['ATTACHMENT_PATH']
                = 'uploads/sales/' .
                date('Y') . '/' .
                $plant . '/' .
                $file['file_name'];

            $headerUpdate['ATTACHMENT_TYPE']
                = $file['file_type'];
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE HEADER
        |--------------------------------------------------------------------------
        */

        $this->Sales_model
            ->update_sales_header(
                $plant,
                $sales,
                $headerUpdate
            );

        /*
        |--------------------------------------------------------------------------
        | DELETE OLD DETAIL
        |--------------------------------------------------------------------------
        */

        $this->Sales_model
            ->delete_sales_detail(
                $plant,
                $sales
            );

        /*
        |--------------------------------------------------------------------------
        | INSERT NEW DETAIL
        |--------------------------------------------------------------------------
        */

        $this->Sales_model
            ->insert_sales_detail_batch(
                $rows
            );

        /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

        if (
            $this->db->trans_status()
            === FALSE
        ) {

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' => 'Gagal update sales'
            ]);

            return;
        }

        $this->db->trans_commit();

        echo json_encode([

            'status' => true,

            'message' => 'Sales berhasil diupdate'
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
        require_once APPPATH . '../vendor/autoload.php';

        $this->load->helper('terbilang');

        $sales = trim(
            $this->input->get('sales')
        );

        $plant = trim(
            $this->input->get('plant')
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            empty($sales) ||
            empty($plant)
        ) {

            show_error(
                'Parameter SALES atau PLANT tidak lengkap'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db
            ->select('
                s.SALES,
                s.PLANT,

                plant.CODE_NAME AS PLANT_NAME,

                s.SALES_DATE,

                s.CUSTOMER,

                customer.FULL_NAME AS CUSTOMER_NAME,

                s.JENIS_PAY,
                s.PEMBAYARAN,

                s.NOTA,

                s.AMOUNT,

                s.REMAIN,

                s.STATUS,

                s.REMARK
            ')
            ->from('abc_mst_sales s')

            ->join(
                'abc_cd_code plant',
                "plant.CODE = s.PLANT
                AND plant.HEAD_CODE = 'PLANT'",
                'left'
            )

            ->join(
                'abc_cd_customer customer',
                'customer.CUST = s.CUSTOMER',
                'left'
            )

            ->where(
                's.SALES',
                $sales
            )

            ->where(
                's.PLANT',
                $plant
            )

            ->get()
            ->row();

        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$header) {

            show_error(
                'Data SALES tidak ditemukan'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detail = $this->db
            ->select('
                d.SEQ_NO,

                d.MATERIAL,

                material.MATERIAL_NAME,

                d.JUMLAH,

                d.BERAT,

                d.HARGA,

                d.TOTAL
            ')
            ->from('abc_mst_sales_detail d')

            ->join(
                'abc_cd_material material',
                'material.MATERIAL = d.MATERIAL',
                'left'
            )

            ->where(
                'd.SALES',
                $sales
            )

            ->where(
                'd.PLANT',
                $plant
            )

            ->order_by(
                'd.SEQ_NO',
                'ASC'
            )

            ->get()
            ->result();

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $data = [

            'header' => $header,

            'detail' => $detail
        ];

        /*
        |--------------------------------------------------------------------------
        | HTML VIEW
        |--------------------------------------------------------------------------
        */

        $html = $this->load->view(
            'admin/sales/pdf_template_thermal',
            $data,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | MPDF
        |--------------------------------------------------------------------------
        */

        $mpdf = new \Mpdf\Mpdf([

            'mode' => 'utf-8',

            /*
            |--------------------------------------------------------------------------
            | CONTINUOUS FORM LANDSCAPE
            |--------------------------------------------------------------------------
            |
            | 24.13 cm x 13.97 cm
            |
            */

            'format' => [139.7, 241.3],

            'orientation' => 'L',

            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0
        ]);

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        */

        $mpdf->shrink_tables_to_fit = 0;

        $mpdf->SetDisplayMode('fullpage');

        $mpdf->SetTitle('Sales Print');

        $mpdf->SetJS('this.print();');

        /*
        |--------------------------------------------------------------------------
        | WRITE HTML
        |--------------------------------------------------------------------------
        */

        $mpdf->WriteHTML($html);

        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        $mpdf->Output(
            "SALES_{$sales}.pdf",
            'I'
        );
    }

    public function print_invoice_pdf()
    {
        $sales = $this->input->get('sales');
        $plant = $this->input->get('plant');

        if (!$sales || !$plant) {
            show_error('Parameter SALES atau PLANT tidak lengkap');
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db
            ->select('
                s.SALES,
                s.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                s.SALES_DATE,
                s.CUSTOMER,
                c.FULL_NAME AS CUSTOMER_NAME,
                s.JENIS_PAY,
                s.PEMBAYARAN,
                s.NOTA,
                s.REMARK,
                s.AMOUNT,
                s.REMAIN,
                s.STATUS
            ')
            ->from('abc_mst_sales s')
            ->join(
                'abc_cd_code aj',
                "aj.CODE = s.PLANT
                AND aj.HEAD_CODE = 'PLANT'",
                'left'
            )
            ->join(
                'abc_cd_customer c',
                "c.CUST = s.CUSTOMER",
                'left'
            )
            ->where('s.SALES', $sales)
            ->where('s.PLANT', $plant)
            ->get()
            ->row();

        if (!$header) {
            show_error('Sales invoice tidak ditemukan');
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT INFO
        |--------------------------------------------------------------------------
        */

        $header->PAYMENT_INFO = empty($header->JENIS_PAY)
            ? 'Belum ditentukan'
            : $header->JENIS_PAY . ' - ' . $header->PEMBAYARAN;

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detail = $this->db
            ->select('
                d.SEQ_NO,
                d.MATERIAL,
                m.MATERIAL_NAME,
                d.JUMLAH,
                d.BERAT,
                d.HARGA,
                d.TOTAL
            ')
            ->from('abc_mst_sales_detail d')
            ->join(
                'abc_cd_material m',
                'm.MATERIAL = d.MATERIAL',
                'left'
            )
            ->where('d.SALES', $sales)
            ->where('d.PLANT', $plant)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = [

            'total_qty'     => 0,

            'total_berat'   => 0,

            'grand_total'   => 0,

            'sisa_tagihan'  => 0,
        ];

        foreach ($detail as $row) {

            $summary['total_qty']
                += (float) $row->JUMLAH;

            $summary['total_berat']
                += (float) $row->BERAT;

            $summary['grand_total']
                += (float) $row->TOTAL;
        }

        $summary['sisa_tagihan']
            = (float) $header->REMAIN;

        $data = compact(
            'header',
            'detail',
            'summary'
        );

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $html = $this->load->view(
            'admin/sales/pdf_invoice_template',
            $data,
            true
        );

        $this->load->library('pdf');

        $this->pdf->loadHtml($html);

        $this->pdf->setPaper(
            'A4',
            'portrait'
        );

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
