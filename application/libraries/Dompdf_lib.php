<?php
require_once FCPATH . "vendor/autoload.php";

use Dompdf\Dompdf;

class Dompdf_lib {
    public function load() {
        return new Dompdf();
    }
}
