<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('base_item')) {
            show_404();
        }
        $this->load->model('Item_model');
        $this->load->library(['session','form_validation']);
        $this->load->helper(['url','download','file','form']);
        
    }

    public function index()
    {
        $this->load->model('Code_model');

        $data['goods'] = $this->Code_model->get_by_head('AA');
        $data['sex'] = $this->Code_model->get_by_head('AB');
        $data['price_class'] = $this->Code_model->get_by_head('AC');
        $data['packing_unit'] = $this->Code_model->get_by_head('AD');

        $this->load->view('templates/header', ['title' => 'Item']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/item/list', $data);

        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $page = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $order = $this->input->get('order', TRUE) ?: 'item';
        $dir = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';
        $start = ($page - 1) * $limit;

        $rows = $this->Item_model->get_data($limit, $start, $search, $order, $dir);
        $total = $this->Item_model->count_data($search);
        $pages = $total ? ceil($total / $limit) : 1;

        $pagination = $this->build_pagination($pages, $page);

        echo json_encode(['rows' => $rows, 'total' => $total, 'pagination' => $pagination, 'page' => $page]);
    }

    private function build_pagination($pages, $current)
    {
        $html = '<ul class="pagination pagination-sm mb-0">';
        for ($i=1;$i<=$pages;$i++) {
            $active = ($i == $current) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'"><a href="javascript:void(0)" class="page-link" onclick="loadPage('.$i.')">'.$i.'</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function get_goods()
    {
        $term = $this->input->get('q'); // jika ada pencarian dari Select2

        $this->db->select('CODE_NAME as id, CODE_NAME as text')
                ->from('cd_code')
                ->where('HEAD_CODE', 'AA')
                ->where('CODE !=', '*');

        if (!empty($term)) {
            $this->db->like('CODE_NAME', $term);
        }

        $this->db->group_by('CODE_NAME')
                ->order_by('CODE_NAME', 'ASC');

        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    public function get_sexs()
    {
        $term = $this->input->get('q'); // jika ada pencarian dari Select2

        $this->db->select('CODE_NAME as id, CODE_NAME as text')
                ->from('cd_code')
                ->where('HEAD_CODE', 'AB')
                ->where('CODE !=', '*');

        if (!empty($term)) {
            $this->db->like('CODE_NAME', $term);
        }

        $this->db->group_by('CODE_NAME')
                ->order_by('CODE_NAME', 'ASC');

        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    public function get_price_classes()
    {
        $term = $this->input->get('q'); // jika ada pencarian dari Select2

        $this->db->select('CODE_NAME as id, CODE_NAME as text')
                ->from('cd_code')
                ->where('HEAD_CODE', 'AC')
                ->where('CODE !=', '*');

        if (!empty($term)) {
            $this->db->like('CODE_NAME', $term);
        }

        $this->db->group_by('CODE_NAME')
                ->order_by('CODE_NAME', 'ASC');

        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    public function get_packing_units()
    {
        $term = $this->input->get('q'); // jika ada pencarian dari Select2

        $this->db->select('CODE_NAME as id, CODE_NAME as text')
                ->from('cd_code')
                ->where('HEAD_CODE', 'AD')
                ->where('CODE !=', '*');

        if (!empty($term)) {
            $this->db->like('CODE_NAME', $term);
        }

        $this->db->group_by('CODE_NAME')
                ->order_by('CODE_NAME', 'ASC');

        $data = $this->db->get()->result_array();

        echo json_encode($data);
    }

    public function create()
    {
        // server-side validation
        $this->form_validation->set_rules('item', 'Item', 'required|max_length[50]');
        $this->form_validation->set_rules('full_name', 'Full Name', 'required|max_length[255]');
        $this->form_validation->set_rules('packing_qty', 'Packing Qty', 'trim|numeric');
        $this->form_validation->set_rules('price_goods', 'Price Goods', 'trim|numeric');
        $this->form_validation->set_rules('price_delivery', 'Price Delivery', 'trim|numeric');
        $this->form_validation->set_rules('price_vaccine', 'Price Vaccine', 'trim|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status'=>false,'message'=>trim(strip_tags(validation_errors()))]);
            return;
        }

        $data = [
            'item' => $this->input->post('item', TRUE),
            'plant'   => $this->session->userdata('plant'),
            'full_name' => $this->input->post('full_name', TRUE),
            'goods' => $this->input->post('goods', TRUE),
            'sex' => $this->input->post('sex', TRUE),
            'price_class' => $this->input->post('price_class', TRUE),
            'packing_unit' => $this->input->post('packing_unit', TRUE),
            'packing_qty' => $this->input->post('packing_qty', TRUE) ?: 0,
            'price_goods' => $this->input->post('price_goods', TRUE) ?: 0,
            'price_delivery' => $this->input->post('price_delivery', TRUE) ?: 0,
            'price_vaccine' => $this->input->post('price_vaccine', TRUE) ?: 0,
            'acc_dr' => $this->input->post('acc_dr', TRUE),
            'acc_cr' => $this->input->post('acc_cr', TRUE),
            'remark' => $this->input->post('remark', TRUE)
        ];

        // duplicate check
        if ($this->Item_model->get_by_pk($data['item'])) {
            echo json_encode(['status'=>false,'message'=>'Item sudah ada']);
            return;
        }

        $ok = $this->Item_model->insert($data);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Data berhasil ditambahkan' : 'Gagal menambahkan data']);
    }

    public function edit()
    {
        $item = $this->input->get('item', TRUE);
        if (!$item) { echo json_encode(['status'=>false,'message'=>'Key invalid']); return; }
        $row = $this->Item_model->get_by_pk($item);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    public function update()
    {
        $orig = $this->input->post('orig_item', TRUE);
        if (!$orig) { echo json_encode(['status'=>false,'message'=>'Primary key missing']); return; }

        // validation
        $this->form_validation->set_rules('item', 'Item', 'required|max_length[50]');
        $this->form_validation->set_rules('full_name', 'Full Name', 'required|max_length[255]');
        $this->form_validation->set_rules('packing_qty', 'Packing Qty', 'numeric');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status'=>false,'message'=>trim(strip_tags(validation_errors()))]);
            return;
        }

        $data = [
            'item' => $this->input->post('item', TRUE),
            'full_name' => $this->input->post('full_name', TRUE),
            'goods' => $this->input->post('goods', TRUE),
            'sex' => $this->input->post('sex', TRUE),
            'price_class' => $this->input->post('price_class', TRUE),
            'packing_unit' => $this->input->post('packing_unit', TRUE),
            'packing_qty' => $this->input->post('packing_qty', TRUE) ?: 0,
            'price_goods' => $this->input->post('price_goods', TRUE) ?: 0,
            'price_delivery' => $this->input->post('price_delivery', TRUE) ?: 0,
            'price_vaccine' => $this->input->post('price_vaccine', TRUE) ?: 0,
            'acc_dr' => $this->input->post('acc_dr', TRUE),
            'acc_cr' => $this->input->post('acc_cr', TRUE),
            'remark' => $this->input->post('remark', TRUE)
        ];

        $ok = $this->Item_model->update($orig, $data);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Data berhasil diupdate' : 'Gagal update data']);
    }

    public function remove()
    {
        $item = $this->input->post('item', TRUE);
        if (!$item) { echo json_encode(['status'=>false,'message'=>'Key invalid']); return; }
        $ok = $this->Item_model->delete($item);
        echo json_encode(['status'=>$ok,'message'=>$ok ? 'Data dihapus' : 'Gagal menghapus']);
    }

    // Export Excel using PhpSpreadsheet
    public function export_excel()
    {
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'item';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';
        $rows = $this->Item_model->get_all($search, $order, $dir);

        // require composer autoload via small loader library
        $this->load->library('Spreadsheet_loader');
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // headers
        $headers = ['Item','Full Name','Goods','Sex','Price Class','Packing Unit','Packing Qty','Price Goods','Price Delivery','Price Vaccine','Acc Dr','Acc Cr','Remark'];
        $sheet->fromArray($headers, NULL, 'A1');

        $r = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue('A'.$r, $row->item);
            $sheet->setCellValue('B'.$r, $row->full_name);
            $sheet->setCellValue('C'.$r, $row->goods);
            $sheet->setCellValue('D'.$r, $row->sex);
            $sheet->setCellValue('E'.$r, $row->price_class);
            $sheet->setCellValue('F'.$r, $row->packing_unit);
            $sheet->setCellValue('G'.$r, $row->packing_qty);
            $sheet->setCellValue('H'.$r, $row->price_goods);
            $sheet->setCellValue('I'.$r, $row->price_delivery);
            $sheet->setCellValue('J'.$r, $row->price_vaccine);
            $sheet->setCellValue('K'.$r, $row->acc_dr);
            $sheet->setCellValue('L'.$r, $row->acc_cr);
            $sheet->setCellValue('M'.$r, $row->remark);
            $r++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'abc_cd_item_'.date('Ymd_His').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    // Export PDF (styled header) using Dompdf
    public function export_pdf()
    {
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'item';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';
        $rows = $this->Item_model->get_all($search, $order, $dir);

        $html = '<h3>CD ITEM Export</h3>';
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $html .= '<thead>
                    <tr style="background:#2f75b5;color:#fff;">
                      <th style="padding:6px;border:1px solid #ddd;">Item</th>
                      <th style="padding:6px;border:1px solid #ddd;">Full Name</th>
                      <th style="padding:6px;border:1px solid #ddd;">Goods</th>
                      <th style="padding:6px;border:1px solid #ddd;">Sex</th>
                      <th style="padding:6px;border:1px solid #ddd;">Price Goods</th>
                      <th style="padding:6px;border:1px solid #ddd;">Remark</th>
                    </tr>
                  </thead><tbody>';

        foreach ($rows as $r) {
            $html .= '<tr>';
            $html .= '<td style="padding:6px;border:1px solid #ddd;">'.htmlspecialchars($r->item).'</td>';
            $html .= '<td style="padding:6px;border:1px solid #ddd;">'.htmlspecialchars($r->full_name).'</td>';
            $html .= '<td style="padding:6px;border:1px solid #ddd;">'.htmlspecialchars($r->goods).'</td>';
            $html .= '<td style="padding:6px;border:1px solid #ddd;">'.htmlspecialchars($r->sex).'</td>';
            $html .= '<td style="padding:6px;border:1px solid #ddd;text-align:right;">'.number_format($r->price_goods,2).'</td>';
            $html .= '<td style="padding:6px;border:1px solid #ddd;">'.htmlspecialchars($r->remark).'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $this->load->library('Pdf_loader');
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','landscape');
        $dompdf->render();
        $dompdf->stream('abc_cd_item_'.date('Ymd_His').'.pdf', ['Attachment'=>1]);
    }
}
