<div class="container-fluid dashboard-modern" style="overflow-x: hidden">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .dashboard-modern {
            background: #f5f7fb;
            padding-bottom: 30px;
        }

        .modern-select {
            border-radius: 10px;
            padding: 6px 12px;
        }

        .kpi-card {
            border-radius: 18px;
            padding: 25px;
            color: #fff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transition: 0.3s ease;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
        }

        .kpi-sales { background: linear-gradient(135deg,#4e73df,#224abe); }
        .kpi-outstanding { background: linear-gradient(135deg,#e74a3b,#c0392b); }
        .kpi-dp { background: linear-gradient(135deg,#1cc88a,#17a673); }
        .kpi-customer { background: linear-gradient(135deg,#f6c23e,#d4a017); }

        .kpi-card span {
            font-size: 14px;
            opacity: 0.9;
        }

        .kpi-card h3 {
            margin-top: 8px;
            font-weight: bold;
        }

        .modern-card {
            background: #fff;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            transition: 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-4px);
        }
        .chart-wrapper {
            position: relative;
            height: 350px; /* samakan tinggi */
        }

        .chart-wrapper canvas {
            height: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            border-radius: 12px !important;
            height: 42px !important;
            padding: 6px 12px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.04);
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: #4e73df;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 0px;
        }

        .select2-dropdown {
            border-radius: 8px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            margin-top: 5px
        }
    </style>

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Sales Dashboard</h4>

        <div class="d-flex gap-2" style="width: 40%; justify-content: flex-end;">
            <select id="plantFilter" class="form-control select2-filter" style="width:220px;">
                <option value="">All Plant</option>
                <?php foreach($plants as $p): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select id="yearFilter" class="form-control select2-filter" style="width:200px;">
                <?php for($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                    <option value="<?= $y ?>"><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <!-- KPI CARDS -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="kpi-card kpi-sales">
                <div>
                    <span>Total Sales</span>
                    <h3 id="totalSales">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card kpi-outstanding">
                <div>
                    <span>Total Outstanding</span>
                    <h3 id="totalOutstanding">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card kpi-dp">
                <div>
                    <span>Total DP</span>
                    <h3 id="totalDP">0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card kpi-customer">
                <div>
                    <span>Total Customer</span>
                    <h3 id="totalCustomer">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="modern-card h-100">
                <h6 class="mb-3">Sales Trend</h6>
                <div class="chart-wrapper">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="modern-card h-100">
                <h6 class="mb-3">Sales per Plant</h6>
                <div class="chart-wrapper">
                    <canvas id="plantChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="modern-card">
                <h6 class="mb-3">Top 10 Item</h6>
                <canvas id="topItemChart"></canvas>
            </div>
        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    $('.select2-filter').select2({
        placeholder: "Select option",
        allowClear: true,
        minimumResultsForSearch: 0
    });

    let trendChart;
    let plantChart;
    let topItemChart;

    function formatRupiah(number){
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(number);
    }

    function animateValue(id, value, isCurrency = true) {
        const el = document.getElementById(id);
        let start = 0;
        let duration = 600;
        let increment = value / (duration / 16);

        function update() {
            start += increment;
            if (start >= value) start = value;

            el.innerText = isCurrency 
                ? formatRupiah(Math.floor(start))
                : Math.floor(start);

            if (start < value) requestAnimationFrame(update);
        }
        update();
    }

    function formatRupiahFull(number){
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    function formatRupiahShort(number){

        if (number >= 1000000000) {
            return 'Rp' + (number / 1000000000).toFixed(1).replace('.', ',') + ' M';
        }

        if (number >= 1000000) {
            return 'Rp' + (number / 1000000).toFixed(1).replace('.', ',') + ' Jt';
        }

        return formatRupiahFull(number);
    }

    function loadDashboard(year, plant) {

        $('canvas').css('opacity', '0.3');

        $.get("<?= base_url('dashboard/get_sales_dashboard') ?>",
            {year: year, plant: plant},
            function(res){

                animateValue('totalSales', res.kpi?.total_sales ?? 0, true);
                animateValue('totalOutstanding', res.kpi?.total_outstanding ?? 0, true);
                animateValue('totalDP', res.kpi?.total_dp ?? 0, true);
                animateValue('totalCustomer', res.kpi?.total_customer ?? 0, false);

                if (trendChart) trendChart.destroy();
                if (plantChart) plantChart.destroy();
                if (topItemChart) topItemChart.destroy();

                trendChart = new Chart(document.getElementById('salesTrendChart'), {
                    type: 'line',
                    data: {
                        labels: res.trend.map(r => r.ym),
                        datasets: [{
                            label: 'Sales',
                            data: res.trend.map(r => r.total_sales),
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return formatRupiahShort(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return formatRupiahFull(context.raw);
                                    }
                                }
                            }
                        }
                    }
                });

                plantChart = new Chart(document.getElementById('plantChart'), {
                    type: 'bar',
                    data: {
                        labels: res.plant.map(r => r.plant_name ?? r.PLANT),
                        datasets: [{
                            label: 'Sales',
                            data: res.plant.map(r => r.total_sales),
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return formatRupiahShort(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return formatRupiahFull(context.raw);
                                    }
                                }
                            }
                        }
                    }
                });

                topItemChart = new Chart(document.getElementById('topItemChart'), {
                    type: 'bar',
                    data: {
                        labels: res.items.map(r => r.item_name ?? r.ITEM),
                        datasets: [{
                            label: 'Revenue',
                            data: res.items.map(r => r.total_sales),
                            borderRadius: 8
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        scales: {
                            x: {
                                ticks: {
                                    callback: function(value) {
                                        return formatRupiahShort(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return formatRupiahFull(context.raw);
                                    }
                                }
                            }
                        }
                    }
                });

                $('canvas').css('opacity', '1');

        }, 'json');
    }

    $(document).ready(function(){

        function reload() {
            loadDashboard(
                $('#yearFilter').val(),
                $('#plantFilter').val()
            );
        }

        reload();

        $('#yearFilter, #plantFilter').on('change', function(){
            loadDashboard(
                $('#yearFilter').val(),
                $('#plantFilter').val()
            );
        });

    });
</script>