<!-- application/views/dashboard/index.php -->

<div class="container-fluid dashboard-modern">

    <style>

        .dashboard-modern{
            padding: 25px;
        }

        /*
        |--------------------------------------------------------------------------
        | WELCOME
        |--------------------------------------------------------------------------
        */

        .welcome-wrapper{
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            padding: 30px;
            min-height: 210px;
            background: rgba(255,255,255,.75);
            border-radius: 24px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,.05);
            animation: fadeInUp .5s ease;
        }

        .welcome-title{
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1f2937;
            line-height: 1.3;
        }

        .welcome-title span{
            background: linear-gradient(
                90deg,
                #e60012,
                #0072bc
            );

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-user{
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #374151;
            letter-spacing: 1px;
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY CARD
        |--------------------------------------------------------------------------
        */

        .card-summary{
            border-radius: 22px;
            padding: 24px;
            color: white;
            min-height: 210px;

            display:flex;
            flex-direction:column;
            justify-content:center;

            box-shadow: 0 15px 35px rgba(0,0,0,.08);

            transition: .25s;
        }

        .card-summary:hover{
            transform: translateY(-4px);
        }

        .po-card{
            background: linear-gradient(
                135deg,
                #2563eb,
                #1d4ed8
            );
        }

        .receive-card{
            background: linear-gradient(
                135deg,
                #f59e0b,
                #d97706
            );
        }

        .sales-card{
            background: linear-gradient(
                135deg,
                #10b981,
                #059669
            );
        }

        .material-card{
            background: linear-gradient(
                135deg,
                #ef4444,
                #dc2626
            );
        }

        .summary-title{
            font-size: 14px;
            opacity: .9;
            margin-bottom: 12px;
            font-weight: 600;
            letter-spacing: .5px;
        }

        .summary-value{
            font-size: 42px;
            font-weight: 800;
            line-height: 1;
        }

        .summary-sub{
            margin-top: 12px;
            font-size: 16px;
            opacity: .95;
        }

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD CARD
        |--------------------------------------------------------------------------
        */

        .dashboard-card{
            background: white;
            border-radius: 22px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,.05);
            height: 100%;
        }

        .card-title-dashboard{
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
        }

        /*
        |--------------------------------------------------------------------------
        | CHART
        |--------------------------------------------------------------------------
        */

        #monthlyChart{
            min-height: 380px;
        }

        #materialChart{
            min-height: 380px;
        }

        /*
        |--------------------------------------------------------------------------
        | ANIMATION
        |--------------------------------------------------------------------------
        */

        @keyframes fadeInUp {

            from{
                opacity:0;
                transform:translateY(10px);
            }

            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media(max-width:991px){

            .card-summary{
                min-height:auto;
            }

            .welcome-wrapper{
                min-height:auto;
            }

            .welcome-title{
                font-size: 24px;
            }

            .summary-value{
                font-size: 30px;
            }
        }

    </style>

    <!-- =========================================================
    HEADER
    ========================================================== -->

    <div class="row g-3 mb-4">

        <div class="col-lg-4">

            <div class="welcome-wrapper">

                <h2 class="welcome-title">

                    Welcome to
                    <span>ABC System</span>
                    👋

                </h2>

                <p class="welcome-user">

                    <?= strtoupper(
                        $this->session->userdata('name')
                    ); ?>

                </p>

            </div>

        </div>

        <!-- =====================================================
        SUMMARY
        ====================================================== -->

        <div class="col-lg-2 col-md-6">

            <div class="card-summary po-card">

                <div class="summary-title">
                    TOTAL PO
                </div>

                <div class="summary-value">

                    <?= number_format(
                        $summary['po']['total_po']
                    ); ?>

                </div>

                <div class="summary-sub">

                    Rp
                    <?= number_format(
                        $summary['po']['total_nominal']
                    ); ?>

                </div>

            </div>

        </div>

        <div class="col-lg-2 col-md-6">

            <div class="card-summary receive-card">

                <div class="summary-title">
                    TOTAL RECEIVE
                </div>

                <div class="summary-value">

                    <?= number_format(
                        $summary['receive']['total_receive']
                    ); ?>

                </div>

                <div class="summary-sub">
                    Receive Data
                </div>

            </div>

        </div>

        <div class="col-lg-2 col-md-6">

            <div class="card-summary sales-card">

                <div class="summary-title">
                    TOTAL SALES
                </div>

                <div class="summary-value">

                    <?= number_format(
                        $summary['sales']['total_sales']
                    ); ?>

                </div>

                <div class="summary-sub">

                    Rp
                    <?= number_format(
                        $summary['sales']['omzet']
                    ); ?>

                </div>

            </div>

        </div>

        <div class="col-lg-2 col-md-6">

            <div class="card-summary material-card">

                <div class="summary-title">
                    TOP MATERIAL
                </div>

                <div class="summary-value" style="font-size:22px; line-height:1.3;">

                    <?= $top_material[0]['MATERIAL_NAME']
                        ?? '-'; ?>

                </div>

                <div class="summary-sub">

                    Qty :
                    <?= number_format(
                        $top_material[0]['qty']
                        ?? 0
                    ); ?>

                </div>

            </div>

        </div>

    </div>

    <!-- =========================================================
    CHART
    ========================================================== -->

    <div class="row g-3">

        <div class="col-lg-8">

            <div class="dashboard-card">

                <div class="card-title-dashboard">
                    Monthly Trend
                </div>

                <div id="monthlyChart"></div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="dashboard-card">

                <div class="card-title-dashboard">
                    Top Material
                </div>

                <div id="materialChart"></div>

            </div>

        </div>

    </div>

</div>

<?php

/*
|--------------------------------------------------------------------------
| MONTHLY TREND
|--------------------------------------------------------------------------
*/

$months = [];

$poData = [];

$receiveData = [];

$salesData = [];

foreach($monthly_trend as $row){

    $months[] = date(
        'M Y',
        strtotime($row['bulan'].'-01')
    );

    $poData[] =
        (float)$row['po_total'];

    $receiveData[] =
        (float)$row['receive_total'];

    $salesData[] =
        (float)$row['sales_total'];
}

/*
|--------------------------------------------------------------------------
| TOP MATERIAL
|--------------------------------------------------------------------------
*/

$materialLabel = [];

$materialQty = [];

foreach($top_material as $m){

    $materialLabel[] =
        $m['MATERIAL_NAME'];

    $materialQty[] =
        (float)$m['qty'];
}
?>

<script>

    /*
    |--------------------------------------------------------------------------
    | MONTHLY CHART
    |--------------------------------------------------------------------------
    */

    const monthlyOptions = {

        chart: {

            type: 'bar',

            height: 380,

            toolbar: {
                show: false
            }
        },

        series: [

            {
                name: 'PO',
                data:
                    <?= json_encode($poData); ?>
            },

            {
                name: 'Receive',
                data:
                    <?= json_encode($receiveData); ?>
            },

            {
                name: 'Sales',
                data:
                    <?= json_encode($salesData); ?>
            }

        ],

        xaxis: {

            categories:
                <?= json_encode($months); ?>
        },

        plotOptions: {

            bar: {

                horizontal: false,

                columnWidth: '45%',

                borderRadius: 6
            }
        },

        dataLabels: {
            enabled: false
        },

        stroke: {
            show: false
        },

        legend: {
            position: 'top'
        },

        grid: {
            borderColor: '#f1f1f1'
        },

        tooltip: {

            y: {

                formatter: function(val){

                    return 'Rp ' +
                        Number(val).toLocaleString(
                            'id-ID'
                        );

                }
            }
        },

        yaxis: {

            labels: {

                formatter: function(val){

                    return 'Rp ' +
                        Number(val).toLocaleString(
                            'id-ID'
                        );

                }
            }
        }
    };

    new ApexCharts(
        document.querySelector("#monthlyChart"),
        monthlyOptions
    ).render();

    /*
    |--------------------------------------------------------------------------
    | TOP MATERIAL CHART
    |--------------------------------------------------------------------------
    */

    const materialOptions = {

        chart: {

            type: 'bar',

            height: 380,

            toolbar: {
                show: false
            }
        },

        series: [

            {

                name: 'Qty',

                data:
                    <?= json_encode($materialQty); ?>
            }

        ],

        xaxis: {

            categories:
                <?= json_encode($materialLabel); ?>
        },

        plotOptions: {

            bar: {

                borderRadius: 8,

                horizontal: true
            }
        },

        dataLabels: {
            enabled: false
        },

        grid: {
            borderColor: '#f1f1f1'
        }
    };

    new ApexCharts(
        document.querySelector("#materialChart"),
        materialOptions
    ).render();

</script>