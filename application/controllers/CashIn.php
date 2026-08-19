<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CashIn extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('accounting_cash_in')) {
            show_404();
        }
        $this->load->model('CashIn_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Cash In']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/cash_in/list'); // view list
        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $page =
            max(
                1,
                (int) $this->input->get('page')
            );

        $limit =
            max(
                1,
                (int) $this->input->get('limit')
            );

        $search =
            trim(
                $this->input->get('search', true)
            );

        $order =
            $this->input->get(
                'order',
                true
            ) ?: 'CASHIN_DATE';

        $dir =
            strtoupper(
                $this->input->get(
                    'dir',
                    true
                )
            ) === 'DESC'
                ? 'DESC'
                : 'ASC';

        $start =
            ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $pembayaran =
            $this->input->get(
                'pembayaran',
                true
            );

        $date_from =
            $this->input->get(
                'date_from',
                true
            );

        $date_to =
            $this->input->get(
                'date_to',
                true
            );

        $rows =
            $this->CashIn_model
                ->get_data(
                    $limit,
                    $start,
                    $search,
                    $order,
                    $dir,
                    $pembayaran,
                    $date_from,
                    $date_to
                );

        $total =
            $this->CashIn_model
                ->count_data(
                    $search,
                    $pembayaran,
                    $date_from,
                    $date_to
                );

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $pages =
            $total > 0
                ? ceil($total / $limit)
                : 1;

        echo json_encode([

            'status'    => true,

            'rows'      => $rows,

            'total'     => (int) $total,

            'page'      => (int) $page,

            'pages'     => (int) $pages,

            'pagination'=> $this->build_pagination(
                $pages,
                $page
            )

        ]);
    }

    public function load_sales_picker()
    {
        $plant = $this->input->get('plant', true);

        $customer = $this->input->get('customer', true);

        $search = $this->input->get('search', true);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if(empty($plant)){

            echo json_encode([]);

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->CashIn_model
            ->get_sales_picker(
                $plant,
                $customer,
                $search
            );

        echo json_encode($rows);
    }

    private function build_pagination($pages, $current)
    {
        $html = '<ul class="pagination pagination-sm">';
        for ($i=1; $i <= $pages; $i++) {
            $active = ($i == $current) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'">
                        <a href="javascript:void(0)" class="page-link" onclick="loadPage('.$i.')">'.$i.'</a>
                     </li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function get_all_item()
    {
        $plant_id = $this->session->userdata('plant');

        if (!$plant_id) {
            echo json_encode([]);
            return;
        }

        // ambil item + stock langsung
        $items = $this->CashIn_model->get_stock_actual_by_plant($plant_id);
        echo json_encode($items);
    }

    public function get_plant_select2()
    {
        $data = $this->CashIn_model->get_plant_select2();
        echo json_encode($data);
    }

    public function get_invoice_tempo()
    {
        $username = $this->session->userdata('username');
        $customer = $this->input->get('customer', TRUE);
        $plant    = $this->input->get('plant', TRUE);

        if (!$customer || !$plant) {
            echo json_encode([]);
            return;
        }

        // Ambil kode customer saja (hilangkan " - NAMA")
        $customer = trim(explode('-', $customer)[0]);

        // Validasi plant milik user
        $plants = array_map('strval', $this->CashIn_model->get_user_plants($username));
        if (!in_array((string)$plant, $plants, true)) {
            echo json_encode([]);
            return;
        }

        /*
        ==========================================================
        HEADER INVOICE
        Ambil langsung dari abc_mst_sales TANPA hitung ulang pembayaran
        REMAIN diambil dari kolom abc_mst_sales.REMAIN (PALING AKURAT)
        ==========================================================
        */
        $headerSub = "
            SELECT 
                SALES,
                PLANT,
                MAX(SALES_DATE) AS SALES_DATE,
                MAX(SLIP_NO) AS SLIP_NO,
                MAX(CUSTOMER) AS CUSTOMER,
                MAX(JENIS_PAY) AS JENIS_PAY,
                SUM(AMOUNT) AS AMOUNT,
                MAX(REMAIN) AS REMAIN
            FROM abc_mst_sales
            WHERE JENIS_PAY = 'TEMPO'
            AND CUSTOMER = ".$this->db->escape($customer)."
            AND PLANT = ".$this->db->escape($plant)."
            GROUP BY SALES, PLANT
        ";

        $this->db->select("
            s.SALES,
            s.SALES_DATE,
            s.SLIP_NO,
            s.CUSTOMER,
            cust.FULL_NAME AS CUSTOMER_NAME,
            s.PLANT,
            cc.CODE_NAME AS PLANT_NAME,
            s.JENIS_PAY,
            s.AMOUNT,
            s.REMAIN,

            d.BERAT,
            d.QTY,
            d.HARGA,
            d.DISCOUNT,
            d.AMOUNT AS DETAIL_AMOUNT
        ", false);

        $this->db->from("($headerSub) s");

        // Detail barang
        $this->db->join(
            'abc_mst_sales_detail d',
            'd.SALES = s.SALES AND d.PLANT = s.PLANT AND d.DELETED IS NULL',
            'left'
        );

        $this->db->join('abc_cd_code cc', "cc.HEAD_CODE = 'AJ' AND cc.CODE = s.PLANT", 'left');
        $this->db->join('abc_cd_customer cust', 'cust.CUST = s.CUSTOMER', 'left');

        // Hanya invoice yang masih ada sisa
        $this->db->where('s.REMAIN >', 0);

        $this->db->order_by('s.SALES_DATE', 'ASC');
        $this->db->order_by('s.SALES', 'ASC');

        echo json_encode($this->db->get()->result_array());
    }

    public function get_customer()
    {
        $term = trim($this->input->get('q') ?? '');

        if (strlen($term) < 3) {
            echo json_encode([]);
            return;
        }

        $data = $this->CashIn_model->search_customer($term);
        echo json_encode($data);
    }

    public function get_customer_saving()
    {
        $customer = $this->input->get('customer', true);
        $plant    = $this->input->get('plant', true);

        if (!$customer || !$plant) {
            echo json_encode(['amount' => 0]);
            return;
        }

        $amount = $this->CashIn_model->get_customer_saving_debt($customer, $plant);

        echo json_encode(['amount' => (float) $amount]);
    }

    public function get_rekening()
    {
        $term = $this->input->get('q');
        $data = $this->CashIn_model->search_rekening($term);
        echo json_encode($data);
    }

    private function toNumber($value)
    {
        if ($value === null) return 0;
        return (float) str_replace(['.', ','], ['', '.'], $value);
    }

    public function get_customer_deposit()
    {
        $customer = $this->input->get('customer', true);
        $plant    = $this->input->get('plant', true);

        if (!$customer || !$plant) {
            echo json_encode(['amount' => 0]);
            return;
        }

        $amount = $this->CashIn_model->get_total_deposit($customer, $plant);

        echo json_encode(['amount' => (float)$amount]);
    }

    public function preview_fifo()
    {
        $customer = $this->input->post('customer', true);
        $plant    = $this->input->post('plant', true);
        $amount   = (float)$this->input->post('amount');

        if (!$customer || !$plant || $amount <= 0) {
            echo json_encode([
                'status'  => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        $username = $this->session->userdata('username');
        $plants   = $this->CashIn_model->get_user_plants($username);

        // 🔥 FIX TYPE MISMATCH
        $plant  = (string)$plant;
        $plants = array_map('strval', $plants);

        if (!in_array($plant, $plants, true)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Plant tidak valid'
            ]);
            return;
        }

        $result = $this->CashIn_model->simulate_fifo($customer, $plant, $amount);

        echo json_encode([
            'status' => true,
            'data'   => $result
        ]);
    }

    public function get_invoice_fifo_source()
    {
        $customer = $this->input->get('customer', true);
        $plant    = $this->input->get('plant', true);

        if (!$customer || !$plant) {
            echo json_encode([]);
            return;
        }

        // bersihkan "004 - HERI"
        $customer = trim(explode('-', $customer)[0]);

        // validasi plant user
        $username = $this->session->userdata('username');
        $plants   = $this->CashIn_model->get_user_plants($username);

        $plant  = (string)$plant;
        $plants = array_map('strval', $plants);

        if (!in_array($plant, $plants, true)) {
            echo json_encode([]);
            return;
        }

        $data = $this->CashIn_model->get_all_open_tempo_invoices($customer, $plant);

        echo json_encode($data);
    }

    public function validate_invoice_remain()
    {
        $sales = $this->input->get('sales', true);
        $plant = $this->input->get('plant', true);

        if (!$sales || !$plant) {
            echo json_encode(['status'=>false,'message'=>'Invalid parameter']);
            return;
        }

        $invoice = $this->CashIn_model->get_invoice_remain($sales, $plant);

        if (!$invoice) {
            echo json_encode(['status'=>false,'message'=>'Invoice tidak ditemukan']);
            return;
        }

        if ((float)$invoice['remain'] <= 0) {
            echo json_encode([
                'status' => false,
                'message' => 'Invoice ini sudah lunas'
            ]);
            return;
        }

        echo json_encode([
            'status' => true,
            'remain' => (float)$invoice['remain'],
            'amount' => (float)$invoice['invoice_amount']
        ]);
    }

    public function create()
    {
        $post = $this->input->post(NULL, TRUE);

        $user = $this->session->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | BASIC PARAMETER
        |--------------------------------------------------------------------------
        */

        $plant = trim(
            $post['PLANT'] ?? ''
        );

        $customer = trim(
            $post['CUSTOMER'] ?? ''
        );

        $date = trim(
            $post['CASHIN_DATE'] ?? ''
        );

        $payment = trim(
            $post['PEMBAYARAN'] ?? ''
        );

        $remark = trim(
            $post['REMARK'] ?? ''
        );

        $mode = strtoupper(
            trim(
                $post['MODE_CASH_IN'] ?? 'FIFO'
            )
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($plant === '') {
            echo json_encode([
                'status'  => false,
                'message' => 'Plant wajib diisi'
            ]);
            return;
        }

        if ($customer === '') {
            echo json_encode([
                'status'  => false,
                'message' => 'Customer wajib dipilih'
            ]);
            return;
        }

        if ($date === '') {
            echo json_encode([
                'status'  => false,
                'message' => 'Tanggal wajib diisi'
            ]);
            return;
        }

        if ($payment === '') {
            echo json_encode([
                'status'  => false,
                'message' => 'Tipe pembayaran wajib dipilih'
            ]);
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL CASH IN
        |--------------------------------------------------------------------------
        */

        $totalInput = $this->normalize_number(
            $post['TOTAL_INPUT'] ?? 0
        );

        $totalInput = round(
            (float)$totalInput,
            2
        );

        if ($totalInput <= 0) {

            echo json_encode([
                'status'  => false,
                'message' => 'Total Cash In harus lebih dari 0'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $details =
            $post['DETAIL'] ?? [];

        if (!is_array($details)) {
            $details = [];
        }

        $normalizedDetails = [];

        foreach ($details as $d) {

            $salesNo = trim(
                $d['SALES'] ?? ''
            );

            if ($salesNo === '') {
                continue;
            }

            $bayar = $this->normalize_number(
                $d['BAYAR'] ?? 0
            );

            $bayar = round(
                (float)$bayar,
                2
            );

            if ($bayar <= 0) {
                continue;
            }

            $normalizedDetails[] = [
                'SALES'  => $salesNo,
                'BAYAR'  => $bayar,
                'REMARK' => trim($d['REMARK'] ?? '')
            ];
        }

        if (empty($normalizedDetails)) {

            echo json_encode([
                'status'  => false,
                'message' => 'Belum ada invoice yang dibayar'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL DETAIL PAYMENT
        |--------------------------------------------------------------------------
        */

        $requestedTotal = 0;

        foreach ($normalizedDetails as $detail) {

            $requestedTotal +=
                $detail['BAYAR'];
        }

        $requestedTotal = round(
            $requestedTotal,
            2
        );

        if ($requestedTotal > $totalInput) {

            echo json_encode([
                'status'  => false,
                'message' =>
                    'Total pembayaran invoice '
                    . number_format(
                        $requestedTotal,
                        0,
                        ',',
                        '.'
                    )
                    . ' melebihi Cash In '
                    . number_format(
                        $totalInput,
                        0,
                        ',',
                        '.'
                    )
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE NUMBER
        |--------------------------------------------------------------------------
        */

        $cashInNo =
            $this->CashIn_model
                ->generate_cash_in_number(
                    $plant
                );

        $slipNo =
            $this->CashIn_model
                ->generate_slip_number(
                    $plant
                );

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_begin();

        try {

            /*
            |--------------------------------------------------------------------------
            | INSERT HEADER
            |--------------------------------------------------------------------------
            */

            $this->db->insert(
                'abc_mst_cash_in',
                [

                    'CASH_IN' =>
                        $cashInNo,

                    'PLANT' =>
                        $plant,

                    'CASHIN_DATE' =>
                        $date,

                    'CUSTOMER' =>
                        $customer,

                    'SLIP_NO' =>
                        $slipNo,

                    'PEMBAYARAN' =>
                        $payment,

                    'AMOUNT' =>
                        $totalInput,

                    'STATUS' =>
                        'OPEN',

                    'REMARK' =>
                        $remark,

                    'CREATED_AT' =>
                        date(
                            'Y-m-d H:i:s'
                        ),

                    'CREATED_BY' =>
                        $user

                ]
            );

            if (
                $this->db->affected_rows() <= 0
            ) {

                throw new Exception(
                    'Gagal membuat header Cash In'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CASH AVAILABLE
            |--------------------------------------------------------------------------
            */

            $cashRemaining =
                $totalInput;

            $totalSavingAllocated = 0;

            $totalSalesAllocated = 0;

            $seq = 1;

            /*
            |--------------------------------------------------------------------------
            | LOOP INVOICE
            |--------------------------------------------------------------------------
            */

            foreach ($normalizedDetails as $detail) {

                if ($cashRemaining <= 0) {
                    break;
                }

                $salesNo =
                    $detail['SALES'];

                /*
                ========================================================
                | LOCK SALES
                ========================================================
                */

                $this->CashIn_model
                    ->lock_sales_row(
                        $salesNo,
                        $plant
                    );

                /*
                ========================================================
                | GET CURRENT SALES
                ========================================================
                */

                $sales =
                    $this->CashIn_model
                        ->get_sales_by_number(
                            $salesNo,
                            $plant
                        );

                if (!$sales) {

                    throw new Exception(
                        'Sales tidak ditemukan: '
                        . $salesNo
                    );
                }

                /*
                ========================================================
                | VALIDATE CUSTOMER
                ========================================================
                */

                if (
                    (string)$sales['CUSTOMER']
                    !==
                    (string)$customer
                ) {

                    throw new Exception(
                        'Sales '
                        . $salesNo
                        . ' bukan milik customer '
                        . $customer
                    );
                }

                /*
                ========================================================
                | GET CURRENT SALES REMAIN
                ========================================================
                */

                $salesRemain = round(
                    max(
                        (float)$sales['REMAIN'],
                        0
                    ),
                    2
                );

                /*
                ========================================================
                | GET CURRENT SAVING REMAIN
                ========================================================
                */

                $savingInfo =
                    $this->CashIn_model
                        ->get_saving_total_by_sales(
                            $salesNo,
                            $plant
                        );

                if (!is_array($savingInfo)) {

                    $savingInfo = [
                        'amount' => 0,
                        'offset' => 0,
                        'remain' => 0,
                        'rows'   => []
                    ];
                }

                $savingRemain =
                    round(
                        max(
                            (float)($savingInfo['remain'] ?? 0),
                            0
                        ),
                        2
                    );

                /*
                ========================================================
                | GRAND OUTSTANDING
                ========================================================
                */

                $grandOutstanding =
                    round(
                        $salesRemain
                        +
                        $savingRemain,
                        2
                    );

                if ($grandOutstanding <= 0) {

                    throw new Exception(
                        'Invoice '
                        . $salesNo
                        . ' sudah lunas'
                    );
                }

                /*
                ========================================================
                | PAYMENT UNTUK INVOICE INI
                ========================================================
                */

                $invoicePayment =
                    min(
                        $detail['BAYAR'],
                        $cashRemaining,
                        $grandOutstanding
                    );

                $invoicePayment =
                    round(
                        $invoicePayment,
                        2
                    );

                if ($invoicePayment <= 0) {
                    continue;
                }

                /*
                ========================================================
                | 1. BAYAR SAVING DULU
                ========================================================
                */

                $savingResult =
                    $this->CashIn_model
                        ->allocate_saving(
                            $cashInNo,
                            $plant,
                            $salesNo,
                            $invoicePayment,
                            $date,
                            $user
                        );

                if (!is_array($savingResult)) {

                    throw new Exception(
                        'Allocation Saving mengembalikan response tidak valid untuk Sales '
                        . $salesNo
                    );
                }

                $savingAllocated =
                    round(
                        (float)($savingResult['allocated'] ?? 0),
                        2
                    );

                /*
                ========================================================
                | SISA PAYMENT → SALES
                ========================================================
                */

                $remainingToSales =
                    round(
                        $invoicePayment
                        -
                        $savingAllocated,
                        2
                    );

                $salesAllocated = 0;

                if ($remainingToSales > 0) {

                    $salesResult =
                        $this->CashIn_model
                            ->allocate_sales(
                                $cashInNo,
                                $plant,
                                $salesNo,
                                $remainingToSales,
                                $date,
                                $slipNo,
                                $user,
                                $seq
                            );

                    if (!is_array($salesResult)) {

                        throw new Exception(
                            'Allocation Sales mengembalikan response tidak valid untuk Sales '
                            . $salesNo
                        );
                    }

                    $salesAllocated =
                        round(
                            (float)($salesResult['allocated'] ?? 0),
                            2
                        );

                    if ($salesAllocated > 0) {
                        $seq++;
                    }
                }

                /*
                ========================================================
                | ACTUAL ALLOCATION
                ========================================================
                */

                $actualAllocated =
                    round(
                        $savingAllocated
                        +
                        $salesAllocated,
                        2
                    );

                /*
                ========================================================
                | SAFETY CHECK
                ========================================================
                */

                if (
                    $actualAllocated
                    >
                    $invoicePayment
                ) {

                    throw new Exception(
                        'Allocation Cash In melebihi pembayaran invoice '
                        . $salesNo
                    );
                }

                /*
                ========================================================
                | REDUCE CASH
                ========================================================
                */

                $cashRemaining =
                    round(
                        $cashRemaining
                        -
                        $actualAllocated,
                        2
                    );

                if ($cashRemaining < 0) {
                    $cashRemaining = 0;
                }

                $totalSavingAllocated +=
                    $savingAllocated;

                $totalSalesAllocated +=
                    $salesAllocated;
            }

            /*
            |--------------------------------------------------------------------------
            | DEPOSIT
            |--------------------------------------------------------------------------
            */

            $depositAmount =
                round(
                    max(
                        $cashRemaining,
                        0
                    ),
                    2
                );

            if ($depositAmount > 0) {

                $this->CashIn_model
                    ->insert_customer_deposit([
                        'CUSTOMER' =>
                            $customer,

                        'PLANT' =>
                            $plant,

                        'CASH_IN' =>
                            $cashInNo,

                        'AMOUNT' =>
                            $depositAmount,

                        'REMAIN' =>
                            $depositAmount,

                        'CREATED_AT' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'CREATED_BY' =>
                            $user
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CASH IN STATUS
            |--------------------------------------------------------------------------
            */

            $totalAllocated =
                round(
                    $totalSavingAllocated
                    +
                    $totalSalesAllocated,
                    2
                );

            if ($depositAmount > 0) {

                $cashStatus = 'DEPOSIT';

            } elseif (
                $totalAllocated >= $totalInput
            ) {

                $cashStatus = 'PAID';

            } elseif (
                $totalAllocated > 0
            ) {

                $cashStatus = 'PARTIAL';

            } else {

                $cashStatus = 'OPEN';
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE HEADER STATUS
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where(
                    'CASH_IN',
                    $cashInNo
                )
                ->where(
                    'PLANT',
                    $plant
                )
                ->update(
                    'abc_mst_cash_in',
                    [
                        'STATUS' =>
                            $cashStatus
                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | TRANSACTION CHECK
            |--------------------------------------------------------------------------
            */

            if (
                $this->db->trans_status()
                === FALSE
            ) {

                throw new Exception(
                    'Gagal menyimpan Cash In'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            $this->db->trans_commit();

            echo json_encode([
                'status' => true,
                'message' => 'Cash In berhasil disimpan',

                'cash_in' =>
                    $cashInNo,

                'total_cash_in' =>
                    $totalInput,

                'saving_allocated' =>
                    round(
                        $totalSavingAllocated,
                        2
                    ),

                'sales_allocated' =>
                    round(
                        $totalSalesAllocated,
                        2
                    ),

                'deposit' =>
                    $depositAmount,

                'status_cash_in' =>
                    $cashStatus
            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function clean_number($value)
    {
        return (float) str_replace(
            ['.', ','],
            ['', '.'],
            $value
        );
    }

    public function edit()
    {
        $cashInNo     = $this->input->get('cash_in', TRUE);
        $role_id      = $this->session->userdata('role_id');
        $sessionPlant = $this->session->userdata('plant');

        if (!$cashInNo) {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid CASH IN'
            ]);
            return;
        }

        // ================= GET HEADER =================
        if ($role_id == 1) {
            $header = $this->db
                ->from('abc_mst_cash_in')
                ->where('CASH_IN', $cashInNo)
                ->get()
                ->row_array();
        } else {
            $header = $this->CashIn_model->get_header($sessionPlant, $cashInNo);
        }

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan / tidak berhak'
            ]);
            return;
        }

        // ================= TAMBAHAN INFO HEADER =================

        // Nama Customer
        $header['CUSTOMER_NAME'] = $this->db
            ->select('FULL_NAME')
            ->from('abc_cd_customer')
            ->where('CUST', $header['CUSTOMER'])
            ->get()->row('FULL_NAME');

        // Nama Plant
        $header['PLANT_NAME'] = $this->db
            ->select('CODE_NAME')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'AJ')
            ->where('CODE', $header['PLANT'])
            ->get()->row('CODE_NAME');

        // Nama Rekening
        $header['REK_NAME'] = $this->db
            ->select('CODE_NAME')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'AK')
            ->where('CODE', $header['NO_REK'])
            ->get()->row('CODE_NAME');

        // Pastikan amount numerik (TANPA FORMAT)
        $header['AMOUNT'] = (float)$header['AMOUNT'];
        $header['ATTACHMENT'] = $header['ATTACHMENT'] ?? null;

        // 🔑 PLANT FINAL DARI HEADER
        $plant = $header['PLANT'];

        // ================= GET DETAIL =================
        $detail = $this->CashIn_model
            ->get_detail_by_cash_in($plant, $cashInNo);

        // ================= FORMAT DETAIL UNTUK UI =================
        foreach ($detail as &$row) {

            $invoiceNow = $this->CashIn_model->get_invoice_remain($row['SALES'], $row['PLANT']);

            $row['AMOUNT_INVOICE'] = (float)$invoiceNow['invoice_amount'];

            // 💡 Remain saat ini DI DATABASE (bukan invoice-offset lama)
            $row['REMAIN_NOW'] = (float)$invoiceNow['remain'];

            $row['AMOUNT_OFFSET']  = (float)$row['AMOUNT_OFFSET'];
            $row['DATE_OFFSET']    = $row['DATE_OFFSET'];
        }

        echo json_encode([
            'status' => true,
            'header' => $header,
            'detail' => $detail
        ]);
    }

    public function update()
    {
        $data       = $this->input->post(NULL, TRUE);
        $cashInNo   = $data['CASH_IN'];
        $plant      = $data['PLANT'];
        $customer   = $data['CUSTOMER'];
        $username   = $this->session->userdata('username');
        $inputCashIn= $this->toNumber($data['JUMLAH'] ?? 0);

        if ($this->CashIn_model->is_cash_in_locked($cashInNo, $customer, $plant)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Cash In ini tidak dapat diedit karena sudah ada transaksi Cash In yang lebih baru.'
            ]);
            return;
        }

        $inputCashIn = $this->toNumber($data['JUMLAH'] ?? 0);

        if ($inputCashIn <= 0) {
            echo json_encode([
                'status'  => false,
                'message' => 'Jumlah Cash In harus lebih dari 0'
            ]);
            return;
        }

        if (empty($customer) || empty($plant)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Customer dan Plant tidak valid'
            ]);
            return;
        }

        $this->db->trans_start();

        /* =====================
        UPDATE HEADER DULU
        ===================== */
        $this->CashIn_model->update_header($plant,$cashInNo,[
            'CASHIN_DATE'=>$data['CASHIN_DATE'],
            'PEMBAYARAN'=>$data['PEMBAYARAN'],
            'BON'=>$data['BON'],
            'NO_REK'=>$data['NO_REK'],
            'AMOUNT'=>$inputCashIn,
            'UPDATED_AT'=>date('Y-m-d H:i:s'),
            'UPDATED_BY'=>$username
        ]);

        /* =====================
        STEP 1 — ROLLBACK DETAIL LAMA CASH IN INI
        ===================== */
        $oldDetails = $this->CashIn_model->get_cash_in_details($cashInNo, $plant);

        foreach ($oldDetails as $d) {
            $this->CashIn_model->restore_invoice_remain(
                $d['SALES'],
                $d['PLANT'],
                $d['AMOUNT_OFFSET']
            );
        }

        $this->CashIn_model->delete_cash_in_details($cashInNo, $plant);
        $this->CashIn_model->delete_deposit_by_cash_in($cashInNo);
        $this->CashIn_model->delete_saving_payment_by_cash_in($cashInNo, $plant, $username);


        /* =====================
        STEP 2 — SAVING DULU, LALU OFFSET AR (FIFO)
        ===================== */
        $savingDebt =
            $this->CashIn_model
                ->get_customer_saving_debt(
                    $customer,
                    $plant
                );

        $savingPayment =
            min(
                $inputCashIn,
                max($savingDebt, 0)
            );

        if ($savingPayment > 0) {
            $this->CashIn_model->insert_saving_payment(
                $customer,
                $plant,
                $savingPayment,
                $cashInNo,
                $data['CASHIN_DATE'],
                $username
            );
        }

        $amountLeft = $inputCashIn - $savingPayment;
        $seq        = 1;

        $invoices = $this->CashIn_model->get_fifo_open_invoices($customer, $plant);

        foreach ($invoices as $inv) {

            if ($amountLeft <= 0) break;

            // 🔒 LOCK BARIS INVOICE
            $this->CashIn_model->lock_invoice_row($inv['SALES'], $plant);

            $latest = $this->CashIn_model->get_invoice_remain($inv['SALES'], $plant);
            $remain = (float)$latest['remain'];

            if ($remain <= 0) continue;

            $offset = min($remain, $amountLeft);

            $this->db->insert('abc_mst_cash_in_detail', [
                'CASH_IN'        => $cashInNo,
                'PLANT'          => $plant,
                'SEQ_NO'         => $seq++,
                'SALES'          => $inv['SALES'],
                'AMOUNT_INVOICE' => $inv['AMOUNT'],
                'AMOUNT_OFFSET'  => $offset,
                'DATE_OFFSET'    => $data['CASHIN_DATE'],
                'ORG_SLIP_NO'    => 'AUTO',
                'SLIP_NO'        => 'AUTO',
                'CREATED_AT'     => date('Y-m-d H:i:s'),
                'CREATED_BY'     => $username
            ]);

            $this->CashIn_model->reduce_invoice_remain($inv['SALES'], $plant, $offset);

            $amountLeft -= $offset;
        }


        /* =====================
        STEP 3 — SISA JADI DEPOSIT
        ===================== */
        if ($amountLeft > 0) {
            $this->CashIn_model->insert_customer_deposit([
                'CUSTOMER'   => $customer,
                'PLANT'      => $plant,
                'CASH_IN'    => $cashInNo,
                'AMOUNT'     => $amountLeft,
                'REMAIN'     => $amountLeft,
                'CREATED_AT' => date('Y-m-d H:i:s'),
                'CREATED_BY' => $username
            ]);
        }


        /* =====================
        STEP 4 — UPDATE STATUS INVOICE YANG TERKENA
        ===================== */
        $affectedSales = array_unique(array_column($oldDetails, 'SALES'));

        foreach ($affectedSales as $sales) {
            $this->CashIn_model->recalc_invoice($sales, $plant);
        }

        /* =====================
        ATTACHMENT UPDATE
        ===================== */
        if (!empty($_FILES['ATTACHMENT']['name'])) {
            $uploadDir = FCPATH.'uploads/cash_in/';
            if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

            $old = $this->db->select('ATTACHMENT')
                            ->where('CASH_IN',$cashInNo)
                            ->where('PLANT',$plant)
                            ->get('abc_mst_cash_in')->row();

            if($old && $old->ATTACHMENT && file_exists(FCPATH.$old->ATTACHMENT)){
                unlink(FCPATH.$old->ATTACHMENT);
            }

            $config=['upload_path'=>$uploadDir,'allowed_types'=>'jpg|jpeg|png|pdf','max_size'=>2048,'file_name'=>$cashInNo];
            $this->load->library('upload',$config);

            if($this->upload->do_upload('ATTACHMENT')){
                $fileData=$this->upload->data();
                $this->CashIn_model->update_header($plant,$cashInNo,[
                    'ATTACHMENT'=>'uploads/cash_in/'.$fileData['file_name']
                ]);
            }
        }

        $this->db->trans_complete();

        // $this->CashIn_model->recalc_invoice($d['SALES'], $d['PLANT']);

        echo json_encode([
            'status'=>$this->db->trans_status(),
            'message'=>$this->db->trans_status() ? 'Cash In berhasil diperbarui & histori dihitung ulang' : 'Gagal update'
        ]);
    }

    public function remove()
    {
        $cashInNo = trim(
            $this->input->post('cashin', true)
        );

        $plant = trim(
            $this->input->post('plant', true)
        );

        $user = $this->session->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDASI PARAMETER
        |--------------------------------------------------------------------------
        */

        if ($cashInNo === '' || $plant === '') {

            echo json_encode([
                'status'  => false,
                'message' => 'Cash In tidak valid'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GET CASH IN HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db
            ->where('CASH_IN', $cashInNo)
            ->where('PLANT', $plant)
            ->where(
                'DELETED IS NULL',
                null,
                false
            )
            ->get('abc_mst_cash_in')
            ->row_array();

        if (!$header) {

            echo json_encode([
                'status'  => false,
                'message' => 'Cash In tidak ditemukan / sudah dihapus'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GET SALES ALLOCATION
        |--------------------------------------------------------------------------
        */

        $salesDetails = $this->db
            ->where('CASH_IN', $cashInNo)
            ->where('PLANT', $plant)
            ->where(
                'DELETED IS NULL',
                null,
                false
            )
            ->get('abc_mst_cash_in_detail')
            ->result_array();

        /*
        |--------------------------------------------------------------------------
        | GET SAVING ALLOCATION
        |--------------------------------------------------------------------------
        */

        $savingDetails = $this->db
            ->where('CASH_IN', $cashInNo)
            ->where('PLANT', $plant)
            ->where(
                'DELETED IS NULL',
                null,
                false
            )
            ->get('abc_mst_cash_in_saving')
            ->result_array();

        /*
        |--------------------------------------------------------------------------
        | CHECK TRANSACTION LEBIH BARU
        |--------------------------------------------------------------------------
        |
        | Jika invoice yang sama sudah terkena Cash In lebih baru,
        | Cash In lama tidak boleh dihapus karena rollback akan
        | merusak saldo invoice.
        |--------------------------------------------------------------------------
        */

        $affectedSales = [];

        foreach ($salesDetails as $detail) {

            if (!empty($detail['SALES'])) {

                $affectedSales[] =
                    $detail['SALES'];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Tambahkan Sales dari Saving allocation
        |--------------------------------------------------------------------------
        |
        | Bisa terjadi Cash In hanya membayar Saving sehingga
        | cash_in_detail kosong.
        |--------------------------------------------------------------------------
        */

        foreach ($savingDetails as $detail) {

            if (!empty($detail['SALES'])) {

                $affectedSales[] =
                    $detail['SALES'];
            }
        }

        $affectedSales =
            array_values(
                array_unique(
                    $affectedSales
                )
            );

        /*
        |--------------------------------------------------------------------------
        | BLOCK JIKA ADA CASH IN LEBIH BARU
        |--------------------------------------------------------------------------
        */

        if (!empty($affectedSales)) {

            $newerExists = $this->db
                ->select('1', false)
                ->from('abc_mst_cash_in_detail d')
                ->join(
                    'abc_mst_cash_in c',
                    'c.CASH_IN = d.CASH_IN
                    AND c.PLANT = d.PLANT',
                    'inner'
                )
                ->where(
                    'd.PLANT',
                    $plant
                )
                ->where_in(
                    'd.SALES',
                    $affectedSales
                )
                ->where(
                    'd.CASH_IN !=',
                    $cashInNo
                )
                ->where(
                    'c.CREATED_AT >',
                    $header['CREATED_AT']
                )
                ->where(
                    'c.DELETED IS NULL',
                    null,
                    false
                )
                ->limit(1)
                ->count_all_results();

            /*
            |--------------------------------------------------------------------------
            | Saving-only Cash In
            |--------------------------------------------------------------------------
            |
            | Cash In yang hanya membayar Saving tidak punya row
            | di cash_in_detail.
            |
            | Jadi kita juga cek cash_in_saving terhadap Cash In
            | yang lebih baru.
            |--------------------------------------------------------------------------
            */

            $newerSavingExists = $this->db
                ->select('1', false)
                ->from('abc_mst_cash_in_saving cs')
                ->join(
                    'abc_mst_cash_in c',
                    'c.CASH_IN = cs.CASH_IN
                    AND c.PLANT = cs.PLANT',
                    'inner'
                )
                ->where(
                    'cs.PLANT',
                    $plant
                )
                ->where_in(
                    'cs.SALES',
                    $affectedSales
                )
                ->where(
                    'cs.CASH_IN !=',
                    $cashInNo
                )
                ->where(
                    'c.CREATED_AT >',
                    $header['CREATED_AT']
                )
                ->where(
                    'c.DELETED IS NULL',
                    null,
                    false
                )
                ->limit(1)
                ->count_all_results();

            if (
                $newerExists > 0
                ||
                $newerSavingExists > 0
            ) {

                echo json_encode([
                    'status'  => false,
                    'message' =>
                        'Cash In tidak dapat dihapus karena sudah ada Cash In yang lebih baru pada invoice yang sama.'
                ]);

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_begin();

        try {

            /*
            ==========================================================
            | STEP 1
            | ROLLBACK SALES
            ==========================================================
            */

            foreach ($salesDetails as $detail) {

                $salesNo =
                    $detail['SALES'];

                $offset =
                    round(
                        (float)$detail['AMOUNT_OFFSET'],
                        2
                    );

                if (
                    $salesNo === ''
                    ||
                    $offset <= 0
                ) {
                    continue;
                }

                /*
                ------------------------------------------------------
                | LOCK SALES
                ------------------------------------------------------
                */

                $this->CashIn_model
                    ->lock_sales_row(
                        $salesNo,
                        $plant
                    );

                /*
                ------------------------------------------------------
                | GET CURRENT SALES
                ------------------------------------------------------
                */

                $sales = $this->db
                    ->where(
                        'SALES',
                        $salesNo
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
                    ->get(
                        'abc_mst_sales'
                    )
                    ->row_array();

                if (!$sales) {
                    continue;
                }

                /*
                ------------------------------------------------------
                | KEMBALIKAN REMAIN
                ------------------------------------------------------
                */

                $currentRemain =
                    round(
                        (float)$sales['REMAIN'],
                        2
                    );

                $newRemain =
                    round(
                        $currentRemain + $offset,
                        2
                    );

                /*
                ------------------------------------------------------
                | STATUS
                ------------------------------------------------------
                */

                if ($newRemain <= 0) {

                    $status = 'PAID';

                } else {

                    /*
                    * Setelah rollback, invoice kembali memiliki
                    * outstanding.
                    */
                    $status = 'PARTIAL';

                }

                /*
                ------------------------------------------------------
                | UPDATE SALES
                ------------------------------------------------------
                */

                $this->db
                    ->where(
                        'SALES',
                        $salesNo
                    )
                    ->where(
                        'PLANT',
                        $plant
                    )
                    ->update(
                        'abc_mst_sales',
                        [
                            'REMAIN' =>
                                $newRemain,

                            'STATUS' =>
                                $status,

                            'UPDATED_AT' =>
                                date(
                                    'Y-m-d H:i:s'
                                ),

                            'UPDATED_BY' =>
                                $user
                        ]
                    );
            }

            /*
            ==========================================================
            | STEP 2
            | SOFT DELETE SAVING ALLOCATION
            ==========================================================
            */

            $this->db
                ->where(
                    'CASH_IN',
                    $cashInNo
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
                    'abc_mst_cash_in_saving',
                    [
                        'DELETED' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'DELETED_BY' =>
                            $user
                    ]
                );

            /*
            ==========================================================
            | STEP 3
            | RECALCULATE SAVING REMAIN
            ==========================================================
            |
            | Jangan sekadar:
            |
            | REMAIN = REMAIN + OFFSET
            |
            | Lebih aman hitung ulang berdasarkan seluruh allocation
            | Cash In yang masih aktif.
            ==========================================================
            */

            $savingNos = [];

            foreach ($savingDetails as $detail) {

                if (!empty($detail['SV_NO'])) {

                    $savingNos[] =
                        $detail['SV_NO'];
                }
            }

            $savingNos =
                array_values(
                    array_unique(
                        $savingNos
                    )
                );

            foreach ($savingNos as $svNo) {

                /*
                ------------------------------------------------------
                | LOCK SAVING
                ------------------------------------------------------
                */

                $saving = $this->db
                    ->where(
                        'SV_NO',
                        $svNo
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
                    ->get(
                        'abc_mst_saving'
                    )
                    ->row_array();

                if (!$saving) {
                    continue;
                }

                /*
                ------------------------------------------------------
                | HITUNG TOTAL OFFSET AKTIF
                ------------------------------------------------------
                */

                $offsetRow =
                    $this->db
                        ->select(
                            'COALESCE(SUM(AMOUNT_OFFSET),0) AS TOTAL_OFFSET',
                            false
                        )
                        ->from(
                            'abc_mst_cash_in_saving'
                        )
                        ->where(
                            'SV_NO',
                            $svNo
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
                        ->row_array();

                $totalOffset =
                    round(
                        (float)(
                            $offsetRow['TOTAL_OFFSET']
                            ?? 0
                        ),
                        2
                    );

                /*
                ------------------------------------------------------
                | REMAIN BARU
                ------------------------------------------------------
                */

                $amount =
                    round(
                        (float)$saving['AMOUNT'],
                        2
                    );

                $newRemain =
                    round(
                        $amount - $totalOffset,
                        2
                    );

                if ($newRemain < 0) {
                    $newRemain = 0;
                }

                /*
                ------------------------------------------------------
                | STATUS
                ------------------------------------------------------
                */

                if ($newRemain <= 0) {

                    $savingStatus =
                        'PAID';

                } elseif (
                    $newRemain < $amount
                ) {

                    $savingStatus =
                        'PARTIAL';

                } else {

                    $savingStatus =
                        'OPEN';
                }

                /*
                ------------------------------------------------------
                | UPDATE SAVING
                ------------------------------------------------------
                */

                $this->db
                    ->where(
                        'SV_NO',
                        $svNo
                    )
                    ->where(
                        'PLANT',
                        $plant
                    )
                    ->update(
                        'abc_mst_saving',
                        [
                            'REMAIN' =>
                                $newRemain,

                            'STATUS' =>
                                $savingStatus,

                            'UPDATED_AT' =>
                                date(
                                    'Y-m-d H:i:s'
                                ),

                            'UPDATED_BY' =>
                                $user
                        ]
                    );
            }

            /*
            ==========================================================
            | STEP 4
            | SOFT DELETE SALES DETAIL
            ==========================================================
            */

            $this->db
                ->where(
                    'CASH_IN',
                    $cashInNo
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
                    'abc_mst_cash_in_detail',
                    [
                        'DELETED' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'UPDATED_AT' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'UPDATED_BY' =>
                            $user
                    ]
                );

            /*
            ==========================================================
            | STEP 5
            | DELETE / ROLLBACK DEPOSIT
            ==========================================================
            */

            if (
                method_exists(
                    $this->CashIn_model,
                    'delete_deposit_by_cash_in'
                )
            ) {

                $this->CashIn_model
                    ->delete_deposit_by_cash_in(
                        $cashInNo
                    );
            }

            /*
            ==========================================================
            | STEP 6
            | SOFT DELETE CASH IN HEADER
            ==========================================================
            */

            $this->db
                ->where(
                    'CASH_IN',
                    $cashInNo
                )
                ->where(
                    'PLANT',
                    $plant
                )
                ->update(
                    'abc_mst_cash_in',
                    [
                        'DELETED' =>
                            date(
                                'Y-m-d H:i:s'
                            )
                    ]
                );

            /*
            ==========================================================
            | CHECK TRANSACTION
            ==========================================================
            */

            if (
                $this->db->trans_status()
                === FALSE
            ) {

                throw new Exception(
                    'Gagal menghapus Cash In'
                );
            }

            /*
            ==========================================================
            | COMMIT
            ==========================================================
            */

            $this->db->trans_commit();

            echo json_encode([
                'status'  => true,
                'message' => 'Cash In berhasil dihapus'
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
        $cash_in =
            $this->input->get(
                'cash_in',
                true
            );

        $plant =
            $this->input->get(
                'plant',
                true
            );

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if(
            !$cash_in ||
            !$plant
        ){

            show_error(
                'Parameter CASH IN atau PLANT tidak lengkap'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db

            ->select('

                c.CASH_IN,

                c.PLANT,

                plant.CODE_NAME AS PLANT_NAME,

                cust.FULL_NAME,

                c.CUSTOMER,

                c.CASHIN_DATE,

                c.PEMBAYARAN,

                c.BON,

                c.SLIP_NO,

                c.NO_REK,

                c.AMOUNT,

                c.REMARK

            ')

            ->from('abc_mst_cash_in c')

            /*
            |--------------------------------------------------------------------------
            | PLANT
            |--------------------------------------------------------------------------
            */

            ->join(
                'abc_cd_code plant',
                "
                    plant.CODE = c.PLANT
                    AND plant.HEAD_CODE = 'PLANT'
                ",
                'left'
            )

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            ->join(
                'abc_cd_customer cust',
                "
                    cust.CUST = c.CUSTOMER
                ",
                'left'
            )

            /*
            |--------------------------------------------------------------------------
            | FILTER
            |--------------------------------------------------------------------------
            */

            ->where(
                'c.CASH_IN',
                $cash_in
            )

            ->where(
                'c.PLANT',
                $plant
            )

            ->where(
                'c.DELETED IS NULL',
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

        if(!$header){

            show_error(
                'Data cash in tidak ditemukan'
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

                d.SALES,

                d.AMOUNT_INVOICE,

                d.AMOUNT_OFFSET,

                (
                    d.AMOUNT_INVOICE
                    -
                    d.AMOUNT_OFFSET
                ) AS REMAINING

            ')

            ->from('abc_mst_cash_in_detail d')

            ->where(
                'd.CASH_IN',
                $cash_in
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
        | DATA
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

            'admin/cash_in/pdf_template',

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

            "CASH_IN_{$cash_in}.pdf",

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
