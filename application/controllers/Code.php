<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Code extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('base_general_code')) {
            show_404();
        }
        $this->load->model('Code_model');
        $this->load->library('session');
        $this->load->helper(['url','download','file']);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'General Code']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/code/list'); // konten dinamis

        $this->load->view('templates/footer');
    }

    public function get_head_code_panel()
    {
        $rows = $this->Code_model->get_head_star();
        echo json_encode($rows); // [{HEAD_CODE, CODE_NAME}, ...]
    }

    public function get_codes_by_head()
    {
        $head = $this->input->get('head', TRUE); // HEAD_CODE dari panel kiri
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $order  = $this->input->get('order', TRUE) ?: 'code';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $start = ($page - 1) * $limit;

        // Ambil data untuk HEAD_CODE tertentu
        $rows = $this->Code_model->get_data_by_head($head, $limit, $start, $order, $dir);
        $total = $this->Code_model->count_data_by_head($head);

        // pagination bootstrap
        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page);

        echo json_encode([
            'rows' => $rows,
            'total' => $total,
            'pagination' => $pagination,
            'page' => $page
        ]);
    }

    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'head_code';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $start = ($page - 1) * $limit;

        $rows = $this->Code_model->get_data($limit, $start, $search, $order, $dir);
        $total = $this->Code_model->count_data($search);

        // build pagination html (bootstrap)
        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page);

        // return JSON
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

    /**
     * AJAX: get detail (for modal)
     */
    public function detail()
    {
        $head = $this->input->get('head', TRUE);
        $code = $this->input->get('code', TRUE);
        if (!$head || !$code) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }
        $row = $this->Code_model->get_by_pk($head,$code);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    /**
     * AJAX: create new record
     */
    public function create()
    {
        $data = $this->input->post(NULL, TRUE);

        // Validasi wajib diisi
        if (empty($data['head_code']) || empty($data['code']) || empty($data['code_name'])) {
            echo json_encode(['status'=>false,'message'=>'head_code, code dan code_name wajib diisi']);
            return;
        }

        // Validasi UNIQUE: HEAD_CODE + CODE
        $exists = $this->db->where('head_code', $data['head_code'])
                        ->where('code', $data['code'])
                        ->get('cd_code')
                        ->row();

        if($exists){
            echo json_encode([
                'status' => false,
                'message' => 'Kombinasi HEAD_CODE + CODE sudah ada'
            ]);
            return;
        }

        // Insert ke DB
        $ok = $this->db->insert('cd_code', [
            'head_code' => $data['head_code'],
            'code'      => $data['code'],
            'code_name' => $data['code_name'],
            'desc1'     => $data['desc1'] ?? '',
            'use_yn'    => $data['use_yn'] ?? 'N'
        ]);

        echo json_encode([
            'status' => $ok,
            'message' => $ok ? 'Data berhasil ditambahkan' : 'Gagal menambahkan data'
        ]);
    }

    public function get_head_code(){
        $data = $this->db->select('HEAD_CODE as id, HEAD_CODE as text')
                        ->group_by('HEAD_CODE')
                        ->order_by('HEAD_CODE')
                        ->get('cd_code')
                        ->result_array();

        echo json_encode($data); // langsung array, tidak pakai 'data'
    }

    // Ambil CODE berdasarkan HEAD_CODE
    public function get_code_by_head(){
        $head = $this->input->get('head');
        $data = $this->db->select('CODE as id, CODE as text')
                        ->where('HEAD_CODE', $head)
                        ->order_by('CODE')
                        ->get('cd_code')
                        ->result_array();

        echo json_encode($data); // langsung array
    }

    public function edit()
    {
        $head = $this->input->get('head', TRUE);
        $code = $this->input->get('code', TRUE);
        if (!$head || !$code) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }
        $row = $this->Code_model->get_by_pk($head,$code);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    /**
     * AJAX: update
     */
    public function update()
    {
        $orig_head = $this->input->post('orig_head', TRUE);
        $orig_code = $this->input->post('orig_code', TRUE);

        if (!$orig_head || !$orig_code) {
            echo json_encode(['status'=>false,'message'=>'Primary key missing']);
            return;
        }

        $data = $this->input->post(NULL, TRUE);
        // remove originals from data array
        unset($data['orig_head'],$data['orig_code']);

        $ok = $this->Code_model->update($orig_head, $orig_code, $data);
        echo json_encode(['status'=>$ok, 'message' => $ok ? 'Data berhasil diupdate' : 'Gagal update data']);
    }

    /**
     * AJAX: delete
     */
    public function remove()
    {
        $head = $this->input->post('head', TRUE);
        $code = $this->input->post('code', TRUE);
        if (!$head || !$code) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }
        $ok = $this->Code_model->delete($head,$code);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Data dihapus' : 'Gagal menghapus']);
    }

    /**
     * Export Excel (all filtered data)
     * Requires phpoffice/phpspreadsheet via composer
     */
    public function export_excel()
    {
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'head_code';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $rows = $this->Code_model->get_all($search, $order, $dir);

        // load PhpSpreadsheet
        // composer require phpoffice/phpspreadsheet
        $this->load->library('Spreadsheet_loader'); // see instructions below
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // headers
        $sheet->fromArray(['HEAD_CODE','CODE','CODE_NAME','DESC1','DESC2','USE_YN'], NULL, 'A1');

        $r = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue('A'.$r, $row->head_code);
            $sheet->setCellValue('B'.$r, $row->code);
            $sheet->setCellValue('C'.$r, $row->code_name);
            $sheet->setCellValue('D'.$r, $row->desc1);
            $sheet->setCellValue('E'.$r, $row->desc2);
            $sheet->setCellValue('F'.$r, $row->use_yn);
            $r++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'cd_code_export_'.date('Ymd_His').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    /**
     * Export PDF (filtered)
     * Requires dompdf/dompdf via composer
     */
    public function export_pdf()
    {
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'head_code';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $rows = $this->Code_model->get_all($search, $order, $dir);

        // create html
        $html = '<h3>CD CODE Export</h3><table border="1" cellpadding="5" cellspacing="0" width="100%">
                <thead><tr><th>HEAD_CODE</th><th>CODE</th><th>CODE_NAME</th><th>DESC1</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            $html .= '<tr>';
            $html .= '<td>'.htmlspecialchars($r->head_code).'</td>';
            $html .= '<td>'.htmlspecialchars($r->code).'</td>';
            $html .= '<td>'.htmlspecialchars($r->code_name).'</td>';
            $html .= '<td>'.htmlspecialchars($r->desc1).'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        // load dompdf (composer require dompdf/dompdf)
        $this->load->library('Pdf_loader'); // see instructions below
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','landscape');
        $dompdf->render();
        $dompdf->stream('cd_code_export_'.date('Ymd_His').'.pdf', ['Attachment'=>1]);
    }
}
