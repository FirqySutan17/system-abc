<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportClosingPl extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('closing_pl')) {
            show_404();
        }
        $this->load->model('ReportClosingPl_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data = [
            'plants'    => $this->ReportClosingPl_model->get_plant_list(),
            'userPlant' => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report Closing P/L']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_closing_pl/index', $data);
        $this->load->view('templates/footer');
    }

    public function load_daily_closing_pl()
    {
        header('Content-Type: application/json');

        $page  = max((int)$this->input->get('page'),1);
        $limit = max((int)$this->input->get('limit'),1);

        $order = $this->input->get('order', TRUE) ?: 'account_cd';

        $dirInput = $this->input->get('dir', TRUE);
        $dir = strtoupper($dirInput ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        /* ===== PLANT SECURITY ===== */

        $sessionPlant = $this->session->userdata('plant');
        $userPlants   = json_decode($sessionPlant,true);

        if(!is_array($userPlants)){
            $userPlants = [$sessionPlant];
        }

        $plantInput = $this->input->get('plant',TRUE);

        if(!$plantInput){
            $plantFilter = $userPlants;
        }else{
            if(!in_array($plantInput,$userPlants)){
                echo json_encode(['rows'=>[],'total'=>0,'grand'=>['amount'=>0],'pagination'=>'']);
                return;
            }
            $plantFilter = [$plantInput];
        }

        /* ===== DATE ===== */

        $date = $this->input->get('date',TRUE);

        if(!$date){
            echo json_encode(['rows'=>[],'total'=>0,'grand'=>['amount'=>0],'pagination'=>'']);
            return;
        }

        $filters=[
            'plants'=>$plantFilter,
            'account'=>$this->input->get('account',TRUE),
            'date'=>str_replace('-','',$date)
        ];

        $start = ($page-1)*$limit;

        $rows = $this->ReportClosingPl_model
            ->get_daily_closing_pl($limit,$start,$filters,$order,$dir);

        $totalRows = $this->ReportClosingPl_model
            ->count_daily_closing_pl($filters);

        $grandRow = $this->ReportClosingPl_model
            ->get_daily_closing_pl_grand($filters);

        $grand=[
            'amount'=>(float)($grandRow->amount ?? 0)
        ];

        $pages=$limit?ceil($totalRows/$limit):1;

        $pagination=$this->build_pagination($pages,$page,'ajax');

        echo json_encode([
            'rows'=>$rows,
            'total'=>$totalRows,
            'grand'=>$grand,
            'pagination'=>$pagination,
            'page'=>$page
        ]);
    }

    public function export_excel_daily_closing_pl()
    {
        $sessionPlant = $this->session->userdata('plant');
        $userPlants   = json_decode($sessionPlant,true);

        if(!is_array($userPlants)){
            $userPlants = [$sessionPlant];
        }

        $plantInput = $this->input->get('plant',TRUE);

        if(!$plantInput){
            $plantFilter = $userPlants;
        }else{
            if(!in_array($plantInput,$userPlants)){
                show_error('Invalid plant');
            }
            $plantFilter = [$plantInput];
        }

        $date = $this->input->get('date',TRUE);
        if(!$date) show_error('Date required');

        $filters = [
            'plants'=>$plantFilter,
            'account'=>$this->input->get('account'),
            'date'=>str_replace('-','',$date)
        ];

        $rows = $this->ReportClosingPl_model
            ->get_daily_closing_pl(0,0,$filters,'account_cd','ASC');

        if(!$rows) show_error('No data');

        $grand = $this->ReportClosingPl_model
            ->get_daily_closing_pl_grand($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ===== TITLE ===== */

        $sheet->setCellValue('A1','DAILY CLOSING PROFIT & LOSS');
        $sheet->mergeCells('A1:E1');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* ===== HEADER ===== */

        $headers = ['PLANT','DATE','ACCOUNT','ACCOUNT NAME','AMOUNT'];

        foreach($headers as $i=>$h){
            $sheet->setCellValue(chr(65+$i).'3',$h);
        }

        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal('center');

        /* ===== DATA ===== */

        $row = 4;

        foreach($rows as $r){

            $dateFormat = substr($r->ymd,6,2).'/'
                        . substr($r->ymd,4,2).'/'
                        . substr($r->ymd,0,4);

            $sheet->fromArray([
                $r->plant_name,
                $dateFormat,
                $r->account_cd,
                $r->ACCOUNT_NAME,
                (float)$r->amount
            ],NULL,"A$row");

            $row++;
        }

        /* ===== GRAND TOTAL ===== */

        $sheet->setCellValue("A$row",'GRAND TOTAL');
        $sheet->mergeCells("A$row:D$row");

        $sheet->setCellValue("E$row",(float)($grand->amount ?? 0));

        $sheet->getStyle("A$row:E$row")->getFont()->setBold(true);

        /* ===== NUMBER FORMAT ===== */

        $sheet->getStyle("E4:E$row")
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        /* ===== AUTO WIDTH ===== */

        foreach(range('A','E') as $col){
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        /* ===== BORDER ===== */

        $sheet->getStyle("A3:E$row")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        /* ===== FREEZE ===== */

        $sheet->freezePane('A4');

        /* ===== FILTER ===== */

        $sheet->setAutoFilter("A3:E$row");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="daily_closing_pl.xlsx"');

        (new Xlsx($spreadsheet))->save('php://output');
    }

    public function export_pdf_daily_closing_pl()
    {
        $sessionPlant = $this->session->userdata('plant');
        $userPlants   = json_decode($sessionPlant,true);

        if(!is_array($userPlants)){
            $userPlants = [$sessionPlant];
        }

        $plantInput = $this->input->get('plant',TRUE);

        if(!$plantInput){
            $plantFilter = $userPlants;
        }else{
            if(!in_array($plantInput,$userPlants)){
                show_error('Invalid plant');
            }
            $plantFilter = [$plantInput];
        }

        $date = $this->input->get('date',TRUE);
        if(!$date) show_error('Date required');

        $filters = [
            'plants'=>$plantFilter,
            'account'=>$this->input->get('account'),
            'date'=>str_replace('-','',$date)
        ];

        $rows = $this->ReportClosingPl_model
            ->get_daily_closing_pl(0,0,$filters,'account_cd','ASC');

        if(!$rows) show_error('No data');

        $grand = $this->ReportClosingPl_model
            ->get_daily_closing_pl_grand($filters);

        $html='
        <style>
            table{border-collapse:collapse;width:100%;font-size:9px}
            th,td{border:1px solid #000;padding:4px}
            th{text-align:center;background:#eee}
            .r{text-align:right}
            .c{text-align:center}
        </style>

        <h3 style="text-align:center">DAILY CLOSING PROFIT & LOSS</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>DATE</th>
                    <th>ACCOUNT</th>
                    <th>ACCOUNT NAME</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach($rows as $r){

            $dateFormat = substr($r->ymd,6,2).'/'
                        . substr($r->ymd,4,2).'/'
                        . substr($r->ymd,0,4);

            $html .= "
            <tr>
                <td class='c'><b>{$r->plant_name}</b></td>
                <td class='c'>{$dateFormat}</td>
                <td class='c'>{$r->account_cd}</td>
                <td>{$r->ACCOUNT_NAME}</td>
                <td class='r'>".number_format($r->amount,0,',','.')."</td>
            </tr>";
        }

        $html .= "
            <tr style='font-weight:bold;background:#f2f2f2'>
                <td colspan='4' class='c'>GRAND TOTAL</td>
                <td class='r'>".number_format($grand->amount ?? 0,0,',','.')."</td>
            </tr>
        ";

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('daily_closing_pl.pdf','I');
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

    public function load_monthly_closing_pl()
    {
        header('Content-Type: application/json');

        $page  = max((int)$this->input->get('page'),1);
        $limit = max((int)$this->input->get('limit'),17);

        $order = $this->input->get('order',TRUE) ?: 'ym';

        $dirInput = $this->input->get('dir',TRUE);
        $dir = strtoupper($dirInput ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        /* ===== MULTI PLANT SECURITY ===== */

        $sessionPlant = $this->session->userdata('plant');
        $userPlants   = json_decode($sessionPlant,true);

        if(!is_array($userPlants)){
            $userPlants = [$sessionPlant];
        }

        $plantInput = $this->input->get('plant',TRUE);

        if(!$plantInput){
            $plantFilter = $userPlants;
        }else{
            if(!in_array($plantInput,$userPlants)){
                echo json_encode(['rows'=>[],'total'=>0,'grand'=>['amount'=>0],'pagination'=>'']);
                return;
            }
            $plantFilter = [$plantInput];
        }

        $filters = [
            'plants'=>$plantFilter,
            'month'=>$this->input->get('month',TRUE)
        ];

        $start = ($page-1)*$limit;

        $rows = $this->ReportClosingPl_model
            ->get_monthly_closing_pl($limit,$start,$filters,$order,$dir);

        $totalRows = $this->ReportClosingPl_model
            ->count_monthly_closing_pl($filters);

        $grandRow = $this->ReportClosingPl_model
            ->get_monthly_closing_pl_grand($filters);

        $pages = $limit ? ceil($totalRows/$limit) : 1;

        $pagination = $this->build_pagination($pages,$page,'ajax');

        echo json_encode([
            'rows'=>$rows,
            'total'=>$totalRows,
            'grand'=>['amount'=>(float)($grandRow->amount ?? 0)],
            'pagination'=>$pagination,
            'page'=>$page
        ]);
    }

    public function export_excel_monthly_closing_pl()
    {
        $filters = $this->buildMonthlyFilter();

        $rows = $this->ReportClosingPl_model
            ->get_monthly_closing_pl(0,0,$filters,'ym','ASC');

        if(!$rows) show_error('No data');

        $grand = $this->ReportClosingPl_model
            ->get_monthly_closing_pl_grand($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ===== TITLE ===== */

        $sheet->setCellValue('A1','MONTHLY CLOSING PROFIT & LOSS');
        $sheet->mergeCells('A1:E1');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* ===== HEADER ===== */

        $headers = ['PLANT','MONTH','ACCOUNT','ACCOUNT NAME','AMOUNT'];

        foreach($headers as $i=>$h){
            $sheet->setCellValue(chr(65+$i).'3',$h);
        }

        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal('center');

        /* ===== DATA ===== */

        $row = 4;

        foreach($rows as $r){

            $formattedMonth = substr($r->ym,4,2).'/'.substr($r->ym,0,4);

            $sheet->fromArray([
                $r->plant_name,
                $formattedMonth,
                $r->account_cd,
                $r->ACCOUNT_NAME,
                (float)$r->amount
            ],NULL,"A$row");

            $row++;
        }

        /* ===== GRAND TOTAL ===== */

        $sheet->setCellValue("A$row",'GRAND TOTAL');
        $sheet->mergeCells("A$row:D$row");

        $sheet->setCellValue("E$row",(float)($grand->amount ?? 0));

        $sheet->getStyle("A$row:E$row")->getFont()->setBold(true);

        /* ===== FORMAT ===== */

        $sheet->getStyle("E4:E$row")
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        foreach(range('A','E') as $col){
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("A3:E$row")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->freezePane('A4');

        $sheet->setAutoFilter("A3:E$row");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="monthly_closing_pl.xlsx"');

        (new Xlsx($spreadsheet))->save('php://output');
    }

    public function export_pdf_monthly_closing_pl()
    {
        $filters = $this->buildMonthlyFilter();

        $rows = $this->ReportClosingPl_model
            ->get_monthly_closing_pl(0,0,$filters,'ym','ASC');

        if(!$rows) show_error('No data');

        $grand = $this->ReportClosingPl_model
            ->get_monthly_closing_pl_grand($filters);

        $html = '
        <style>
        table{border-collapse:collapse;width:100%;font-size:9px}
        th,td{border:1px solid #000;padding:4px}
        th{text-align:center;background:#eee}
        .r{text-align:right}
        .c{text-align:center}
        </style>

        <h3 style="text-align:center">MONTHLY CLOSING PROFIT & LOSS</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>MONTH</th>
                    <th>ACCOUNT</th>
                    <th>ACCOUNT NAME</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach($rows as $r){

            $formattedMonth = substr($r->ym,4,2).'/'.substr($r->ym,0,4);

            $html .= "
            <tr>
                <td class='c'><b>{$r->plant_name}</b></td>
                <td class='c'>{$formattedMonth}</td>
                <td class='c'>{$r->account_cd}</td>
                <td>{$r->ACCOUNT_NAME}</td>
                <td class='r'>".number_format($r->amount,0,',','.')."</td>
            </tr>";
        }

        $html .= "
            <tr style='font-weight:bold;background:#f2f2f2'>
                <td colspan='4' class='c'>GRAND TOTAL</td>
                <td class='r'>".number_format($grand->amount ?? 0,0,',','.')."</td>
            </tr>
        ";

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('monthly_closing_pl.pdf','I');
    }

    private function buildMonthlyFilter()
    {
        /* ===== MULTI PLANT SECURITY ===== */

        $sessionPlant = $this->session->userdata('plant');
        $userPlants   = json_decode($sessionPlant, true);

        if (!is_array($userPlants)) {
            $userPlants = [$sessionPlant];
        }

        $plantInput = $this->input->get('plant', TRUE);

        if (!$plantInput) {
            $plantFilter = $userPlants;
        } else {
            if (!in_array($plantInput, $userPlants)) {
                show_error('Invalid plant');
            }
            $plantFilter = [$plantInput];
        }

        /* ===== SINGLE MONTH ===== */

        $month = $this->input->get('month', TRUE);
        if (!$month) {
            show_error('Month required');
        }

        return [
            'plants' => $plantFilter,
            'month'  => $month
        ];
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
