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

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Receive']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/receive/list');   // your list view (the one you provided)
        $this->load->view('templates/footer');
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

    /**
     * Load data for table (ajax)
     */
    public function load_data()
    {
        $page   = (int)$this->input->get('page', true);
        $limit  = (int)$this->input->get('limit', true);
        $search = $this->input->get('search', true);
        $order  = $this->input->get('order', true) ?: 'RECEIVE_DATE';
        $dir    = $this->input->get('dir', true) ?: 'DESC';

        $page  = $page > 0 ? $page : 1;
        $limit = $limit > 0 ? $limit : 10;
        $start = ($page - 1) * $limit;

        $role_id = $this->session->userdata('role_id');
        $username = $this->session->userdata('username');
        $plant    = $this->session->userdata('plant'); // JSON ["10","1001",...]

        $rows = $this->Receive_model->get_data(
            $limit,
            $start,
            $role_id,
            $plant,
            $username,
            $search,
            $order,
            $dir
        );

        $total = $this->Receive_model->count_data(
            $role_id,
            $plant,
            $username,
            $search
        );

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'limit'      => $limit,
            'pagination' => $this->build_pagination($page, $limit, $total)
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

        $this->db->where('p.STATUS', 0);

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

    public function create()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try{

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            $plant       = $this->input->post('PLANT', true);
            $po          = $this->input->post('PO', true);
            $receiveDate = $this->input->post('RECEIVE_DATE', true);

            $supplier    = $this->input->post('SUPPLIER', true);

            $nota        = $this->input->post('NOTA', true);
            $noRef       = $this->input->post('NO_REF', true);

            $remark      = $this->input->post('REMARK', true);

            $pembayaran  = $this->input->post('PEMBAYARAN', true);

            $jenisPay    = $this->input->post('JENIS_PAY', true);

            $detail      = json_decode(
                $this->input->post('DETAIL'),
                true
            );

            if(
                empty($plant) ||
                empty($po)
            ){

                throw new Exception(
                    'Header receive belum lengkap'
                );

            }

            if(empty($detail)){

                throw new Exception(
                    'Detail receive kosong'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE RECEIVE
            |--------------------------------------------------------------------------
            */

            $prefix = 'RCV';

            $ym = date('ym');

            $q = $this->db
                ->query("
                    SELECT MAX(
                        RIGHT(RECEIVE,4)
                    ) AS seq
                    FROM abc_mst_receive
                    WHERE LEFT(RECEIVE,6)=?
                ", [
                    $prefix . $ym
                ])
                ->row();

            $seq = $q && $q->seq
                ? ((int)$q->seq + 1)
                : 1;

            $receiveNo =
                $prefix .
                $ym .
                str_pad(
                    $seq,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            /*
            |--------------------------------------------------------------------------
            | GENERATE SLIP
            |--------------------------------------------------------------------------
            */

            $slipNo =
                'SLP-' .
                date('Ymd') .
                '-' .
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

            if(
                isset($_FILES['ATTACHMENT']) &&
                $_FILES['ATTACHMENT']['name']
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

                $attachName =
                    $uploadData['file_name'];

            }

            /*
            |--------------------------------------------------------------------------
            | INSERT HEADER
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

                'STATUS' => 1,

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
            | DETAIL
            |--------------------------------------------------------------------------
            */

            $seqNo = 1;

            foreach($detail as $d){

                $insert = [

                    'RECEIVE' => $receiveNo,

                    'PLANT'   => $plant,

                    'SEQ_NO'  => $seqNo,

                    'PO_SEQ'  => $d['PO_SEQ'],

                    'CUSTOMER' =>
                        $d['CUSTOMER'] ?? null,

                    'PO_TYPE' =>
                        $d['PO_TYPE'] ?? null,

                    'MATERIAL' =>
                        explode(
                            ' - ',
                            $d['MATERIAL']
                        )[0],

                    'JUMLAH' =>
                        (float)$d['JUMLAH'],

                    'BERAT' =>
                        (float)$d['BERAT'],

                    'HARGA' =>
                        (float)$d['HARGA'],

                    'TOTAL' =>
                        (float)$d['TOTAL'],

                    'SUSUT_JUMLAH' =>
                        (float)$d['SUSUT_JUMLAH'],

                    'SUSUT_BERAT' =>
                        (float)$d['SUSUT_BERAT'],

                    'KETERANGAN' =>
                        $d['KETERANGAN'],

                    'IS_EXTRA' =>
                        $d['IS_EXTRA'],

                    'CREATED_BY' =>
                        $this->session
                            ->userdata('username'),

                    'CREATED_AT' =>
                        date('Y-m-d H:i:s')

                ];

                $this->db->insert(
                    'abc_mst_receive_detail',
                    $insert
                );

                /*
                |--------------------------------------------------------------------------
                | SALES AUTO
                |--------------------------------------------------------------------------
                */

                if(
                    strtoupper(
                        trim(
                            $d['CUSTOMER']
                        )
                    ) !== 'INTERNAL FARM'
                ){

                    $sales = [

                        'RECEIVE' => $receiveNo,

                        'PLANT'   => $plant,

                        'CUSTOMER' =>
                            $d['CUSTOMER'],

                        'MATERIAL' =>
                            explode(
                                ' - ',
                                $d['MATERIAL']
                            )[0],

                        'JUMLAH' =>
                            (float)$d['JUMLAH'],

                        'BERAT' =>
                            (float)$d['BERAT'],

                        'HARGA' =>
                            (float)$d['HARGA'],

                        'TOTAL' =>
                            (float)$d['TOTAL'],

                        'CREATED_BY' =>
                            $this->session
                                ->userdata('username'),

                        'CREATED_AT' =>
                            date('Y-m-d H:i:s')

                    ];

                    $this->db->insert(
                        'abc_trx_sales',
                        $sales
                    );

                }

                $seqNo++;

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
                        'STATUS' => 1
                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            if(
                $this->db->trans_status() === false
            ){

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

        }catch(Exception $e){

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' =>
                    $e->getMessage()

            ]);

        }
    }

    /**
     * Edit: return header + detail by RECEIVE (ajax)
     */
    public function edit()
    {
        $receive = $this->input->get('receive', TRUE);
        $plant   = $this->input->get('plant', TRUE);
        $username = $this->session->userdata('username');

        if (!$receive || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive & Plant required'
            ]);
            return;
        }

        $header = $this->Receive_model->get_receive_header($plant, $receive);
        $detail = $this->Receive_model->get_receive_detail($plant, $receive);

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive tidak ditemukan'
            ]);
            return;
        }

        // format date untuk input type="date"
        $header['RECEIVE_DATE'] = date('Y-m-d', strtotime($header['RECEIVE_DATE']));

        echo json_encode([
            'status' => true,
            'header' => $header,
            'detail' => $detail
        ]);
    }

    public function update()
    {
        header('Content-Type: application/json');

        $data     = $_POST;
        $receive  = trim($data['RECEIVE'] ?? '');
        $plant    = trim($data['PLANT'] ?? '');
        $username = $this->session->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */
        if (empty($receive) || empty($plant)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive & Plant required'
            ]);
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER LAMA
        |--------------------------------------------------------------------------
        */
        $oldHeader = $this->Receive_model->get_receive_header($plant, $receive);

        if (!$oldHeader) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive tidak ditemukan'
            ]);
            return;
        }

        $oldPo = $oldHeader['PO'] ?? null;

        $newPo = trim($data['PO'] ?? '');
        $newPo = ($newPo === '' || strtolower($newPo) === 'null')
            ? null
            : $newPo;

        /*
        |--------------------------------------------------------------------------
        | PARSE DETAIL
        |--------------------------------------------------------------------------
        */
        $detailRaw = $data['DETAIL'] ?? '[]';
        $detailArr = json_decode($detailRaw, true);

        if (!is_array($detailArr)) {
            $detailArr = [];
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER UPDATE
        |--------------------------------------------------------------------------
        */
        $header = [
            'RECEIVE_DATE' => !empty($data['RECEIVE_DATE'])
                ? date('Y-m-d', strtotime($data['RECEIVE_DATE']))
                : null,
            'SUPPLIER'     => $data['SUPPLIER'] ?? null,
            'PO'           => $newPo,
            'PEMBAYARAN'   => $data['PEMBAYARAN_EDIT'] ?? null,
            'JENIS_PAY'    => $data['JENIS_PAY_EDIT'] ?? null,
            'NOTA'         => $data['NOTA'] ?? null,
            'NO_REF'       => $data['NO_REF'] ?? null,
            'REMARK'       => $data['REMARK'] ?? null,
            'UPDATED_AT'   => date('Y-m-d H:i:s'),
            'UPDATED_BY'   => $username
        ];

        $this->db->trans_begin();

        try {

            /*
            |--------------------------------------------------------------------------
            | UPDATE HEADER
            |--------------------------------------------------------------------------
            */
            $this->Receive_model->update_receive_header(
                $receive,
                $header,
                $plant
            );

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS PO
            |--------------------------------------------------------------------------
            */
            if ($oldPo && $oldPo !== $newPo) {
                $this->Receive_model->reset_po_status($oldPo, $plant);
            }

            if ($newPo) {
                $this->Receive_model->set_po_received(
                    $newPo,
                    $plant,
                    $username
                );
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE DETAIL
            |--------------------------------------------------------------------------
            */
            $lastSeq   = (int) $this->Receive_model->get_max_seq_no($plant, $receive);
            $seq       = $lastSeq + 1;

            $keepSeq   = [];
            $insertRow = [];

            foreach ($detailArr as $row) {

                // UPDATE DETAIL LAMA
                if (!empty($row['SEQ_NO'])) {

                    $keepSeq[] = $row['SEQ_NO'];

                    $this->db
                        ->where([
                            'PLANT'   => $plant,
                            'RECEIVE' => $receive,
                            'SEQ_NO'  => $row['SEQ_NO']
                        ])
                        ->update('abc_mst_receive_detail', [
                            'PO'           => $newPo,
                            'PO_SEQ'       => $row['PO_SEQ'] ?? null,
                            'CUSTOMER'     => $row['CUSTOMER'] ?? null,
                            'MATERIAL'     => $row['MATERIAL'] ?? null,
                            'JUMLAH'       => (float)($row['JUMLAH'] ?? 0),
                            'BERAT'        => (float)($row['BERAT'] ?? 0),
                            'HARGA'        => (float)($row['HARGA'] ?? 0),
                            'TOTAL'        => (float)($row['TOTAL'] ?? 0),
                            'SUSUT_JUMLAH' => (float)($row['SUSUT_JUMLAH'] ?? 0),
                            'SUSUT_BERAT'  => (float)($row['SUSUT_BERAT'] ?? 0),
                            'KETERANGAN'   => $row['KETERANGAN'] ?? null,
                            'UPDATED_AT'   => date('Y-m-d H:i:s'),
                            'UPDATED_BY'   => $username
                        ]);

                }
                // INSERT DETAIL BARU
                else {

                    $insertRow[] = [
                        'PLANT'        => $plant,
                        'RECEIVE'      => $receive,
                        'SEQ_NO'       => $seq,
                        'PO'           => $newPo,
                        'PO_SEQ'       => $row['PO_SEQ'] ?? null,
                        'CUSTOMER'     => $row['CUSTOMER'] ?? null,
                        'MATERIAL'     => $row['MATERIAL'] ?? null,
                        'JUMLAH'       => (float)($row['JUMLAH'] ?? 0),
                        'BERAT'        => (float)($row['BERAT'] ?? 0),
                        'HARGA'        => (float)($row['HARGA'] ?? 0),
                        'TOTAL'        => (float)($row['TOTAL'] ?? 0),
                        'SUSUT_JUMLAH' => (float)($row['SUSUT_JUMLAH'] ?? 0),
                        'SUSUT_BERAT'  => (float)($row['SUSUT_BERAT'] ?? 0),
                        'KETERANGAN'   => $row['KETERANGAN'] ?? null,
                        'STATUS'       => 'RECEIVED',
                        'CREATED_AT'   => date('Y-m-d H:i:s'),
                        'CREATED_BY'   => $username
                    ];

                    $keepSeq[] = $seq;
                    $seq++;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT DETAIL BARU
            |--------------------------------------------------------------------------
            */
            if (!empty($insertRow)) {
                $this->Receive_model->insert_receive_detail_batch($insertRow);
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE DETAIL YANG DIHAPUS
            |--------------------------------------------------------------------------
            */
            if (!empty($keepSeq)) {
                $this->Receive_model->delete_receive_detail_not_in_seq(
                    $plant,
                    $receive,
                    $keepSeq
                );
            }

            /*
            |--------------------------------------------------------------------------
            | UPLOAD ATTACHMENT
            |--------------------------------------------------------------------------
            */
            if (!empty($_FILES['ATTACHMENT']['name'])) {

                $upload = $this->Receive_model->upload_file(
                    'ATTACHMENT',
                    $plant,
                    $receive,
                    $data['RECEIVE_DATE'] ?? date('Y-m-d')
                );

                if ($upload) {
                    $this->Receive_model->update_receive_header(
                        $receive,
                        [
                            'ATTACH_FILE_NAME'     => $upload['filename'],
                            'ATTACH_ORIGINAL_NAME' => $_FILES['ATTACHMENT']['name'],
                            'ATTACH_PATH'          => $upload['path'],
                            'ATTACH_EXT'           => pathinfo($upload['filename'], PATHINFO_EXTENSION),
                            'ATTACH_SIZE'          => $_FILES['ATTACHMENT']['size'],
                            'UPDATED_AT'           => date('Y-m-d H:i:s'),
                            'UPDATED_BY'           => $username
                        ],
                        $plant
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */
            if ($this->db->trans_status() === false) {
                throw new Exception('Gagal update receive');
            }

            $this->db->trans_commit();

            echo json_encode([
                'status'  => true,
                'message' => 'Receive berhasil diupdate'
            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove receive by RECEIVE
     */
    public function remove()
    {
        $receive = $this->input->post('receive', true);
        $plant   = $this->input->post('plant', true);
        $role_id = (int) $this->session->userdata('role_id');
        $username = $this->session->userdata('username');

        if (!$receive || !$plant) {
            echo json_encode([
                'status' => false,
                'message' => 'RECEIVE dan PLANT wajib dikirim'
            ]);
            return;
        }

        // ================= VALIDASI HEADER =================
        $header = $this->Receive_model->get_receive_header($plant, $receive);

        if (!$header) {
            echo json_encode([
                'status' => false,
                'message' => 'Data receive tidak ditemukan'
            ]);
            return;
        }

        // ================= NON ADMIN CHECK =================
        if ($role_id !== 1) {
            if ($header['CREATED_BY'] !== $username) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Anda tidak berhak menghapus receive ini'
                ]);
                return;
            }
        }

        $this->db->trans_start();

        // ================= ROLLBACK STATUS PO =================
        if (!empty($header['PO'])) {
            $this->Receive_model->update_po_status(
                $header['PO'],
                $plant,
                null // kembalikan ke NULL
            );
        }

        // ================= DELETE DETAIL =================
        $this->Receive_model->delete_receive_detail_by_receive($receive, $plant);

        // ================= DELETE HEADER =================
        $this->Receive_model->delete_receive_header_by_receive_and_plant($receive, $plant);

        $this->db->trans_complete();

        echo json_encode([
            'status'  => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'Receive berhasil dihapus'
                : 'Gagal menghapus receive'
        ]);
    }

    /**
     * Print PDF stub (you can implement PDF generation here)
     */
    // CONTROLLER
    public function print_slip_pdf()
    {
        $receive = $this->input->get('receive', true);
        $plant   = $this->input->get('plant', true);

        if (!$receive || !$plant) {
            show_error('Parameter RECEIVE / PLANT tidak lengkap');
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
                r.REMARK
            ", false)
            ->from('abc_mst_receive r')

            ->join(
                'abc_cd_code plant',
                "plant.HEAD_CODE='PLANT'
                AND plant.CODE COLLATE utf8mb4_unicode_ci =
                r.PLANT COLLATE utf8mb4_unicode_ci",
                'left',
                false
            )

            ->join(
                'abc_cd_customer supplier',
                "supplier.CUST COLLATE utf8mb4_unicode_ci =
                r.SUPPLIER COLLATE utf8mb4_unicode_ci",
                'left',
                false
            )

            ->where('r.RECEIVE', $receive)
            ->where('r.PLANT', $plant)
            ->where('r.DELETED IS NULL', null, false)
            ->get()
            ->row();

        if (!$header) {
            show_error('Receive tidak ditemukan');
        }

        $header->PO_TEXT = empty($header->PO)
            ? 'Direct Receive'
            : $header->PO;

        $header->STATUS_TEXT = 'RECEIVED';

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */
        $detail = $this->db
            ->select("
                d.SEQ_NO,
                d.MATERIAL,
                m.MATERIAL_NAME,
                d.JUMLAH,
                d.BERAT,
                d.SUSUT_JUMLAH,
                d.SUSUT_BERAT,
                d.HARGA,
                d.TOTAL,
                d.KETERANGAN,
                d.STATUS
            ", false)
            ->from('abc_mst_receive_detail d')

            ->join(
                'abc_cd_material m',
                "m.MATERIAL COLLATE utf8mb4_unicode_ci =
                d.MATERIAL COLLATE utf8mb4_unicode_ci",
                'left',
                false
            )

            ->where('d.RECEIVE', $receive)
            ->where('d.PLANT', $plant)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        if (empty($detail)) {
            show_error('Detail receive tidak ditemukan');
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */
        $summary = [
            'qty'    => 0,
            'weight' => 0,
            'total'  => 0
        ];

        foreach ($detail as $d) {
            $summary['qty']    += (float)$d->JUMLAH;
            $summary['weight'] += (float)$d->BERAT;
            $summary['total']  += (float)$d->TOTAL;
        }

        $data = [
            'header'  => $header,
            'detail'  => $detail,
            'summary' => $summary
        ];

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */
        $html = $this->load->view(
            'admin/receive/pdf_template',
            $data,
            true
        );

        $this->load->library('pdf');

        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            'RECEIVE_'.$header->RECEIVE.'.pdf',
            ['Attachment' => false]
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
