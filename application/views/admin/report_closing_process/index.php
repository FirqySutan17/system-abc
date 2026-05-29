<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h3 class="fw-bold mb-1">
                        Month End Closing Process
                    </h3>

                    <p class="text-muted mb-0">
                        Execute monthly inventory, costing and profit & loss closing procedures.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Plant
                    </label>

                    <select
                        name="plant"
                        id="plant"
                        class="form-select">

                        <option value="">
                            -- Select Plant --
                        </option>

                        <?php foreach($plants as $p): ?>
                            <option
                                value="<?= $p->CODE; ?>"
                                <?= $p->CODE == $defaultPlant ? 'selected' : ''; ?>>

                                <?= $p->CODE_NAME; ?>

                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Closing Month
                    </label>

                    <input
                        type="month"
                        id="month"
                        class="form-control">
                </div>

                <div class="col-md-4">

                    <label class="form-label fw-semibold">
                        Selected Process
                    </label>

                    <div class="form-control bg-light">
                        <span id="selectedCount">4</span> Process Selected
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- PROGRESS -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">
                    Process Progress
                </span>

                <span id="progressText">
                    0%
                </span>
            </div>

            <div class="progress" style="height:12px;">
                <div
                    id="processProgress"
                    class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                    style="width:0%">
                </div>
            </div>

        </div>
    </div>

    <div class="card border-0 mb-4" style="background: transparent; display: flex; align-items: center; justify-content: center;">
        <!-- PROCESS CARD -->
        <div class="row" style="width: 100%">

            <div class="col-md-3">
                <div class="process-card selected">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="process-icon text-success">
                                <i class="ti ti-package"></i>
                            </div>

                            <div class="process-title">
                                Inventory Closing
                            </div>

                            <div class="process-desc">
                                Calculate inventory valuation and ending stock.
                            </div>
                        </div>

                        <div>
                            <input
                                type="checkbox"
                                class="form-check-input process-check"
                                value="inventory_closing"
                                checked>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="process-card selected">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="process-icon text-primary">
                                <i class="ti ti-calculator"></i>
                            </div>

                            <div class="process-title">
                                Cost Closing
                            </div>

                            <div class="process-desc">
                                Calculate production and operational costs.
                            </div>
                        </div>

                        <div>
                            <input
                                type="checkbox"
                                class="form-check-input process-check"
                                value="cost_closing"
                                checked>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="process-card selected">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="process-icon text-dark">
                                <i class="ti ti-chart-line"></i>
                            </div>

                            <div class="process-title">
                                Sales P/L Closing
                            </div>

                            <div class="process-desc">
                                Generate sales profit and loss calculation.
                            </div>
                        </div>

                        <div>
                            <input
                                type="checkbox"
                                class="form-check-input process-check"
                                value="sales_pl_closing"
                                checked>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="process-card selected">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="process-icon text-warning">
                                <i class="ti ti-report-money"></i>
                            </div>

                            <div class="process-title">
                                P/L Closing
                            </div>

                            <div class="process-desc">
                                Finalize company profit and loss statement.
                            </div>
                        </div>

                        <div>
                            <input
                                type="checkbox"
                                class="form-check-input process-check"
                                value="pl_closing"
                                checked>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    

    <!-- ACTION -->
    <div class="text-center mt-4 mb-4">
        <button
            type="button"
            id="runProcessBtn"
            class="btn btn-danger btn-lg px-5 shadow">

            <i class="ti ti-player-play me-2"></i>
            Run Closing Process

        </button>
    </div>

    <!-- LOG -->
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">
                <i class="ti ti-history"></i>
                Process Activity Log
            </h5>
        </div>

        <div
            class="card-body"
            id="processLog"
            style="height:350px;overflow-y:auto;">
        </div>

    </div>

</div>

<style>

.process-card{
    border:1px solid #e9ecef;
    border-radius:16px;
    padding:20px;
    background:#fff;
    transition:.25s ease;
    cursor:pointer;
    height:100%;
}

.process-card:hover{
    transform:translateY(-4px);
    box-shadow:0 12px 25px rgba(0,0,0,.08);
}

.process-card.selected{
    border:2px solid #dc3545;
    background:#fff5f5;
}

.process-icon{
    font-size:32px;
    margin-bottom:10px;
}

.process-title{
    font-size:16px;
    font-weight:700;
}

.process-desc{
    font-size:13px;
    color:#6c757d;
    margin-top:6px;
}

#processLog{
    font-size:14px;
}

</style>

<script>

function updateSelectedCount()
{
    $("#selectedCount").text(
        $(".process-check:checked").length
    );
}

function addLog(message,type="info")
{
    let badge = "secondary";

    if(type==="success") badge="success";
    if(type==="error") badge="danger";
    if(type==="process") badge="primary";

    $("#processLog").append(`
        <div class="border-bottom py-2">
            <span class="badge bg-${badge}">
                ${type.toUpperCase()}
            </span>
            <span class="ms-2">${message}</span>
        </div>
    `);

    $("#processLog").scrollTop(
        $("#processLog")[0].scrollHeight
    );
}

function updateProgress(percent)
{
    $("#processProgress")
        .css("width", percent + "%");

    $("#progressText")
        .text(percent + "%");
}

$(document).ready(function(){

    const now = new Date();

    $("#month").val(
        now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2,'0')
    );

    updateSelectedCount();

});

$(document).on("change",".process-check",function(){

    $(this)
        .closest(".process-card")
        .toggleClass(
            "selected",
            $(this).is(":checked")
        );

    updateSelectedCount();

});

$("#runProcessBtn").on("click",function(){

    let plant = $("#plant").val();
    let month = $("#month").val();

    let selected = $(".process-check:checked");

    if(!plant)
        return addLog("Plant must be selected.","error");

    if(!month)
        return addLog(
            "Closing month must be selected.",
            "error"
        );

    if(selected.length===0)
        return addLog("Please select at least one process.","error");

    $("#processLog").html("");

    updateProgress(0);

    let postData = {
        plant : plant,
        month : month,
        process : selected.map(function(){
            return this.value;
        }).get()
    };

    $.ajax({

        url : "<?= base_url('closing-process/run') ?>",

        type : "POST",

        data : postData,

        dataType : "json",

        beforeSend : function(){

            $("#runProcessBtn")
                .prop("disabled",true)
                .html(`
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Processing...
                `);

            $(".process-check,#plant,#month")
                .prop("disabled",true);
        },

        success : function(res){

            let total = res.logs.length;

            let current = 0;

            res.logs.forEach(function(log){

                current++;

                updateProgress(
                    Math.round((current/total)*100)
                );

                addLog(
                    log.message,
                    log.status
                );

            });

            $("#runProcessBtn")
                .prop("disabled",false)
                .html(`
                    <i class="ti ti-player-play me-2"></i>
                    Run Closing Process
                `);

            $(".process-check,#plant,#month")
                .prop("disabled",false);

        },

        error : function(){

            addLog(
                "Server error occurred.",
                "error"
            );

            $("#runProcessBtn")
                .prop("disabled",false)
                .html(`
                    <i class="ti ti-player-play me-2"></i>
                    Run Closing Process
                `);

            $(".process-check,#plant,#month")
                .prop("disabled",false);
        }

    });

});

</script>