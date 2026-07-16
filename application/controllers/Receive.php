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
        header('Content-Type: application/json');

        $plant = $this->input->get('plant', true);
        $q     = $this->input->get('q', true);

        if(empty($plant)){
            echo json_encode([]);
            return;
        }

        $role_id = $this->session->userdata('role_id');

        $result = $this->Receive_model->search_po(
            $role_id,
            $plant,
            $q
        );

        echo json_encode($result);
    }

    public function get_po_detail()
    {
        header('Content-Type: application/json');

        try{

            $plant = $this->input->get('plant',true);
            $po    = $this->input->get('po',true);

            if(empty($plant)){
                throw new Exception('Plant kosong.');
            }

            if(empty($po)){
                throw new Exception('PO kosong.');
            }

            $header = $this->Receive_model
                ->get_po_actual(
                    $plant,
                    $po
                );

            if(!$header){
                throw new Exception(
                    'PO tidak ditemukan.'
                );
            }

            $customer = $this->Receive_model
                ->get_customer_master();

            echo json_encode([

                'status'=>true,

                'header'=>$header,

                'customer'=>$customer

            ]);

        }
        catch(Exception $e){

            echo json_encode([

                'status'=>false,

                'message'=>$e->getMessage()

            ]);

        }
    }

    public function get_customer_master()
    {
        return $this->db

            ->select("
                CUST,
                FULL_NAME
            ")

            ->from("abc_cd_customer")

            ->where("STATUS","N")

            ->where("CUST_KIND","CUSTOMER")

            ->where("CUST <>","CS000001")

            ->order_by(
                "FULL_NAME",
                "ASC"
            )

            ->get()

            ->result_array();
    }

    public function create()
    {
        header('Content-Type: application/json');

        try{

            /*
            |--------------------------------------------------------------------------
            | PAYLOAD
            |--------------------------------------------------------------------------
            */

            $payload = $this->getPayload();

            $this->validatePayloadStructure(
                $payload
            );

            /*
            |--------------------------------------------------------------------------
            | DATA
            |--------------------------------------------------------------------------
            */

            $header = $payload['header'];

            $customers = $payload['customers'];

            $savings = $payload['savings'];

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            $header = $this->validateHeader(
                $header
            );

            $po = $this->getPOActual(
                $header['PLANT'],
                $header['PO']
            );

            $customers = $this->validateCustomer(
                $customers
            );

            $savings = $this->validateSaving(
                $savings,
                $customers
            );

            $actual = $this->validateQtyAgainstPO(
                $po,
                $customers
            );

            /*
            |--------------------------------------------------------------------------
            | FILE
            |--------------------------------------------------------------------------
            */

            $attachment = $this->uploadAttachment();

            /*
            |--------------------------------------------------------------------------
            | BUILD SUMMARY
            |--------------------------------------------------------------------------
            */

            $summary = $this->buildAllocationSummary(
                $customers,
                $savings
            );

            /*
            |--------------------------------------------------------------------------
            | PREPARE
            |--------------------------------------------------------------------------
            */

            $receiveHeader = $this->prepareReceiveHeader(
                $header,
                $po,
                $attachment
            );

            $receiveDetail = $this->prepareReceiveDetail(
                $receiveHeader,
                $customers,
                $actual
            );

            $receiveSaving = $this->prepareSaving(
                $receiveHeader,
                $summary
            );

            $receiveSales = $this->prepareSales(
                $receiveHeader,
                $summary
            );

            $companyStock = $this->prepareCompanyStock(
                $receiveHeader,
                $actual
            );

            $poUpdate = $this->preparePOUpdate(
                $po,
                $actual
            );

            $this->db->trans_begin();

            /*
            |--------------------------------------------------------------------------
            | MODEL
            |--------------------------------------------------------------------------
            */

            $this->saveReceiveTransaction(

                $receiveHeader,

                $receiveDetail,

                $receiveSaving,

                $receiveSales,

                $companyStock,

                $poUpdate

            );

            if($this->db->trans_status() === FALSE){

                throw new Exception(
                    'Transaction gagal.'
                );

            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' => 'Receive berhasil disimpan.'

            ]);

            return;

        }
        catch(Exception $e){

            if($this->db->trans_status()){

                $this->db->trans_rollback();

            }

            echo json_encode([

                'status'=>false,

                'message'=>$e->getMessage()

            ]);

            return;

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

    public function get_lookup_po()
    {
        echo json_encode(

            $this->Receive_model
                ->getLookupPO()

        );
    }

    public function lookup_po()
    {
        $plant = $this->input->get("plant", true);
        $keyword = $this->input->get("keyword", true);

        if (empty($plant)) {

            echo json_encode([]);

            return;
        }

        echo json_encode(
            $this->Receive_model->lookupPO(
                $plant,
                $keyword
            )
        );
    }

    public function lookup_customer()
    {
        $keyword =

            $this->input
                ->get("keyword");

        echo json_encode(

            $this->Receive_model
                ->lookupCustomer(
                    $keyword
                )

        );
    }

    private function generateReceiveNo()
    {
        $prefix = 'RC';

        $dateCode = date('ym');

        $q = $this->db
            ->query("
                SELECT MAX(RIGHT(RECEIVE,4)) seq
                FROM abc_mst_receive
                WHERE LEFT(RECEIVE,6)=?
            ",[
                $prefix.$dateCode
            ])
            ->row();

        $seq = ($q && $q->seq)
            ? ((int)$q->seq)+1
            :1;

        return
            $prefix.
            $dateCode.
            str_pad(
                $seq,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    private function generateSlipNo()
    {
        $prefix='SL';

        $dateCode=date('ym');

        $q=$this->db
            ->query("
                SELECT MAX(RIGHT(SLIP_NO,4)) seq
                FROM abc_mst_receive
                WHERE LEFT(SLIP_NO,6)=?
            ",[
                $prefix.$dateCode
            ])
            ->row();

        $seq=($q && $q->seq)
            ?((int)$q->seq)+1
            :1;

        return
            $prefix.
            $dateCode.
            str_pad(
                $seq,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    private function getPayload()
    {
        $json = $this->input->post('data');

        if(empty($json))
        {
            throw new Exception(
                'Payload tidak ditemukan.'
            );
        }

        $payload = json_decode(
            $json,
            true
        );

        if(
            json_last_error() !== JSON_ERROR_NONE
        ){
            throw new Exception(
                'Format payload tidak valid.'
            );
        }

        return $payload;
    }

    private function validatePayloadStructure(
        array $payload
    )
    {
        if(
            !isset($payload['header'])
        ){
            throw new Exception(
                'Header tidak ditemukan.'
            );
        }

        if(
            !isset($payload['customers'])
        ){
            throw new Exception(
                'Customer Allocation tidak ditemukan.'
            );
        }

        if(
            !isset($payload['savings'])
        ){
            throw new Exception(
                'Saving tidak ditemukan.'
            );
        }

        return true;
    }

    private function validateHeader(array $header)
    {
        /*
        |--------------------------------------------------------------------------
        | REQUIRED FIELD
        |--------------------------------------------------------------------------
        */

        $required = [

            'PLANT'         => 'Plant',

            'PO'            => 'Purchase Order',

            'RECEIVE_DATE'  => 'Receive Date',

            'PEMBAYARAN'    => 'Pembayaran',

            'JENIS_PAY'     => 'Jenis Pembayaran'

        ];

        foreach($required as $key => $label)
        {
            if(
                !isset($header[$key]) ||
                trim($header[$key]) === ''
            ){
                throw new Exception(
                    $label . ' wajib diisi.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FORMAT DATE
        |--------------------------------------------------------------------------
        */

        $date =
            DateTime::createFromFormat(
                'Y-m-d',
                $header['RECEIVE_DATE']
            );

        if(
            !$date ||
            $date->format('Y-m-d') != $header['RECEIVE_DATE']
        ){
            throw new Exception(
                'Format Receive Date tidak valid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

        $header['PLANT'] =
            trim($header['PLANT']);

        $header['PO'] =
            trim($header['PO']);

        $header['PEMBAYARAN'] =
            trim($header['PEMBAYARAN']);

        $header['JENIS_PAY'] =
            trim($header['JENIS_PAY']);

        $header['NOTA'] =
            isset($header['NOTA'])
                ? trim($header['NOTA'])
                : '';

        $header['NO_REF'] =
            isset($header['NO_REF'])
                ? trim($header['NO_REF'])
                : '';

        $header['REMARK'] =
            isset($header['REMARK'])
                ? trim($header['REMARK'])
                : '';

        return $header;
    }

    private function getPOActual($plant, $po)
    {
        $row = $this->Receive_model
            ->get_po_actual($plant, $po);

        if(!$row){
            throw new Exception("PO tidak ditemukan.");
        }

        if((int)$row['STATUS'] === 1){
            throw new Exception("PO sudah selesai di Receive.");
        }

        return (object)$row;
    }

    private function validateCustomer(array $rows)
    {
        if (empty($rows)) {
            throw new Exception(
                'Customer Allocation minimal harus memiliki 1 customer.'
            );
        }

        $customers = [];

        foreach ($rows as $i => $row) {

            $rowNo = $i + 1;

            /*
            |--------------------------------------------------------------------------
            | NORMALIZE
            |--------------------------------------------------------------------------
            */

            $customer = trim($row['CUSTOMER'] ?? '');

            $qty = (float)($row['QTY'] ?? 0);

            $bw = (float)($row['BW'] ?? 0);

            $harga = (float)($row['HARGA'] ?? 0);

            $discount = (float)($row['DISCOUNT'] ?? 0);

            $remark = trim($row['REMARK'] ?? '');

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            if ($customer === '') {

                throw new Exception(
                    "Customer pada baris {$rowNo} wajib dipilih."
                );

            }

            /*
            |--------------------------------------------------------------------------
            | DUPLICATE CUSTOMER
            |--------------------------------------------------------------------------
            */

            if (isset($customers[$customer])) {

                throw new Exception(
                    "Customer {$customer} tidak boleh dipilih lebih dari satu kali."
                );

            }

            /*
            |--------------------------------------------------------------------------
            | QTY
            |--------------------------------------------------------------------------
            */

            if ($qty <= 0) {

                throw new Exception(
                    "Qty pada customer {$customer} harus lebih dari 0."
                );

            }

            /*
            |--------------------------------------------------------------------------
            | BW
            |--------------------------------------------------------------------------
            */

            if ($bw <= 0) {

                throw new Exception(
                    "Berat pada customer {$customer} harus lebih dari 0."
                );

            }

            /*
            |--------------------------------------------------------------------------
            | HARGA
            |--------------------------------------------------------------------------
            */

            if ($harga <= 0) {

                throw new Exception(
                    "Harga pada customer {$customer} harus lebih dari 0."
                );

            }

            /*
            |--------------------------------------------------------------------------
            | DISCOUNT
            |--------------------------------------------------------------------------
            */

            if ($discount < 0) {

                throw new Exception(
                    "Discount pada customer {$customer} tidak boleh negatif."
                );

            }

            /*
            |--------------------------------------------------------------------------
            | SAVE NORMALIZED DATA
            |--------------------------------------------------------------------------
            */

            $customers[$customer] = [

                'CUSTOMER' => $customer,

                'QTY' => round($qty, 2),

                'BW' => round($bw, 2),

                'HARGA' => round($harga, 2),

                'DISCOUNT' => round($discount, 2),

                'REMARK' => $remark

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | RETURN NORMALIZED ARRAY
        |--------------------------------------------------------------------------
        */

        return array_values($customers);
    }

    private function validateSaving(
        array $rows,
        array $customers
    )
    {
        /*
        |--------------------------------------------------------------------------
        | CUSTOMER MAP
        |--------------------------------------------------------------------------
        */

        $customerMap = [];

        foreach ($customers as $customer) {

            $customerMap[
                $customer['CUSTOMER']
            ] = true;

        }

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

        $savingRows = [];

        foreach ($rows as $i => $row) {

            $customer = trim(
                $row['CUSTOMER'] ?? ''
            );

            /*
            |--------------------------------------------------------------------------
            | INTERNAL FARM
            |--------------------------------------------------------------------------
            */

            if ($customer === 'CS000001') {

                continue;

            }

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER EXIST
            |--------------------------------------------------------------------------
            */

            if (
                !isset(
                    $customerMap[$customer]
                )
            ) {

                throw new Exception(

                    "Saving customer {$customer} tidak ditemukan pada Customer Allocation."

                );

            }

            /*
            |--------------------------------------------------------------------------
            | SAVING
            |--------------------------------------------------------------------------
            */

            $saving = (float)(
                $row['SAVING_AMOUNT'] ?? 0
            );

            if ($saving < 0) {

                throw new Exception(

                    "Saving customer {$customer} tidak boleh negatif."

                );

            }

            /*
            |--------------------------------------------------------------------------
            | REMARK
            |--------------------------------------------------------------------------
            */

            $remark = trim(
                $row['REMARK'] ?? ''
            );

            /*
            |--------------------------------------------------------------------------
            | SAVE
            |--------------------------------------------------------------------------
            */

            $savingRows[] = [

                'CUSTOMER' => $customer,

                'SAVING_AMOUNT' => round(
                    $saving,
                    2
                ),

                'REMARK' => $remark

            ];

        }

        return $savingRows;
    }

    private function validateQtyAgainstPO(
        $po,
        array $customers
    )
    {
        /*
        |--------------------------------------------------------------------------
        | RECEIVE QTY / BW
        |--------------------------------------------------------------------------
        */

        $receiveQty = (float) $po->TOTAL_TERIMA_QTY;

        $receiveBw = (float) $po->TOTAL_TERIMA_BW;

        /*
        |--------------------------------------------------------------------------
        | ALLOCATION
        |--------------------------------------------------------------------------
        */

        $allocQty = 0;

        $allocBw = 0;

        foreach ($customers as $customer) {

            $allocQty += (float) $customer['QTY'];

            $allocBw += (float) $customer['BW'];

        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($allocQty > $receiveQty) {

            throw new Exception(
                'Total Qty Allocation melebihi Qty Receive.'
            );

        }

        if ($allocBw > $receiveBw) {

            throw new Exception(
                'Total BW Allocation melebihi BW Receive.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | REMAINING
        |--------------------------------------------------------------------------
        */

        $remainQty = round(
            $receiveQty - $allocQty,
            2
        );

        $remainBw = round(
            $receiveBw - $allocBw,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

        if ($remainQty < 0) {

            $remainQty = 0;

        }

        if ($remainBw < 0) {

            $remainBw = 0;

        }

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            'RECEIVE_QTY' => round(
                $receiveQty,
                2
            ),

            'RECEIVE_BW' => round(
                $receiveBw,
                2
            ),

            'ALLOC_QTY' => round(
                $allocQty,
                2
            ),

            'ALLOC_BW' => round(
                $allocBw,
                2
            ),

            'REMAIN_QTY' => $remainQty,

            'REMAIN_BW' => $remainBw

        ];
    }

    private function uploadAttachment()
    {
        if(
            empty($_FILES['ATTACHMENT']['name'])
        ){
            return null;
        }

        $config = [

            'upload_path' =>

                './uploads/receive/',

            'allowed_types' =>

                'jpg|jpeg|png|pdf|xls|xlsx|doc|docx',

            'encrypt_name' => true,

            'max_size' => 5120

        ];

        if(!is_dir('./uploads/receive')){
            mkdir(
                './uploads/receive',
                0777,
                true
            );
        }

        if(!is_writable('./uploads/receive')){
            throw new Exception(
                'Folder upload tidak bisa ditulis.'
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

        return $this->upload
            ->data('file_name');
    }

    private function prepareReceiveHeader(
        $header,
        $po,
        $attachment
    )
    {
        $username = $this->session
            ->userdata('username');

        return [

            /*
            |--------------------------------------------------------------------------
            | KEY
            |--------------------------------------------------------------------------
            */

            'RECEIVE' => $this->generateReceiveNo(),

            'SLIP_NO' => $this->generateSlipNo(),

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            'PLANT' => $header['PLANT'],

            'PO' => $header['PO'],

            'SUPPLIER' => $po->SUPPLIER,

            'RECEIVE_DATE' => $header['RECEIVE_DATE'],

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            'PEMBAYARAN' => $header['PEMBAYARAN'],

            'JENIS_PAY' => $header['JENIS_PAY'],

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT
            |--------------------------------------------------------------------------
            */

            'NOTA' => $header['NOTA'],

            'NO_REF' => $header['NO_REF'],

            'ATTACH_FILE_NAME'=>$attachment,

            /*
            |--------------------------------------------------------------------------
            | OTHER
            |--------------------------------------------------------------------------
            */

            'REMARK' => $header['REMARK'],

            'STATUS_RECEIVE' => 0,

            'CREATED_BY' => $username,

            'CREATED_AT' => date('Y-m-d H:i:s')

        ];

    }

    private function prepareReceiveDetail(
        $receiveHeader,
        $customer,
        $actual
    )
    {
        $username = $this->session
            ->userdata('username');

        $rows = [];

        $seq = 1;

        foreach($customer as $row)
        {
            $qty = (float)$row['QTY'];

            $bw = (float)$row['BW'];

            $harga = (float)$row['HARGA'];

            $discount = (float)$row['DISCOUNT'];

            $subtotal = round(
                $bw * $harga,
                2
            );

            $total = round(
                $subtotal - $discount,
                2
            );

            if($total < 0){

                $total = 0;

            }

            $rows[] = [

                /*
                |--------------------------------------------------------------------------
                | KEY
                |--------------------------------------------------------------------------
                */

                'RECEIVE' =>

                    $receiveHeader['RECEIVE'],

                'PLANT' =>

                    $receiveHeader['PLANT'],

                'SEQ_NO' =>

                    $seq,

                /*
                |--------------------------------------------------------------------------
                | CUSTOMER
                |--------------------------------------------------------------------------
                */

                'CUSTOMER' =>

                    trim(
                        $row['CUSTOMER']
                    ),

                /*
                |--------------------------------------------------------------------------
                | QTY
                |--------------------------------------------------------------------------
                */

                'JUMLAH' =>

                    $qty,

                'BERAT' =>

                    $bw,

                /*
                |--------------------------------------------------------------------------
                | PRICE
                |--------------------------------------------------------------------------
                */

                'HARGA' =>

                    $harga,

                'DISCOUNT' =>

                    $discount,

                'TOTAL' =>

                    $total,

                /*
                |--------------------------------------------------------------------------
                | OTHER
                |--------------------------------------------------------------------------
                */

                'KETERANGAN' =>

                    trim(
                        $row['REMARK']
                    ),

                'CREATED_BY' =>

                    $username,

                'CREATED_AT' =>

                    date(
                        'Y-m-d H:i:s'
                    )

            ];

            $seq++;

        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY STOCK
        |--------------------------------------------------------------------------
        */

        $remainingQty = round(
            (float)$actual['REMAIN_QTY'],
            2
        );

        $remainingBw = round(
            (float)$actual['REMAIN_BW'],
            2
        );

        if(
            $remainingQty > 0 ||
            $remainingBw > 0
        ){

            $rows[] = [

                'RECEIVE' =>

                    $receiveHeader['RECEIVE'],

                'PLANT' =>

                    $receiveHeader['PLANT'],

                'SEQ_NO' =>

                    $seq,

                'CUSTOMER' =>

                    'CS000001',

                'JUMLAH' =>

                    $remainingQty,

                'BERAT' =>

                    $remainingBw,

                'HARGA' =>

                    0,

                'DISCOUNT' =>

                    0,

                'TOTAL' =>

                    0,

                'REMARK' =>

                    'AUTO COMPANY STOCK',

                'CREATED_BY' =>

                    $username,

                'CREATED_AT' =>

                    date('Y-m-d H:i:s')

            ];

        }

        return $rows;

    }

    private function prepareSaving(
        array $receiveHeader,
        array $summary
    )
    {
        $username = $this->session
            ->userdata('username');

        $saving = [];

        foreach ($summary as $row) {

            /*
            |--------------------------------------------------------------------------
            | INTERNAL FARM
            |--------------------------------------------------------------------------
            */

            if ($row['CUSTOMER'] == 'CS000001') {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | NO SAVING
            |--------------------------------------------------------------------------
            */

            if ($row['SAVING_AMOUNT'] <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE
            |--------------------------------------------------------------------------
            */

            $saving[] = [

                'SV_NO' => $this->Receive_model->generateSavingNo(),

                'PLANT' => $receiveHeader['PLANT'],

                'SV_DATE' => $receiveHeader['RECEIVE_DATE'],

                'CUSTOMER' => $row['CUSTOMER'],

                /*
                |--------------------------------------------------------------------------
                | RELATED DOCUMENT
                |--------------------------------------------------------------------------
                */

                'RELATED' => $receiveHeader['RECEIVE'],

                /*
                |--------------------------------------------------------------------------
                | AMOUNT
                |--------------------------------------------------------------------------
                */

                'AMOUNT' => $row['SAVING_AMOUNT'],

                'REMARK' => $row['REMARK'],

                /*
                |--------------------------------------------------------------------------
                | AUDIT
                |--------------------------------------------------------------------------
                */

                'CREATED_AT' => date('Y-m-d H:i:s'),

                'CREATED_BY' => $username

            ];

        }

        return $saving;
    }

    private function buildAllocationSummary(
        array $customers,
        array $savings
    )
    {
        /*
        |--------------------------------------------------------------------------
        | SAVING MAP
        |--------------------------------------------------------------------------
        */

        $savingMap = [];

        foreach ($savings as $saving) {

            $savingMap[
                $saving['CUSTOMER']
            ] = (float) $saving['SAVING_AMOUNT'];

        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = [];

        foreach ($customers as $customer) {

            $customerCode = $customer['CUSTOMER'];

            $qty = (float) $customer['QTY'];

            $bw = (float) $customer['BW'];

            $price = (float) $customer['HARGA'];

            $discount = (float) $customer['DISCOUNT'];

            $salesAmount =
                ($bw * $price) - $discount;

            if ($salesAmount < 0) {

                $salesAmount = 0;

            }

            $savingAmount =
                $savingMap[$customerCode] ?? 0;

            $summary[$customerCode] = [

                'CUSTOMER' => $customerCode,

                'QTY' => $qty,

                'BW' => $bw,

                'PRICE' => $price,

                'DISCOUNT' => $discount,

                'SALES_AMOUNT' => round(
                    $salesAmount,
                    2
                ),

                'SAVING_AMOUNT' => round(
                    $savingAmount,
                    2
                ),

                'TOTAL_BAYAR' => round(
                    $salesAmount + $savingAmount,
                    2
                ),

                'REMARK' => $customer['REMARK']

            ];

        }

        return $summary;
    }

    private function prepareSales(
        array $receiveHeader,
        array $summary
    )
    {
        $username = $this->session
            ->userdata('username');

        $sales = [];

        foreach ($summary as $row) {

            /*
            |--------------------------------------------------------------------------
            | INTERNAL FARM TIDAK MENJADI SALES
            |--------------------------------------------------------------------------
            */

            if ($row['CUSTOMER'] == 'CS000001') {
                continue;
            }

            $sales[] = [

                /*
                |--------------------------------------------------------------------------
                | KEY
                |--------------------------------------------------------------------------
                */

                'SALES' => $this->Receive_model->generate_sales_no(),

                /*
                |--------------------------------------------------------------------------
                | RECEIVE
                |--------------------------------------------------------------------------
                */

                'RECEIVE' => $receiveHeader['RECEIVE'],

                'PLANT' => $receiveHeader['PLANT'],

                /*
                |--------------------------------------------------------------------------
                | CUSTOMER
                |--------------------------------------------------------------------------
                */

                'CUSTOMER' => $row['CUSTOMER'],

                /*
                |--------------------------------------------------------------------------
                | TRANSACTION
                |--------------------------------------------------------------------------
                */

                'QTY' => $row['QTY'],

                'BW' => $row['BW'],

                'PRICE' => $row['PRICE'],

                'DISCOUNT' => $row['DISCOUNT'],

                'SALES_AMOUNT' => $row['SALES_AMOUNT'],

                'SAVING_AMOUNT' => $row['SAVING_AMOUNT'],

                'TOTAL_BAYAR' => $row['TOTAL_BAYAR'],

                /*
                |--------------------------------------------------------------------------
                | OTHER
                |--------------------------------------------------------------------------
                */

                'STATUS' => 0,

                'CREATED_BY' => $username,

                'CREATED_AT' => date('Y-m-d H:i:s')

            ];

        }

        return $sales;
    }

    private function prepareCompanyStock(
        array $receiveHeader,
        array $actual
    )
    {
        /*
        |--------------------------------------------------------------------------
        | NO REMAINING
        |--------------------------------------------------------------------------
        */

        if (
            $actual['REMAIN_QTY'] <= 0 &&
            $actual['REMAIN_BW'] <= 0
        ) {
            return [];
        }

        $username = $this->session
            ->userdata('username');

        return [
            /*
            |--------------------------------------------------------------------------
            | KEY
            |--------------------------------------------------------------------------
            */

            'STOCK_NO' => $this->generate_company_stock(),

            /*
            |--------------------------------------------------------------------------
            | RECEIVE
            |--------------------------------------------------------------------------
            */

            'RECEIVE' => $receiveHeader['RECEIVE'],

            'PLANT' => $receiveHeader['PLANT'],

            /*
            |--------------------------------------------------------------------------
            | COMPANY STOCK
            |--------------------------------------------------------------------------
            */

            'CUSTOMER' => 'CS000001',

            'QTY' => $actual['REMAIN_QTY'],

            'BW' => $actual['REMAIN_BW'],

            /*
            |--------------------------------------------------------------------------
            | AUDIT
            |--------------------------------------------------------------------------
            */

            'CREATED_AT' => date(
                'Y-m-d H:i:s'
            ),

            'CREATED_BY' => $username

        ];
    }

    private function preparePOUpdate(
        $po,
        array $actual
    )
    {
        /*
        |--------------------------------------------------------------------------
        | REMAINING
        |--------------------------------------------------------------------------
        */

        $remainQty = $actual['REMAIN_QTY'];

        $remainBw = $actual['REMAIN_BW'];

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $remainQty <= 0 &&
            $remainBw <= 0
        ) {

            $status = 'RECEIVED';

        } else {

            $status = 'OPEN';

        }

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            'PO' => $po->PO,

            'STATUS' => $status

        ];
    }

    private function saveReceiveTransaction(
        array $receiveHeader,
        array $receiveDetail,
        array $receiveSaving,
        array $receiveSales,
        array $companyStock,
        array $poUpdate
    )
    {
        /*
        |--------------------------------------------------------------------------
        | RECEIVE
        |--------------------------------------------------------------------------
        */

        $this->Receive_model
            ->insertReceiveHeader(
                $receiveHeader
            );

        $this->Receive_model
            ->insertReceiveDetail(
                $receiveDetail
            );

        /*
        |--------------------------------------------------------------------------
        | SAVING
        |--------------------------------------------------------------------------
        */

        if (!empty($receiveSaving)) {

            $this->Receive_model
                ->insertSaving(
                    $receiveSaving
                );

        }

        /*
        |--------------------------------------------------------------------------
        | SALES
        |--------------------------------------------------------------------------
        */

        if (!empty($receiveSales)) {

            $this->Receive_model
                ->insertSales(
                    $receiveSales
                );

        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY STOCK
        |--------------------------------------------------------------------------
        */

        if (!empty($companyStock)) {

            $this->Receive_model
                ->insertCompanyStock(
                    $companyStock
                );

        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE PO
        |--------------------------------------------------------------------------
        */

        $this->Receive_model
            ->updatePO(
                $poUpdate
            );
    }
}
