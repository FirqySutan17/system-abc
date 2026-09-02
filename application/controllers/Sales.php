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

        $order =
            $this->input->get(
                'order',
                true
            )
            ?: 'CREATED_AT';

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

        // $role_id = (int)$this->session
        //     ->userdata('role_id');

        // $username = $this->session
        //     ->userdata('username');

        // $plants = ($role_id === 1)
        //     ? []
        //     : $this->Sales_model
        //         ->get_user_plants($username);

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->Sales_model->get_data(
            $limit,
            $start,
            $filters,
            $order,
            $dir
        );

        $total = $this->Sales_model->count_data(
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

    public function flag_sales()
    {
        ob_clean();

        header('Content-Type: application/json');

        $sales =
            trim(
                $this->input->post(
                    'sales',
                    true
                )
            );

        $plant =
            trim(
                $this->input->post(
                    'plant',
                    true
                )
            );

        $reason =
            trim(
                $this->input->post(
                    'reason',
                    true
                )
            );

        $username =
            $this->session->userdata(
                'username'
            );

        if (
            $sales === '' ||
            $plant === ''
        ) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Sales dan Plant wajib diisi'
            ]);

            return;
        }

        if ($reason === '') {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Alasan flag wajib diisi'
            ]);

            return;
        }

        $role_id =
            (int) $this->session
                ->userdata('role_id');

        /*
        |--------------------------------------------------------------------------
        | AUTHORIZATION PLANT
        |--------------------------------------------------------------------------
        */

        if ($role_id !== 1) {

            $plants =
                $this->Sales_model
                    ->get_user_plants(
                        $username
                    );

            if (
                empty($plants)
                ||
                !in_array(
                    $plant,
                    $plants,
                    true
                )
            ) {

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Anda tidak memiliki akses ke Plant ini'
                ]);

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK SALES
        |--------------------------------------------------------------------------
        */

        $salesRow =
            $this->db
                ->select(
                    'SALES, PLANT, FLAG'
                )
                ->from(
                    'abc_mst_sales'
                )
                ->where(
                    'SALES',
                    $sales
                )
                ->where(
                    'PLANT',
                    $plant
                )
                ->where(
                    'DELETED IS NULL',
                    null,
                    false
                )
                ->get()
                ->row();

        if (!$salesRow) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Data Sales tidak ditemukan'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE FLAG
        |--------------------------------------------------------------------------
        */

        $updated =
            $this->db
                ->where(
                    'SALES',
                    $sales
                )
                ->where(
                    'PLANT',
                    $plant
                )
                ->update(
                    'abc_mst_sales',
                    [

                        'FLAG' =>
                            1,

                        'FLAG_REASON' =>
                            $reason,

                        'FLAGGED_AT' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'FLAGGED_BY' =>
                            $username,

                        'UPDATED_AT' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'UPDATED_BY' =>
                            $username
                    ]
                );

        if (!$updated) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal melakukan Flag Sales'
            ]);

            return;
        }

        echo json_encode([
            'status'  => true,
            'message' =>
                'Sales berhasil di-Flag'
        ]);
    }

    public function unflag_sales()
    {
        ob_clean();

        header('Content-Type: application/json');

        $sales =
            trim(
                $this->input->post(
                    'sales',
                    true
                )
            );

        $plant =
            trim(
                $this->input->post(
                    'plant',
                    true
                )
            );

        $username =
            $this->session->userdata(
                'username'
            );

        if (
            $sales === '' ||
            $plant === ''
        ) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Sales dan Plant wajib diisi'
            ]);

            return;
        }

        $role_id =
            (int) $this->session
                ->userdata('role_id');

        if ($role_id !== 1) {

            $plants =
                $this->Sales_model
                    ->get_user_plants(
                        $username
                    );

            if (
                empty($plants)
                ||
                !in_array(
                    $plant,
                    $plants,
                    true
                )
            ) {

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Anda tidak memiliki akses ke Plant ini'
                ]);

                return;
            }
        }

        $updated =
            $this->db
                ->where(
                    'SALES',
                    $sales
                )
                ->where(
                    'PLANT',
                    $plant
                )
                ->where(
                    'DELETED IS NULL',
                    null,
                    false
                )
                ->update(
                    'abc_mst_sales',
                    [

                        'FLAG' =>
                            0,

                        'FLAG_REASON' =>
                            null,

                        'FLAGGED_AT' =>
                            null,

                        'FLAGGED_BY' =>
                            null,

                        'UPDATED_AT' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'UPDATED_BY' =>
                            $username
                    ]
                );

        if (!$updated) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal menghapus Flag Sales'
            ]);

            return;
        }

        echo json_encode([
            'status'  => true,
            'message' =>
                'Flag Sales berhasil dihapus'
        ]);
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
            echo json_encode($row);
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
        $selectedPlant = $this->input->get('plant');
        $data = $this->Sales_model->search_material($term, $selectedPlant);
        echo json_encode($data);
    }

    public function get_stock_preview()
    {
        ob_clean();

        header('Content-Type: application/json');

        $plant = trim($this->input->get('plant', true));
        $material = trim($this->input->get('material', true));

        if (!$plant || !$material) {
            echo json_encode([
                'status' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        $stock = $this->Sales_model->get_company_stock($plant, $material);

        echo json_encode([
            'status' => true,
            'stock' => $stock
        ]);
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

        $data =
            $this->input->post(
                NULL,
                TRUE
            );

        $username =
            $this->session->userdata(
                'username'
            );

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
                'message' =>
                    'Plant, Customer dan Tanggal wajib diisi'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detailRows =
            json_decode(
                $data['DETAIL'] ?? '[]',
                true
            );

        if (
            !is_array($detailRows) ||
            empty($detailRows)
        ) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Detail item tidak boleh kosong'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | BASIC HEADER VALUES
        |--------------------------------------------------------------------------
        */

        $plant =
            trim(
                $data['PLANT']
            );

        $customer =
            trim(
                $data['CUSTOMER']
            );

        $jenisPay =
            strtoupper(
                trim(
                    $data['JENIS_PAY']
                    ?? 'LUNAS'
                )
            );

        /*
        |--------------------------------------------------------------------------
        | PARSE FINANCIAL INPUT
        |--------------------------------------------------------------------------
        */

        $modal = 0;

        $biaya =
            round(
                (float) str_replace(
                    ',',
                    '',
                    $data['BIAYA'] ?? 0
                ),
                2
            );

        $discount =
            round(
                (float) str_replace(
                    ',',
                    '',
                    $data['DISCOUNT'] ?? 0
                ),
                2
            );

        $rounding =
            round(
                (float) str_replace(
                    ',',
                    '',
                    $data['ROUNDING'] ?? 0
                ),
                2
            );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION FINANCIAL INPUT
        |--------------------------------------------------------------------------
        */

        if ($biaya < 0) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Biaya tidak boleh negatif'
            ]);

            return;
        }

        if ($discount < 0) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Discount tidak boleh negatif'
            ]);

            return;
        }

        if ($rounding < 0) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Pembulatan tidak boleh negatif'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_start();

        /*
        |--------------------------------------------------------------------------
        | GENERATE SALES NUMBER
        |--------------------------------------------------------------------------
        */

        $salesNo =
            $this->Sales_model
                ->generate_sales_no(
                    $plant
                );

        /*
        |--------------------------------------------------------------------------
        | DETAIL LOOP
        |--------------------------------------------------------------------------
        */

        $rows =
            [];

        $stockTransactions =
            [];

        $seq =
            1;

        $baseSales   = 0;
        $totalQty    = 0;
        $totalWeight = 0;

        foreach (
            $detailRows
            as $row
        ) {

            /*
            |--------------------------------------------------------------------------
            | MATERIAL
            |--------------------------------------------------------------------------
            */

            $material =
                trim(
                    $row['MATERIAL']
                    ?? ''
                );

            if ($material === '') {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | NUMERIC VALUES
            |--------------------------------------------------------------------------
            */

            $jumlah =
                (float) str_replace(
                    ',',
                    '',
                    $row['JUMLAH']
                    ?? 0
                );

            $berat =
                (float) str_replace(
                    ',',
                    '',
                    $row['BERAT']
                    ?? 0
                );

            $harga =
                (float) str_replace(
                    ',',
                    '',
                    $row['HARGA']
                    ?? 0
                );

            /*
            |--------------------------------------------------------------------------
            | CALCULATION BASIS
            |--------------------------------------------------------------------------
            */

            $calcBasis =
                strtoupper(
                    trim(
                        $row['CALC_BASIS']
                        ?? ''
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | VALIDATION BASIS
            |--------------------------------------------------------------------------
            */

            if (
                !in_array(
                    $calcBasis,
                    [
                        'EKOR',
                        'BERAT'
                    ],
                    true
                )
            ) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Basis perhitungan detail material ' .
                        $material .
                        ' harus EKOR atau BERAT'
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION QTY / WEIGHT / PRICE
            |--------------------------------------------------------------------------
            */

            if ($jumlah <= 0) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Jumlah/Ekor harus lebih dari 0 untuk material ' .
                        $material
                ]);

                return;
            }

            if ($berat <= 0) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Berat harus lebih dari 0 untuk material ' .
                        $material
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL QTY & TOTAL WEIGHT
            |--------------------------------------------------------------------------
            */

            $totalQty += $jumlah;

            $totalWeight += $berat;

            if ($harga < 0) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Harga tidak boleh negatif untuk material ' .
                        $material
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | CALCULATE DETAIL TOTAL
            |--------------------------------------------------------------------------
            */

            if ($calcBasis === 'EKOR') {

                $amount =
                    round(
                        $jumlah * $harga,
                        2
                    );

            } else {

                $amount =
                    round(
                        $berat * $harga,
                        2
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | BASE SALES TOTAL
            |--------------------------------------------------------------------------
            */

            $baseSales +=
                $amount;

            /*
            |--------------------------------------------------------------------------
            | GET COMPANY STOCK
            |--------------------------------------------------------------------------
            */

            $stock =
                $this->Sales_model
                    ->get_company_stock(
                        $plant,
                        $material
                    );

            $availableQty =
                (float) (
                    $stock['QTY']
                    ?? 0
                );

            $availableBw =
                (float) (
                    $stock['BW']
                    ?? 0
                );

            /*
            |--------------------------------------------------------------------------
            | STOCK VALIDATION
            |--------------------------------------------------------------------------
            */

            if (
                ($stock['STATUS'] ?? '')
                    === 'NOT_FOUND'
                ||
                $availableQty < $jumlah
                ||
                $availableBw < $berat
            ) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Stok tidak mencukupi untuk material ' .
                        $material
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | STOCK TRANSACTION
            |--------------------------------------------------------------------------
            */

            $stockTransactions[] = [

                'PLANT' =>
                    $plant,

                'MATERIAL' =>
                    $material,

                'QTY_OUT' =>
                    $jumlah,

                'BW_OUT' =>
                    $berat,

                'CREATED_AT' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

                'CREATED_BY' =>
                    $username
            ];

            /*
            |--------------------------------------------------------------------------
            | DETAIL ROW
            |--------------------------------------------------------------------------
            */

            $rows[] = [

                'PLANT' =>
                    $plant,

                'SALES' =>
                    $salesNo,

                'SEQ_NO' =>
                    $seq++,

                'CUSTOMER' =>
                    $customer,

                'MATERIAL' =>
                    $material,

                /*
                | Legacy METHOD
                | Tidak digunakan untuk calculation baru
                */
                'METHOD' =>
                    null,

                /*
                | New calculation basis
                */
                'CALC_BASIS' =>
                    $calcBasis,

                'JUMLAH' =>
                    $jumlah,

                'BERAT' =>
                    $berat,

                'HARGA' =>
                    $harga,

                'TOTAL' =>
                    $amount,

                'CREATED_AT' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

                'CREATED_BY' =>
                    $username
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL VALIDATION
        |--------------------------------------------------------------------------
        */

        if (empty($rows)) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Detail sales tidak valid'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CALCULATE MODAL / EKOR
        |--------------------------------------------------------------------------
        |
        | Modal = Base Sales / Total Ekor
        |
        */

        if ($totalQty > 0) {

            $modal =
                round(
                    $baseSales /
                    $totalQty,
                    2
                );

        } else {

            $modal = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | PARSE SAVING
        |--------------------------------------------------------------------------
        |
        | Saving dihitung berdasarkan:
        | - Total Ekor jika basis EKOR
        | - Total Berat jika basis BERAT
        |
        */

        try {

            $savings =
                $this->parseSavingPayload(
                    $data['SAVINGS'] ?? '[]',
                    $customer,
                    $totalQty,
                    $totalWeight
                );

            if (!is_array($savings)) {
                $savings = [];
            }

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    $e->getMessage()
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL SAVING
        |--------------------------------------------------------------------------
        */

        $totalSaving = 0;

        foreach ($savings as $saving) {

            $totalSaving +=
                (float) (
                    $saving['SAVING_AMOUNT']
                    ?? 0
                );
        }

        $totalSaving =
            round(
                $totalSaving,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | SALES COMPONENT
        |--------------------------------------------------------------------------
        |
        | Base Sales
        | + Biaya
        | - Discount
        | + Rounding
        |--------------------------------------------------------------------------
        */

        $salesComponent =
            round(
                $baseSales
                +
                $biaya
                -
                $discount
                +
                $rounding,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | VALIDATE SALES COMPONENT
        |--------------------------------------------------------------------------
        */

        if ($salesComponent < 0) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Total Sales setelah Discount tidak boleh negatif'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GRAND AMOUNT
        |--------------------------------------------------------------------------
        |
        | Sales Component
        | +
        | Saving
        |--------------------------------------------------------------------------
        */

        $grandAmount =
            round(
                $salesComponent
                +
                $totalSaving,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | PAYMENT STATUS
        |--------------------------------------------------------------------------
        |
        | DP TIDAK DIGUNAKAN.
        |
        | Untuk TEMPO:
        | REMAIN = Sales Component
        |
        | Saving memiliki REMAIN sendiri.
        |
        |--------------------------------------------------------------------------
        */

        if (
            $jenisPay === 'TEMPO'
        ) {

            $status =
                'OPEN';

            $remain =
                $salesComponent;

        } else {

            /*
            | LUNAS
            */

            $status =
                'PAID';

            $remain =
                0;
        }

        /*
        |--------------------------------------------------------------------------
        | SALES HEADER
        |--------------------------------------------------------------------------
        */

        $header = [

            'PLANT' =>
                $plant,

            'SALES' =>
                $salesNo,

            'CUSTOMER' =>
                $customer,

            'SALES_DATE' =>
                date(
                    'Y-m-d H:i:s',
                    strtotime(
                        $data['SALES_DATE']
                    )
                ),

            'PEMBAYARAN' =>
                trim(
                    $data['PEMBAYARAN']
                    ?? ''
                ),

            'JENIS_PAY' =>
                $jenisPay,

            'NOTA' =>
                trim(
                    $data['NOTA']
                    ?? ''
                ),

            'REMARK' =>
                trim(
                    $data['REMARK']
                    ?? ''
                ),

            /*
            | Final Sales + Saving
            */
            'AMOUNT' =>
                $grandAmount,

            /*
            | Internal / financial fields
            */
            'MODAL' =>
                $modal,

            'BIAYA' =>
                $biaya,

            'DISCOUNT' =>
                $discount,

            'ROUNDING' =>
                $rounding,

            /*
            | Legacy DP
            | Tidak digunakan
            */
            'DP_AMOUNT' =>
                0,

            /*
            | Sales component only
            | Saving has own REMAIN
            */
            'REMAIN' =>
                $remain,

            'STATUS' =>
                $status,

            'CREATED_AT' =>
                date(
                    'Y-m-d H:i:s'
                ),

            'CREATED_BY' =>
                $username
        ];

        /*
        |--------------------------------------------------------------------------
        | INSERT HEADER
        |--------------------------------------------------------------------------
        */

        if (
            !$this->Sales_model
                ->insert_sales_header(
                    $header
                )
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal menyimpan header sales'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ATTACHMENT
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $_FILES['ATTACHMENT']
            )
            &&
            !empty(
                $_FILES['ATTACHMENT']['name']
            )
        ) {

            $uploadPath =
                FCPATH .
                'uploads/sales/' .
                date('Y') .
                '/' .
                $plant;

            if (!is_dir($uploadPath)) {

                mkdir(
                    $uploadPath,
                    0777,
                    true
                );
            }

            $config = [

                'upload_path' =>
                    $uploadPath,

                'allowed_types' =>
                    'jpg|jpeg|png|pdf|doc|docx|xls|xlsx',

                'max_size' =>
                    5120,

                'file_name' =>
                    $salesNo .
                    '_' .
                    time(),

                'overwrite' =>
                    false
            ];

            $this->load->library(
                'upload',
                $config
            );

            if (
                !$this->upload
                    ->do_upload(
                        'ATTACHMENT'
                    )
            ) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status' =>
                        false,

                    'message' =>
                        strip_tags(
                            $this->upload
                                ->display_errors()
                        )
                ]);

                return;
            }

            $file =
                $this->upload
                    ->data();

            $this->Sales_model
                ->update_sales_header(
                    $plant,
                    $salesNo,
                    [

                        'ATTACHMENT_NAME' =>
                            $file[
                                'client_name'
                            ],

                        'ATTACHMENT_PATH' =>
                            'uploads/sales/' .
                            date('Y') .
                            '/' .
                            $plant .
                            '/' .
                            $file[
                                'file_name'
                            ],

                        'ATTACHMENT_TYPE' =>
                            $file[
                                'file_type'
                            ]
                    ]
                );
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT DETAIL
        |--------------------------------------------------------------------------
        */

        if (
            !$this->Sales_model
                ->insert_sales_detail_batch(
                    $rows
                )
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal menyimpan detail sales'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE COMPANY STOCK
        |--------------------------------------------------------------------------
        */

        if (
            !$this->Sales_model
                ->update_company_stock_for_sales(
                    $stockTransactions,
                    $username
                )
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal mengurangi stok perusahaan'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT COMPANY STOCK CARD
        |--------------------------------------------------------------------------
        */

        if (
            !$this->Sales_model
                ->insert_company_stock_card(
                    $stockTransactions,
                    $salesNo,
                    'SALES',
                    $header['SALES_DATE'],
                    $username
                )
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal mencatat kartu stock perusahaan'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SAVING
        |--------------------------------------------------------------------------
        */

        try {

            $savingRows =
                $this->buildSavingRecords(
                    $salesNo,
                    $plant,
                    $customer,
                    $header['SALES_DATE'],
                    $savings,
                    $totalQty,
                    $totalWeight,
                    $username
                );

            if (
                !empty($savingRows)
                &&
                !$this->Sales_model
                    ->insert_saving_batch(
                        $savingRows
                    )
            ) {

                throw new Exception(
                    'Gagal menyimpan data Saving'
                );
            }

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    $e->getMessage()
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $this->db->trans_status()
            === FALSE
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal menyimpan sales'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

        $this->db->trans_commit();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        echo json_encode([

            'status' =>
                true,

            'message' =>
                'Sales berhasil dibuat',

            'sales' =>
                $salesNo
        ]);
    }

    private function parseSavingPayload(
        $raw,
        $customer,
        $totalQty = 0,
        $totalWeight = 0
    )
    {
        $rows =
            json_decode(
                $raw ?? '[]',
                true
            );

        if (!is_array($rows)) {
            return [];
        }

        $result = [];

        foreach ($rows as $row) {

            $cust =
                trim(
                    $row['CUSTOMER']
                    ?? ''
                );

            if (
                $cust === ''
                ||
                $cust !== $customer
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SAVING BASIS
            |--------------------------------------------------------------------------
            */

            $basis =
                strtoupper(
                    trim(
                        $row['BASIS']
                        ?? ''
                    )
                );

            if (
                !in_array(
                    $basis,
                    [
                        'EKOR',
                        'BERAT'
                    ],
                    true
                )
            ) {

                throw new Exception(
                    'Basis Saving harus EKOR atau BERAT'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | SAVING RATE
            |--------------------------------------------------------------------------
            */

            $rate =
                round(
                    $this->parseDecimalID(
                        $row['SAVING_RATE']
                        ?? 0
                    ),
                    2
                );

            if ($rate < 0) {

                throw new Exception(
                    'Saving rate tidak boleh negatif'
                );
            }

            if ($rate <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | CALCULATE SAVING AMOUNT
            |--------------------------------------------------------------------------
            */

            if ($basis === 'EKOR') {

                $amount =
                    round(
                        $totalQty * $rate,
                        2
                    );

            } else {

                $amount =
                    round(
                        $totalWeight * $rate,
                        2
                    );
            }

            if ($amount <= 0) {
                continue;
            }

            $result[] = [

                'CUSTOMER' =>
                    $cust,

                'SAVING_AMOUNT' =>
                    $amount,

                'BASIS' =>
                    $basis,

                'REMARK' =>
                    trim(
                        $row['REMARK']
                        ?? ''
                    )
            ];
        }

        return $result;
    }

    private function buildSavingRecords(
        $salesNo,
        $plant,
        $customer,
        $salesDate,
        array $savings,
        $totalQty,
        $totalWeight,
        $username
    )
    {
        if (empty($savings)) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL SAVING
        |--------------------------------------------------------------------------
        */

        $amount =
            round(
                array_sum(
                    array_column(
                        $savings,
                        'SAVING_AMOUNT'
                    )
                ),
                2
            );

        if ($amount <= 0) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | BASIS SAVING
        |--------------------------------------------------------------------------
        |
        | Satu Sales hanya boleh menggunakan satu basis Saving.
        |
        */

        $basisList = [];

        foreach ($savings as $saving) {

            $basis =
                strtoupper(
                    trim(
                        $saving['BASIS']
                        ?? ''
                    )
                );

            if (
                !in_array(
                    $basis,
                    [
                        'EKOR',
                        'BERAT'
                    ],
                    true
                )
            ) {

                throw new Exception(
                    'Basis Saving harus EKOR atau BERAT'
                );
            }

            $basisList[] = $basis;
        }

        $basisList =
            array_values(
                array_unique(
                    $basisList
                )
            );

        if (count($basisList) > 1) {

            throw new Exception(
                'Dalam satu Sales, basis Saving harus sama'
            );
        }

        $basis =
            $basisList[0]
            ?? null;

        /*
        |--------------------------------------------------------------------------
        | TOTAL QTY / WEIGHT
        |--------------------------------------------------------------------------
        */

        $totalQty =
            round(
                (float)$totalQty,
                2
            );

        $totalWeight =
            round(
                (float)$totalWeight,
                2
            );

        if ($basis === 'EKOR') {

            if ($totalQty <= 0) {

                throw new Exception(
                    'Total ekor tidak valid untuk perhitungan Saving'
                );
            }

            $rate =
                round(
                    $amount / $totalQty,
                    2
                );

        } else {

            if ($totalWeight <= 0) {

                throw new Exception(
                    'Total berat tidak valid untuk perhitungan Saving'
                );
            }

            $rate =
                round(
                    $amount / $totalWeight,
                    2
                );
        }

        /*
        |--------------------------------------------------------------------------
        | USER REMARK
        |--------------------------------------------------------------------------
        */

        $remarks =
            array_filter(
                array_column(
                    $savings,
                    'REMARK'
                )
            );

        $userRemark =
            !empty($remarks)
                ? implode(
                    '; ',
                    $remarks
                )
                : '';

        /*
        |--------------------------------------------------------------------------
        | SYSTEM REMARK
        |--------------------------------------------------------------------------
        */

        $systemRemark =
            'AUTO FROM SALES ' .
            $salesNo;

        $finalRemark =
            $userRemark !== ''
                ? $userRemark .
                    ' | ' .
                    $systemRemark
                : $systemRemark;

        /*
        |--------------------------------------------------------------------------
        | SAVING RECORD
        |--------------------------------------------------------------------------
        */

        return [[

            'SV_NO' =>
                $this->Sales_model
                    ->generate_saving_no(),

            'PLANT' =>
                $plant,

            'SV_DATE' =>
                date(
                    'Y-m-d',
                    strtotime(
                        $salesDate
                    )
                ),

            'CUSTOMER' =>
                $customer,

            'RELATED' =>
                'SALES',

            'RECEIVE' =>
                null,

            'SALES' =>
                $salesNo,

            /*
            | Total Saving
            */
            'AMOUNT' =>
                $amount,

            /*
            | New Saving metadata
            */
            'BASIS' =>
                $basis,

            'RATE' =>
                $rate,

            'TOTAL_QTY' =>
                $totalQty,

            'TOTAL_WEIGHT' =>
                $totalWeight,

            /*
            | Current outstanding Saving
            */
            'REMAIN' =>
                $amount,

            'REMARK' =>
                $finalRemark,

            'STATUS' =>
                'OPEN',

            'CREATED_AT' =>
                date(
                    'Y-m-d H:i:s'
                ),

            'CREATED_BY' =>
                $username

        ]];
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

        $saving = $this->Sales_model
            ->get_saving_by_sales($sales, $plant);

        echo json_encode([

            'status' => true,

            'header' => $header,

            'detail' => $detail,

            'saving' => $saving ?: null
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
        | REVERSE OLD STOCK
        |--------------------------------------------------------------------------
        */

        $oldDetailRows = $this->Sales_model
            ->get_sales_detail_rows($plant, $sales);

        $reverseStockRows = [];

        foreach ($oldDetailRows as $oldRow) {
            $reverseStockRows[] = [
                'PLANT' => $plant,
                'MATERIAL' => $oldRow['MATERIAL'],
                'QTY_OUT' => (float)($oldRow['JUMLAH'] ?? 0),
                'BW_OUT' => (float)($oldRow['BERAT'] ?? 0)
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_start();

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

        $grand = $grand + $data['TOTAL_SAVING_EDIT'];

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

            'DISCOUNT' => $data['DISCOUNT'] ?? 0,

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
        | RESET OLD STOCK
        |--------------------------------------------------------------------------
        */

        if (!empty($reverseStockRows)) {
            $this->Sales_model->delete_company_stock_card_by_reference($sales, 'SALES');
            $this->Sales_model->restore_company_stock_for_sales($reverseStockRows, $username);
            $this->Sales_model->insert_company_stock_card_reversal($reverseStockRows, $sales, 'SALES', $header['SALES_DATE'], $username);
        }

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
        | NEW STOCK VALIDATION
        |--------------------------------------------------------------------------
        */

        $newStockRows = [];

        foreach ($rows as $row) {
            $newStockRows[] = [
                'PLANT' => $row['PLANT'],
                'MATERIAL' => $row['MATERIAL'],
                'QTY_OUT' => (float)($row['JUMLAH'] ?? 0),
                'BW_OUT' => (float)($row['BERAT'] ?? 0)
            ];
        }

        foreach ($newStockRows as $stockRow) {
            $stock = $this->Sales_model->get_company_stock($stockRow['PLANT'], $stockRow['MATERIAL']);

            if (($stock['STATUS'] ?? '') === 'NOT_FOUND') {
                $this->db->trans_rollback();

                echo json_encode([
                    'status' => false,
                    'message' => 'Stok tidak ditemukan untuk material ' . $stockRow['MATERIAL']
                ]);

                return;
            }

            if (
                (float)($stock['QTY'] ?? 0) < (float)($stockRow['QTY_OUT'] ?? 0) ||
                (float)($stock['BW'] ?? 0) < (float)($stockRow['BW_OUT'] ?? 0)
            ) {
                $this->db->trans_rollback();

                echo json_encode([
                    'status' => false,
                    'message' => 'Stok tidak mencukupi untuk material ' . $stockRow['MATERIAL']
                ]);

                return;
            }
        }

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
        | APPLY NEW STOCK
        |--------------------------------------------------------------------------
        */

        $this->Sales_model->update_company_stock_for_sales($newStockRows, $username);
        $this->Sales_model->insert_company_stock_card($newStockRows, $sales, 'SALES', $header['SALES_DATE'], $username);

        /*
        |--------------------------------------------------------------------------
        | SAVING
        |--------------------------------------------------------------------------
        */

        try {

            $this->Sales_model->delete_saving_by_sales(
                $sales,
                $plant,
                $username
            );

            $savings = $this->parseSavingPayload(
                $data['SAVINGS'] ?? '[]',
                trim($data['CUSTOMER'])
            );

            $savingRows = $this->buildSavingRecords(
                $sales,
                $plant,
                trim($data['CUSTOMER']),
                $headerUpdate['SALES_DATE'],
                $savings,
                $username
            );

            if (
                !empty($savingRows) &&
                !$this->Sales_model->insert_saving_batch($savingRows)
            ) {
                throw new Exception('Gagal menyimpan data Saving');
            }

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);

            return;
        }

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
        ob_clean();

        header('Content-Type: application/json');

        $sales =
            trim(
                $this->input->post(
                    'sales',
                    true
                )
            );

        $plant =
            trim(
                $this->input->post(
                    'plant',
                    true
                )
            );

        $username =
            $this->session->userdata(
                'username'
            );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            $sales === '' ||
            $plant === ''
        ) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Parameter Sales dan Plant tidak lengkap'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GET HEADER
        |--------------------------------------------------------------------------
        */

        $header =
            $this->Sales_model
                ->get_sales_header(
                    $sales,
                    $plant
                );

        if (!$header) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Data Sales tidak ditemukan'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK SAVING USED BY CASH IN
        |--------------------------------------------------------------------------
        */

        if (
            $this->Sales_model
                ->is_saving_used_by_cash_in(
                    $sales,
                    $plant
                )
        ) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Sales tidak dapat dihapus karena Saving sudah digunakan pada Cash In.'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GET ACTIVE DETAIL
        |--------------------------------------------------------------------------
        */

        $detailRows =
            $this->Sales_model
                ->get_sales_detail_rows(
                    $plant,
                    $sales
                );

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_begin();

        /*
        |--------------------------------------------------------------------------
        | RESTORE STOCK
        |--------------------------------------------------------------------------
        */

        $restoreRows = [];

        foreach (
            $detailRows
            as $row
        ) {

            $restoreRows[] = [

                'PLANT' =>
                    $plant,

                'MATERIAL' =>
                    $row['MATERIAL'],

                'QTY_OUT' =>
                    (float) (
                        $row['JUMLAH']
                        ?? 0
                    ),

                'BW_OUT' =>
                    (float) (
                        $row['BERAT']
                        ?? 0
                    )
            ];
        }

        if (!empty($restoreRows)) {

            /*
            |----------------------------------------------------------------------
            | SOFT DELETE ORIGINAL STOCK CARD
            |----------------------------------------------------------------------
            */

            if (
                !$this->Sales_model
                    ->delete_company_stock_card_by_reference(
                        $sales,
                        'SALES'
                    )
            ) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Gagal membatalkan kartu stock Sales'
                ]);

                return;
            }

            /*
            |----------------------------------------------------------------------
            | RESTORE COMPANY STOCK
            |----------------------------------------------------------------------
            */

            if (
                !$this->Sales_model
                    ->restore_company_stock_for_sales(
                        $restoreRows,
                        $username
                    )
            ) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Gagal mengembalikan stock Sales'
                ]);

                return;
            }

            /*
            |----------------------------------------------------------------------
            | INSERT REVERSAL STOCK CARD
            |----------------------------------------------------------------------
            */

            if (
                !$this->Sales_model
                    ->insert_company_stock_card_reversal(
                        $restoreRows,
                        $sales,
                        'SALES',
                        $header['SALES_DATE']
                            ?? date(
                                'Y-m-d H:i:s'
                            ),
                        $username
                    )
            ) {

                $this->db->trans_rollback();

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Gagal mencatat reversal kartu stock'
                ]);

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SOFT DELETE SAVING
        |--------------------------------------------------------------------------
        */

        if (
            !$this->Sales_model
                ->delete_saving_by_sales(
                    $sales,
                    $plant,
                    $username
                )
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal menghapus Saving terkait Sales'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SOFT DELETE DETAIL
        |--------------------------------------------------------------------------
        */

        if (
            !$this->Sales_model
                ->delete_sales_detail(
                    $plant,
                    $sales
                )
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal menghapus detail Sales'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SOFT DELETE HEADER
        |--------------------------------------------------------------------------
        */

        if (
            !$this->Sales_model
                ->delete_sales_header(
                    $plant,
                    $sales
                )
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal menghapus header Sales'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $this->db->trans_status()
            === FALSE
        ) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Gagal menghapus Sales'
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
            'message' =>
                'Sales berhasil dihapus'
        ]);
    }

    public function print_pdf()
    {
        $this->load->helper('terbilang');

        $sales =
            trim(
                $this->input->get(
                    'sales',
                    true
                )
            );

        $plant =
            trim(
                $this->input->get(
                    'plant',
                    true
                )
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

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header =
            $this->db
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

                    s.MODAL,

                    s.BIAYA,

                    s.DISCOUNT,

                    s.ROUNDING,

                    s.REMARK

                ', false)

                ->from(
                    'abc_mst_sales s'
                )

                ->join(
                    'abc_cd_code plant',
                    "
                        plant.CODE = s.PLANT
                        AND plant.HEAD_CODE = 'PLANT'
                    ",
                    'left',
                    false
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

                ->where(
                    's.DELETED IS NULL',
                    null,
                    false
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

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detail =
            $this->db
                ->select('

                    d.SEQ_NO,

                    d.MATERIAL,

                    material.MATERIAL_NAME,

                    d.CALC_BASIS,

                    d.JUMLAH,

                    d.BERAT,

                    d.HARGA,

                    d.TOTAL

                ', false)

                ->from(
                    'abc_mst_sales_detail d'
                )

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

                ->where(
                    'd.DELETED IS NULL',
                    null,
                    false
                )

                ->order_by(
                    'd.SEQ_NO',
                    'ASC'
                )

                ->get()

                ->result();

        /*
        |--------------------------------------------------------------------------
        | SAVING
        |--------------------------------------------------------------------------
        */

        $saving =
            $this->db
                ->select('

                    SV_NO,

                    AMOUNT,

                    BASIS,

                    RATE,

                    TOTAL_QTY,

                    TOTAL_WEIGHT,

                    REMAIN

                ', false)

                ->from(
                    'abc_mst_saving'
                )

                ->where(
                    'SALES',
                    $sales
                )

                ->where(
                    'PLANT',
                    $plant
                )

                ->where(
                    'RELATED',
                    'SALES'
                )

                ->where(
                    'DELETED IS NULL',
                    null,
                    false
                )

                ->order_by(
                    'SV_NO',
                    'ASC'
                )

                ->get()

                ->result();

        /*
        |--------------------------------------------------------------------------
        | CALCULATE DETAIL TOTAL
        |--------------------------------------------------------------------------
        */

        $totalQty =
            0;

        $totalBerat =
            0;

        $baseSales =
            0;

        foreach (
            $detail
            as $row
        ) {

            $totalQty +=
                (float) (
                    $row->JUMLAH
                    ?? 0
                );

            $totalBerat +=
                (float) (
                    $row->BERAT
                    ?? 0
                );

            $baseSales +=
                (float) (
                    $row->TOTAL
                    ?? 0
                );
        }

        /*
        |--------------------------------------------------------------------------
        | FINANCIAL COMPONENT
        |--------------------------------------------------------------------------
        */

        $biaya =
            (float) (
                $header->BIAYA
                ?? 0
            );

        $discount =
            (float) (
                $header->DISCOUNT
                ?? 0
            );

        $rounding =
            (float) (
                $header->ROUNDING
                ?? 0
            );

        /*
        |--------------------------------------------------------------------------
        | TOTAL SALES
        |--------------------------------------------------------------------------
        */

        $salesAmount =
            round(
                $baseSales
                +
                $biaya
                -
                $discount
                +
                $rounding,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | TOTAL SAVING
        |--------------------------------------------------------------------------
        */

        $totalSaving =
            0;

        $savingRemain =
            0;

        foreach (
            $saving
            as $sv
        ) {

            $totalSaving +=
                (float) (
                    $sv->AMOUNT
                    ?? 0
                );

            $savingRemain +=
                (float) (
                    $sv->REMAIN
                    ?? 0
                );
        }

        $totalSaving =
            round(
                $totalSaving,
                2
            );

        $savingRemain =
            round(
                $savingRemain,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */

        $grandTotal =
            round(
                $salesAmount
                +
                $totalSaving,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING
        |--------------------------------------------------------------------------
        */

        $outstandingSales =
            round(
                (float) (
                    $header->REMAIN
                    ?? 0
                ),
                2
            );

        $totalOutstanding =
            round(
                $outstandingSales
                +
                $savingRemain,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $data = [

            'header' =>
                $header,

            'detail' =>
                $detail,

            'saving' =>
                $saving,

            'summary' => [

                'total_qty' =>
                    $totalQty,

                'total_berat' =>
                    $totalBerat,

                'base_sales' =>
                    $baseSales,

                'biaya' =>
                    $biaya,

                'discount' =>
                    $discount,

                'rounding' =>
                    $rounding,

                'sales_amount' =>
                    $salesAmount,

                'total_saving' =>
                    $totalSaving,

                'grand_total' =>
                    $grandTotal,

                'outstanding_sales' =>
                    $outstandingSales,

                'outstanding_saving' =>
                    $savingRemain,

                'total_outstanding' =>
                    $totalOutstanding
            ]
        ];

        /*
        |--------------------------------------------------------------------------
        | PRINT VIEW
        |--------------------------------------------------------------------------
        */

        $this->load->view(
            'admin/sales/pdf_template_thermal',
            $data
        );
    }

    public function print_invoice_pdf()
    {
        $sales =
            $this->input->get(
                'sales',
                true
            );

        $plant =
            $this->input->get(
                'plant',
                true
            );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            !$sales ||
            !$plant
        ) {

            show_error(
                'Parameter SALES atau PLANT tidak lengkap'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header =
            $this->db

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

                    s.MODAL,

                    s.BIAYA,

                    s.DISCOUNT,

                    s.ROUNDING,

                    s.REMAIN,

                    s.STATUS

                ', false)

                ->from(
                    'abc_mst_sales s'
                )

                /*
                |--------------------------------------------------------------------------
                | PLANT
                |--------------------------------------------------------------------------
                */

                ->join(
                    'abc_cd_code aj',
                    "
                        aj.CODE = s.PLANT
                        AND aj.HEAD_CODE = 'PLANT'
                    ",
                    'left',
                    false
                )

                /*
                |--------------------------------------------------------------------------
                | CUSTOMER
                |--------------------------------------------------------------------------
                */

                ->join(
                    'abc_cd_customer c',
                    'c.CUST = s.CUSTOMER',
                    'left'
                )

                /*
                |--------------------------------------------------------------------------
                | FILTER
                |--------------------------------------------------------------------------
                */

                ->where(
                    's.SALES',
                    $sales
                )

                ->where(
                    's.PLANT',
                    $plant
                )

                ->where(
                    's.DELETED IS NULL',
                    null,
                    false
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
                'Sales invoice tidak ditemukan'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT INFO
        |--------------------------------------------------------------------------
        */

        $header->PAYMENT_INFO =
            empty($header->JENIS_PAY)

                ? 'Belum ditentukan'

                : $header->JENIS_PAY .
                ' - ' .
                (
                    $header->PEMBAYARAN
                    ?: '-'
                );

        /*
        |--------------------------------------------------------------------------
        | DETAIL SALES
        |--------------------------------------------------------------------------
        */

        $detail =
            $this->db

                ->select('

                    d.SEQ_NO,

                    d.MATERIAL,

                    m.MATERIAL_NAME,

                    d.CALC_BASIS,

                    d.JUMLAH,

                    d.BERAT,

                    d.HARGA,

                    d.TOTAL

                ', false)

                ->from(
                    'abc_mst_sales_detail d'
                )

                ->join(
                    'abc_cd_material m',
                    'm.MATERIAL = d.MATERIAL',
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

                ->where(
                    'd.DELETED IS NULL',
                    null,
                    false
                )

                ->order_by(
                    'd.SEQ_NO',
                    'ASC'
                )

                ->get()

                ->result();

        /*
        |--------------------------------------------------------------------------
        | SAVING
        |--------------------------------------------------------------------------
        */

        $saving =
            $this->db

                ->select('

                    SV_NO,

                    AMOUNT,

                    BASIS,

                    RATE,

                    TOTAL_QTY,

                    TOTAL_WEIGHT,

                    REMAIN,

                    REMARK

                ', false)

                ->from(
                    'abc_mst_saving'
                )

                ->where(
                    'SALES',
                    $sales
                )

                ->where(
                    'PLANT',
                    $plant
                )

                ->where(
                    'RELATED',
                    'SALES'
                )

                ->where(
                    'DELETED IS NULL',
                    null,
                    false
                )

                ->order_by(
                    'SV_NO',
                    'ASC'
                )

                ->get()

                ->result();

        /*
        |--------------------------------------------------------------------------
        | DETAIL SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalQty =
            0;

        $totalBerat =
            0;

        $baseSales =
            0;

        foreach (
            $detail
            as $row
        ) {

            $totalQty +=
                (float) (
                    $row->JUMLAH
                    ?? 0
                );

            $totalBerat +=
                (float) (
                    $row->BERAT
                    ?? 0
                );

            $baseSales +=
                (float) (
                    $row->TOTAL
                    ?? 0
                );
        }

        /*
        |--------------------------------------------------------------------------
        | FINANCIAL COMPONENT
        |--------------------------------------------------------------------------
        */

        $biaya =
            (float) (
                $header->BIAYA
                ?? 0
            );

        $discount =
            (float) (
                $header->DISCOUNT
                ?? 0
            );

        $rounding =
            (float) (
                $header->ROUNDING
                ?? 0
            );

        /*
        |--------------------------------------------------------------------------
        | TOTAL SALES
        |--------------------------------------------------------------------------
        */

        $salesAmount =
            round(
                $baseSales
                +
                $biaya
                -
                $discount
                +
                $rounding,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | TOTAL SAVING
        |--------------------------------------------------------------------------
        */

        $totalSaving =
            0;

        $savingRemain =
            0;

        foreach (
            $saving
            as $sv
        ) {

            $totalSaving +=
                (float) (
                    $sv->AMOUNT
                    ?? 0
                );

            $savingRemain +=
                (float) (
                    $sv->REMAIN
                    ?? 0
                );
        }

        $totalSaving =
            round(
                $totalSaving,
                2
            );

        $savingRemain =
            round(
                $savingRemain,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        |
        | Sales + Saving
        |
        */

        $grandTotal =
            round(
                $salesAmount
                +
                $totalSaving,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING
        |--------------------------------------------------------------------------
        |
        | Sales REMAIN
        | +
        | Saving REMAIN
        |
        */

        $outstandingSales =
            round(
                (float) (
                    $header->REMAIN
                    ?? 0
                ),
                2
            );

        $totalOutstanding =
            round(
                $outstandingSales
                +
                $savingRemain,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = [

            'total_qty' =>
                $totalQty,

            'total_berat' =>
                $totalBerat,

            'base_sales' =>
                $baseSales,

            'biaya' =>
                $biaya,

            'discount' =>
                $discount,

            'rounding' =>
                $rounding,

            'sales_amount' =>
                $salesAmount,

            'total_saving' =>
                $totalSaving,

            'grand_total' =>
                $grandTotal,

            'outstanding_sales' =>
                $outstandingSales,

            'outstanding_saving' =>
                $savingRemain,

            'total_outstanding' =>
                $totalOutstanding
        ];

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $data = [

            'header' =>
                $header,

            'detail' =>
                $detail,

            'saving' =>
                $saving,

            'summary' =>
                $summary
        ];

        /*
        |--------------------------------------------------------------------------
        | HTML
        |--------------------------------------------------------------------------
        */

        $html =
            $this->load->view(
                'admin/sales/pdf_invoice_template',
                $data,
                true
            );

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $this->load->library(
            'pdf'
        );

        $this->pdf->loadHtml(
            $html
        );

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
            "INVOICE_{$sales}.pdf",
            [
                'Attachment' => false
            ]
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
