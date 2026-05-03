<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Material extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!has_permission('base_material')) {
            show_404();
        }
        $this->load->model('Material_model', 'm');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Material']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/material/list'); // konten dinamis

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

        // generate pagination
        $pages = ceil($total / $limit);

        $pagination = '<nav><ul class="pagination justify-content-end">';
        for ($i=1; $i <= $pages; $i++) {
            $active = $i == $page ? 'active' : '';
            $pagination .= "
                <li class='page-item $active'>
                    <a class='page-link' href='javascript:loadPage($i)'>$i</a>
                </li>";
        }
        $pagination .= '</ul></nav>';

        echo json_encode([
            'rows' => $rows,
            'total' => $total,
            'pagination' => $pagination
        ]);
    }

    // ======================
    // DETAIL
    // ======================
    public function detail() {
        $id = $this->input->get('material');

        $row = $this->m->detail($id);
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
                ->where('HEAD_CODE', 'AE')
                ->where('CODE !=', '*');

        if (!empty($term)) {
            $this->db->like('CODE_NAME', $term);
        }

        $this->db->group_by('CODE_NAME')
                ->order_by('CODE_NAME', 'ASC');

        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    public function get_grades()
    {
        $term = $this->input->get('q'); // jika ada pencarian dari Select2

        $this->db->select('CODE_NAME as id, CODE_NAME as text')
                ->from('cd_code')
                ->where('HEAD_CODE', 'AF')
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
        // server-side validation
        $this->form_validation->set_rules('material', 'Material', 'required|max_length[50]');
        $this->form_validation->set_rules('material_name', 'Material Name', 'required|max_length[255]');
        $this->form_validation->set_rules('class', 'Class', 'required');
        $this->form_validation->set_rules('grade', 'Grade', 'required');
        $this->form_validation->set_rules('stock', 'Stock', 'numeric');
        $this->form_validation->set_rules('htc', 'HTC', 'numeric');
        $this->form_validation->set_rules('unit_price', 'Unit Price', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status'=>false,'message'=>trim(strip_tags(validation_errors()))]);
            return;
        }

        $data = [
            'plant'   => $this->session->userdata('plant'),
            'material' => $this->input->post('material', TRUE),
            'material_name' => $this->input->post('material_name', TRUE),
            'material_class' => $this->input->post('class', TRUE),
            'grade' => $this->input->post('grade', TRUE),
            'stock' => $this->input->post('stock', TRUE) ?: 0,
            'htc' => $this->input->post('htc', TRUE) ?: 0,
            'mat_spec' => $this->input->post('mat_spec', TRUE),
            'mat_unit' => $this->input->post('mat_unit', TRUE),
            'unit_price' => $this->input->post('unit_price', TRUE) ?: 0,
            'remark' => $this->input->post('remark', TRUE)
        ];

        // duplicate check
        if ($this->m->get_by_pk($data['material'])) {
            echo json_encode(['status'=>false,'message'=>'Material sudah ada']);
            return;
        }

        $ok = $this->m->insert($data);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Berhasil menambahkan data.' : 'Gagal menambahkan data.']);
    }


    public function edit() {
        $material = $this->input->get('material', TRUE);
        if (!$material) {
            echo json_encode(['status'=>false,'message'=>'Key invalid']);
            return;
        }

        $row = $this->m->get_by_pk($material);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    public function update() {
        $orig_material = $this->input->post('orig_material', TRUE);
        if (!$orig_material) {
            echo json_encode(['status'=>false,'message'=>'Primary key missing']);
            return;
        }

        // validation
        $this->form_validation->set_rules('material', 'Material', 'required|max_length[50]');
        $this->form_validation->set_rules('material_name', 'Material Name', 'required|max_length[255]');
        $this->form_validation->set_rules('class', 'Class', 'required');
        $this->form_validation->set_rules('grade', 'Grade', 'required');
        $this->form_validation->set_rules('stock', 'Stock', 'numeric');
        $this->form_validation->set_rules('htc', 'HTC', 'numeric');
        $this->form_validation->set_rules('unit_price', 'Unit Price', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status'=>false,'message'=>trim(strip_tags(validation_errors()))]);
            return;
        }

        $data = [
            'material' => $this->input->post('material', TRUE),
            'material_name' => $this->input->post('material_name', TRUE),
            'material_class' => $this->input->post('class', TRUE),
            'grade' => $this->input->post('grade', TRUE),
            'stock' => $this->input->post('stock', TRUE) ?: 0,
            'htc' => $this->input->post('htc', TRUE) ?: 0,
            'mat_spec' => $this->input->post('mat_spec', TRUE),
            'mat_unit' => $this->input->post('mat_unit', TRUE),
            'unit_price' => $this->input->post('unit_price', TRUE) ?: 0,
            'remark' => $this->input->post('remark', TRUE)
        ];

        $ok = $this->m->update($orig_material, $data);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Data berhasil diupdate' : 'Gagal update data']);
    }


    // ======================
    // DELETE
    // ======================
    public function remove() {
        $id = $this->input->post('material');

        $ok = $this->m->delete($id);

        echo json_encode([
            'status' => $ok,
            'message' => $ok ? 'Data berhasil dihapus.' : 'Gagal menghapus data.'
        ]);
    }

    // ======================
    // EXPORT EXCEL
    // ======================
    public function export_excel() {
        $search = $this->input->get('search');
        $order  = $this->input->get('order');
        $dir    = $this->input->get('dir');

        $data['rows'] = $this->m->export($search, $order, $dir);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=material_export.xls");
        $this->load->view('material/export_excel', $data);
    }

    // ======================
    // EXPORT PDF
    // ======================
    public function export_pdf() {
        $search = $this->input->get('search');
        $order  = $this->input->get('order');
        $dir    = $this->input->get('dir');

        $data['rows'] = $this->m->export($search, $order, $dir);

        $this->load->library('pdf');

        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->load_view('material/export_pdf', $data);
        $this->pdf->render();
        $this->pdf->stream("material.pdf", ["Attachment" => 1]);
    }
}
