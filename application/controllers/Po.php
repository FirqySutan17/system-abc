<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Po extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('inventory_po')) {
            show_404();
        }
        $this->load->model('Po_model');
        $this->load->library('session');
        $this->load->helper(['url','file','download']);

        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Purchase Order']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/po/list');   // dynamic content
        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $allowedOrder = [
            'PO',
            'PO_DATE',
            'SUPPLIER',
            'MATERIAL',
            'CREATED_AT'
        ];

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

        $status = trim(
            $this->input->get('status', true)
        );

        $dateFrom = trim(
            $this->input->get('date_from', true)
        );

        $dateTo = trim(
            $this->input->get('date_to', true)
        );

        $orderInput = strtoupper(
            $this->input->get('order', true)
        );

        $order = in_array(
            $orderInput,
            $allowedOrder
        )
            ? $orderInput
            : 'PO_DATE';

        $dir = strtoupper(
            $this->input->get('dir', true)
        ) === 'ASC'
            ? 'ASC'
            : 'DESC';

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->Po_model->get_data(
            $limit,
            $start,
            $search,
            $order,
            $dir,
            $status,
            $dateFrom,
            $dateTo
        );

        echo "<pre>";
        print_r($rows);
        die();

        $total = $this->Po_model->count_data(
            $search,
            $status,
            $dateFrom,
            $dateTo
        );

        $pages = $total > 0
            ? ceil($total / $limit)
            : 1;

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

    public function detail()
    {
        $po = $this->input->get('po', TRUE);
        if (!$po) {
            echo json_encode(['status'=>false,'message'=>'Invalid PO']);
            return;
        }

        $header = $this->Po_model->get_header($po);
        $detail = $this->Po_model->get_detail($po);

        echo json_encode([
            'status'  => true,
            'header'  => $header,
            'detail'  => $detail
        ]);
    }

    public function get_supplier()
    {
        $term = $this->input->get('q');

        $this->db->select('CUST, FULL_NAME')
                ->from('abc_cd_customer')
                ->where('CUST_KIND', 'SUPPLIER')
                ->where('CUST_CLASS', 'SUPPLIER')
                ->where('STATUS', 'Y');

        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('CUST', $term);
            $this->db->or_like('FULL_NAME', $term);
            $this->db->group_end();
        }

        $this->db->order_by('CUST', 'ASC');
        $query = $this->db->get()->result();

        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'id'   => $row->CUST,
                'text' => $row->CUST . ' - ' . $row->FULL_NAME
            ];
        }

        echo json_encode($data);
    }

    public function get_supplier_by_id()
    {
        $cust = $this->input->get('cust');

        $row = $this->db->select('CUST, FULL_NAME')
            ->from('cd_customer')
            ->where('CUST', $cust)
            ->where('CUST_KIND', 'SUPPLIER')
            ->where('CUST_CLASS', 'SUPPLIER')
            ->where('STATUS', 'N')
            ->get()
            ->row();

        if ($row) {
            echo json_encode([
                'id'   => $row->CUST,
                'text' => $row->CUST . ' - ' . $row->FULL_NAME
            ]);
        }
    }

    public function get_customer()
    {
        $term = $this->input->get('q');

        $this->db->select('CUST, FULL_NAME')
            ->from('abc_cd_customer')
            ->where('CUST_KIND', 'CUSTOMER')
            ->where('CUST_CLASS', 'CUSTOMER')
            ->where('STATUS', 'Y');

        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('CUST', $term);
            $this->db->or_like('FULL_NAME', $term);
            $this->db->group_end();
        }

        $this->db->order_by('CUST', 'ASC');

        $rows = $this->db->get()->result();

        $data = [];

        foreach ($rows as $row) {
            $data[] = [
                'id'   => $row->CUST,
                'text' => $row->CUST . ' - ' . $row->FULL_NAME
            ];
        }

        echo json_encode($data);
    }

    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');

        $data = $this->Po_model->get_plant_select2_by_user($username);

        echo json_encode($data);
    }

    public function get_plant()
    {
        $data = $this->Po_model->get_plant_select2();
        echo json_encode($data);
    }

    public function get_material()
    {
        $term = $this->input->get('q');

        $this->db->select('material, material_name')
                ->from('abc_cd_material');

        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('material', $term);
            $this->db->or_like('material_name', $term);
            $this->db->group_end();
        }

        $this->db->order_by('material', 'ASC');
        $query = $this->db->get()->result();

        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'id'   => $row->material,
                'text' => $row->material . ' - ' . $row->material_name
            ];
        }

        echo json_encode($data);
    }

    public function get_po_type()
    {
        $term = $this->input->get('q');

        $this->db->select('CODE, CODE_NAME')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'PO')
            ->where('CODE <>', '*')
            ->where('USE_YN', 'Y');

        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('CODE', $term);
            $this->db->or_like('CODE_NAME', $term);
            $this->db->group_end();
        }

        $this->db->order_by('HEAD_CODE', 'ASC');

        $rows = $this->db->get()->result();

        $data = [];

        foreach ($rows as $row) {
            $data[] = [
                'id'   => $row->CODE,
                'text' => $row->CODE_NAME
            ];
        }

        echo json_encode($data);
    }

    // ================= CREATE =================
    public function create()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try {

            // ================= HEADER =================
            $plant     = trim($this->input->post('PLANT', true));
            $type      = trim($this->input->post('TYPE', true));
            $material  = trim($this->input->post('MATERIAL', true));

            $po_date   = trim($this->input->post('PO_DATE', true));
            $supplier  = trim($this->input->post('SUPPLIER', true));

            $jumlah    = $this->decimal(
                $this->input->post('JUMLAH')
            );

            $berat     = $this->decimal(
                $this->input->post('BERAT')
            );

            $harga     = $this->decimal(
                $this->input->post('HARGA')
            );

            /*
            |--------------------------------------------------------------------------
            | ACTUAL
            |--------------------------------------------------------------------------
            */

            $matiQty = $this->decimal(
                $this->input->post('MATI_QTY')
            );

            $actualHasilTimbang = $this->decimal(
                $this->input->post('ACTUAL_HASIL_TIMBANG')
            );

            $claimQty = $this->decimal(
                $this->input->post('CLAIM_QTY')
            );

            /*
            |--------------------------------------------------------------------------
            | TOTAL (SERVER SIDE)
            |--------------------------------------------------------------------------
            */

            $actual = $this->calculateActual(
                $jumlah,
                $berat,
                $matiQty,
                $actualHasilTimbang,
                $claimQty
            );

            $total = round(
                $actual['TOTAL_BAYAR_BW'] * $harga,
                2
            );

            $no_truck = trim(
                $this->input->post('NO_TRUCK', true)
            );

            $driver = trim(
                $this->input->post('DRIVER', true)
            );

            $remark = trim(
                $this->input->post('REMARK', true)
            );

            $detail = $this->input->post('DETAIL');

            /*
            |--------------------------------------------------------------------------
            | VALIDATION HEADER
            |--------------------------------------------------------------------------
            */

            if (
                empty($plant) ||
                empty($type) ||
                empty($material) ||
                empty($supplier)
            ) {

                throw new Exception(
                    'Header PO belum lengkap'
                );

            }

            if (empty($po_date)) {

                throw new Exception(
                    'Tanggal PO wajib diisi'
                );

            }

            if ($jumlah <= 0) {

                throw new Exception(
                    'Qty / Ekor harus lebih dari 0'
                );

            }

            if ($berat <= 0) {

                throw new Exception(
                    'Weight / BW harus lebih dari 0'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION ACTUAL
            |--------------------------------------------------------------------------
            */

            $this->validateActual(
                $jumlah,
                $berat,
                $matiQty,
                $actualHasilTimbang,
                $claimQty
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDATION MATERIAL
            |--------------------------------------------------------------------------
            */

            $materialExists = $this->db
                ->where('MATERIAL', $material)
                ->count_all_results('abc_cd_material');

            if (!$materialExists) {

                throw new Exception(
                    'Material tidak valid'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION SUPPLIER
            |--------------------------------------------------------------------------
            */

            $supplierExists = $this->db
                ->where('CUST', $supplier)
                ->count_all_results('abc_cd_customer');

            if (!$supplierExists) {

                throw new Exception(
                    'Supplier tidak valid'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER DETAIL
            | OPTIONAL
            |--------------------------------------------------------------------------
            */

            if (!empty($detail) && !is_array($detail)) {

                throw new Exception(
                    'Format Customer Detail tidak valid'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION DETAIL
            |--------------------------------------------------------------------------
            */

            if (!empty($detail)) {

                $detailJumlah = 0;

                $detailBerat = 0;

                $customerCheck = [];

                foreach ($detail as $i => $d) {

                    $row = $i + 1;

                    $customer = trim(
                        $d['CUSTOMER'] ?? ''
                    );

                    $dJumlah = $this->decimal(
                        $d['JUMLAH'] ?? 0
                    );

                    $dBerat = $this->decimal(
                        $d['BERAT'] ?? 0
                    );

                    $dHarga = $this->decimal(
                        $d['HARGA'] ?? 0
                    );

                    $dTotal = round(
                        $dBerat * $dHarga,
                        2
                    );

                    if (empty($customer)) {

                        throw new Exception(
                            "Customer detail baris ke-{$row} wajib dipilih"
                        );

                    }

                    if (in_array(
                        $customer,
                        $customerCheck
                    )) {

                        throw new Exception(
                            "Customer duplicate pada detail baris ke-{$row}"
                        );

                    }

                    $customerCheck[] = $customer;

                    $customerExists = $this->db
                        ->where('CUST', $customer)
                        ->count_all_results('abc_cd_customer');

                    if (!$customerExists) {

                        throw new Exception(
                            "Customer detail baris ke-{$row} tidak valid"
                        );

                    }

                    if ($dJumlah <= 0) {

                        throw new Exception(
                            "Qty detail baris ke-{$row} harus lebih dari 0"
                        );

                    }

                    if ($dBerat <= 0) {

                        throw new Exception(
                            "Weight detail baris ke-{$row} harus lebih dari 0"
                        );

                    }

                    $detailJumlah += $dJumlah;

                    $detailBerat += $dBerat;

                }

                if ($detailJumlah > $jumlah) {

                    throw new Exception(
                        'Total Qty detail melebihi Qty master'
                    );

                }

                if ($detailBerat > $berat) {

                    throw new Exception(
                        'Total Weight detail melebihi Weight master'
                    );

                }

            }

            // ================= GENERATE PO =================
        $prefix = 'PO';

        $dateCode = date('ym');

        $q = $this->db
            ->query("
                SELECT MAX(RIGHT(PO,4)) AS seq
                FROM abc_mst_po
                WHERE LEFT(PO,6) = ?
            ", [$prefix . $dateCode])
            ->row();

        $seq = $q && $q->seq
            ? ((int)$q->seq + 1)
            : 1;

        $po = $prefix .
            $dateCode .
            str_pad($seq, 4, '0', STR_PAD_LEFT);

        /*
        |--------------------------------------------------------------------------
        | INSERT HEADER
        |--------------------------------------------------------------------------
        */

        $header = [

            'PO'       => $po,
            'PLANT'    => $plant,
            'PO_DATE'  => $po_date,

            'PO_TYPE'  => $type,
            'SUPPLIER' => $supplier,

            'MATERIAL' => $material,

            'JUMLAH'   => $jumlah,
            'BERAT'    => $berat,

            /*
            |--------------------------------------------------------------------------
            | ACTUAL
            |--------------------------------------------------------------------------
            */

            'AVG_BW'                => $actual['AVG_BW'],

            'MATI_QTY'              => $matiQty,
            'MATI_BW'               => $actual['MATI_BW'],

            'ACTUAL_HASIL_TIMBANG'  => $actualHasilTimbang,

            'SUSUT_BW'              => $actual['SUSUT_BW'],

            'TOTAL_TERIMA_QTY'      => $actual['TOTAL_TERIMA_QTY'],
            'TOTAL_TERIMA_BW'       => $actual['TOTAL_TERIMA_BW'],

            'CLAIM_QTY'             => $claimQty,
            'CLAIM_BW'              => $actual['CLAIM_BW'],

            'TOTAL_BAYAR_BW'        => $actual['TOTAL_BAYAR_BW'],

            /*
            |--------------------------------------------------------------------------
            | PRICE
            |--------------------------------------------------------------------------
            */

            'HARGA'   => $harga,
            'TOTAL'   => $total,

            'NO_TRUCK'=> $no_truck,
            'DRIVER'  => $driver,

            'REMARK'  => $remark,

            'STATUS'  => 'OPEN',

            'CREATED_BY' => $this->session->userdata('username'),
            'CREATED_AT' => date('Y-m-d H:i:s')

        ];

        $this->db->insert(
            'abc_mst_po',
            $header
        );

        /*
        |--------------------------------------------------------------------------
        | INSERT DETAIL
        |--------------------------------------------------------------------------
        */

        if (!empty($detail)) {

            $seqNo = 1;

            foreach ($detail as $d) {

                $detailJumlah = $this->decimal(
                    $d['JUMLAH']
                );

                $detailBerat = $this->decimal(
                    $d['BERAT']
                );

                $detailHarga = $this->decimal(
                    $d['HARGA']
                );

                $detailTotal = round(
                    $detailBerat * $detailHarga,
                    2
                );

                $detailInsert = [

                    'PO'       => $po,
                    'PLANT'    => $plant,

                    'SEQ_NO'   => $seqNo,

                    'CUSTOMER' => trim($d['CUSTOMER']),

                    'JUMLAH'   => $detailJumlah,
                    'BERAT'    => $detailBerat,

                    'HARGA'    => $detailHarga,
                    'TOTAL'    => $detailTotal,

                    'CREATED_BY' => $this->session->userdata('username'),
                    'CREATED_AT' => date('Y-m-d H:i:s')

                ];

                $this->db->insert(
                    'abc_mst_po_detail',
                    $detailInsert
                );

                $seqNo++;
            }

        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION CHECK
        |--------------------------------------------------------------------------
        */

        if ($this->db->trans_status() === false) {

            throw new Exception(
                'Database transaction failed'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

        $this->db->trans_commit();

        echo json_encode([

            'status'  => true,

            'message' => 'PO berhasil dibuat',

            'po'      => $po

        ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([

                'status'  => false,

                'message' => $e->getMessage()

            ]);

        }
    }

    public function edit()
    {
        $po       = $this->input->get('po', true);
        $plant    = $this->input->get('plant', true);

        if (!$po || !$plant) {

            echo json_encode([
                'status'  => false,
                'message' => 'Key missing'
            ]);

            return;
        }

        // ================= HEADER =================
        $header = $this->Po_model->get_header_for_edit(
            $plant,
            $po
        );

        if (!$header) {

            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan / tidak punya akses'
            ]);

            return;
        }

        // ================= LOCK RECEIVE =================
        if ($header['STATUS'] !== 'OPEN') {

            echo json_encode([
                'status'  => false,
                'message' => 'PO sudah diproses dan tidak dapat diedit'
            ]);

            return;
        }

        // ================= DETAIL =================
        $detail = $this->Po_model->get_detail_for_edit(
            $plant,
            $po
        );

        // ================= FORMAT DATE =================
        if (!empty($header['PO_DATE'])) {

            $header['PO_DATE'] = date(
                'Y-m-d',
                strtotime($header['PO_DATE'])
            );
        }

        echo json_encode([
            'status' => true,
            'header' => $header,
            'detail' => $detail
        ]);
    }

    public function update()
    {
        header('Content-Type: application/json');

        $username = $this->session
            ->userdata('username');

        $this->db->trans_begin();

        try {

            // ================= KEY =================
            $po    = trim($this->input->post('orig_po', true));
            $plant = trim($this->input->post('PLANT', true));

            if (empty($po) || empty($plant)) {

                throw new Exception(
                    'Key PO / PLANT tidak lengkap'
                );

            }

            // ================= LOCK RECEIVE =================
            $current = $this->Po_model->get_header_only(
                $plant,
                $po
            );

            if (!$current) {

                throw new Exception(
                    'PO tidak ditemukan'
                );

            }

            if ($current->STATUS !== 'OPEN') {

                throw new Exception(
                    'PO sudah diproses'
                );

            }

            // ================= HEADER =================
            $type      = trim($this->input->post('TYPE', true));
            $supplier  = trim($this->input->post('SUPPLIER', true));
            $material  = trim($this->input->post('MATERIAL', true));

            $po_date   = trim($this->input->post('PO_DATE', true));

            $remark    = trim($this->input->post('REMARK', true));

            $no_truck  = trim($this->input->post('NO_TRUCK', true));
            $driver    = trim($this->input->post('DRIVER', true));

            $jumlah = $this->decimal(
                $this->input->post('JUMLAH')
            );

            $berat = $this->decimal(
                $this->input->post('BERAT')
            );

            $harga = $this->decimal(
                $this->input->post('HARGA')
            );

            /*
            |--------------------------------------------------------------------------
            | ACTUAL
            |--------------------------------------------------------------------------
            */

            $matiQty = $this->decimal(
                $this->input->post('MATI_QTY')
            );

            $actualHasilTimbang = $this->decimal(
                $this->input->post('ACTUAL_HASIL_TIMBANG')
            );

            $claimQty = $this->decimal(
                $this->input->post('CLAIM_QTY')
            );

            /*
            |--------------------------------------------------------------------------
            | TOTAL SERVER SIDE
            |--------------------------------------------------------------------------
            */

            $actual = $this->calculateActual(
                $jumlah,
                $berat,
                $matiQty,
                $actualHasilTimbang,
                $claimQty
            );

            $total = round(
                $actual['TOTAL_BAYAR_BW'] * $harga,
                2
            );

            $detail = $this->input->post('DETAIL');

            /*
            |--------------------------------------------------------------------------
            | VALIDATION HEADER
            |--------------------------------------------------------------------------
            */

            if (
                empty($type) ||
                empty($supplier) ||
                empty($material)
            ) {

                throw new Exception(
                    'Header PO belum lengkap'
                );

            }

            if (empty($po_date)) {

                throw new Exception(
                    'Tanggal PO wajib diisi'
                );

            }

            if ($jumlah <= 0) {

                throw new Exception(
                    'Qty / Ekor harus lebih dari 0'
                );

            }

            if ($berat <= 0) {

                throw new Exception(
                    'Weight / BW harus lebih dari 0'
                );

            }

            if ($harga <= 0) {

                throw new Exception(
                    'Harga harus lebih dari 0'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION ACTUAL
            |--------------------------------------------------------------------------
            */

            $this->validateActual(
                $jumlah,
                $berat,
                $matiQty,
                $actualHasilTimbang,
                $claimQty
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDATION MATERIAL
            |--------------------------------------------------------------------------
            */

            $materialExists = $this->db
                ->where('MATERIAL', $material)
                ->count_all_results('abc_cd_material');

            if (!$materialExists) {

                throw new Exception(
                    'Material tidak valid'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION SUPPLIER
            |--------------------------------------------------------------------------
            */

            $supplierExists = $this->db
                ->where('CUST', $supplier)
                ->count_all_results('abc_cd_customer');

            if (!$supplierExists) {

                throw new Exception(
                    'Supplier tidak valid'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER DETAIL OPTIONAL
            |--------------------------------------------------------------------------
            */

            if (!empty($detail) && !is_array($detail)) {

                throw new Exception(
                    'Format Customer Detail tidak valid'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION DETAIL
            |--------------------------------------------------------------------------
            */

            if (!empty($detail)) {

                $detailJumlah = 0;
                $detailBerat  = 0;

                $customerCheck = [];

                foreach ($detail as $i => $d) {

                    $row = $i + 1;

                    $customer = trim(
                        $d['CUSTOMER'] ?? ''
                    );

                    $dJumlah = $this->decimal(
                        $d['JUMLAH'] ?? 0
                    );

                    $dBerat = $this->decimal(
                        $d['BERAT'] ?? 0
                    );

                    if (empty($customer)) {

                        throw new Exception(
                            "Customer detail baris ke-{$row} wajib dipilih"
                        );

                    }

                    if (in_array($customer, $customerCheck)) {

                        throw new Exception(
                            "Customer duplicate pada detail baris ke-{$row}"
                        );

                    }

                    $customerCheck[] = $customer;

                    $customerExists = $this->db
                        ->where('CUST', $customer)
                        ->count_all_results('abc_cd_customer');

                    if (!$customerExists) {

                        throw new Exception(
                            "Customer detail baris ke-{$row} tidak valid"
                        );

                    }

                    if ($dJumlah <= 0) {

                        throw new Exception(
                            "Qty detail baris ke-{$row} harus lebih dari 0"
                        );

                    }

                    if ($dBerat <= 0) {

                        throw new Exception(
                            "Weight detail baris ke-{$row} harus lebih dari 0"
                        );

                    }

                    $detailJumlah += $dJumlah;
                    $detailBerat += $dBerat;

                }

                if ($detailJumlah > $jumlah) {

                    throw new Exception(
                        'Total Qty detail melebihi Qty master'
                    );

                }

                if ($detailBerat > $berat) {

                    throw new Exception(
                        'Total Weight detail melebihi Weight master'
                    );

                }

            }

            // ================= UPDATE HEADER =================

            $header = [

                'PO_DATE' => $po_date,

                'PO_TYPE' => $type,

                'SUPPLIER' => $supplier,

                'MATERIAL' => $material,

                /*
                |--------------------------------------------------------------------------
                | MASTER
                |--------------------------------------------------------------------------
                */

                'JUMLAH' => $jumlah,

                'BERAT' => $berat,

                /*
                |--------------------------------------------------------------------------
                | ACTUAL
                |--------------------------------------------------------------------------
                */

                'AVG_BW' => $actual['AVG_BW'],

                'MATI_QTY' => $matiQty,

                'MATI_BW' => $actual['MATI_BW'],

                'ACTUAL_HASIL_TIMBANG' => $actualHasilTimbang,

                'SUSUT_BW' => $actual['SUSUT_BW'],

                'TOTAL_TERIMA_QTY' => $actual['TOTAL_TERIMA_QTY'],

                'TOTAL_TERIMA_BW' => $actual['TOTAL_TERIMA_BW'],

                'CLAIM_QTY' => $claimQty,

                'CLAIM_BW' => $actual['CLAIM_BW'],

                'TOTAL_BAYAR_BW' => $actual['TOTAL_BAYAR_BW'],

                'HARGA' => $harga,

                'TOTAL' => $total,

                /*
                |--------------------------------------------------------------------------
                | OTHER
                |--------------------------------------------------------------------------
                */

                'NO_TRUCK' => $no_truck,

                'DRIVER' => $driver,

                'REMARK' => $remark,

                'UPDATED_BY' => $username,

                'UPDATED_AT' => date('Y-m-d H:i:s')

            ];

            $this->Po_model->update_header_safe(
                $plant,
                $po,
                $header
            );

            /*
            |--------------------------------------------------------------------------
            | DELETE DETAIL
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where('PLANT', $plant)
                ->where('PO', $po)
                ->delete('abc_mst_po_detail');

            /*
            |--------------------------------------------------------------------------
            | INSERT DETAIL
            |--------------------------------------------------------------------------
            */

            if (!empty($detail)) {

                $seqNo = 1;

                foreach ($detail as $d) {

                    $detailJumlah = $this->decimal(
                        $d['JUMLAH']
                    );

                    $detailBerat = $this->decimal(
                        $d['BERAT']
                    );

                    $detailHarga = $this->decimal(
                        $d['HARGA']
                    );

                    $detailTotal = round(
                        $detailBerat * $detailHarga,
                        2
                    );

                    $insert = [

                        'PO' => $po,

                        'PLANT' => $plant,

                        'SEQ_NO' => $seqNo,

                        'CUSTOMER' => trim(
                            $d['CUSTOMER']
                        ),

                        'JUMLAH' => $detailJumlah,

                        'BERAT' => $detailBerat,

                        'HARGA' => $detailHarga,

                        'TOTAL' => $detailTotal,

                        'UPDATED_BY' => $username,

                        'UPDATED_AT' => date('Y-m-d H:i:s')

                    ];

                    $this->db->insert(
                        'abc_mst_po_detail',
                        $insert
                    );

                    $seqNo++;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | TRANSACTION
            |--------------------------------------------------------------------------
            */

            if ($this->db->trans_status() === false) {

                throw new Exception(
                    'Transaction failed'
                );

            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' => 'PO berhasil diperbarui'

            ]);

            } catch (Exception $e) {

                $this->db->trans_rollback();

                echo json_encode([

                    'status' => false,

                    'message' => $e->getMessage()

                ]);

            }
    }

    public function remove()
    {
        $po       = $this->input->post('po', TRUE);
        $plant    = $this->input->post('plant', TRUE); // ← dari JS

        if (!$po || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid key'
            ]);
            return;
        }

        $deleted = $this->Po_model->delete_po_safe(
            $plant,
            $po
        );

        echo json_encode([
            'status'  => (bool)$deleted,
            'message' => $deleted
                ? 'PO berhasil dihapus'
                : 'Gagal menghapus PO / tidak punya akses'
        ]);
    }

    public function print_pdf()
    {
        $po    = $this->input->get('po', true);

        $plant = $this->input->get('plant', true);

        if (!$po || !$plant) {

            show_error(
                'Parameter PO / PLANT tidak lengkap'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db->select("
                p.PO,

                p.PLANT,

                plant.CODE_NAME AS PLANT_NAME,

                p.PO_DATE,

                p.PO_TYPE,

                type.CODE_NAME AS PO_NAME,

                p.SUPPLIER,

                supplier.FULL_NAME AS SUPPLIER_NAME,

                p.MATERIAL,

                material.MATERIAL_NAME,

                p.NO_TRUCK,

                p.DRIVER,

                p.JUMLAH,

                p.BERAT,

                p.AVG_BW,

                p.MATI_QTY,

                p.MATI_BW,

                p.ACTUAL_HASIL_TIMBANG,

                p.SUSUT_BW,

                p.TOTAL_TERIMA_QTY,

                p.TOTAL_TERIMA_BW,

                p.CLAIM_QTY,

                p.CLAIM_BW,

                p.TOTAL_BAYAR_BW,

                p.HARGA,

                p.TOTAL,

                p.REMARK,

                p.STATUS,

                p.PAYMENT_STATUS
            ", false)

            ->from('abc_mst_po p')

            // ================= PLANT =================
            ->join(
                'abc_cd_code plant',
                "plant.HEAD_CODE='PLANT'
                AND plant.CODE COLLATE utf8mb4_unicode_ci =
                p.PLANT COLLATE utf8mb4_unicode_ci",
                'left',
                false
            )

            // ================= TYPE =================
            ->join(
                'abc_cd_code type',
                "type.HEAD_CODE='PO'
                AND type.CODE COLLATE utf8mb4_unicode_ci =
                p.PO_TYPE COLLATE utf8mb4_unicode_ci",
                'left',
                false
            )

            // ================= SUPPLIER =================
            ->join(
                'abc_cd_customer supplier',
                "supplier.CUST COLLATE utf8mb4_unicode_ci =
                p.SUPPLIER COLLATE utf8mb4_unicode_ci",
                'left',
                false
            )

            // ================= MATERIAL =================
            ->join(
                'abc_cd_material material',
                "material.MATERIAL COLLATE utf8mb4_unicode_ci =
                p.MATERIAL COLLATE utf8mb4_unicode_ci",
                'left',
                false
            )

            ->where('p.PO', $po)

            ->where('p.PLANT', $plant)

            ->where('p.DELETED IS NULL', null, false)

            ->get()

            ->row();

        if (!$header) {

            show_error('PO tidak ditemukan');
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detail = $this->db
            ->select("
                d.SEQ_NO,

                d.CUSTOMER,

                customer.FULL_NAME AS CUSTOMER_NAME,

                d.JUMLAH,

                d.BERAT,

                d.HARGA,

                d.TOTAL
            ", false)

            ->from('abc_mst_po_detail d')

            ->join(
                'abc_cd_customer customer',
                "customer.CUST COLLATE utf8mb4_unicode_ci =
                d.CUSTOMER COLLATE utf8mb4_unicode_ci",
                'left',
                false
            )

            ->where('d.PO', $po)

            ->where('d.PLANT', $plant)

            ->order_by('d.SEQ_NO', 'ASC')

            ->get()

            ->result();

        if (empty($detail)) {

            $detail = [];

        }

        $subtotal = [

            'qty'    => 0,

            'weight' => 0,

            'total'  => 0

        ];

        if (!empty($detail)) {

            foreach ($detail as $d) {

                $subtotal['qty'] += (float)$d->JUMLAH;

                $subtotal['weight'] += (float)$d->BERAT;

                $subtotal['total'] += (float)$d->TOTAL;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | PREPARE DATA
        |--------------------------------------------------------------------------
        */

        $data = [

            'header'   => $header,

            'detail'   => $detail,

            'subtotal' => $subtotal

        ];

        /*
        |--------------------------------------------------------------------------
        | GENERATE HTML
        |--------------------------------------------------------------------------
        */

        $html = $this->load->view(
            'admin/po/pdf_template',
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

            'PO_' . $header->PO . '.pdf',

            [
                'Attachment' => false
            ]
        );

        exit;
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

    private function decimal($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return round((float)$value, 2);
        }

        $value = trim($value);

        $value = str_replace('.', '', $value);

        $value = str_replace(',', '.', $value);

        return round((float)$value, 2);
    }

    private function validateActual(
        $jumlah,
        $berat,
        $matiQty,
        $actualHasilTimbang = 0,
        $claimQty = 0
    )
    {
        /*
        |--------------------------------------------------------------------------
        | MATI
        |--------------------------------------------------------------------------
        */

        if ($matiQty > $jumlah) {

            throw new Exception(
                'Mati Qty tidak boleh melebihi Qty.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | ACTUAL TIMBANG
        |--------------------------------------------------------------------------
        */

        $avgBw = ($jumlah > 0)
            ? ($berat / $jumlah)
            : 0;

        $matiBw = $avgBw * $matiQty;

        $maxTerimaBw = $berat - $matiBw;

        if ($actualHasilTimbang > $maxTerimaBw) {

            throw new Exception(
                'Actual Hasil Timbang tidak boleh melebihi berat setelah dikurangi Mati BW.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | CLAIM
        |--------------------------------------------------------------------------
        */

        if ($claimQty > $matiQty) {

            throw new Exception(
                'Claim Qty tidak boleh melebihi Mati Qty.'
            );

        }

    }

    private function calculateActual(
        $jumlah,
        $berat,
        $matiQty,
        $actualHasilTimbang = 0,
        $claimQty = 0
    )
    {
        // ================= AVG BW =================
        $avgBw = 0;

        if ($jumlah > 0) {
            $avgBw = $berat / $jumlah;
        }

        // ================= MATI BW =================
        $matiBw = $avgBw * $matiQty;

        // ================= TOTAL TERIMA QTY =================
        $totalTerimaQty = $jumlah - $matiQty;

        if ($totalTerimaQty < 0) {
            $totalTerimaQty = 0;
        }

        // ================= TOTAL TERIMA BW & SUSUT =================
        if ($actualHasilTimbang > 0) {

            $susutBw = ($berat - $matiBw) - $actualHasilTimbang;

            if ($susutBw < 0) {
                $susutBw = 0;
            }

            $totalTerimaBw = $actualHasilTimbang;

        } else {

            $susutBw = 0;

            $totalTerimaBw = $berat - $matiBw;

            if ($totalTerimaBw < 0) {
                $totalTerimaBw = 0;
            }
        }

        // ================= CLAIM =================
        $claimBw = $avgBw * $claimQty;

        if ($claimBw > $totalTerimaBw) {
            $claimBw = $totalTerimaBw;
        }

        // ================= TOTAL BAYAR =================
        $totalBayarBw = $totalTerimaBw - $claimBw;

        if ($totalBayarBw < 0) {
            $totalBayarBw = 0;
        }

        return [

            'AVG_BW'             => round($avgBw, 2),

            'MATI_BW'            => round($matiBw, 2),

            'SUSUT_BW'           => round($susutBw, 2),

            'TOTAL_TERIMA_QTY'   => round($totalTerimaQty, 2),

            'TOTAL_TERIMA_BW'    => round($totalTerimaBw, 2),

            'CLAIM_BW'           => round($claimBw, 2),

            'TOTAL_BAYAR_BW'     => round($totalBayarBw, 2),

        ];
    }

}
