<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!has_permission('base_customer')) {
            show_404();
        }
        $this->load->model('Customer_model', 'm');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Customer']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/customer/list'); 
        $this->load->view('templates/footer');
    }

    // ======================
    // LOAD DATA AJAX
    // ======================
    public function load_data() {
        $page  = intval($this->input->get('page'));
        $limit = intval($this->input->get('limit'));
        $order = $this->input->get('order');
        $dir   = $this->input->get('dir');
        $search = $this->input->get('search');

        $offset = ($page - 1) * $limit;

        $rows  = $this->m->get_data($limit, $offset, $search, $order, $dir);
        $total = $this->m->count_data($search);

        $pages = ceil($total / $limit);
        $pagination = '<nav><ul class="pagination justify-content-end">';
        for ($i=1; $i <= $pages; $i++) {
            $active = $i == $page ? 'active' : '';
            $pagination .= "<li class='page-item $active'>
                                <a class='page-link' href='javascript:loadPage($i)'>$i</a>
                            </li>";
        }
        $pagination .= '</ul></nav>';

        echo json_encode(['rows' => $rows,'total' => $total,'pagination' => $pagination]);
    }

    // ======================
    // DETAIL
    // ======================
    public function detail() {
        $id = $this->input->get('cust');
        $row = $this->m->get_by_pk($id);
        if (!$row) {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
            return;
        }
        echo json_encode(['status' => true, 'data' => $row]);
    }

    public function get_class()
    {
        $term = $this->input->get('q'); // jika ada pencarian dari Select2

        $this->db->select('CODE_NAME as id, CODE_NAME as text')
                ->from('cd_code')
                ->where('HEAD_CODE', 'AG')
                ->where('CODE !=', '*');

        if (!empty($term)) {
            $this->db->like('CODE_NAME', $term);
        }

        $this->db->group_by('CODE_NAME')
                ->order_by('CODE_NAME', 'ASC');

        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    public function get_kinds()
    {
        $term = $this->input->get('q'); // jika ada pencarian dari Select2

        $this->db->select('CODE_NAME as id, CODE_NAME as text')
                ->from('cd_code')
                ->where('HEAD_CODE', 'AH')
                ->where('CODE !=', '*');

        if (!empty($term)) {
            $this->db->like('CODE_NAME', $term);
        }

        $this->db->group_by('CODE_NAME')
                ->order_by('CODE_NAME', 'ASC');

        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    public function get_banks()
    {
        $term = $this->input->get('q'); // jika ada pencarian dari Select2

        $this->db->select('CODE_NAME as id, CODE_NAME as text')
                ->from('cd_code')
                ->where('HEAD_CODE', 'AI')
                ->where('CODE !=', '*');

        if (!empty($term)) {
            $this->db->like('CODE_NAME', $term);
        }

        $this->db->group_by('CODE_NAME')
                ->order_by('CODE_NAME', 'ASC');

        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    // ======================
    // CREATE
    // ======================
    public function create() {
        $this->form_validation->set_rules('full_name', 'Full Name', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status'=>false,'message'=>trim(strip_tags(validation_errors()))]);
            return;
        }

        $cust_kind  = $this->input->post('cust_kind', TRUE);
        $cust_class = $this->input->post('cust_class', TRUE);

        // generate otomatis
        $cust = $this->m->generate_cust($cust_kind, $cust_class);

        $data = [
            'CUST' => $cust,
            'FULL_NAME' => $this->input->post('full_name', TRUE),
            'CUST_KIND' => $cust_kind,
            'CUST_CLASS' => $cust_class,
            'CHIEF_NAME' => $this->input->post('chief_name', TRUE),
            'MOBILE_PHONE' => $this->input->post('mobile_phone', TRUE),
            'ADDRESS1' => $this->input->post('address1', TRUE),
            'BANK_CODE' => $this->input->post('bank_code', TRUE),
            'BANK_ACCOUNT' => $this->input->post('bank_account', TRUE),
            'OWNER_PICTURE' => $this->input->post('owner_picture', TRUE),
            'REMARK' => $this->input->post('remark', TRUE),
            'STATUS' => $this->input->post('status', TRUE),
        ];

        if ($this->m->get_by_pk($data['CUST'])) {
            echo json_encode(['status'=>false,'message'=>'Customer sudah ada']);
            return;
        }

        $ok = $this->m->insert($data);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Berhasil menambahkan data.' : 'Gagal menambahkan data.']);
    }

    // ======================
    // EDIT
    // ======================
    public function edit() {
        $cust = $this->input->get('cust', TRUE);
        if (!$cust) {
            echo json_encode(['status'=>false,'message'=>'Key invalid']);
            return;
        }

        $row = $this->m->get_by_pk($cust);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    public function update() {
        $orig_cust = $this->input->post('orig_cust', TRUE);
        if (!$orig_cust) {
            echo json_encode(['status'=>false,'message'=>'Primary key missing']);
            return;
        }

        // Validation
        $this->form_validation->set_rules('cust_edit', 'Customer', 'required');
        $this->form_validation->set_rules('full_name_edit', 'Full Name', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status'=>false,'message'=>trim(strip_tags(validation_errors()))]);
            return;
        }

        $data = [
            'CUST'          => $this->input->post('cust_edit', TRUE),
            'FULL_NAME'     => $this->input->post('full_name_edit', TRUE),
            'CUST_KIND'     => $this->input->post('cust_kind_edit', TRUE),
            'CUST_CLASS'    => $this->input->post('cust_class_edit', TRUE),
            'CHIEF_NAME'    => $this->input->post('chief_name_edit', TRUE),
            'MOBILE_PHONE'  => $this->input->post('mobile_phone_edit', TRUE),
            'ADDRESS1'      => $this->input->post('address1_edit', TRUE),
            'BANK_CODE'     => $this->input->post('bank_code_edit', TRUE),
            'BANK_ACCOUNT'  => $this->input->post('bank_account_edit', TRUE),
            'OWNER_PICTURE' => $this->input->post('owner_picture_edit', TRUE),
            'REMARK'        => $this->input->post('remark_edit', TRUE),
            'STATUS'        => $this->input->post('status_edit', TRUE)
        ];

        $ok = $this->m->update($orig_cust, $data);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Data berhasil diupdate' : 'Gagal update data']);
    }


    // ======================
    // DELETE
    // ======================
    public function remove() {
        $id = $this->input->post('cust');
        $ok = $this->m->delete($id);
        echo json_encode(['status' => $ok, 'message' => $ok ? 'Data berhasil dihapus.' : 'Gagal menghapus data.']);
    }
}