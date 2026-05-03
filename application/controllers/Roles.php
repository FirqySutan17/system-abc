<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('settings_roles')) {
            show_404();
        }
        $this->load->model('Roles_model');
        $this->load->library('session');
        $this->load->helper(['url','form']);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Role & Permission']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/role/list');
        $this->load->view('templates/footer');
    }

    /* ===================== LOAD DATA ===================== */

    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'role_name';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $start = ($page - 1) * $limit;

        $rows  = $this->Roles_model->get_data($limit, $start, $search, $order, $dir);
        $total = $this->Roles_model->count_data($search);

        $pages = ceil($total / $limit);

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'pagination' => $this->build_pagination($pages, $page),
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

    /* ===================== DETAIL ===================== */

    public function detail()
    {
        $id = $this->input->get('id', TRUE);
        if (!$id) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }

        $row = $this->Roles_model->get_by_pk($id);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    /* ===================== CREATE ===================== */

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);

        if (empty($data['role_name'])) {
            echo json_encode([
                'status'=>false,
                'message'=>'Role Name wajib diisi'
            ]);
            return;
        }

        // unique validation
        $exists = $this->db->where('role_name', $data['role_name'])->get('roles')->row();
        if ($exists) {
            echo json_encode(['status'=>false,'message'=>'Role sudah ada']);
            return;
        }

        $ok = $this->Roles_model->insert($data);

        echo json_encode([
            'status'  => $ok,
            'message' => $ok ? 'Role berhasil ditambahkan' : 'Gagal menambahkan role'
        ]);
    }

    /* ===================== EDIT ===================== */

    public function edit()
    {
        $id = $this->input->get('id', TRUE);
        if (!$id) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }

        $row = $this->Roles_model->get_by_pk($id);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    /* ===================== UPDATE ===================== */

    public function update()
    {
        $orig_id = $this->input->post('orig_id', TRUE);
        if (!$orig_id) {
            echo json_encode(['status'=>false,'message'=>'Primary key missing']);
            return;
        }

        $data = $this->input->post(NULL, TRUE);
        unset($data['orig_id']);

        $ok = $this->Roles_model->update($orig_id, $data);

        echo json_encode([
            'status'  => $ok,
            'message' => $ok ? 'Role berhasil diupdate' : 'Gagal update role'
        ]);
    }

    /* ===================== DELETE ===================== */

    public function remove()
    {
        $id = $this->input->post('id', TRUE);
        if (!$id) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }

        $ok = $this->Roles_model->delete($id);

        echo json_encode([
            'status'=>$ok,
            'message'=>$ok ? 'Role dihapus' : 'Gagal menghapus role'
        ]);
    }

    /* ===================== PERMISSION ===================== */

    public function get_permission()
    {
        $role_id = $this->input->get('id', TRUE);
        if (!$role_id) {
            echo json_encode(['status'=>false,'message'=>'Role ID missing']);
            return;
        }

        $menus = $this->Roles_model->get_permissions(); // array of objects
        $role_menu = $this->Roles_model->get_role_permissions($role_id); // array of permission_id

        $permissions = [];
        foreach($menus as $m){
            $permissions[] = [
                'menu_id'   => $m->id,
                'menu_name' => $m->permission_name, // <-- sesuaikan dengan tabel permissions
                'checked'   => in_array($m->id, $role_menu)
            ];
        }

        echo json_encode(['status'=>true,'permissions'=>$permissions]);
    }

    public function permission()
    {
        $role_id = $this->input->get('role_id', TRUE);
        if(!$role_id){
            echo json_encode(['status'=>false,'message'=>'Role ID missing']);
            return;
        }

        $data = [
            'role'        => $this->Roles_model->get_by_pk($role_id),
            'menu'        => $this->Roles_model->get_permissions(),
            'role_menu'  => $this->Roles_model->get_role_permissions($role_id)
        ];

        $this->load->view('admin/role/permission', $data);
    }

    public function save_permission()
    {
        $role_id = $this->input->post('role_id', TRUE);
        $menus   = $this->input->post('permissions'); // ambil array checkbox

        if(!$role_id){
            echo json_encode(['status'=>false,'message'=>'Role ID missing']);
            return;
        }

        $ok = $this->Roles_model->save_permissions($role_id, $menus);

        echo json_encode([
            'status'  => $ok,
            'message' => $ok ? 'Permission berhasil disimpan' : 'Gagal menyimpan permission'
        ]);
    }

}
