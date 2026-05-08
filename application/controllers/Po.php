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
        $allowedOrder = ['PO','PO_DATE','SUPPLIER','CREATED_AT'];
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $username = $this->session->userdata('username');
        $search = $this->input->get('search', TRUE);
        $orderInput = $this->input->get('order', TRUE);
        $order = in_array($orderInput, $allowedOrder) ? $orderInput : 'PO';
        $dir = strtoupper($this->input->get('dir', TRUE)) === 'DESC'
            ? 'DESC'
            : 'ASC';

        $start = ($page - 1) * $limit;

        // 🔹 ambil user login
        $role_id = $this->session->userdata('role_id');
        $plant   = $this->session->userdata('plant'); // contoh: 1001

        $rows  = $this->Po_model->get_data($limit, $start, $role_id, $plant, $username, $search, $order, $dir);
        $total = $this->Po_model->count_data($role_id, $plant, $username, $search);

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

    public function create()
    {
        $data     = $this->input->post(NULL, TRUE);
        $username = $this->session->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDASI HEADER
        |--------------------------------------------------------------------------
        */
        if (
            empty($data['PLANT']) ||
            empty($data['TYPE']) ||
            empty($data['PO_DATE']) ||
            empty($data['SUPPLIER'])
        ) {
            echo json_encode([
                'status'  => false,
                'message' => 'Plant, PO Type, Tanggal, dan Supplier wajib diisi'
            ]);
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DETAIL
        |--------------------------------------------------------------------------
        */
        if (empty($data['DETAIL']) || !is_array($data['DETAIL'])) {
            echo json_encode([
                'status'  => false,
                'message' => 'Minimal 1 material harus diisi'
            ]);
            return;
        }

        $validDetail = array_filter($data['DETAIL'], function($row){
            return !empty($row['MATERIAL']);
        });

        if (empty($validDetail)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Minimal 1 material harus diisi'
            ]);
            return;
        }

        $plant = $data['PLANT'];

        /*
        |--------------------------------------------------------------------------
        | GENERATE PO
        |--------------------------------------------------------------------------
        */
        $newPO = $this->Po_model->generate_auto_po($plant);

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */
        $header = [
            'PLANT'      => $plant,
            'PO'         => $newPO,
            'PO_DATE'    => $data['PO_DATE'],
            'PO_TYPE'    => $data['TYPE'],
            'SUPPLIER'   => $data['SUPPLIER'],
            'REMARK'     => $data['REMARK'] ?? null,
            'CREATED_AT' => date('Y-m-d H:i:s'),
            'CREATED_BY' => $username
        ];

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */
        $this->db->trans_begin();

        try {

            // insert header
            $this->Po_model->insert_header($header);

            // detail batch
            $detailRows = [];
            $seq = 1;

            foreach ($validDetail as $row) {

                $jumlah = (float) ($row['JUMLAH'] ?? 0);
                $berat  = (float) ($row['BERAT'] ?? 0);
                $harga  = (float) $this->normalize_number($row['HARGA'] ?? 0);
                $total  = $jumlah * $harga;

                $detailRows[] = [
                    'PLANT'      => $plant,
                    'PO'         => $newPO,
                    'SEQ_NO'     => $seq,
                    'CUSTOMER'   => $row['CUSTOMER'] ?? null,
                    'MATERIAL'   => $row['MATERIAL'],
                    'JUMLAH'     => $jumlah,
                    'BERAT'      => $berat,
                    'HARGA'      => $harga,
                    'TOTAL'      => $total,
                    'CREATED_AT' => date('Y-m-d H:i:s'),
                    'CREATED_BY' => $username
                ];

                $seq++;
            }

            // insert batch detail
            $this->Po_model->insert_detail($detailRows);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menyimpan data');
            }

            $this->db->trans_commit();

            echo json_encode([
                'status'  => true,
                'po'      => $newPO,
                'message' => 'PO berhasil ditambahkan'
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
        $po       = $this->input->get('po', TRUE);
        $plant    = $this->input->get('plant', TRUE);
        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if (!$po || !$plant) {
            echo json_encode([
                'status' => false,
                'message'=> 'Key missing'
            ]);
            return;
        }

        // 🔐 Ambil header + cek ownership
        $header = $this->Po_model->get_header_for_edit(
            $plant,
            $po,
            $username,
            $role_id
        );

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan / tidak punya akses'
            ]);
            return;
        }

        // Ambil detail
        $detail = $this->Po_model->get_detail_for_edit($plant, $po);

        // format tanggal untuk input[type=date]
        if (!empty($header['PO_DATE'])) {
            $header['PO_DATE'] = date('Y-m-d', strtotime($header['PO_DATE']));
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

        $data     = $this->input->post(NULL, TRUE);
        $po       = $data['orig_po'] ?? null; // PO lama (readonly)
        $plant    = $data['PLANT'] ?? null;

        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if (empty($po) || empty($plant)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Key PO / PLANT tidak lengkap'
            ]);
            return;
        }

        // 🔐 Validasi akses
        if (!$this->Po_model->user_can_access_po($plant, $po, $username, $role_id)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Tidak punya hak update PO ini'
            ]);
            return;
        }

        // ================= TRANSACTION =================
        $this->db->trans_start();

        // ================= HEADER =================
        $header = [
            'PO_DATE'    => $data['PO_DATE'] ?? null,
            'PO_TYPE'    => $data['TYPE'] ?? null,
            'SUPPLIER'   => $data['SUPPLIER'] ?? null,
            'REMARK'     => $data['REMARK'] ?? null,
            'UPDATED_AT' => date('Y-m-d H:i:s'),
            'UPDATED_BY' => $username
        ];

        $updated = $this->Po_model->update_header_safe(
            $plant,
            $po,
            $header,
            $username,
            $role_id
        );

        if (!$updated) {
            $this->db->trans_rollback();
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal update header PO'
            ]);
            return;
        }

        // ================= DETAIL =================
        if (!empty($data['DETAIL']) && is_array($data['DETAIL'])) {

            $this->Po_model->replace_detail_safe(
                $plant,
                $po,
                $data['DETAIL'],
                $username
            );
        }

        $this->db->trans_complete();

        echo json_encode([
            'status'  => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'PO berhasil diperbarui'
                : 'Gagal update PO'
        ]);
    }

    public function remove()
    {
        $po       = $this->input->post('po', TRUE);
        $plant    = $this->input->post('plant', TRUE); // ← dari JS
        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if (!$po || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid key'
            ]);
            return;
        }

        $deleted = $this->Po_model->delete_po_safe(
            $plant,
            $po,
            $username,
            $role_id
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
            show_error('Parameter PO atau PLANT tidak lengkap');
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                p.PO,
                p.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                p.PO_DATE,
                p.SUPPLIER,
                s.FULL_NAME AS SUPPLIER_NAME,
                p.REMARK
            ')
            ->from('abc_mst_po p')
            ->join('cd_code aj', "aj.CODE = p.PLANT AND aj.HEAD_CODE = 'AJ'", 'left')
            ->join('cd_customer s', 's.CUST = p.SUPPLIER', 'left')
            ->where('p.PO', $po)
            ->where('p.PLANT', $plant)
            ->get()
            ->row();

        if (!$header) {
            show_error('PO tidak ditemukan');
        }

        /* ================= DETAIL ================= */
        $detail = $this->db
            ->select('
                d.SEQ_NO,
                d.MATERIAL,
                m.material_name AS MATERIAL_NAME,
                d.JUMLAH,
                d.BERAT,
                d.HARGA,
                d.TOTAL
            ')
            ->from('abc_mst_po_detail d')
            ->join('abc_cd_material m', 'm.material = d.MATERIAL', 'left')
            ->where('d.PO', $po)
            ->where('d.PLANT', $plant)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        $data = [
            'header' => $header,
            'detail' => $detail
        ];

        /* ================= PDF ================= */
        $html = $this->load->view('admin/po/pdf_template', $data, true);

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            "PO_{$po}.pdf",
            ['Attachment' => false] // preview di browser
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
