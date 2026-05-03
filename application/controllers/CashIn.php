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

    /**
     * Load data for table (ajax)
     */
    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $allowedOrder = ['CASH_IN','CASHIN_DATE','CUSTOMER','AMOUNT','SLIP_NO','PLANT'];

        $orderInput = $this->input->get('order', TRUE);
        $order = in_array($orderInput, $allowedOrder) ? $orderInput : 'CASHIN_DATE';

        $dirInput = strtoupper($this->input->get('dir', TRUE));
        $dir = ($dirInput === 'ASC') ? 'ASC' : 'DESC';

        $start = ($page - 1) * $limit;

        $rows  = $this->CashIn_model->get_data($limit, $start, $search, $order, $dir);
        $total = $this->CashIn_model->count_data($search);

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

    public function get_user_plant_select2()
    {
        $username = $this->session->userdata('username');
        $data = $this->CashIn_model->get_plant_select2_by_user($username);
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
        Ambil langsung dari mst_sales TANPA hitung ulang pembayaran
        REMAIN diambil dari kolom mst_sales.REMAIN (PALING AKURAT)
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
            FROM mst_sales
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
            'mst_sales_detail d',
            'd.SALES = s.SALES AND d.PLANT = s.PLANT AND d.DELETED IS NULL',
            'left'
        );

        $this->db->join('cd_code cc', "cc.HEAD_CODE = 'AJ' AND cc.CODE = s.PLANT", 'left');
        $this->db->join('cd_customer cust', 'cust.CUST = s.CUSTOMER', 'left');

        // Hanya invoice yang masih ada sisa
        $this->db->where('s.REMAIN >', 0);

        $this->db->order_by('s.SALES_DATE', 'ASC');
        $this->db->order_by('s.SALES', 'ASC');

        echo json_encode($this->db->get()->result_array());
    }

    public function get_customer()
    {
        $term = $this->input->get('q');
        $data = $this->CashIn_model->search_customer($term);
        echo json_encode($data);
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
        header('Content-Type: application/json');

        try {

            $data     = $this->input->post(NULL, TRUE);
            $username = $this->session->userdata('username');

            $plant    = $data['PLANT'] ?? null;
            $customer = $data['CUSTOMER'] ?? null;
            $amount   = $this->toNumber($data['JUMLAH'] ?? 0);
            $mode     = $data['mode_cash_in'] ?? 'FIFO'; // 🔥 MODE DARI VIEW

            if (!$plant || !$customer) {
                echo json_encode(['status'=>false,'message'=>'Customer dan Plant wajib diisi']);
                return;
            }

            if ($mode === 'FIFO' && $amount <= 0) {
                echo json_encode(['status'=>false,'message'=>'Jumlah Cash In harus lebih dari 0']);
                return;
            }

            if ($mode === 'MANUAL') {
                $amount = 0; // akan dioverride nanti oleh totalOffset
            }

            $this->db->trans_start();

            /* ===================== 1. GENERATE NUMBER ===================== */
            $cashInNo = $this->CashIn_model->generate_cash_in_no($plant, $data['CASHIN_DATE']);
            $slipNo   = $this->CashIn_model->generate_slip_no($plant);

            /* ===================== 2. INSERT HEADER ===================== */
            $this->CashIn_model->insert_header([
                'CASH_IN'     => $cashInNo,
                'PLANT'       => $plant,
                'CUSTOMER'    => $customer,
                'CASHIN_DATE' => $data['CASHIN_DATE'],
                'PEMBAYARAN'  => $data['PEMBAYARAN'] ?? null,
                'NO_REK'      => $data['NO_REK'] ?? null,
                'BON'         => $data['BON'] ?? null,
                'AMOUNT'      => $amount,
                'SLIP_NO'     => $slipNo,
                'CREATED_AT'  => date('Y-m-d H:i:s'),
                'CREATED_BY'  => $username
            ]);

            $amountLeft    = $amount;
            $seq           = 1;
            $affectedSales = [];

            if ($mode === 'MANUAL') {

                $detailsInput = $this->input->post('DETAIL');

                if (!is_array($detailsInput) || count($detailsInput) == 0) {
                    throw new Exception('Detail invoice manual kosong');
                }

                $totalOffset = 0;

                foreach ($detailsInput as $row) {

                    $sales  = $row['SALES'] ?? null;
                    $offset = (float)($row['AMOUNT_OFFSET'] ?? 0);

                    if (!$sales || $offset <= 0) continue;

                    // 🔒 LOCK INVOICE
                    $this->CashIn_model->lock_invoice_row($sales, $plant);

                    // 🔁 AMBIL REMAIN TERBARU SETELAH LOCK
                    $latest = $this->CashIn_model->get_invoice_remain($sales, $plant);
                    $remain = (float)$latest['remain'];

                    if ($remain <= 0) continue; // invoice sudah lunas oleh transaksi lain

                    if ($offset > $remain) {
                        $offset = $remain; // cegah overpay
                    }

                    $this->db->insert('mst_cash_in_detail', [
                        'CASH_IN'        => $cashInNo,
                        'PLANT'          => $plant,
                        'SEQ_NO'         => $seq++,
                        'SALES'          => $sales,
                        'AMOUNT_INVOICE' => $latest['invoice_amount'],
                        'AMOUNT_OFFSET'  => $offset,
                        'DATE_OFFSET'    => $data['CASHIN_DATE'],
                        'ORG_SLIP_NO'    => 'MANUAL',
                        'SLIP_NO'        => 'MANUAL',
                        'CREATED_AT'     => date('Y-m-d H:i:s'),
                        'CREATED_BY'     => $username
                    ]);

                    $this->CashIn_model->reduce_invoice_remain($sales, $plant, $offset);

                    $totalOffset += $offset;
                    $affectedSales[] = $sales;
                }

                if ($totalOffset <= 0) {
                    throw new Exception('Semua invoice sudah lunas / tidak valid');
                }

                // 🔥 HEADER = REAL UANG TERPAKAI
                $this->CashIn_model->update_header_amount($cashInNo, $plant, $totalOffset);

                $amountLeft = 0; // manual tidak pakai deposit
            } else {

                $detailsInput = $this->input->post('DETAIL');

                if (!is_array($detailsInput) || count($detailsInput) == 0) {
                    throw new Exception('Tidak ada invoice FIFO yang dikirim dari preview');
                }

                foreach ($detailsInput as $row) {

                    $sales  = $row['SALES'] ?? null;
                    $offset = (float)($row['AMOUNT_OFFSET'] ?? 0);

                    if (!$sales || $offset <= 0) continue;

                    // 🔒 Lock invoice row (hindari race condition)
                    $this->CashIn_model->lock_invoice_row($sales, $plant);

                    // 🔁 Ambil remain terbaru setelah lock
                    $latest = $this->CashIn_model->get_invoice_remain($sales, $plant);
                    $remain = (float)$latest['remain'];

                    if ($remain <= 0) continue; // invoice sudah lunas oleh transaksi lain

                    // 🚫 Jangan izinkan overpay walau preview salah
                    if ($offset > $remain) {
                        $offset = $remain;
                    }

                    if ($offset <= 0) continue;

                    $this->db->insert('mst_cash_in_detail', [
                        'CASH_IN'        => $cashInNo,
                        'PLANT'          => $plant,
                        'SEQ_NO'         => $seq++,
                        'SALES'          => $sales,
                        'AMOUNT_INVOICE' => $latest['invoice_amount'],
                        'AMOUNT_OFFSET'  => $offset,
                        'DATE_OFFSET'    => $data['CASHIN_DATE'],
                        'ORG_SLIP_NO'    => 'AUTO',
                        'SLIP_NO'        => 'AUTO',
                        'CREATED_AT'     => date('Y-m-d H:i:s'),
                        'CREATED_BY'     => $username
                    ]);

                    $this->CashIn_model->reduce_invoice_remain($sales, $plant, $offset);

                    $amountLeft -= $offset;
                    $affectedSales[] = $sales;
                }
            }

            if (empty($affectedSales)) {
                throw new Exception('Tidak ada invoice yang berhasil dialokasikan');
            }

            /* =======================================================
            5. SISA → DEPOSIT
            ======================================================= */
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

            /* =======================================================
            6. UPDATE STATUS INVOICE
            ======================================================= */
            foreach (array_unique($affectedSales) as $sales) {
                $this->CashIn_model->recalc_invoice($sales, $plant);
            }

            /* =======================================================
            7. UPLOAD ATTACHMENT
            ======================================================= */
            if (!empty($_FILES['ATTACHMENT']['name'])) {

                $uploadDir = FCPATH.'uploads/cash_in/';
                if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

                $config = [
                    'upload_path'   => $uploadDir,
                    'allowed_types' => 'jpg|jpeg|png|pdf',
                    'max_size'      => 2048,
                    'file_name'     => $cashInNo
                ];

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('ATTACHMENT')) {
                    $fileData = $this->upload->data();
                    $this->CashIn_model->update_header($plant,$cashInNo,[
                        'ATTACHMENT' => 'uploads/cash_in/'.$fileData['file_name']
                    ]);
                }
            }

            $this->db->trans_complete();

            echo json_encode([
                'status'  => $this->db->trans_status(),
                'message' => $this->db->trans_status()
                    ? 'Cash In berhasil disimpan'
                    : 'Gagal simpan Cash In'
            ]);

        } catch (Throwable $e) {

            echo json_encode([
                'status'  => false,
                'message' => 'Server error: '.$e->getMessage()
            ]);
        }
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
                ->from('mst_cash_in')
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
            ->from('cd_customer')
            ->where('CUST', $header['CUSTOMER'])
            ->get()->row('FULL_NAME');

        // Nama Plant
        $header['PLANT_NAME'] = $this->db
            ->select('CODE_NAME')
            ->from('cd_code')
            ->where('HEAD_CODE', 'AJ')
            ->where('CODE', $header['PLANT'])
            ->get()->row('CODE_NAME');

        // Nama Rekening
        $header['REK_NAME'] = $this->db
            ->select('CODE_NAME')
            ->from('cd_code')
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


        /* =====================
        STEP 2 — HITUNG ULANG OFFSET BARU (FIFO)
        ===================== */
        $amountLeft = $inputCashIn;
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

            $this->db->insert('mst_cash_in_detail', [
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
                            ->get('mst_cash_in')->row();

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
        $cashIn = $this->input->post('cash_in', TRUE);
        $plant  = $this->input->post('plant', TRUE);

        if ($this->CashIn_model->deposit_has_remain($cashIn)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Cash In tidak bisa dihapus karena deposit sudah digunakan transaksi lain'
            ]);
            return;
        }

        $this->db->trans_start();

        $header = $this->db->where(['CASH_IN'=>$cashIn,'PLANT'=>$plant])
                           ->get('mst_cash_in')->row();

        if ($header && $header->ATTACHMENT && file_exists(FCPATH.$header->ATTACHMENT)) {
            unlink(FCPATH.$header->ATTACHMENT);
        }

        $details = $this->CashIn_model->get_cash_in_details($cashIn,$plant);

        /* KEMBALIKAN REMAIN INVOICE */
        foreach($details as $d){
            $this->CashIn_model->restore_invoice_remain(
                $d['SALES'],
                $d['PLANT'],
                $d['AMOUNT_OFFSET']
            );
        }

        /* HAPUS DATA */
        $this->CashIn_model->delete_deposit_by_cash_in($cashIn);
        $this->CashIn_model->delete_cash_in_details($cashIn,$plant);
        $this->CashIn_model->delete_header($cashIn,$plant);

        /* HITUNG ULANG STATUS */
        foreach($details as $d){
            $this->CashIn_model->recalc_invoice($d['SALES'],$d['PLANT']);
        }

        $this->CashIn_model->delete_deposit_by_cashin($cashIn);
        $this->CashIn_model->delete_cash_in_details($cashIn,$plant);
        $this->CashIn_model->delete_header($cashIn,$plant);

        $this->db->trans_complete();

        echo json_encode(['status'=>$this->db->trans_status()]);
    }

    public function print_pdf()
    {
        $cash_in = $this->input->get('cash_in');
        $plant   = $this->input->get('plant');

        if (!$cash_in || !$plant) {
            show_error('Parameter CASH_IN atau PLANT tidak lengkap');
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                c.CASH_IN,
                c.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                cust.FULL_NAME,
                ak.CODE_NAME AS REK_NAME,
                c.CASHIN_DATE,
                c.CUSTOMER,
                c.PEMBAYARAN,
                c.BON,
                c.SLIP_NO,
                c.NO_REK,
                c.AMOUNT
            ')
            ->from('mst_cash_in c')
            ->join('cd_code aj', "aj.CODE = c.PLANT AND aj.HEAD_CODE = 'AJ'", 'left')
            ->join('cd_code ak', "ak.CODE = c.NO_REK AND ak.HEAD_CODE = 'AK'", 'left')
            ->join('cd_customer cust', "cust.CUST = c.CUSTOMER", 'left')
            ->where('c.CASH_IN', $cash_in)
            ->where('c.PLANT', $plant)
            ->where('c.DELETED IS NULL')
            ->get()
            ->row();

        if (!$header) {
            show_error('Data CASH IN tidak ditemukan');
        }

        /* ================= DETAIL ================= */
        $detail = $this->db
            ->select('
                d.SEQ_NO,
                d.SALES,
                d.ORG_SLIP_NO,
                d.SLIP_NO,
                d.DATE_OFFSET,
                d.AMOUNT_INVOICE,
                d.AMOUNT_OFFSET
            ')
            ->from('mst_cash_in_detail d')
            ->where('d.CASH_IN', $cash_in)
            ->where('d.PLANT', $plant)
            ->where('d.DELETED IS NULL')
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        $data = compact('header', 'detail');

        /* ================= PDF ================= */
        $html = $this->load->view('admin/cash_in/pdf_template', $data, true);

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            "CASH_IN_{$cash_in}.pdf",
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
