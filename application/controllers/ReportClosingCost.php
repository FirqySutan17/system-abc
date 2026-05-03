<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportClosingCost extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('closing_cost')) {
            show_404();
        }
        $this->load->model('ReportClosingCost_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data = [
            'plants'     => $this->ReportClosingCost_model->get_plant_list(),
            'suppliers'  => $this->ReportClosingCost_model->get_supplier_list(),
            'customers'  => $this->ReportClosingCost_model->get_customer_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report Closing Cost']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_closing_cost/index', $data);
        $this->load->view('templates/footer');
    }

    public function load_daily_closing_cost()
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

        /* 📅 SINGLE DAY */
        $date = str_replace('-', '', $this->input->get('date', TRUE));
        if (!$date) {
            echo json_encode(['rows'=>[],'total'=>0,'grand'=>[],'pagination'=>'']);
            return;
        }

        $filters = [
            'plants' => $plantFilter,
            'date'   => $date
        ];

        $start = ($page - 1) * $limit;

        $rows = $this->ReportClosingCost_model
            ->get_daily_closing_cost($limit, $start, $filters);

        $totalRows = $this->ReportClosingCost_model
            ->count_daily_closing_cost($filters);

        $grandRow = $this->ReportClosingCost_model
            ->get_daily_closing_cost_grand($filters);

        $grand = [
            'qty'    => (float)($grandRow->qty ?? 0),
            'bw'     => (float)($grandRow->bw ?? 0),
            'amount' => (float)($grandRow->amount ?? 0),
        ];

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

    public function export_excel_daily_closing_cost()
    {
        /* 🔐 PLANT SECURITY */
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

        $date = str_replace('-', '', $this->input->get('date'));
        if (!$date) show_error('Date required');

        $filters = [
            'plants' => $plantFilter,
            'date'   => $date
        ];

        $rows = $this->ReportClosingCost_model
            ->get_daily_closing_cost(0,0,$filters);

        if (!$rows) show_error('No data');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'PLANT','DATE','ITEM','ITEM NAME','CLASS',
            'QTY','KG',
            'INDEX PRICE','INDEX AMOUNT',
            'MARKET PRICE','MARKET AMOUNT',
            'MODAL','MODAL AMOUNT'
        ];

        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65+$i).'1', $h);
        }

        $rowNum = 2;

        foreach ($rows as $r) {
            $sheet->fromArray([
                $r->plant_name ?? $r->plant,
                $r->ymd,
                $r->item,
                $r->item_name,
                $r->class_name,
                $r->qty,
                $r->kg,
                $r->harga,
                $r->amount,
                $r->trend_market,
                $r->amount_market,
                $r->modal,
                $r->amount_modal
            ], NULL, "A$rowNum");

            $rowNum++;
        }

        foreach (range('A','M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="daily_closing_cost.xlsx"');

        (new Xlsx($spreadsheet))->save('php://output');
    }

    public function export_pdf_daily_closing_cost()
    {
        /* 🔐 PLANT SECURITY */
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

        $date = str_replace('-', '', $this->input->get('date'));
        if (!$date) show_error('Date required');

        $filters = [
            'plants' => $plantFilter,
            'date'   => $date
        ];

        $rows = $this->ReportClosingCost_model
            ->get_daily_closing_cost(0,0,$filters);

        if (!$rows) show_error('No data');

        $html = '
        <style>
            table { border-collapse: collapse; width: 100%; font-size: 8px; }
            th, td { border: 1px solid #000; padding: 3px; }
            th { background: #eee; text-align: center; }
            td { text-align: right; }
            .left { text-align: left; }
            .center { text-align: center; }
        </style>

        <h3 style="text-align:center">DAILY CLOSING COST REPORT</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>DATE</th>
                    <th>ITEM</th>
                    <th>ITEM NAME</th>
                    <th>CLASS</th>
                    <th>QTY</th>
                    <th>KG</th>
                    <th>INDEX PRICE</th>
                    <th>INDEX AMOUNT</th>
                    <th>MARKET PRICE</th>
                    <th>MARKET AMOUNT</th>
                    <th>MODAL</th>
                    <th>MODAL AMOUNT</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($rows as $r) {
            $html .= "
                <tr>
                    <td class='center'>".($r->plant_name ?? $r->plant)."</td>
                    <td class='center'>{$r->ymd}</td>
                    <td class='left'>{$r->item}</td>
                    <td class='left'>{$r->item_name}</td>
                    <td class='center'>{$r->class_name}</td>
                    <td>".number_format($r->qty,2,',','.')."</td>
                    <td>".number_format($r->kg,2,',','.')."</td>
                    <td>".number_format($r->harga,0,',','.')."</td>
                    <td>".number_format($r->amount,0,',','.')."</td>
                    <td>".number_format($r->trend_market,0,',','.')."</td>
                    <td>".number_format($r->amount_market,0,',','.')."</td>
                    <td>".number_format($r->modal,0,',','.')."</td>
                    <td>".number_format($r->amount_modal,0,',','.')."</td>
                </tr>
            ";
        }

        $html .= "</tbody></table>";

        $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('daily_closing_cost.pdf','I');
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

    public function load_monthly_closing_cost()
    {
        header('Content-Type: application/json');

        $page  = max((int)$this->input->get('page'), 1);
        $limit = max((int)$this->input->get('limit'), 1);

        /* 🔐 PLANT SECURITY (JSON SUPPORT) */
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
        $month = $this->input->get('month', TRUE); // format: 202602

        if (!$month) {
            echo json_encode(['rows'=>[],'total'=>0,'grand'=>[],'pagination'=>'']);
            return;
        }

        $filters = [
            'plants' => $plantFilter,
            'item'   => trim($this->input->get('material', TRUE)),
            'month'  => $month
        ];

        $start = ($page - 1) * $limit;

        $rows = $this->ReportClosingCost_model
            ->get_monthly_closing_cost($limit, $start, $filters);

        $totalRows = $this->ReportClosingCost_model
            ->count_monthly_closing_cost($filters);

        $grandRow = $this->ReportClosingCost_model
            ->get_monthly_closing_cost_grand($filters);

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

    public function export_excel_monthly_closing_cost()
    {
        /* 🔐 PLANT SECURITY */
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants)) {
            $userPlants = [$this->session->userdata('plant')];
        }

        $plantInput = $this->input->get('plant');
        $month      = $this->input->get('month');

        if (!$month) show_error('Month required');

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
            'item'   => trim($this->input->get('material')),
            'month'  => $month
        ];

        $rows = $this->ReportClosingCost_model
            ->get_monthly_closing_cost(0, 0, $filters);

        if (!$rows) show_error('No data found');

        $grand = $this->ReportClosingCost_model
            ->get_monthly_closing_cost_grand($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'PLANT','MONTH','ITEM','ITEM NAME','CLASS',
            'QTY','KG','INDEX AMOUNT','MARKET AMOUNT','MODAL AMOUNT'
        ];

        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65+$i).'1', $h);
        }

        $rowNum = 2;

        foreach ($rows as $r) {
            $sheet->fromArray([
                $r->plant_name ?? $r->plant,
                substr($r->ym,4,2).'/'.substr($r->ym,0,4),
                $r->item,
                $r->item_name,
                $r->class_name,
                $r->qty,
                $r->kg,
                $r->index_amount,
                $r->market_amount,
                $r->modal_amount
            ], NULL, "A$rowNum");
            $rowNum++;
        }

        /* 🔥 GRAND TOTAL */
        $sheet->setCellValue("A$rowNum", 'GRAND TOTAL');
        $sheet->mergeCells("A$rowNum:E$rowNum");

        $sheet->setCellValue("F$rowNum", $grand->qty ?? 0);
        $sheet->setCellValue("G$rowNum", $grand->kg ?? 0);
        $sheet->setCellValue("H$rowNum", $grand->index_amount ?? 0);
        $sheet->setCellValue("I$rowNum", $grand->market_amount ?? 0);
        $sheet->setCellValue("J$rowNum", $grand->modal_amount ?? 0);

        foreach (range('A','J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="monthly_closing_cost.xlsx"');

        (new Xlsx($spreadsheet))->save('php://output');
    }

    public function export_pdf_monthly_closing_cost()
    {
        /* 🔐 PLANT SECURITY */
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants)) {
            $userPlants = [$this->session->userdata('plant')];
        }

        $plantInput = $this->input->get('plant');
        $month      = $this->input->get('month');

        if (!$month) show_error('Month required');

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
            'item'   => trim($this->input->get('material')),
            'month'  => $month
        ];

        $rows = $this->ReportClosingCost_model
            ->get_monthly_closing_cost(0, 0, $filters);

        if (!$rows) show_error('No data found');

        $grand = $this->ReportClosingCost_model
            ->get_monthly_closing_cost_grand($filters);

        $html = '
        <style>
            table{border-collapse:collapse;width:100%;font-size:9px}
            th,td{border:1px solid #000;padding:4px;text-align:right}
            th{text-align:center;background:#eee}
            .left{text-align:left}
            .center{text-align:center}
        </style>

        <h3 style="text-align:center">MONTHLY CLOSING COST REPORT</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>MONTH</th>
                    <th>ITEM</th>
                    <th>ITEM NAME</th>
                    <th>CLASS</th>
                    <th>QTY</th>
                    <th>KG</th>
                    <th>INDEX AMOUNT</th>
                    <th>MARKET AMOUNT</th>
                    <th>MODAL AMOUNT</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($rows as $r) {

            $html .= "
                <tr>
                    <td class='center'>".($r->plant_name ?? $r->plant)."</td>
                    <td class='center'>".substr($r->ym,4,2)."/".substr($r->ym,0,4)."</td>
                    <td class='left'>{$r->item}</td>
                    <td class='left'>{$r->item_name}</td>
                    <td class='center'>{$r->class_name}</td>
                    <td>".number_format($r->qty,2,',','.')."</td>
                    <td>".number_format($r->kg,2,',','.')."</td>
                    <td>".number_format($r->index_amount,0,',','.')."</td>
                    <td>".number_format($r->market_amount,0,',','.')."</td>
                    <td>".number_format($r->modal_amount,0,',','.')."</td>
                </tr>
            ";
        }

        /* 🔥 GRAND TOTAL */
        $html .= "
            <tr style='font-weight:bold;background:#f2f2f2'>
                <td colspan='5' class='center'>GRAND TOTAL</td>
                <td>".number_format($grand->qty ?? 0,2,',','.')."</td>
                <td>".number_format($grand->kg ?? 0,2,',','.')."</td>
                <td>".number_format($grand->index_amount ?? 0,0,',','.')."</td>
                <td>".number_format($grand->market_amount ?? 0,0,',','.')."</td>
                <td>".number_format($grand->modal_amount ?? 0,0,',','.')."</td>
            </tr>
        ";

        $html .= "</tbody></table>";

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L',
            'margin_left' => 8,
            'margin_right'=> 8,
            'margin_top'  => 10,
            'margin_bottom'=>10
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('monthly_closing_cost.pdf','I');
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
