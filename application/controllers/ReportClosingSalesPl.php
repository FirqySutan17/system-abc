<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportClosingSalesPl extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('closing_sales_pl')) {
            show_404();
        }
        $this->load->model('ReportClosingSalesPl_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data = [
            'plants'     => $this->ReportClosingSalesPl_model->get_plant_list(),
            'suppliers'  => $this->ReportClosingSalesPl_model->get_supplier_list(),
            'customers'  => $this->ReportClosingSalesPl_model->get_customer_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report Closing Sales P/L']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_closing_sales_pl/index', $data);
        $this->load->view('templates/footer');
    }

    public function load_monthly_sales_pl()
    {
        header('Content-Type: application/json');

        $page  = max((int)$this->input->get('page'),1);
        $limit = max((int)$this->input->get('limit'),1);

        $plantInput = $this->input->get('plant', TRUE);

        if (!$plantInput) {

            echo json_encode([
                'rows'       => [],
                'total'      => 0,
                'grand'      => [],
                'pagination' => ''
            ]);
            return;
        }

        $plantFilter = [$plantInput];

        $month = $this->input->get('month',TRUE);

        if(!$month){
            echo json_encode(['rows'=>[],'total'=>0,'grand'=>[],'pagination'=>'']);
            return;
        }

        $filters=[
            'plants'=>$plantFilter,
            'item'=>$this->input->get('item',TRUE),
            'month'=>$month
        ];

        $start = ($page-1)*$limit;

        $rows = $this->ReportClosingSalesPl_model
            ->get_monthly_sales_pl($limit,$start,$filters);

        $totalRows = $this->ReportClosingSalesPl_model
            ->count_monthly_sales_pl($filters);

        $grandRow = $this->ReportClosingSalesPl_model
            ->get_monthly_sales_pl_grand($filters);

        $pages = $limit ? ceil($totalRows/$limit) : 1;

        $pagination = $this->build_pagination($pages,$page,'ajax');

        echo json_encode([
            'rows'=>$rows,
            'total'=>$totalRows,
            'grand'=>$grandRow ?: (object)[],
            'pagination'=>$pagination,
            'page'=>$page
        ]);
    }

    public function export_excel_monthly_sales_pl()
    {
        $plantInput = $this->input->get('plant');
        $month      = $this->input->get('month');
        $item       = $this->input->get('item');

        if(!$month) show_error('Month required');

        if (!$plantInput) {
            show_error('Plant required');
        }

        $plantFilter = [$plantInput];

        $filters=[
            'plants'=>$plantFilter,
            'item'=>$item,
            'month'=>$month
        ];

        $rows = $this->ReportClosingSalesPl_model
            ->get_monthly_sales_pl(0,0,$filters);

        $grand = $this->ReportClosingSalesPl_model
            ->get_monthly_sales_pl_grand($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ================= TITLE ================= */

        $sheet->setCellValue('A1','MONTHLY SALES PROFIT & LOSS');
        $sheet->mergeCells('A1:W1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* ================= HEADER ================= */

        $sheet->fromArray([
            'PLANT','MONTH','ITEM','ITEM NAME','CLASS',
            'BEGINNING','','',
            'PRODUCTION','','',
            'PURCHASE','','',
            'ADJUST','','',
            'COGS','','',
            'ENDING','NET SALES','PROFIT'
        ],NULL,'A3');

        $sheet->fromArray([
            '','','','','',
            'BW','UP','AMT',
            'BW','UP','AMT',
            'BW','UP','AMT',
            'BW','UP','AMT',
            'BW','UP','AMT',
            '','',''
        ],NULL,'A4');

        /* MERGE */

        $sheet->mergeCells('F3:H3');
        $sheet->mergeCells('I3:K3');
        $sheet->mergeCells('L3:N3');
        $sheet->mergeCells('O3:Q3');
        $sheet->mergeCells('R3:T3');

        $sheet->mergeCells('A3:A4');
        $sheet->mergeCells('B3:B4');
        $sheet->mergeCells('C3:C4');
        $sheet->mergeCells('D3:D4');
        $sheet->mergeCells('E3:E4');

        $sheet->mergeCells('U3:U4');
        $sheet->mergeCells('V3:V4');
        $sheet->mergeCells('W3:W4');

        $sheet->getStyle('A3:W4')->getFont()->setBold(true);
        $sheet->getStyle('A3:W4')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:W4')->getAlignment()->setVertical('center');

        /* ================= DATA ================= */

        $row = 5;

        foreach($rows as $r){

            $formattedMonth = substr($r->ym,4,2).'/'.substr($r->ym,0,4);

            $sheet->fromArray([
                $r->plant_name,
                $formattedMonth,
                $r->item,
                $r->item_name,
                $r->class_name,

                (float)$r->bg_bw,
                (float)$r->bg_up,
                (float)$r->begin_amt,

                (float)$r->production_bw,
                (float)$r->production_up,
                (float)$r->production_amt,

                (float)$r->purchase_bw,
                (float)$r->purchase_up,
                (float)$r->purchase_amt,

                (float)$r->adjust_bw,
                (float)$r->adjust_up,
                (float)$r->adjust_amt,

                (float)$r->cogs_bw,
                (float)$r->cogs_up,
                (float)$r->cogs_amt,

                (float)$r->ending_amt,
                (float)$r->sales_net_amt,
                (float)$r->sales_profit_amt
            ],NULL,"A$row");

            $row++;
        }

        /* ================= GRAND TOTAL ================= */

        $sheet->setCellValue("A$row",'GRAND TOTAL');
        $sheet->mergeCells("A$row:E$row");

        $sheet->fromArray([
            $grand->bg_bw,
            $grand->bg_up,
            $grand->begin_amt,

            $grand->production_bw,
            $grand->production_up,
            $grand->production_amt,

            $grand->purchase_bw,
            $grand->purchase_up,
            $grand->purchase_amt,

            $grand->adjust_bw,
            $grand->adjust_up,
            $grand->adjust_amt,

            $grand->cogs_bw,
            $grand->cogs_up,
            $grand->cogs_amt,

            $grand->ending_amt,
            $grand->sales_net_amt,
            $grand->sales_profit_amt
        ],NULL,"F$row");

        $sheet->getStyle("A$row:W$row")->getFont()->setBold(true);

        /* ================= NUMBER FORMAT ================= */

        $sheet->getStyle("F5:W$row")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        /* ================= AUTO WIDTH ================= */

        foreach(range('A','W') as $col){
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        /* ================= BORDER ================= */

        $sheet->getStyle("A3:W$row")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        /* ================= FREEZE ================= */

        $sheet->freezePane('A5');

        /* ================= FILTER ================= */

        $sheet->setAutoFilter("A4:W$row");

        /* ================= OUTPUT ================= */

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="monthly_sales_pl.xlsx"');

        (new Xlsx($spreadsheet))->save('php://output');
    }

    public function export_pdf_monthly_sales_pl()
    {
        $plantInput = $this->input->get('plant');
        $month      = $this->input->get('month');
        $item       = $this->input->get('item');

        if (!$month) show_error('Month required');

        if (!$plantInput) {
            show_error('Plant required');
        }

        $plantFilter = [$plantInput];

        $filters = [
            'plants' => $plantFilter,
            'item'   => $item,
            'month'  => $month
        ];

        $rows = $this->ReportClosingSalesPl_model
            ->get_monthly_sales_pl(0, 0, $filters);

        if (!$rows) show_error('No data');

        $grand = $this->ReportClosingSalesPl_model
            ->get_monthly_sales_pl_grand($filters);

        $html = '
        <style>
        table{border-collapse:collapse;width:100%;font-size:8px}
        th,td{border:1px solid #000;padding:3px}
        th{text-align:center;background:#eee}
        .r{text-align:right}
        .c{text-align:center}
        </style>

        <h3 style="text-align:center">MONTHLY SALES PROFIT & LOSS</h3>

        <table>

        <tr>
        <th rowspan="2">PLANT</th>
        <th rowspan="2">MONTH</th>
        <th rowspan="2">ITEM</th>
        <th rowspan="2">ITEM NAME</th>
        <th rowspan="2">CLASS</th>

        <th colspan="3">BEGINNING</th>
        <th colspan="3">PRODUCTION</th>
        <th colspan="3">PURCHASE</th>
        <th colspan="3">ADJUST</th>
        <th colspan="3">COGS</th>

        <th rowspan="2">ENDING</th>
        <th rowspan="2">NET SALES</th>
        <th rowspan="2">PROFIT</th>
        </tr>

        <tr>
        <th>BW</th><th>UP</th><th>AMT</th>
        <th>BW</th><th>UP</th><th>AMT</th>
        <th>BW</th><th>UP</th><th>AMT</th>
        <th>BW</th><th>UP</th><th>AMT</th>
        <th>BW</th><th>UP</th><th>AMT</th>
        </tr>
        ';

        foreach ($rows as $r) {

        $formattedMonth = substr($r->ym,4,2).'/'.substr($r->ym,0,4);

        $html .= "<tr>

        <td class='c'>{$r->plant_name}</td>
        <td class='c'>{$formattedMonth}</td>
        <td class='c'>{$r->item}</td>
        <td>{$r->item_name}</td>
        <td class='c'>{$r->class_name}</td>

        <td class='r'>".number_format((float)($r->bg_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($r->bg_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($r->begin_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($r->production_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($r->production_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($r->production_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($r->purchase_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($r->purchase_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($r->purchase_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($r->adjust_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($r->adjust_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($r->adjust_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($r->cogs_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($r->cogs_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($r->cogs_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($r->ending_amt ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($r->sales_net_amt ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($r->sales_profit_amt ?? 0),0,',','.')."</td>

        </tr>";
        }

        /* ===== GRAND TOTAL ===== */

        $html .= "
        <tr style='font-weight:bold;background:#f2f2f2'>

        <td colspan='5' class='c'>GRAND TOTAL</td>

        <td class='r'>".number_format((float)($grand->bg_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($grand->bg_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($grand->begin_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($grand->production_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($grand->production_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($grand->production_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($grand->purchase_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($grand->purchase_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($grand->purchase_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($grand->adjust_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($grand->adjust_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($grand->adjust_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($grand->cogs_bw ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($grand->cogs_up ?? 0),2,',','.')."</td>
        <td class='r'>".number_format((float)($grand->cogs_amt ?? 0),0,',','.')."</td>

        <td class='r'>".number_format((float)($grand->ending_amt ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($grand->sales_net_amt ?? 0),0,',','.')."</td>
        <td class='r'>".number_format((float)($grand->sales_profit_amt ?? 0),0,',','.')."</td>

        </tr>";

        $html .= '</table>';

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('monthly_sales_pl.pdf','I');
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
