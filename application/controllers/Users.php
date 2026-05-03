<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('settings_users')) {
            show_404();
        }
        $this->load->model('Users_model');
        $this->load->library('session');
        $this->load->helper(['url','form']);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Users']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/users/list'); // konten dinamis
        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'username';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $start = ($page - 1) * $limit;

        $rows = $this->Users_model->get_data($limit, $start, $search, $order, $dir);
        $total = $this->Users_model->count_data($search);

        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page);

        echo json_encode([
            'rows' => $rows,
            'total' => $total,
            'pagination' => $pagination,
            'page' => $page
        ]);
    }

    private function build_pagination($pages, $current)
    {
        $html = '<ul class="pagination pagination-sm">';
        for ($i=1;$i<=$pages;$i++) {
            $active = ($i == $current) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'"><a href="javascript:void(0)" class="page-link" onclick="loadPage('.$i.')">'.$i.'</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function detail()
    {
        $id = $this->input->get('id', TRUE);
        if (!$id) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }
        $row = $this->Users_model->get_by_pk($id);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);

        if (
            empty($data['username']) ||
            empty($data['name']) ||
            empty($data['password']) ||
            !isset($data['plant']) ||
            !is_array($data['plant']) ||
            count($data['plant']) === 0
        ) {
            echo json_encode([
                'status'  => false,
                'message' => 'Username, Name, Password, dan Plant wajib diisi'
            ]);
            return;
        }

        // CEK USERNAME
        $exists = $this->db->where('username', $data['username'])->get('users')->row();
        if ($exists) {
            echo json_encode([
                'status'  => false,
                'message' => 'Username sudah ada'
            ]);
            return;
        }

        // UBAH ARRAY PLANT → JSON
        $data['plant'] = json_encode(array_values($data['plant']));

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // INSERT USER
        $ok = $this->db->insert('users', $data);

        echo json_encode([
            'status'  => $ok,
            'message' => $ok ? 'Data berhasil ditambahkan' : 'Gagal menambahkan data'
        ]);
    }

    public function edit()
    {
        $id = $this->input->get('id', TRUE);
        if (!$id) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }

        $row = $this->Users_model->get_by_pk($id);
        if (!$row) {
            echo json_encode(['status'=>false,'message'=>'Data tidak ditemukan']);
            return;
        }

        // decode plant JSON → array
        $plantCodes = json_decode($row->plant, true);
        if (!is_array($plantCodes)) {
            $plantCodes = [];
        }

        // ambil nama plant untuk select2
        $plantOptions = $this->Users_model->get_plants_by_codes($plantCodes);

        echo json_encode([
            'status' => true,
            'data'   => $row,
            'plants' => $plantOptions
        ]);
    }

    public function update()
    {
        $orig_id = (int)$this->input->post('orig_id', TRUE);
        if (!$orig_id) {
            echo json_encode(['status' => false, 'message' => 'Primary key missing']);
            return;
        }

        // whitelist field (WAJIB)
        $data = [
            'username' => trim($this->input->post('username', TRUE)),
            'name'     => trim($this->input->post('name', TRUE)),
            'plant'    => $this->input->post('plant', TRUE)
        ];

        // validasi dasar
        if ($data['username'] === '' || $data['name'] === '') {
            echo json_encode(['status' => false, 'message' => 'Data wajib tidak boleh kosong']);
            return;
        }

        // validasi JSON plant
        $plants = json_decode($data['plant'], true);
        if (!is_array($plants)) {
            echo json_encode(['status' => false, 'message' => 'Format plant tidak valid']);
            return;
        }

        // cek username unik (exclude diri sendiri)
        if ($this->Users_model->username_exists($data['username'], $orig_id)) {
            echo json_encode(['status' => false, 'message' => 'Username sudah digunakan']);
            return;
        }

        // password opsional
        $password = $this->input->post('password', TRUE);
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $ok = $this->Users_model->update($orig_id, $data);

        echo json_encode([
            'status'  => $ok,
            'message' => $ok ? 'Data berhasil diupdate' : 'Gagal update data'
        ]);
    }

    public function remove()
    {
        $id = $this->input->post('id', TRUE);
        if (!$id) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }

        $ok = $this->Users_model->delete($id);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Data dihapus' : 'Gagal menghapus']);
    }

    /**
     * AJAX: Ambil plant dari cd_code HEAD_CODE='AJ'
     */
    public function get_plant()
    {
        $term = $this->input->get('q');
        $this->db->select('CODE, DESC1')
                 ->from('cd_code')
                 ->where('HEAD_CODE','AJ');

        if(!empty($term)){
            $this->db->group_start();
            $this->db->like('CODE', $term);
            $this->db->or_like('DESC1', $term);
            $this->db->group_end();
        }

        $this->db->order_by('CODE','ASC');
        $query = $this->db->get()->result();

        $data = [];
        foreach($query as $row){
            $data[] = ['id'=>$row->CODE,'text'=>$row->CODE.' - '.$row->DESC1];
        }
        echo json_encode($data);
    }

}
