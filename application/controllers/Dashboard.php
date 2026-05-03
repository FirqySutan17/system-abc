<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
    }

    public function index()
    {
        $data['plants'] = $this->Dashboard_model->get_plant_list();

        $this->load->view('templates/header', ['title' => 'Dashboard']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/footer');
    }

    public function get_sales_dashboard()
    {
        $year = $this->input->get('year');
        $plant = $this->input->get('plant');

        $kpi   = $this->Dashboard_model->get_kpi($year, $plant);
        $trend = $this->Dashboard_model->get_sales_trend($year, $plant);
        $plantData = $this->Dashboard_model->get_sales_per_plant($year);
        $items = $this->Dashboard_model->get_top_items($year, $plant);

        echo json_encode([
            'kpi'   => $kpi,
            'trend' => $trend,
            'plant' => $plantData,
            'items' => $items
        ]);
    }
}
