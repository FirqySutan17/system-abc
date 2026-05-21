<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('inventory_receive')) {
            show_404();
        }
        $this->load->model('Receive_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
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

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Receive']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/receive/list');   // your list view (the one you provided)
        $this->load->view('templates/footer');
    }

    public function load_data()
    {
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

        $order = trim(
            $this->input->get('order', true)
        );

        $dir = strtoupper(
            $this->input->get('dir', true)
        );

        /*
        |--------------------------------------------------------------------------
        | DEFAULT ORDER
        |--------------------------------------------------------------------------
        */

        if(empty($order)){

            $order = 'RECEIVE_DATE';

        }

        if(
            $dir !== 'ASC' &&
            $dir !== 'DESC'
        ){

            $dir = 'DESC';

        }

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | SESSION
        |--------------------------------------------------------------------------
        */

        $role_id = (int)$this->session
            ->userdata('role_id');

        $username = $this->session
            ->userdata('username');

        $plant = $this->session
            ->userdata('plant');

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->Receive_model->get_data(
            $limit,
            $start,
            $role_id,
            $plant,
            $username,
            $search,
            $order,
            $dir,
            $status,
            $dateFrom,
            $dateTo
        );

        $total = $this->Receive_model->count_data(
            $role_id,
            $plant,
            $username,
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

    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');

        $data = $this->Receive_model->get_user_plant_options($username);

        echo json_encode($data);
    }

    public function get_plant()
    {
        $data = $this->Receive_model->get_plant_select2();
        echo json_encode($data);
    }

    /**
     * Select2: supplier
     */
    public function get_supplier()
    {
        $term = $this->input->get('q');
        $data = $this->Receive_model->search_supplier($term);
        echo json_encode($data);
    }

    /**
     * Select2: material
     */
    public function get_material()
    {
        $term = $this->input->get('q');
        $data = $this->Receive_model->search_material($term);
        echo json_encode($data);
    }

    /**
     * Select2: PO list (for selecting PO in form)
     */
    public function get_po()
    {
        $q     = $this->input->get('q', true);
        $plant = $this->input->get('plant', true);

        $this->db->select("
            p.PO,
            p.PO_DATE,
            p.SUPPLIER,
            s.FULL_NAME AS SUPPLIER_NAME
        ");

        $this->db->from('abc_mst_po p');

        $this->db->join(
            'abc_cd_customer s',
            's.CUST = p.SUPPLIER',
            'left'
        );

        $this->db->where('p.DELETED IS NULL', null, false);

        $this->db->group_start();

        $this->db->where(
            'p.STATUS IS NULL',
            null,
            false
        );

        $this->db->or_where(
            'p.STATUS',
            '0'
        );

        $this->db->or_where(
            'p.STATUS',
            'OPEN'
        );

        $this->db->group_end();

        if(!empty($plant)){

            $this->db->where(
                'p.PLANT',
                $plant
            );

        }

        if(!empty($q)){

            $this->db->group_start();

            $this->db->like('p.PO', $q);

            $this->db->or_like(
                's.FULL_NAME',
                $q
            );

            $this->db->group_end();

        }

        $this->db->order_by(
            'p.PO_DATE',
            'DESC'
        );

        $rows = $this->db
            ->get()
            ->result();

        $result = [];

        foreach($rows as $r){

            $result[] = [

                'id' => $r->PO,

                'text' =>
                    $r->PO .
                    ' | ' .
                    date(
                        'd/m/Y',
                        strtotime($r->PO_DATE)
                    ) .
                    ' | ' .
                    $r->SUPPLIER_NAME

            ];

        }

        echo json_encode($result);
    }

    public function get_po_detail()
    {
        $po    = $this->input->get('po', true);
        $plant = $this->input->get('plant', true);

        if (
            empty($po) ||
            empty($plant)
        ) {

            echo json_encode([
                'status'  => false,
                'message' => 'PO / Plant kosong'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db
            ->select("
                p.*,

                supplier.FULL_NAME AS SUPPLIER_NAME,

                material.MATERIAL_NAME,

                type.CODE_NAME AS PO_TYPE_NAME
            ")

            ->from('abc_mst_po p')

            ->join(
                'abc_cd_customer supplier',
                'supplier.CUST = p.SUPPLIER',
                'left'
            )

            ->join(
                'abc_cd_material material',
                'material.MATERIAL = p.MATERIAL',
                'left'
            )

            ->join(
                'abc_cd_code type',
                "type.CODE = p.PO_TYPE
                AND type.HEAD_CODE = 'PO'",
                'left'
            )

            ->where('p.PO', $po)

            ->where('p.PLANT', $plant)

            ->where('p.DELETED IS NULL', null, false)

            ->get()

            ->row();

        if (!$header) {

            echo json_encode([
                'status'  => false,
                'message' => 'PO tidak ditemukan'
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $detail = $this->db
            ->select("
                d.*,

                customer.FULL_NAME AS CUSTOMER_NAME
            ")

            ->from('abc_mst_po_detail d')

            ->join(
                'abc_cd_customer customer',
                'customer.CUST = d.CUSTOMER',
                'left'
            )

            ->where('d.PO', $po)

            ->where('d.PLANT', $plant)

            ->order_by('d.SEQ_NO', 'ASC')

            ->get()

            ->result();

        echo json_encode([

            'status' => true,

            'header' => $header,

            'detail' => $detail

        ]);
    }

    public function get_po_type()
    {
        $q = trim(
            $this->input->get('q', true)
        );

        $this->db
            ->select('
                CODE,
                CODE_NAME
            ')

            ->from('abc_cd_code')

            ->where('HEAD_CODE', 'PO')

            ->where('CODE !=', '*');

        if(!empty($q)){

            $this->db->group_start();

            $this->db->like(
                'CODE',
                $q
            );

            $this->db->or_like(
                'CODE_NAME',
                $q
            );

            $this->db->group_end();

        }

        $rows = $this->db
            ->order_by('CODE_NAME','ASC')
            ->get()
            ->result();

        $result = [];

        foreach($rows as $r){

            $result[] = [

                'id'   => $r->CODE,

                'text' => $r->CODE_NAME

            ];

        }

        echo json_encode($result);
    }

    public function create()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try {

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            $plant       = trim($this->input->post('PLANT', true));
            $po          = trim($this->input->post('PO', true));
            $receiveDate = trim($this->input->post('RECEIVE_DATE', true));

            $supplier    = trim($this->input->post('SUPPLIER', true));

            $nota        = trim($this->input->post('NOTA', true));
            $noRef       = trim($this->input->post('NO_REF', true));

            $remark      = trim($this->input->post('REMARK', true));

            $pembayaran  = trim($this->input->post('PEMBAYARAN', true));

            $jenisPay    = trim($this->input->post('JENIS_PAY', true));

            $detail = json_decode(
                $this->input->post('DETAIL'),
                true
            );

            if (
                empty($plant) ||
                empty($po) ||
                empty($receiveDate) ||
                empty($pembayaran) ||
                empty($jenisPay)
            ) {

                throw new Exception(
                    'Header receive belum lengkap'
                );
            }

            if (
                empty($detail) ||
                !is_array($detail)
            ) {

                throw new Exception(
                    'Detail receive kosong'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | GET PO HEADER
            |--------------------------------------------------------------------------
            */

            $poHeader = $this->db
                ->where('PO', $po)
                ->where('PLANT', $plant)
                ->where('DELETED IS NULL', null, false)
                ->get('abc_mst_po')
                ->row();

            if (!$poHeader) {

                throw new Exception(
                    'PO tidak ditemukan'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE RECEIVE NUMBER
            |--------------------------------------------------------------------------
            */

            $dateCode = date('Ymd');

            $prefix = 'RCV';

            $q = $this->db
                ->query("
                    SELECT MAX(
                        RIGHT(RECEIVE,4)
                    ) AS seq
                    FROM abc_mst_receive
                    WHERE LEFT(RECEIVE,11)=?
                ", [
                    $prefix . $dateCode
                ])
                ->row();

            $seq = $q && $q->seq
                ? ((int)$q->seq + 1)
                : 1;

            $receiveNo =
                $prefix .
                $dateCode .
                str_pad(
                    $seq,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            /*
            |--------------------------------------------------------------------------
            | GENERATE SLIP NUMBER
            |--------------------------------------------------------------------------
            */

            $slipNo =
                'SLP' .
                $dateCode .
                str_pad(
                    $seq,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            /*
            |--------------------------------------------------------------------------
            | ATTACHMENT
            |--------------------------------------------------------------------------
            */

            $attachName = null;

            if (
                isset($_FILES['ATTACHMENT']) &&
                !empty($_FILES['ATTACHMENT']['name'])
            ) {

                $config['upload_path'] =
                    './uploads/receive/';

                $config['allowed_types'] =
                    'jpg|jpeg|png|pdf|xlsx|docx';

                $config['max_size'] = 10240;

                $config['encrypt_name'] = true;

                if (
                    !is_dir($config['upload_path'])
                ) {

                    mkdir(
                        $config['upload_path'],
                        0777,
                        true
                    );
                }

                $this->load->library(
                    'upload',
                    $config
                );

                if (
                    !$this->upload->do_upload(
                        'ATTACHMENT'
                    )
                ) {

                    throw new Exception(
                        strip_tags(
                            $this->upload->display_errors()
                        )
                    );
                }

                $uploadData =
                    $this->upload->data();

                $attachName =
                    $uploadData['file_name'];
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT RECEIVE HEADER
            |--------------------------------------------------------------------------
            */

            $header = [

                'RECEIVE'      => $receiveNo,

                'PLANT'        => $plant,

                'RECEIVE_DATE' => $receiveDate,

                'PO'           => $po,

                'SUPPLIER'     => $supplier,

                'PEMBAYARAN'   => $pembayaran,

                'JENIS_PAY'    => $jenisPay,

                'NOTA'         => $nota,

                'NO_REF'       => $noRef,

                'SLIP_NO'      => $slipNo,

                'REMARK'       => $remark,

                'ATTACH_FILE_NAME' =>
                    $attachName,

                'STATUS_RECEIVE' => 'OPEN',

                'CREATED_BY' =>
                    $this->session
                        ->userdata('username'),

                'CREATED_AT' =>
                    date('Y-m-d H:i:s')

            ];

            $this->db->insert(
                'abc_mst_receive',
                $header
            );

            /*
            |--------------------------------------------------------------------------
            | PREPARE SALES GROUP
            |--------------------------------------------------------------------------
            */

            $salesGroup = [];

            /*
            |--------------------------------------------------------------------------
            | INSERT RECEIVE DETAIL
            |--------------------------------------------------------------------------
            */

            $seqNo = 1;

            foreach ($detail as $d) {

                $customer = trim(
                    $d['CUSTOMER'] ?? ''
                );

                $poType = trim(
                    $d['PO_TYPE'] ?? ''
                );

                $jumlah = (float)(
                    $d['JUMLAH'] ?? 0
                );

                $berat = (float)(
                    $d['BERAT'] ?? 0
                );

                $harga = (float)(
                    $d['HARGA'] ?? 0
                );

                $total = (float)(
                    $d['TOTAL'] ?? 0
                );

                if (
                    empty($customer)
                ) {

                    throw new Exception(
                        'Customer detail wajib diisi'
                    );
                }

                $receiveDetail = [

                    'RECEIVE' => $receiveNo,

                    'PLANT'   => $plant,

                    'SEQ_NO'  => $seqNo,

                    'PO_SEQ'  => $d['PO_SEQ'] ?? null,

                    'CUSTOMER' => $customer,

                    'PO_TYPE' => $poType,

                    'MATERIAL' => $poHeader->MATERIAL,

                    'JUMLAH' => $jumlah,

                    'BERAT' => $berat,

                    'HARGA' => $harga,

                    'TOTAL' => $total,

                    'SUSUT_JUMLAH' =>
                        (float)(
                            $d['SUSUT_JUMLAH'] ?? 0
                        ),

                    'SUSUT_BERAT' =>
                        (float)(
                            $d['SUSUT_BERAT'] ?? 0
                        ),

                    'KETERANGAN' =>
                        $d['KETERANGAN'] ?? null,

                    'IS_EXTRA' =>
                        (int)(
                            $d['IS_EXTRA'] ?? 0
                        ),

                    'SALES_CREATED' => 0,

                    'CREATED_BY' =>
                        $this->session
                            ->userdata('username'),

                    'CREATED_AT' =>
                        date('Y-m-d H:i:s')

                ];

                $this->db->insert(
                    'abc_mst_receive_detail',
                    $receiveDetail
                );

                /*
                |--------------------------------------------------------------------------
                | GROUP SALES
                |--------------------------------------------------------------------------
                */

                if ($customer != 'CS000001') {

                    $salesGroup[$customer][] = [

                        'receive_seq' =>
                            $seqNo,

                        'customer' =>
                            $customer,

                        'po_type' =>
                            $poType,

                        'material' =>
                            $poHeader->MATERIAL,

                        'jumlah' =>
                            $jumlah,

                        'berat' =>
                            $berat,

                        'harga' =>
                            $harga,

                        'total' =>
                            $total
                    ];
                }

                $seqNo++;
            }

            /*
            |--------------------------------------------------------------------------
            | AUTO CREATE SALES
            |--------------------------------------------------------------------------
            */

            foreach ($salesGroup as $customer => $items) {

                /*
                |--------------------------------------------------------------------------
                | GENERATE SALES NUMBER
                |--------------------------------------------------------------------------
                */

                $salesPrefix = 'SLS';

                $qSales = $this->db
                    ->query("
                        SELECT MAX(
                            RIGHT(SALES,4)
                        ) AS seq
                        FROM abc_mst_sales
                        WHERE LEFT(SALES,11)=?
                    ", [
                        $salesPrefix . $dateCode
                    ])
                    ->row();

                $salesSeq = $qSales && $qSales->seq
                    ? ((int)$qSales->seq + 1)
                    : 1;

                $salesNo =
                    $salesPrefix .
                    $dateCode .
                    str_pad(
                        $salesSeq,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );

                /*
                |--------------------------------------------------------------------------
                | TOTAL SALES
                |--------------------------------------------------------------------------
                */

                $grandTotal = 0;

                foreach ($items as $it) {

                    $grandTotal +=
                        (float)$it['total'];
                }

                /*
                |--------------------------------------------------------------------------
                | INSERT SALES HEADER
                |--------------------------------------------------------------------------
                */

                $salesHeader = [

                    'SALES' => $salesNo,

                    'PLANT' => $plant,

                    'RECEIVE' => $receiveNo,

                    'CUSTOMER' => $customer,

                    'SALES_DATE' =>
                        $receiveDate,

                    'SLIP_NO' =>
                        $slipNo,

                    'PEMBAYARAN' =>
                        $pembayaran,

                    'JENIS_PAY' =>
                        $jenisPay,

                    'NOTA' =>
                        $nota,

                    'AMOUNT' =>
                        $grandTotal,

                    'DP_AMOUNT' => 0,

                    'REMAIN' =>
                        $grandTotal,

                    'STATUS' => 'UNPAID',

                    'REMARK' =>
                        'AUTO FROM RECEIVE ' .
                        $receiveNo,

                    'CREATED_BY' =>
                        $this->session
                            ->userdata('username'),

                    'CREATED_AT' =>
                        date('Y-m-d H:i:s')

                ];

                $this->db->insert(
                    'abc_mst_sales',
                    $salesHeader
                );

                /*
                |--------------------------------------------------------------------------
                | INSERT SALES DETAIL
                |--------------------------------------------------------------------------
                */

                $salesSeqNo = 1;

                foreach ($items as $it) {

                    $salesDetail = [

                        'SALES' => $salesNo,

                        'PLANT' => $plant,

                        'SEQ_NO' =>
                            $salesSeqNo,

                        'MATERIAL' =>
                            $it['material'],

                        'JUMLAH' =>
                            $it['jumlah'],

                        'BERAT' =>
                            $it['berat'],

                        'HARGA' =>
                            $it['harga'],

                        'DISCOUNT' => 0,

                        'TOTAL' =>
                            $it['total'],

                        'CREATED_BY' =>
                            $this->session
                                ->userdata('username'),

                        'CREATED_AT' =>
                            date('Y-m-d H:i:s')

                    ];

                    $this->db->insert(
                        'abc_mst_sales_detail',
                        $salesDetail
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE RECEIVE DETAIL SALES INFO
                    |--------------------------------------------------------------------------
                    */

                    $this->db
                        ->where('RECEIVE', $receiveNo)
                        ->where('PLANT', $plant)
                        ->where(
                            'SEQ_NO',
                            $it['receive_seq']
                        )
                        ->update(
                            'abc_mst_receive_detail',
                            [
                                'SALES_CREATED' => 1,
                                'SALES_NO' => $salesNo
                            ]
                        );

                    $salesSeqNo++;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS PO
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where('PO', $po)
                ->where('PLANT', $plant)
                ->update(
                    'abc_mst_po',
                    [
                        'STATUS' => 'RECEIVED'
                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            if (
                $this->db->trans_status() === false
            ) {

                throw new Exception(
                    'Transaction failed'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Receive berhasil dibuat',

                'receive' =>
                    $receiveNo

            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' =>
                    $e->getMessage()

            ]);
        }
    }

    public function edit()
    {
         
        header('Content-Type: application/json');
        

        $receive = trim(
            $this->input->get('receive', true)
        );

        $plant = trim(
            $this->input->get('plant', true)
        );

        if(
            empty($receive) ||
            empty($plant)
        ){

            echo json_encode([

                'status' => false,

                'message' =>
                    'Receive / Plant kosong'

            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db
            ->select("
                r.*,

                plant.CODE_NAME AS PLANT_NAME,

                supplier.FULL_NAME AS SUPPLIER_NAME,

                po.MATERIAL,

                material.MATERIAL_NAME,

                po.JUMLAH,

                po.BERAT,

                po.HARGA,

                po.TOTAL,

                po.NO_TRUCK,

                po.DRIVER
            ", false)

            ->from('abc_mst_receive r')

            ->join(
                'abc_cd_code plant',
                "
                    plant.HEAD_CODE='PLANT'
                    AND plant.CODE = r.PLANT
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer supplier',
                '
                    supplier.CUST = r.SUPPLIER
                ',
                'left'
            )

            ->join(
                'abc_mst_po po',
                '
                    po.PO = r.PO
                    AND po.PLANT = r.PLANT
                ',
                'left'
            )

            ->join(
                'abc_cd_material material',
                '
                    material.MATERIAL = po.MATERIAL
                ',
                'left'
            )

            ->where('r.RECEIVE', $receive)

            ->where('r.PLANT', $plant)

            ->get()

            ->row_array();

        if(!$header){

            echo json_encode([

                'status' => false,

                'message' =>
                    'Receive tidak ditemukan'

            ]);

            return;
        }

        /*
|--------------------------------------------------------------------------
| DETAIL
|--------------------------------------------------------------------------
*/

    $detail = $this->db
        ->select("
            d.*,

            customer.FULL_NAME AS CUSTOMER_NAME,

            pt.CODE_NAME AS PO_TYPE_NAME
        ", false)

        ->from('abc_mst_receive_detail d')

        ->join(
            'abc_cd_customer customer',
            '
                customer.CUST = d.CUSTOMER
            ',
            'left'
        )

        ->join(
            'abc_cd_code pt',
            "
                TRIM(pt.CODE) = TRIM(d.PO_TYPE)
                AND pt.HEAD_CODE = 'PO'
            ",
            'left',
            false
        )

        ->where('d.RECEIVE', $receive)

        ->where('d.PLANT', $plant)

        ->order_by('d.SEQ_NO', 'ASC')

        ->get()

        ->result_array();

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE DETAIL
        |--------------------------------------------------------------------------
        */

        foreach($detail as &$d){

            $d['PO_TYPE'] =
                !empty($d['PO_TYPE'])
                    ? $d['PO_TYPE']
                    : '-';

            $d['CUSTOMER_NAME'] =
                !empty($d['CUSTOMER_NAME'])
                    ? $d['CUSTOMER_NAME']
                    : '-';

            $d['SUSUT_JUMLAH'] =
                (float)(
                    $d['SUSUT_JUMLAH'] ?? 0
                );

            $d['SUSUT_BERAT'] =
                (float)(
                    $d['SUSUT_BERAT'] ?? 0
                );

            $d['IS_EXTRA'] =
                (int)(
                    $d['IS_EXTRA'] ?? 0
                );

        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        // echo '<pre>';
        // print_r($detail);
        // die;

        echo json_encode([

            'status' => true,

            'header' => $header,

            'detail' => $detail

        ]);
    }

    public function update()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try{

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            $receive = trim(
                $this->input->post('RECEIVE', true)
            );

            $plant = trim(
                $this->input->post('PLANT', true)
            );

            $po = trim(
                $this->input->post('PO', true)
            );

            $receiveDate = trim(
                $this->input->post('RECEIVE_DATE', true)
            );

            $supplier = trim(
                $this->input->post('SUPPLIER', true)
            );

            $nota = trim(
                $this->input->post('NOTA', true)
            );

            $noRef = trim(
                $this->input->post('NO_REF', true)
            );

            $remark = trim(
                $this->input->post('REMARK', true)
            );

            $pembayaran = trim(
                $this->input->post('PEMBAYARAN', true)
            );

            $jenisPay = trim(
                $this->input->post('JENIS_PAY', true)
            );

            $detail = json_decode(
                $this->input->post('DETAIL'),
                true
            );

            if(
                empty($receive) ||
                empty($plant)
            ){

                throw new Exception(
                    'Receive / Plant kosong'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | GET RECEIVE HEADER
            |--------------------------------------------------------------------------
            */

            $oldHeader = $this->db
                ->where('RECEIVE', $receive)
                ->where('PLANT', $plant)
                ->get('abc_mst_receive')
                ->row();

            if(!$oldHeader){

                throw new Exception(
                    'Receive tidak ditemukan'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | GET PO HEADER
            |--------------------------------------------------------------------------
            */

            $poHeader = null;

            if(!empty($po)){

                $poHeader = $this->db
                    ->where('PO', $po)
                    ->where('PLANT', $plant)
                    ->where('DELETED IS NULL', null, false)
                    ->get('abc_mst_po')
                    ->row();

            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE HEADER
            |--------------------------------------------------------------------------
            */

            $header = [

                'PO' => $po,

                'SUPPLIER' => $supplier,

                'RECEIVE_DATE' => $receiveDate,

                'PEMBAYARAN' => $pembayaran,

                'JENIS_PAY' => $jenisPay,

                'NOTA' => $nota,

                'NO_REF' => $noRef,

                'REMARK' => $remark,

                'UPDATED_BY' =>
                    $this->session
                        ->userdata('username'),

                'UPDATED_AT' =>
                    date('Y-m-d H:i:s')

            ];

            /*
            |--------------------------------------------------------------------------
            | ATTACHMENT
            |--------------------------------------------------------------------------
            */

            if(
                isset($_FILES['ATTACHMENT']) &&
                !empty($_FILES['ATTACHMENT']['name'])
            ){

                $config['upload_path'] =
                    './uploads/receive/';

                $config['allowed_types'] =
                    'jpg|jpeg|png|pdf|xlsx|docx';

                $config['max_size'] = 10240;

                $config['encrypt_name'] = true;

                if(
                    !is_dir($config['upload_path'])
                ){

                    mkdir(
                        $config['upload_path'],
                        0777,
                        true
                    );

                }

                $this->load->library(
                    'upload',
                    $config
                );

                if(
                    !$this->upload->do_upload(
                        'ATTACHMENT'
                    )
                ){

                    throw new Exception(
                        strip_tags(
                            $this->upload->display_errors()
                        )
                    );

                }

                $uploadData =
                    $this->upload->data();

                /*
                |--------------------------------------------------------------------------
                | DELETE OLD FILE
                |--------------------------------------------------------------------------
                */

                if(
                    !empty($oldHeader->ATTACH_FILE_NAME)
                ){

                    $oldPath =
                        './uploads/receive/' .
                        $oldHeader->ATTACH_FILE_NAME;

                    if(file_exists($oldPath)){

                        @unlink($oldPath);

                    }

                }

                $header['ATTACH_FILE_NAME'] =
                    $uploadData['file_name'];

            }

            $this->db
                ->where('RECEIVE', $receive)
                ->where('PLANT', $plant)
                ->update(
                    'abc_mst_receive',
                    $header
                );

            /*
            |--------------------------------------------------------------------------
            | DELETE OLD DETAIL
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where('RECEIVE', $receive)
                ->where('PLANT', $plant)
                ->delete('abc_mst_receive_detail');

            /*
            |--------------------------------------------------------------------------
            | DELETE OLD SALES
            |--------------------------------------------------------------------------
            */

            $oldSales = $this->db
                ->select('SALES')
                ->where('RECEIVE', $receive)
                ->where('PLANT', $plant)
                ->get('abc_mst_sales')
                ->result();

            foreach($oldSales as $s){

                $this->db
                    ->where('SALES', $s->SALES)
                    ->where('PLANT', $plant)
                    ->delete('abc_mst_sales_detail');

            }

            $this->db
                ->where('RECEIVE', $receive)
                ->where('PLANT', $plant)
                ->delete('abc_mst_sales');

            /*
            |--------------------------------------------------------------------------
            | INSERT RECEIVE DETAIL
            |--------------------------------------------------------------------------
            */

            $salesGroup = [];

            $seqNo = 1;

            foreach($detail as $d){

                $customer = trim(
                    $d['CUSTOMER'] ?? ''
                );

                $poType = trim(
                    $d['PO_TYPE'] ?? ''
                );

                $jumlah = (float)(
                    $d['JUMLAH'] ?? 0
                );

                $berat = (float)(
                    $d['BERAT'] ?? 0
                );

                $harga = (float)(
                    $d['HARGA'] ?? 0
                );

                $total = (float)(
                    $d['TOTAL'] ?? 0
                );

                $receiveDetail = [

                    'RECEIVE' => $receive,

                    'PLANT' => $plant,

                    'SEQ_NO' => $seqNo,

                    'PO_SEQ' =>
                        !empty($d['PO_SEQ'])
                            ? $d['PO_SEQ']
                            : 0,

                    'CUSTOMER' => $customer,

                    'PO_TYPE' => $poType,

                    'MATERIAL' =>
                        $poHeader
                            ? $poHeader->MATERIAL
                            : null,

                    'JUMLAH' => $jumlah,

                    'BERAT' => $berat,

                    'HARGA' => $harga,

                    'TOTAL' => $total,

                    'SUSUT_JUMLAH' =>
                        (float)(
                            $d['SUSUT_JUMLAH'] ?? 0
                        ),

                    'SUSUT_BERAT' =>
                        (float)(
                            $d['SUSUT_BERAT'] ?? 0
                        ),

                    'KETERANGAN' =>
                        $d['KETERANGAN'] ?? null,

                    'IS_EXTRA' =>
                        (int)(
                            $d['IS_EXTRA'] ?? 0
                        ),

                    'SALES_CREATED' => 0,

                    'CREATED_BY' =>
                        $this->session
                            ->userdata('username'),

                    'CREATED_AT' =>
                        date('Y-m-d H:i:s')

                ];

                $this->db->insert(
                    'abc_mst_receive_detail',
                    $receiveDetail
                );

                /*
                |--------------------------------------------------------------------------
                | SALES GROUP
                |--------------------------------------------------------------------------
                */

                if($customer != 'CS000001'){

                    $salesGroup[$customer][] = [

                        'receive_seq' => $seqNo,

                        'customer' => $customer,

                        'po_type' => $poType,

                        'material' =>
                            $poHeader
                                ? $poHeader->MATERIAL
                                : null,

                        'jumlah' => $jumlah,

                        'berat' => $berat,

                        'harga' => $harga,

                        'total' => $total

                    ];

                }

                $seqNo++;

            }

            /*
            |--------------------------------------------------------------------------
            | REBUILD SALES
            |--------------------------------------------------------------------------
            */

            foreach($salesGroup as $customer => $items){

                $dateCode = date('Ymd');

                $salesPrefix = 'SLS';

                $qSales = $this->db
                    ->query("
                        SELECT MAX(
                            RIGHT(SALES,4)
                        ) AS seq
                        FROM abc_mst_sales
                        WHERE LEFT(SALES,11)=?
                    ", [
                        $salesPrefix . $dateCode
                    ])
                    ->row();

                $salesSeq = $qSales && $qSales->seq
                    ? ((int)$qSales->seq + 1)
                    : 1;

                $salesNo =
                    $salesPrefix .
                    $dateCode .
                    str_pad(
                        $salesSeq,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );

                $grandTotal = 0;

                foreach($items as $it){

                    $grandTotal +=
                        (float)$it['total'];

                }

                /*
                |--------------------------------------------------------------------------
                | SALES HEADER
                |--------------------------------------------------------------------------
                */

                $salesHeader = [

                    'SALES' => $salesNo,

                    'PLANT' => $plant,

                    'RECEIVE' => $receive,

                    'CUSTOMER' => $customer,

                    'SALES_DATE' =>
                        $receiveDate,

                    'SLIP_NO' =>
                        $oldHeader->SLIP_NO,

                    'PEMBAYARAN' =>
                        $pembayaran,

                    'JENIS_PAY' =>
                        $jenisPay,

                    'NOTA' => $nota,

                    'AMOUNT' =>
                        $grandTotal,

                    'DP_AMOUNT' => 0,

                    'REMAIN' =>
                        $grandTotal,

                    'STATUS' => 'UNPAID',

                    'REMARK' =>
                        'AUTO FROM RECEIVE ' .
                        $receive,

                    'CREATED_BY' =>
                        $this->session
                            ->userdata('username'),

                    'CREATED_AT' =>
                        date('Y-m-d H:i:s')

                ];

                $this->db->insert(
                    'abc_mst_sales',
                    $salesHeader
                );

                /*
                |--------------------------------------------------------------------------
                | SALES DETAIL
                |--------------------------------------------------------------------------
                */

                $salesSeqNo = 1;

                foreach($items as $it){

                    $salesDetail = [

                        'SALES' => $salesNo,

                        'PLANT' => $plant,

                        'SEQ_NO' => $salesSeqNo,

                        'MATERIAL' =>
                            $it['material'],

                        'JUMLAH' =>
                            $it['jumlah'],

                        'BERAT' =>
                            $it['berat'],

                        'HARGA' =>
                            $it['harga'],

                        'DISCOUNT' => 0,

                        'TOTAL' =>
                            $it['total'],

                        'CREATED_BY' =>
                            $this->session
                                ->userdata('username'),

                        'CREATED_AT' =>
                            date('Y-m-d H:i:s')

                    ];

                    $this->db->insert(
                        'abc_mst_sales_detail',
                        $salesDetail
                    );

                    $this->db
                        ->where('RECEIVE', $receive)
                        ->where('PLANT', $plant)
                        ->where(
                            'SEQ_NO',
                            $it['receive_seq']
                        )
                        ->update(
                            'abc_mst_receive_detail',
                            [
                                'SALES_CREATED' => 1,
                                'SALES_NO' => $salesNo
                            ]
                        );

                    $salesSeqNo++;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS PO
            |--------------------------------------------------------------------------
            */

            if(!empty($po)){

                $this->db
                    ->where('PO', $po)
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
            | COMMIT
            |--------------------------------------------------------------------------
            */

            if(
                $this->db->trans_status() === false
            ){

                throw new Exception(
                    'Update receive gagal'
                );

            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Receive berhasil diupdate'

            ]);

        }catch(Exception $e){

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' =>
                    $e->getMessage()

            ]);

        }
    }

    public function remove()
    {
        header('Content-Type: application/json');

        $receive = trim(
            $this->input->post('receive', true)
        );

        $plant = trim(
            $this->input->post('plant', true)
        );

        if(
            empty($receive) ||
            empty($plant)
        ){

            echo json_encode([

                'status' => false,

                'message' =>
                    'Receive / Plant wajib diisi'

            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->Receive_model
            ->get_receive_header(
                $plant,
                $receive
            );

        if(!$header){

            echo json_encode([

                'status' => false,

                'message' =>
                    'Receive tidak ditemukan'

            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | START TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->trans_begin();

        try{

            /*
            |--------------------------------------------------------------------------
            | GET SALES LIST
            |--------------------------------------------------------------------------
            */

            $salesList = $this->db
                ->select('SALES_NO')

                ->from('abc_mst_receive_detail')

                ->where('RECEIVE', $receive)

                ->where('PLANT', $plant)

                ->where('SALES_NO IS NOT NULL', null, false)

                ->group_by('SALES_NO')

                ->get()

                ->result_array();

            /*
            |--------------------------------------------------------------------------
            | DELETE SALES
            |--------------------------------------------------------------------------
            */

            if(!empty($salesList)){

                foreach($salesList as $s){

                    $salesNo =
                        $s['SALES_NO'];

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE SALES DETAIL
                    |--------------------------------------------------------------------------
                    */

                    $this->db
                        ->where('SALES', $salesNo)

                        ->where('PLANT', $plant)

                        ->delete(
                            'abc_mst_sales_detail'
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE SALES HEADER
                    |--------------------------------------------------------------------------
                    */

                    $this->db
                        ->where('SALES', $salesNo)

                        ->where('PLANT', $plant)

                        ->delete(
                            'abc_mst_sales'
                        );

                }

            }

            /*
            |--------------------------------------------------------------------------
            | RESET STATUS PO
            |--------------------------------------------------------------------------
            */

            if(!empty($header['PO'])){

                $this->db
                    ->where('PO', $header['PO'])

                    ->where('PLANT', $plant)

                    ->update(
                        'abc_mst_po',
                        [

                            'STATUS' => 'OPEN',

                            'UPDATED_AT' =>
                                date('Y-m-d H:i:s')

                        ]
                    );

            }

            /*
            |--------------------------------------------------------------------------
            | DELETE ATTACHMENT
            |--------------------------------------------------------------------------
            */

            if(
                !empty($header['ATTACH_FILE_NAME'])
            ){

                $path =
                    FCPATH .
                    'uploads/receive/' .
                    $header['ATTACH_FILE_NAME'];

                if(file_exists($path)){

                    @unlink($path);

                }

            }

            /*
            |--------------------------------------------------------------------------
            | DELETE RECEIVE DETAIL
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where('RECEIVE', $receive)

                ->where('PLANT', $plant)

                ->delete(
                    'abc_mst_receive_detail'
                );

            /*
            |--------------------------------------------------------------------------
            | DELETE RECEIVE HEADER
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where('RECEIVE', $receive)

                ->where('PLANT', $plant)

                ->delete(
                    'abc_mst_receive'
                );

            /*
            |--------------------------------------------------------------------------
            | VALIDATE TRANSACTION
            |--------------------------------------------------------------------------
            */

            if(
                $this->db->trans_status()
                === false
            ){

                throw new Exception(
                    'Transaction failed'
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

                'message' =>
                    'Receive berhasil dihapus'

            ]);

        }catch(Exception $e){

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' =>
                    $e->getMessage()

            ]);

        }
    }

    public function print_slip_pdf()
    {
        $receive = trim(
            $this->input->get('receive', true)
        );

        $plant = trim(
            $this->input->get('plant', true)
        );

        if (
            empty($receive) ||
            empty($plant)
        ) {

            show_error(
                'Parameter RECEIVE / PLANT tidak lengkap'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db
            ->select("
                r.RECEIVE,

                r.PLANT,

                plant.CODE_NAME AS PLANT_NAME,

                r.RECEIVE_DATE,

                r.PO,

                r.NOTA,

                r.NO_REF,

                r.SUPPLIER,

                supplier.FULL_NAME AS SUPPLIER_NAME,

                r.PEMBAYARAN,

                r.JENIS_PAY,

                r.SLIP_NO,

                r.ATTACH_FILE_NAME,

                r.REMARK,

                r.STATUS_RECEIVE
            ", false)

            ->from('abc_mst_receive r')

            ->join(
                'abc_cd_code plant',
                "
                    plant.HEAD_CODE='PLANT'

                    AND plant.CODE COLLATE utf8mb4_unicode_ci =
                    r.PLANT COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer supplier',
                "
                    supplier.CUST COLLATE utf8mb4_unicode_ci =
                    r.SUPPLIER COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            )

            ->where('r.RECEIVE', $receive)

            ->where('r.PLANT', $plant)

            ->where(
                'r.DELETED IS NULL',
                null,
                false
            )

            ->get()

            ->row();

        if (!$header) {

            show_error(
                'Receive tidak ditemukan'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PO TEXT
        |--------------------------------------------------------------------------
        */

        $header->PO_TEXT =
            !empty($header->PO)
                ? $header->PO
                : 'DIRECT RECEIVE';

        /*
        |--------------------------------------------------------------------------
        | STATUS TEXT
        |--------------------------------------------------------------------------
        */

        $header->STATUS_TEXT =
            !empty($header->STATUS_RECEIVE)
                ? strtoupper($header->STATUS_RECEIVE)
                : 'OPEN';

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

                d.PO_TYPE,

                po_type.CODE_NAME AS PO_TYPE_NAME,

                d.MATERIAL,

                material.MATERIAL_NAME,

                d.JUMLAH,

                d.BERAT,

                d.SUSUT_JUMLAH,

                d.SUSUT_BERAT,

                d.HARGA,

                d.TOTAL,

                d.KETERANGAN,

                d.STATUS,

                d.IS_EXTRA,

                d.SALES_CREATED,

                d.SALES_NO
            ", false)

            ->from('abc_mst_receive_detail d')

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            ->join(
                'abc_cd_customer customer',
                "
                    customer.CUST COLLATE utf8mb4_unicode_ci =
                    d.CUSTOMER COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            )

            /*
            |--------------------------------------------------------------------------
            | MATERIAL
            |--------------------------------------------------------------------------
            */

            ->join(
                'abc_cd_material material',
                "
                    material.MATERIAL COLLATE utf8mb4_unicode_ci =
                    d.MATERIAL COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            )

            /*
            |--------------------------------------------------------------------------
            | PO TYPE
            |--------------------------------------------------------------------------
            */

            ->join(
                'abc_cd_code po_type',
                "
                    po_type.CODE COLLATE utf8mb4_unicode_ci =
                    d.PO_TYPE COLLATE utf8mb4_unicode_ci

                    AND po_type.HEAD_CODE = 'PO'
                ",
                'left',
                false
            )

            ->where('d.RECEIVE', $receive)

            ->where('d.PLANT', $plant)

            ->order_by('d.SEQ_NO', 'ASC')

            ->get()

            ->result();

        if (empty($detail)) {

            show_error(
                'Detail receive tidak ditemukan'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = [

            'qty' => 0,

            'weight' => 0,

            'total' => 0,

            'susut_qty' => 0,

            'susut_berat' => 0,

            'customer' => [],

            'sales' => []

        ];

        /*
        |--------------------------------------------------------------------------
        | LOOP SUMMARY
        |--------------------------------------------------------------------------
        */

        foreach ($detail as $d) {

            $summary['qty'] +=
                (float)$d->JUMLAH;

            $summary['weight'] +=
                (float)$d->BERAT;

            $summary['total'] +=
                (float)$d->TOTAL;

            $summary['susut_qty'] +=
                (float)$d->SUSUT_JUMLAH;

            $summary['susut_berat'] +=
                (float)$d->SUSUT_BERAT;

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            if (!empty($d->CUSTOMER)) {

                $summary['customer'][
                    $d->CUSTOMER
                ] = true;
            }

            /*
            |--------------------------------------------------------------------------
            | SALES
            |--------------------------------------------------------------------------
            */

            if (!empty($d->SALES_NO)) {

                $summary['sales'][
                    $d->SALES_NO
                ] = true;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL CUSTOMER / SALES
        |--------------------------------------------------------------------------
        */

        $summary['total_customer'] =
            count($summary['customer']);

        $summary['total_sales'] =
            count($summary['sales']);

        /*
        |--------------------------------------------------------------------------
        | DATA VIEW
        |--------------------------------------------------------------------------
        */

        $data = [

            'header' => $header,

            'detail' => $detail,

            'summary' => $summary

        ];

        /*
        |--------------------------------------------------------------------------
        | HTML
        |--------------------------------------------------------------------------
        */

        $html = $this->load->view(
            'admin/receive/pdf_template',
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
            'landscape'
        );

        $this->pdf->render();

        /*
        |--------------------------------------------------------------------------
        | STREAM
        |--------------------------------------------------------------------------
        */

        $this->pdf->stream(

            'RECEIVE_' .
            $header->RECEIVE .
            '.pdf',

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

}
