<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportClosingProcess extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('closing_process')) {
            show_404();
        }
        $this->load->model('ReportClosingProcess_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $plants = $this->ReportClosingProcess_model->get_plant_list();

        // Ambil plant dari session (JSON string)
        $userPlantRaw = $this->session->userdata('plant');

        $userPlants = json_decode($userPlantRaw, true);
        if (!is_array($userPlants)) {
            $userPlants = [$userPlantRaw]; // fallback kalau suatu saat jadi single
        }

        // Default = plant pertama user
        $defaultPlant = $userPlants[0] ?? '';

        $data = [
            'plants'       => $plants,
            'defaultPlant' => $defaultPlant,
            'userPlants'   => $userPlants
        ];

        $this->load->view('templates/header', ['title' => 'Closing Process']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_closing_process/index', $data);
        $this->load->view('templates/footer');
    }

    public function run_process()
    {
        $plant    = $this->input->post('plant', TRUE);
        $date     = $this->input->post('date', TRUE);
        $selected = $this->input->post('process');

        $userPlantRaw = $this->session->userdata('plant');
        $userPlants = json_decode($userPlantRaw, true);

        if (!is_array($userPlants)) {
            $userPlants = [$userPlantRaw];
        }

        // normalize ke string semua
        $userPlants = array_map('strval', $userPlants);
        $plant      = (string)$plant;

        if (!in_array($plant, $userPlants)) {
            echo json_encode([
                'status' => false,
                'logs'   => [['message' => 'Unauthorized plant access.', 'status' => 'error']]
            ]);
            return;
        }

        if (!$plant || !$date || empty($selected) || !is_array($selected)) {
            echo json_encode([
                'status' => false,
                'logs'   => [['message' => 'Parameter tidak lengkap.', 'status' => 'error']]
            ]);
            return;
        }

        // Format tanggal jadi Ymd
        try {
            $dt  = new DateTime($date);
            $ymd = $dt->format('Ymd');
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'logs'   => [['message' => 'Invalid date format.', 'status' => 'error']]
            ]);
            return;
        }

        $processOrder = [
            'inventory_closing'   => 'sp_cl_inv_price',
            'cost_closing'        => 'sp_cl_cost',
            'sales_pl_closing'    => 'sp_cl_sales_pl',
            'pl_closing'          => 'sp_cl_pl'
        ];

        $logs = [];

        foreach ($processOrder as $key => $spName) {
            if (in_array($key, $selected)) {

                $logs[] = [
                    'message' => "[$ymd] Start $key process...",
                    'status'  => 'process'
                ];

                try {
                    $query = $this->db->query("CALL $spName(?, ?)", [$plant, $ymd]);

                    if (!$query) {
                        $error = $this->db->error();
                        throw new Exception($error['message']);
                    }

                    // clear multi result
                    while ($this->db->conn_id->more_results()) {
                        $this->db->conn_id->next_result();
                    }

                    $logs[] = [
                        'message' => "[$ymd] $key success",
                        'status'  => 'success'
                    ];

                } catch (Exception $e) {
                    $logs[] = [
                        'message' => "[$ymd] $key failed: " . $e->getMessage(),
                        'status'  => 'error'
                    ];

                    echo json_encode([
                        'status' => false,
                        'logs'   => $logs
                    ]);
                    return;
                }
            }
        }

        echo json_encode([
            'status' => true,
            'logs'   => $logs
        ]);
    }

    /* =========================
     * PAGINATION BUILDER
     * ========================= */

    private function build_pagination($totalPages, $currentPage, $mode = 'url')
    {
        if ($totalPages <= 1) return '';

        $html = '<ul class="pagination pagination-sm mb-0">';

        for ($i = 1; $i <= $totalPages; $i++) {

            $active = $i == $currentPage ? 'active' : '';

            if ($mode === 'ajax') {
                $html .= "
                    <li class='page-item $active'>
                        <a href='#' class='page-link' data-page='$i'>$i</a>
                    </li>
                ";
            } else {
                $html .= "
                    <li class='page-item $active'>
                        <a href='?page=$i' class='page-link'>$i</a>
                    </li>
                ";
            }
        }

        $html .= '</ul>';

        return $html;
    }

    private function convert_date($date)
    {
        if (!$date) return null;

        // support yyyy-mm-dd (dari input date)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // support dd/mm/yyyy (kalau ada)
        $d = DateTime::createFromFormat('d/m/Y', $date);
        return $d ? $d->format('Y-m-d') : null;
    }

}
