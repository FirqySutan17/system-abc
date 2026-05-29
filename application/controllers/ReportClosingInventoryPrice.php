<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportClosingInventoryPrice extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('closing_inventory_price')) {
            show_404();
        }
        $this->load->model('ReportClosingInventoryPrice_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data = [
            'plants' => $this->ReportClosingInventoryPrice_model->get_plant_list()
        ];

        $this->load->view('templates/header', [
            'title' => 'Report Closing Inventory Price'
        ]);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_closing_inventory_price/index', $data);
        $this->load->view('templates/footer');
    }

    public function load_daily_inventory_price()
    {
        header('Content-Type: application/json');

        $page  = max((int)$this->input->get('page'), 1);
        $limit = max((int)$this->input->get('limit'), 1);

        $order = $this->input->get('order', TRUE) ?: 'material';
        $dir   = strtoupper($this->input->get('dir') ?? 'ASC');
        $dir   = ($dir === 'DESC') ? 'DESC' : 'ASC';

        $allowedOrder = ['plant','material'];
        if (!in_array($order, $allowedOrder)) {
            $order = 'material';
        }

        /* =========================
        🔐 PLANT SECURITY
        ========================== */

        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants)) {
            $userPlants = [$this->session->userdata('plant')];
        }

        $plantInput = $this->input->get('plant', TRUE);

        // jika kosong → semua plant milik user
        if (!$plantInput) {
            $plantFilter = $userPlants;
        } else {
            // validasi injection
            if (!in_array($plantInput, $userPlants)) {
                echo json_encode([
                    'rows'=>[],
                    'total'=>0,
                    'grand'=>[],
                    'pagination'=>''
                ]);
                return;
            }
            $plantFilter = [$plantInput];
        }

        /* =========================
        📅 DATE FILTER (1 DAY)
        ========================== */

        $date = $this->input->get('date', TRUE);

        if (!$date) {
            echo json_encode(['rows'=>[],'total'=>0,'grand'=>[],'pagination'=>'']);
            return;
        }

        $ymd = str_replace('-', '', $date);

        $filters = [
            'plants' => $plantFilter,
            'ymd'    => $ymd
        ];

        $start = ($page - 1) * $limit;

        /* =========================
        DATA
        ========================== */

        $rows = $this->ReportClosingInventoryPrice_model
            ->get_daily_inventory_price($limit, $start, $filters, $order, $dir);

        $totalRows = $this->ReportClosingInventoryPrice_model
            ->count_daily_inventory_price($filters);

        $grandRow = $this->ReportClosingInventoryPrice_model
            ->get_daily_inventory_price_grand($filters);

        $grand = (array)$grandRow;

        $pages = $limit ? ceil($totalRows / $limit) : 1;
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $totalRows,
            'grand'      => $grand,
            'pagination' => $pagination,
            'page'       => $page
        ]);
    }

    public function export_excel_daily_inventory_price()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants)) {
            $userPlants = [$this->session->userdata('plant')];
        }

        $plantInput = $this->input->get('plant');
        $date       = $this->input->get('date');

        if (!$date) show_error('Invalid date');

        if (!$plantInput) {
            $plantFilter = $userPlants;
        } else {
            if (!in_array($plantInput, $userPlants)) {
                show_error('Unauthorized plant');
            }
            $plantFilter = [$plantInput];
        }

        $filters = [
            'plants' => $plantFilter,
            'ymd'    => str_replace('-', '', $date)
        ];

        $rows = $this->ReportClosingInventoryPrice_model
            ->get_daily_inventory_price(0,0,$filters,'material','ASC');

        if (!$rows) show_error('No data');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'PLANT','MATERIAL',
            'BG QTY','BG BW','BG AMOUNT',
            'IN QTY','IN BW','IN AMOUNT',
            'OUT QTY','OUT BW','OUT AMOUNT',
            'END QTY','END BW','END AMOUNT'
        ];

        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65+$i).'1', $h);
        }

        $rowNum = 2;
        foreach ($rows as $r) {
            $sheet->fromArray([
                $r->plant_name,$r->material_name,
                $r->bg_qty,$r->bg_bw,$r->bg_amount,
                $r->in_qty,$r->in_bw,$r->in_amount,
                $r->out_qty,$r->out_bw,$r->out_amount,
                $r->end_qty,$r->end_bw,$r->end_amount
            ], NULL, "A$rowNum");
            $rowNum++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="daily_inventory_price.xlsx"');
        (new Xlsx($spreadsheet))->save('php://output');
    }

    public function export_pdf_daily_inventory_price()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants)) {
            $userPlants = [$this->session->userdata('plant')];
        }

        $plantInput = $this->input->get('plant');
        $date       = $this->input->get('date');

        if (!$date) show_error('Invalid date');

        if (!$plantInput) {
            $plantFilter = $userPlants;
        } else {
            if (!in_array($plantInput, $userPlants)) {
                show_error('Unauthorized plant');
            }
            $plantFilter = [$plantInput];
        }

        $filters = [
            'plants' => $plantFilter,
            'ymd'    => str_replace('-', '', $date)
        ];

        $rows = $this->ReportClosingInventoryPrice_model
            ->get_daily_inventory_price(0,0,$filters,'material','ASC');

        if (!$rows) show_error('No data');

        $html = '<style>
            table{border-collapse:collapse;width:100%;font-size:10px}
            th,td{border:1px solid #000;padding:4px;text-align:right}
            th{text-align:center;background:#eee}
        </style>
        <h3 style="text-align:center">DAILY CLOSING INVENTORY PRICE</h3>
        <table>
        <thead>
            <tr>
                <th>PLANT</th><th>MATERIAL</th>
                <th>BG QTY</th><th>BG BW</th><th>BG AMOUNT</th>
                <th>IN QTY</th><th>IN BW</th><th>IN AMOUNT</th>
                <th>OUT QTY</th><th>OUT BW</th><th>OUT AMOUNT</th>
                <th>END QTY</th><th>END BW</th><th>END AMOUNT</th>
            </tr>
        </thead><tbody>';

        foreach ($rows as $r) {
            $html .= "<tr>
                <td>{$r->plant_name}</td>
                <td>{$r->material_name}</td>
                <td>".number_format($r->bg_qty,2,',','.')."</td>
                <td>".number_format($r->bg_bw,2,',','.')."</td>
                <td>".number_format($r->bg_amount,0,',','.')."</td>
                <td>".number_format($r->in_qty,2,',','.')."</td>
                <td>".number_format($r->in_bw,2,',','.')."</td>
                <td>".number_format($r->in_amount,0,',','.')."</td>
                <td>".number_format($r->out_qty,2,',','.')."</td>
                <td>".number_format($r->out_bw,2,',','.')."</td>
                <td>".number_format($r->out_amount,0,',','.')."</td>
                <td>".number_format($r->end_qty,2,',','.')."</td>
                <td>".number_format($r->end_bw,2,',','.')."</td>
                <td>".number_format($r->end_amount,0,',','.')."</td>
            </tr>";
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('daily_inventory_price.pdf','I');
    }

    private function build_pagination($totalPages, $currentPage, $mode = 'url')
    {
        if ($totalPages <= 1) return '';

        $html = '<ul class="pagination pagination-sm mb-0">';

        $range = 2; // tampilkan ±2 halaman dari current

        $start = max(1, $currentPage - $range);
        $end   = min($totalPages, $currentPage + $range);

        // ================= PREV =================
        if ($currentPage > 1) {
            $prev = $currentPage - 1;

            if ($mode === 'ajax') {
                $html .= "
                    <li class='page-item'>
                        <a href='#' class='page-link' data-page='$prev'>&laquo;</a>
                    </li>
                ";
            } else {
                $html .= "
                    <li class='page-item'>
                        <a href='?page=$prev' class='page-link'>&laquo;</a>
                    </li>
                ";
            }
        }

        // ================= FIRST PAGE =================
        if ($start > 1) {
            $html .= $this->page_link(1, $currentPage, $mode);
            if ($start > 2) {
                $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
        }

        // ================= MIDDLE PAGES =================
        for ($i = $start; $i <= $end; $i++) {
            $html .= $this->page_link($i, $currentPage, $mode);
        }

        // ================= LAST PAGE =================
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
            $html .= $this->page_link($totalPages, $currentPage, $mode);
        }

        // ================= NEXT =================
        if ($currentPage < $totalPages) {
            $next = $currentPage + 1;

            if ($mode === 'ajax') {
                $html .= "
                    <li class='page-item'>
                        <a href='#' class='page-link' data-page='$next'>&raquo;</a>
                    </li>
                ";
            } else {
                $html .= "
                    <li class='page-item'>
                        <a href='?page=$next' class='page-link'>&raquo;</a>
                    </li>
                ";
            }
        }

        $html .= '</ul>';

        return $html;
    }

    private function page_link($page, $currentPage, $mode)
    {
        $active = $page == $currentPage ? 'active' : '';

        if ($mode === 'ajax') {
            return "
                <li class='page-item $active'>
                    <a href='#' class='page-link' data-page='$page'>$page</a>
                </li>
            ";
        } else {
            return "
                <li class='page-item $active'>
                    <a href='?page=$page' class='page-link'>$page</a>
                </li>
            ";
        }
    }

    public function load_monthly_closing_inventory_price()
    {
        header('Content-Type: application/json');

        $page  = max((int)$this->input->get('page'), 1);
        $limit = max((int)$this->input->get('limit'), 1);

        /* 🔐 PLANT SECURITY */
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants)) {
            $userPlants = [$this->session->userdata('plant')];
        }

        $plantInput = $this->input->get('plant', TRUE);

        if (!$plantInput) {
            $plantFilter = $userPlants;
        } else {
            if (!in_array($plantInput, $userPlants)) {
                echo json_encode([
                    'rows'=>[],
                    'total'=>0,
                    'grand'=>[],
                    'pagination'=>''
                ]);
                return;
            }
            $plantFilter = [$plantInput];
        }

        /* 📅 SINGLE MONTH */
        $month = $this->input->get('month', TRUE); // format: 202601
        if (!$month) {
            echo json_encode(['rows'=>[],'total'=>0,'grand'=>[],'pagination'=>'']);
            return;
        }

        $filters = [
            'plants'   => $plantFilter,
            'material' => trim($this->input->get('material', TRUE)),
            'month'    => $month
        ];

        $start = ($page - 1) * $limit;

        $rows = $this->ReportClosingInventoryPrice_model
            ->get_monthly_inventory_price($limit, $start, $filters);

        $totalRows = $this->ReportClosingInventoryPrice_model
            ->count_monthly_inventory_price($filters);

        $grandRow = $this->ReportClosingInventoryPrice_model
            ->get_monthly_inventory_price_grand($filters);

        $grand = (array)$grandRow;

        $pages = $limit ? ceil($totalRows / $limit) : 1;
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $totalRows,
            'grand'      => $grand,
            'pagination' => $pagination,
            'page'       => $page
        ]);
    }

    public function export_excel_monthly_inventory_price()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants)) {
            $userPlants = [$this->session->userdata('plant')];
        }

        $plantInput = $this->input->get('plant');

        if (!$plantInput) {
            $plantFilter = $userPlants;
        } else {
            if (!in_array($plantInput, $userPlants)) {
                show_error('Unauthorized plant');
            }
            $plantFilter = [$plantInput];
        }

        $filters = [
            'plants'     => $plantFilter,
            'material'   => trim($this->input->get('material')),
            'month' => $this->input->get('month'),
        ];

        $rows = $this->ReportClosingInventoryPrice_model
            ->get_monthly_inventory_price(0,0,$filters,'material','ASC');

        if (!$rows) show_error('No data found');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'PLANT','MATERIAL',
            'BG QTY','BG BW','BG AMOUNT',
            'IN QTY','IN BW','IN AMOUNT',
            'OUT QTY','OUT BW','OUT AMOUNT',
            'END QTY','END BW','END AMOUNT'
        ];

        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65+$i).'1', $h);
        }

        $rowNum = 2;
        foreach ($rows as $r) {
            $sheet->fromArray([
                $r->plant_name ?? $r->plant,
                $r->material_name ?? $r->material,
                $r->bg_qty,$r->bg_bw,$r->bg_amount,
                $r->in_qty,$r->in_bw,$r->in_amount,
                $r->out_qty,$r->out_bw,$r->out_amount,
                $r->end_qty,$r->end_bw,$r->end_amount
            ], NULL, "A$rowNum");
            $rowNum++;
        }

        foreach (range('A','N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="monthly_inventory_price.xlsx"');
        (new Xlsx($spreadsheet))->save('php://output');
    }

    public function export_pdf_monthly_inventory_price()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants)) {
            $userPlants = [$this->session->userdata('plant')];
        }

        $plantInput = $this->input->get('plant');

        if (!$plantInput) {
            $plantFilter = $userPlants;
        } else {
            if (!in_array($plantInput, $userPlants)) {
                show_error('Unauthorized plant');
            }
            $plantFilter = [$plantInput];
        }

        $filters = [
            'plants'     => $plantFilter,
            'material'   => trim($this->input->get('material')),
            'month' => $this->input->get('month'),
        ];

        $rows = $this->ReportClosingInventoryPrice_model
            ->get_monthly_inventory_price(0,0,$filters,'material','ASC');

        if (!$rows) show_error('No data found');

        $html = '<style>
            table{border-collapse:collapse;width:100%;font-size:10px}
            th,td{border:1px solid #000;padding:4px;text-align:right}
            th{text-align:center;background:#eee}
        </style>
        <h3 style="text-align:center">MONTHLY INVENTORY PRICE REPORT</h3>
        <table>
        <thead>
            <tr>
                <th>PLANT</th><th>MATERIAL</th>
                <th>BG QTY</th><th>BG BW</th><th>BG AMOUNT</th>
                <th>IN QTY</th><th>IN BW</th><th>IN AMOUNT</th>
                <th>OUT QTY</th><th>OUT BW</th><th>OUT AMOUNT</th>
                <th>END QTY</th><th>END BW</th><th>END AMOUNT</th>
            </tr>
        </thead><tbody>';

        foreach ($rows as $r) {
            $html .= "<tr>
                <td>{$r->plant_name}</td>
                <td>{$r->material_name}</td>
                <td>".number_format($r->bg_qty,2,',','.')."</td>
                <td>".number_format($r->bg_bw,2,',','.')."</td>
                <td>".number_format($r->bg_amount,0,',','.')."</td>
                <td>".number_format($r->in_qty,2,',','.')."</td>
                <td>".number_format($r->in_bw,2,',','.')."</td>
                <td>".number_format($r->in_amount,0,',','.')."</td>
                <td>".number_format($r->out_qty,2,',','.')."</td>
                <td>".number_format($r->out_bw,2,',','.')."</td>
                <td>".number_format($r->out_amount,0,',','.')."</td>
                <td>".number_format($r->end_qty,2,',','.')."</td>
                <td>".number_format($r->end_bw,2,',','.')."</td>
                <td>".number_format($r->end_amount,0,',','.')."</td>
            </tr>";
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('monthly_inventory_price.pdf','I');
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
