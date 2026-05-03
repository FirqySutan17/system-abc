<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// PANGGIL AUTOLOAD COMPOSER
require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf extends Dompdf
{
    public function __construct()
    {
        parent::__construct();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $this->setOptions($options);
    }
}