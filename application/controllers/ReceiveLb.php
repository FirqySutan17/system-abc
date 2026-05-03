<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config['isRemoteEnabled'] = true;

class ReceiveLb extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        set_time_limit(30);
        if (!has_permission('inventory_receive_lb')) {
            show_404();
        }
        $this->load->model('ReceiveLb_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);

        $this->load->driver('cache', [
            'adapter' => 'file',
            'backup'  => 'file'
        ]);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Receive Live Bird']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/receive_lb/list');
        $this->load->view('templates/footer');
    }

    /**
     * Load data table (ajax)
     */
    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $limit  = max(1, min($limit, 50)); 
        $search = $this->input->get('search', TRUE);

        /* 🔒 ORDER WHITELIST */
        $allowedOrder = [
            'RECEIVE_DATE',
            'RECEIVE',
            'DO',
            'DRIVER',
            'PLANT',
            'CREATED_AT'
        ];

        $orderInput = $this->input->get('order', TRUE);
        $order = in_array($orderInput, $allowedOrder)
            ? $orderInput
            : 'RECEIVE_DATE';

        $dir = strtoupper($this->input->get('dir', TRUE)) === 'ASC'
            ? 'ASC'
            : 'DESC';

        $start = ($page - 1) * $limit;

        /* 🔹 USER LOGIN */
        $role_id  = (int)$this->session->userdata('role_id');
        $plants = $this->session->userdata('plant');

        if (is_string($plants)) {
            $plants = json_decode($plants, true);
        }

        if (!is_array($plants)) {
            $plants = [$plants];
        }

        $username = $this->session->userdata('username');
        $result = $this->ReceiveLb_model->get_data_with_total(
            $limit, $start, $role_id, $plants, $username, $search, $order, $dir
        );

        $rows  = $result['rows'];
        $total = $result['total'];

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
        $start = max(1, $current - 2);
        $end   = min($pages, $current + 2);

        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $current) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'">
                        <a href="javascript:void(0)" class="page-link" onclick="loadPage('.$i.')">'.$i.'</a>
                    </li>';
        }

        $html .= '</ul>';
        return $html;
    }

    public function get_supplier()
    {
        $term = $this->input->get('q');
        $cache_key = 'supplier_'.md5($term);

        if (!$data = $this->cache->get($cache_key)) {
            $data = $this->ReceiveLb_model->search_supplier($term);
            $this->cache->save($cache_key, $data, 300); // cache 5 menit
        }

        echo json_encode($data);
    }

    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');
        $cache_key = 'plant_select_'.$username;

        if (!$data = $this->cache->get($cache_key)) {
            $data = $this->ReceiveLb_model->get_plant_select2_by_user($username);
            $this->cache->save($cache_key, $data, 600);
        }

        echo json_encode([
            'results' => $data,
            'single'  => count($data) === 1
        ]);
    }

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);
        $username = $this->session->userdata('username');

        if (empty($data['RECEIVE_DATE'])) {
            echo json_encode(['status'=>false,'message'=>'Tanggal receive wajib diisi']);
            return;
        }

        if (empty($data['SUPPLIER'])) {
            echo json_encode(['status'=>false,'message'=>'Supplier wajib diisi']);
            return;
        }

        if (empty($data['PLANT'])) {
            echo json_encode(['status'=>false,'message'=>'Plant wajib diisi']);
            return;
        }

        $plant = $data['PLANT'];
        if (!$this->ReceiveLb_model->user_has_plant($username, $plant)) {
            echo json_encode([
                'status' => false,
                'message'=> 'Plant tidak diizinkan'
            ]);
            return;
        }

        $receiveNo = $this->ReceiveLb_model->generate_receive_no($plant);

        // Upload file attachment
        
        $file_data = $this->ReceiveLb_model->upload_file('ATTACHMENT', $plant, $receiveNo, $data['RECEIVE_DATE']);

        $attachment_name = $file_data['filename'] ?? null;
        $attachment_path = $file_data['path'] ?? null;

        $insert = [
            'PLANT'          => $plant,
            'RECEIVE'        => $receiveNo,
            'RECEIVE_DATE'   => date('Y-m-d H:i:s', strtotime($data['RECEIVE_DATE'])),

            // === radio ===
            'PEMBAYARAN'     => $data['PEMBAYARAN'] ?? null,
            'JENIS_PAY'      => $data['JENIS_PAY'] ?? null,

            // === optional text ===
            'SLIP_NO'        => $data['SLIP_NO'] ?? null,
            'DO'             => $data['DO'] ?? null,
            'DRIVER'         => $data['DRIVER'] ?? null,
            'NO_CAR'         => $data['NO_CAR'] ?? null,
            'SUPPLIER'       => $data['SUPPLIER'],
            'REMARK'         => $data['REMARK'] ?? null,

            // === datetime ===
            'ARRIVE_SCHEDULE'=> !empty($data['ARRIVE_SCHEDULE']) 
                                    ? date('Y-m-d H:i:s', strtotime($data['ARRIVE_SCHEDULE'])) 
                                    : null,
            'DEPART_SCHEDULE'=> !empty($data['DEPART_SCHEDULE']) 
                                    ? date('Y-m-d H:i:s', strtotime($data['DEPART_SCHEDULE'])) 
                                    : null,

            // === numeric ===
            'QTY'            => $this->ReceiveLb_model->normalize_number($data['QTY'] ?? 0),
            'WEIGHT'         => $this->ReceiveLb_model->normalize_number($data['WEIGHT'] ?? 0),
            'AVG_BW'         => $this->ReceiveLb_model->normalize_number($data['AVG_BW'] ?? 0),
            'DEAD'           => $this->ReceiveLb_model->normalize_number($data['DEAD'] ?? 0),
            'DEAD_WEIGHT'    => $this->ReceiveLb_model->normalize_number($data['DEAD_WEIGHT'] ?? 0),
            'SHRINK'         => $this->ReceiveLb_model->normalize_number($data['SHRINK'] ?? 0),
            'RECEIVE_AMOUNT' => $this->ReceiveLb_model->normalize_number($data['RECEIVE_AMOUNT'] ?? 0),
            'PRICE'          => $this->ReceiveLb_model->normalize_number($data['PRICE'] ?? 0),
            'AMOUNT'         => $this->ReceiveLb_model->normalize_number($data['AMOUNT'] ?? 0),

            // === ATTACHMENT ===
            'ATTACHMENT'            => $attachment_name ? 1 : null,
            'ATTACHMENT_NAME'       => $attachment_name,
            'ATTACHMENT_PATH'       => $attachment_path,
            'ATTACHMENT_UPLOADED_AT'=> $attachment_name ? date('Y-m-d H:i:s') : null,

            'CREATED_AT'     => date('Y-m-d H:i:s'),
            'CREATED_BY'     => $username
        ];

        $this->ReceiveLb_model->insert($insert);

        echo json_encode([
            'status'  => true,
            'receive' => $receiveNo,
            'message' => 'Receive Live Bird berhasil disimpan'
        ]);
    }

    public function edit()
    {
        $receive  = $this->input->get('receive', TRUE);
        $plant    = $this->input->get('plant', TRUE);
        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if (!$receive || !$plant) {
            echo json_encode(['status'=>false,'message'=>'Key missing']);
            return;
        }

        $header = $this->ReceiveLb_model->get_for_edit($plant, $receive, $username, $role_id);

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan / tidak punya akses'
            ]);
            return;
        }

        // format date untuk input[type=date] / datetime-local
        if (!empty($header['RECEIVE_DATE'])) {
            $header['RECEIVE_DATE'] = date('Y-m-d', strtotime($header['RECEIVE_DATE']));
        }
        if (!empty($header['ARRIVE_SCHEDULE'])) {
            $header['ARRIVE_SCHEDULE'] = date('Y-m-d\TH:i', strtotime($header['ARRIVE_SCHEDULE']));
        }
        if (!empty($header['DEPART_SCHEDULE'])) {
            $header['DEPART_SCHEDULE'] = date('Y-m-d\TH:i', strtotime($header['DEPART_SCHEDULE']));
        }

        echo json_encode([
            'status' => true,
            'header' => $header
        ]);
    }

    public function update()
    {
        $data = $_POST;
        $receive = $data['RECEIVE'] ?? null;
        $plant   = $data['PLANT'] ?? null;
        $username = $this->session->userdata('username');

        if (!$receive || !$plant) {
            echo json_encode(['status'=>false,'message'=>'Key missing']);
            return;
        }

        if (!$this->ReceiveLb_model->user_has_plant($username, $plant)) {
            echo json_encode(['status'=>false,'message'=>'Plant tidak diizinkan']);
            return;
        }

        $update = [
            'RECEIVE_DATE'   => $data['RECEIVE_DATE'],
            'PEMBAYARAN'     => $data['PEMBAYARAN'],
            'JENIS_PAY'      => $data['JENIS_PAY'],
            'SLIP_NO'        => $data['SLIP_NO'],
            'DO'             => $data['DO'],
            'DRIVER'         => $data['DRIVER'],
            'NO_CAR'         => $data['NO_CAR'],
            'SUPPLIER'       => $data['SUPPLIER'],
            'QTY'            => (float)$data['QTY'],
            'WEIGHT'         => (float)$data['WEIGHT'],
            'AVG_BW'         => (float)$data['AVG_BW'],
            'PRICE'          => (float)$data['PRICE'],
            'AMOUNT'         => (float)$data['AMOUNT'],
            'DEAD'           => (float)$data['DEAD'],
            'DEAD_WEIGHT'    => (float)$data['DEAD_WEIGHT'],
            'SHRINK'         => (float)$data['SHRINK'],
            'RECEIVE_AMOUNT' => (float)$data['RECEIVE_AMOUNT'],
            'REMARK'         => $data['REMARK'],
            'ARRIVE_SCHEDULE'=> $data['ARRIVE_SCHEDULE'],
            'DEPART_SCHEDULE'=> $data['DEPART_SCHEDULE'],
            'UPDATED_BY'     => $username,
            'UPDATED_AT'     => date('Y-m-d H:i:s')
        ];

        // upload attachment (aman karena plant tidak berubah)
        $file_upload = $this->ReceiveLb_model->upload_file(
            'ATTACHMENT',
            $plant,
            $receive,
            $data['RECEIVE_DATE']
        );
        if ($file_upload) {
            $update['ATTACHMENT_NAME'] = $file_upload['filename'];
            $update['ATTACHMENT_PATH'] = $file_upload['path'];
        }

        $this->ReceiveLb_model->update($plant, $receive, $update);

        echo json_encode([
            'status'  => $this->db->affected_rows() > 0,
            'message' => 'Receive LB berhasil diperbarui'
        ]);
    }

    public function remove()
    {
        header('Content-Type: application/json');

        $receive = $this->input->post('receive', TRUE);
        $plant   = $this->input->post('plant', TRUE);

        if (empty($receive) || empty($plant)) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid key'
            ]);
            return;
        }

        $deleted = $this->ReceiveLb_model->hard_delete($plant, $receive);

        echo json_encode([
            'status'  => $deleted,
            'message' => $deleted
                ? 'Receive LB berhasil dihapus'
                : 'Gagal menghapus Receive LB'
        ]);
    }

    public function print_pdf()
    {
        $receive = $this->input->get('receive');
        $plant   = $this->input->get('plant');

        if (!$receive || !$plant) {
            show_error('Parameter RECEIVE atau PLANT tidak lengkap');
        }

        // 🔥 Cache key unik
        $cache_key = 'pdf_header_' . $plant . '_' . $receive;

        $header = $this->cache->get($cache_key);

        if (!$header) {
            $header = $this->ReceiveLb_model->get_pdf_header($plant, $receive);

            if (!$header) {
                show_error('Receive LB tidak ditemukan');
            }

            // Cache 60 detik (cukup untuk hindari spam klik PDF)
            $this->cache->save($cache_key, $header, 60);
        }

        /* ================= ATTACHMENT URL ================= */
        $attachment_url = null;

        if (!empty($header->ATTACHMENT_PATH)) {
            $relative = ltrim($header->ATTACHMENT_PATH, './');
            $attachment_url = base_url($relative);
        }

        $data = [
            'header'         => $header,
            'attachment_url' => $attachment_url
        ];

        $html = $this->load->view('admin/receive_lb/pdf_template', $data, true);

        $this->load->library('pdf');
        $this->pdf->set_option('isRemoteEnabled', true);

        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            "RECEIVE_LB_{$receive}.pdf",
            ['Attachment' => false]
        );
    }

    /* ================= HELPER FORMAT ================= */

    function format_decimal_id($number, $dec = 2)
    {
        return number_format((float)$number, $dec, ',', '.');
    }

    function format_rupiah($number)
    {
        return number_format((float)$number, 0, ',', '.');
    }


}
