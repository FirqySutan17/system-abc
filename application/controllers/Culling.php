<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Culling extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!has_permission('inventory_receive')) {
            show_404();
        }

        $this->load->model('Culling_model');
        $this->load->library('session');
        $this->load->helper(['url', 'file']);

        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view(
            'templates/header',
            [
                'title' => 'Culling'
            ]
        );

        $this->load->view('templates/sidebar');

        $this->load->view(
            'admin/culling/list'
        );

        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $page = max(
            1,
            (int)$this->input->get('page')
        );

        $limit = max(
            1,
            (int)$this->input->get('limit')
        );

        $search = trim(
            $this->input->get('search', true) ?? ''
        );

        $plant = trim(
            $this->input->get('plant', true) ?? ''
        );

        $dateFrom = trim(
            $this->input->get('date_from', true) ?? ''
        );

        $dateTo = trim(
            $this->input->get('date_to', true) ?? ''
        );

        $order = trim(
            $this->input->get('order', true) ?? ''
        );

        $dir = strtoupper(
            $this->input->get('dir', true) ?? 'DESC'
        );

        /*
        |--------------------------------------------------------------------------
        | DEFAULT ORDER
        |--------------------------------------------------------------------------
        */

        if (empty($order)) {

            $order = 'CREATED_AT';
        }

        if (
            $dir !== 'ASC' &&
            $dir !== 'DESC'
        ) {

            $dir = 'DESC';
        }

        $start =
            ($page - 1) *
            $limit;

        /*
        |--------------------------------------------------------------------------
        | SESSION
        |--------------------------------------------------------------------------
        */

        $role_id =
            (int)$this->session
                ->userdata('role_id');

        $username =
            $this->session
                ->userdata('username');

        $userPlant =
            $this->session
                ->userdata('plant');

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->Culling_model
                ->get_data(
                    $limit,
                    $start,
                    $role_id,
                    $userPlant,
                    $username,
                    $search,
                    $order,
                    $dir,
                    $plant,
                    $dateFrom,
                    $dateTo
                );

        $total =
            $this->Culling_model
                ->count_data(
                    $role_id,
                    $userPlant,
                    $username,
                    $search,
                    $plant,
                    $dateFrom,
                    $dateTo
                );

        $pages =
            $total > 0
                ? ceil($total / $limit)
                : 1;

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'total' =>
                (int)$total,

            'page' =>
                (int)$page,

            'pages' =>
                (int)$pages,

            'pagination' =>
                $this->build_pagination(
                    $pages,
                    $page
                )
        ]);
    }

    private function build_pagination(
        $pages,
        $current
    )
    {
        $html =
            '<ul class="pagination pagination-sm">';

        for (
            $i = 1;
            $i <= $pages;
            $i++
        ) {

            $active =
                ($i == $current)
                    ? 'active'
                    : '';

            $html .=
                '<li class="page-item ' .
                $active .
                '">

                    <a
                        href="javascript:void(0)"
                        class="page-link"
                        onclick="loadPage(' .
                        $i .
                        ')">

                        ' .
                        $i .
                        '

                    </a>

                </li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 PLANT
    |--------------------------------------------------------------------------
    */

    public function get_plant()
    {
        $data =
            $this->Culling_model
                ->get_plant_select2();

        echo json_encode(
            $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 CLASS OUT
    |--------------------------------------------------------------------------
    */

    public function get_class_out()
    {
        $term = trim(
            $this->input->get('q', true) ?? ''
        );

        $data =
            $this->Culling_model
                ->get_class_out_select2(
                    $term
                );

        echo json_encode(
            $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GET SALES UNTUK MODAL CULLING
    |--------------------------------------------------------------------------
    */
    public function get_sales()
    {
        header('Content-Type: application/json');

        $search = trim(
            $this->input->get('search', true) ?? ''
        );

        $data = $this->Culling_model
            ->get_sales_for_culling($search);

        echo json_encode([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function create()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try {
            // --------------------------------------------------
            // AMBIL INPUT EXISTING
            // --------------------------------------------------
            $plant = trim(
                $this->input->post('PLANT', true) ?? ''
            );

            $classOut = trim(
                $this->input->post('CLASS_OUT', true) ?? ''
            );

            $ymd = trim(
                $this->input->post('YMD', true) ?? ''
            );

            $beratInput = trim(
                $this->input->post('BERAT', true) ?? '0'
            );

            $remark = trim(
                $this->input->post('REMARK', true) ?? ''
            );

            // --------------------------------------------------
            // INPUT SALES BARU
            // --------------------------------------------------
            $sales = trim(
                $this->input->post('SALES', true) ?? ''
            );

            $ekorInput = trim(
                $this->input->post('EKOR', true) ?? '0'
            );

            // --------------------------------------------------
            // PARSING ANGKA FORMAT INDONESIA
            // Contoh: 1.500,50 menjadi 1500.50
            // --------------------------------------------------
            $parseNumber = function ($value) {
                if ($value === null || $value === '') {
                    return 0;
                }

                $value = trim((string) $value);

                // Hilangkan pemisah ribuan.
                $value = str_replace('.', '', $value);

                // Ubah desimal koma menjadi titik.
                $value = str_replace(',', '.', $value);

                if (!is_numeric($value)) {
                    throw new Exception(
                        'Format angka tidak valid.'
                    );
                }

                return (float) $value;
            };

            $ekor = $parseNumber($ekorInput);
            $berat = $parseNumber($beratInput);

            // --------------------------------------------------
            // VALIDASI FIELD WAJIB
            // --------------------------------------------------
            if (
                $plant === '' ||
                $ymd === '' ||
                $classOut === '' ||
                $sales === ''
            ) {
                throw new Exception(
                    'Plant, Tanggal, Sales dan Class Out wajib diisi.'
                );
            }

            if ($ekor <= 0) {
                throw new Exception(
                    'Jumlah Ekor harus lebih besar dari 0.'
                );
            }

            // --------------------------------------------------
            // AMBIL SALES DAN HARGA DARI DATABASE
            // Jangan menggunakan harga dari browser.
            // --------------------------------------------------
            $salesData = $this->Culling_model
                ->get_sales_price_for_culling($sales);

            if (!$salesData) {
                throw new Exception(
                    'Sales tidak ditemukan, tidak aktif, sudah dihapus, atau tidak memiliki detail.'
                );
            }

            $harga = (float) $salesData['HARGA'];

            if ($harga < 0) {
                throw new Exception(
                    'Harga Sales tidak valid.'
                );
            }

            // --------------------------------------------------
            // VALIDASI PLANT
            // Plant harus sesuai dengan Sales yang dipilih.
            // --------------------------------------------------
            if (
                isset($salesData['PLANT']) &&
                $salesData['PLANT'] !== $plant
            ) {
                throw new Exception(
                    'Plant tidak sesuai dengan Plant pada Sales yang dipilih.'
                );
            }

            // --------------------------------------------------
            // HITUNG JUMLAH DI SERVER
            // JUMLAH = EKOR x HARGA RATA-RATA
            // --------------------------------------------------
            $jumlah = $ekor * $harga;

            // --------------------------------------------------
            // INSERT DATA CULLING
            // --------------------------------------------------
            $data = [
                'plant'      => $plant,
                'ymd'        => $ymd,
                'SALES'      => $sales,
                'EKOR'       => $ekor,
                'HARGA'      => $harga,
                'jumlah'     => $jumlah,
                'berat'      => $berat,
                'class_out'  => $classOut,
                'remark'     => $remark,
                'CREATED_BY' => $this->session
                    ->userdata('username'),
                'CREATED_AT' => date('Y-m-d H:i:s')
            ];

            $this->db->insert(
                'abc_mst_culling',
                $data
            );

            if ($this->db->trans_status() === false) {
                throw new Exception(
                    'Gagal menyimpan data.'
                );
            }

            $id = $this->db->insert_id();

            $this->db->trans_commit();

            echo json_encode([
                'status'  => true,
                'message' => 'Data culling berhasil disimpan.',
                'idno'    => $id
            ]);

        } catch (Exception $e) {
            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function edit()
    {
        header('Content-Type: application/json');

        $idno = (int)
            $this->input->get(
                'idno',
                true
            );

        if (
            empty($idno)
        ) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'ID Culling kosong.'

            ]);

            return;
        }

        $header =
            $this->db

                ->select("
                    c.*,

                    p.CODE_NAME
                        AS PLANT_NAME,

                    co.CODE_NAME
                        AS CLASS_OUT_NAME
                ")

                ->from('abc_mst_culling c')

                ->join(
                    'abc_cd_code p',
                    "
                        p.CODE = c.plant
                        AND p.HEAD_CODE='PLANT'
                    ",
                    'left',
                    false
                )

                ->join(
                    'abc_cd_code co',
                    "
                        co.CODE = c.class_out
                        AND co.HEAD_CODE='CLASS OUT'
                    ",
                    'left',
                    false
                )

                ->where(
                    'c.idno',
                    $idno
                )

                ->get()

                ->row_array();

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Data culling tidak ditemukan.'

            ]);

            return;
        }

        echo json_encode([

            'status' => true,

            'header' => $header

        ]);
    }

    public function update()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try {
            // --------------------------------------------------
            // ID CULLING
            // --------------------------------------------------
            $idno = (int) $this->input->post('IDNO');

            if (empty($idno)) {
                throw new Exception('ID Culling kosong.');
            }

            $header = $this->Culling_model
                ->get_culling_header($idno);

            if (!$header) {
                throw new Exception(
                    'Data culling tidak ditemukan.'
                );
            }

            // --------------------------------------------------
            // INPUT EXISTING
            // --------------------------------------------------
            $plant = trim(
                $this->input->post('PLANT', true) ?? ''
            );

            $ymd = trim(
                $this->input->post('YMD', true) ?? ''
            );

            $classOut = trim(
                $this->input->post('CLASS_OUT', true) ?? ''
            );

            $remark = trim(
                $this->input->post('REMARK', true) ?? ''
            );

            $beratInput = trim(
                $this->input->post('BERAT', true) ?? '0'
            );

            // --------------------------------------------------
            // INPUT SALES BARU
            // --------------------------------------------------
            $sales = trim(
                $this->input->post('SALES', true) ?? ''
            );

            $ekorInput = trim(
                $this->input->post('EKOR', true) ?? '0'
            );

            // --------------------------------------------------
            // PARSING ANGKA FORMAT INDONESIA
            // Contoh: 1.500,50 menjadi 1500.50
            // --------------------------------------------------
            $parseNumber = function ($value) {
                if ($value === null || $value === '') {
                    return 0;
                }

                $value = trim((string) $value);
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);

                if (!is_numeric($value)) {
                    throw new Exception(
                        'Format angka tidak valid.'
                    );
                }

                return (float) $value;
            };

            $ekor = $parseNumber($ekorInput);
            $berat = $parseNumber($beratInput);

            // --------------------------------------------------
            // VALIDASI FIELD WAJIB
            // --------------------------------------------------
            if (
                $plant === '' ||
                $ymd === '' ||
                $classOut === '' ||
                $sales === ''
            ) {
                throw new Exception(
                    'Plant, Tanggal, Sales dan Class Out wajib diisi.'
                );
            }

            if ($ekor <= 0) {
                throw new Exception(
                    'Jumlah Ekor harus lebih besar dari 0.'
                );
            }

            // --------------------------------------------------
            // AMBIL SALES DAN HARGA DARI DATABASE
            // Harga browser tidak digunakan sebagai acuan.
            // --------------------------------------------------
            $salesData = $this->Culling_model
                ->get_sales_price_for_culling($sales);

            if (!$salesData) {
                throw new Exception(
                    'Sales tidak ditemukan, tidak aktif, sudah dihapus, atau tidak memiliki detail.'
                );
            }

            $harga = (float) $salesData['HARGA'];

            if ($harga < 0) {
                throw new Exception(
                    'Harga Sales tidak valid.'
                );
            }

            // --------------------------------------------------
            // VALIDASI PLANT SALES
            // --------------------------------------------------
            if (
                isset($salesData['PLANT']) &&
                $salesData['PLANT'] !== $plant
            ) {
                throw new Exception(
                    'Plant tidak sesuai dengan Plant pada Sales yang dipilih.'
                );
            }

            // --------------------------------------------------
            // HITUNG ULANG JUMLAH DI SERVER
            // JUMLAH = EKOR x HARGA RATA-RATA
            // --------------------------------------------------
            $jumlah = $ekor * $harga;

            // --------------------------------------------------
            // UPDATE DATA CULLING
            // --------------------------------------------------
            $data = [
                'plant'      => $plant,
                'ymd'        => $ymd,
                'SALES'      => $sales,
                'EKOR'       => $ekor,
                'HARGA'      => $harga,
                'jumlah'     => $jumlah,
                'berat'      => $berat,
                'class_out'  => $classOut,
                'remark'     => $remark,
                'UPDATED_BY' => $this->session
                    ->userdata('username'),
                'UPDATED_AT' => date('Y-m-d H:i:s')
            ];

            $this->db
                ->where('idno', $idno)
                ->update('abc_mst_culling', $data);

            if ($this->db->trans_status() === false) {
                throw new Exception('Update gagal.');
            }

            $this->db->trans_commit();

            echo json_encode([
                'status'  => true,
                'message' => 'Data culling berhasil diupdate.'
            ]);

        } catch (Exception $e) {
            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function remove()
    {
        header('Content-Type: application/json');

        $idno = (int)
            $this->input->post(
                'idno'
            );

        if (
            empty($idno)
        ) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'ID Culling wajib diisi.'

            ]);

            return;
        }

        $header =
            $this->Culling_model
                ->get_culling_header(
                    $idno
                );

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Data culling tidak ditemukan.'

            ]);

            return;
        }

        $this->db->trans_begin();

        try {

            $this->db

                ->where(
                    'idno',
                    $idno
                )

                ->delete(
                    'abc_mst_culling'
                );

            if (
                $this->db->trans_status()
                === false
            ) {

                throw new Exception(
                    'Gagal menghapus data.'
                );

            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Data culling berhasil dihapus.'

            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' =>
                    $e->getMessage()

            ]);

        }
    }

    public function print_pdf()
    {
        $idno = (int)
            $this->input->get(
                'idno',
                true
            );

        if (empty($idno)) {

            show_error(
                'ID Culling tidak ditemukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = $this->db

            ->select("
                c.*,

                p.CODE_NAME
                    AS PLANT_NAME,

                co.CODE_NAME
                    AS CLASS_OUT_NAME
            ", false)

            ->from('abc_mst_culling c')

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = c.plant
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_code co',
                "
                    co.CODE = c.class_out
                    AND co.HEAD_CODE='CLASS OUT'
                ",
                'left',
                false
            )

            ->where(
                'c.idno',
                $idno
            )

            ->get()

            ->row();

        if (!$header) {

            show_error(
                'Data culling tidak ditemukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = [

            'jumlah' =>
                (float)
                $header->jumlah,

            'berat' =>
                (float)
                $header->berat
        ];

        $data = [

            'header' =>
                $header,

            'summary' =>
                $summary
        ];

        /*
        |--------------------------------------------------------------------------
        | HTML
        |--------------------------------------------------------------------------
        */

        $html =
            $this->load->view(
                'admin/culling/pdf_template',
                $data,
                true
            );

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $this->load->library(
            'pdf'
        );

        $this->pdf->loadHtml(
            $html
        );

        $this->pdf->setPaper(
            'A4',
            'portrait'
        );

        $this->pdf->render();

        /*
        |--------------------------------------------------------------------------
        | STREAM
        |--------------------------------------------------------------------------
        */

        $this->pdf->stream(

            'CULLING_' .
            $header->idno .
            '.pdf',

            [
                'Attachment' =>
                    false
            ]
        );

        exit;
    }
}