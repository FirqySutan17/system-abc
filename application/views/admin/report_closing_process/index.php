<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">CLOSING PROCESS</h5>

            <form id="salesProcessForm">

                <!-- FILTER -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Plant</label>
                        <select name="plant" id="plant" class="form-select" required>
                            <option value="">-- Select Plant --</option>
                            <?php foreach($plants as $p): ?>
                                <option value="<?= $p->CODE; ?>"
                                    <?= $p->CODE == $defaultPlant ? 'selected' : ''; ?>>
                                    <?= $p->CODE_NAME; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Closing Month</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <!-- MESSAGE PANEL -->
                    <div class="col-md-3">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white fw-bold">MESSAGE LOG</div>
                            <div class="card-body" id="processLog" style="height:420px; overflow-y:auto; font-size:13px;"></div>
                        </div>
                    </div>

                    <!-- PROCESS PANEL -->
                    <div class="col-md-9">

                        <div class="mb-3">
                            <div class="progress" style="height:22px;">
                                <div id="processProgress" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%">0%</div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <?php
                            $processes = [
                                'inventory_closing'   => ['INVENTORY PRICE CLOSING', 'success'],
                                'cost_closing'        => ['COST CLOSING', 'primary'],
                                'sales_pl_closing'    => ['SALES PL CLOSING', 'dark'],
                                'pl_closing'          => ['PL CLOSING', 'warning']
                            ];
                            foreach($processes as $val => $cfg): ?>
                            <div class="col-md-6">
                                <div class="card border-<?= $cfg[1]; ?>">
                                    <div class="card-header fw-bold text-<?= $cfg[1]; ?>"><?= $cfg[0]; ?></div>
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input process-check"
                                                   type="checkbox"
                                                   name="process[]"
                                                   value="<?= $val; ?>"
                                                   checked>
                                            <label class="form-check-label">Run <?= $cfg[0]; ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="button" id="runProcessBtn" class="btn btn-danger fw-bold px-4">
                                RUN SELECTED PROCESS
                            </button>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function addLog(message, type="info") {
        let color = {success:"green", error:"red", process:"#0d6efd"}[type] || "#000";
        $("#processLog").append(`<div style="color:${color}">➤ ${message}</div>`);
        $("#processLog").scrollTop($("#processLog")[0].scrollHeight);
    }

    function updateProgress(percent){
        $("#processProgress").css("width", percent+"%").text(percent+"%");
    }

    // Default Closing Month = bulan berjalan
    $(document).ready(function(){
        let now = new Date();
        $("#date").val(new Date().toISOString().slice(0,10));
    });

    $("#runProcessBtn").on("click", function(){

        let plant = $("#plant").val();
        let date = $("#date").val();
        let selected = $(".process-check:checked");

        if(!plant) return addLog("Plant must be selected.", "error");
        if(!date) return addLog("Closing date must be selected.", "error");
        if(selected.length === 0) return addLog("Please select at least one process.", "error");

        $("#processLog").html("");
        updateProgress(0);

        let postData = {
            plant: plant,
            date: date,
            process: selected.map(function(){ return this.value; }).get()
        };

        $.ajax({
            url: "<?= base_url('closing-process/run') ?>",
            type: "POST",
            data: postData,
            dataType: "json",

            beforeSend: function(){
                $("#runProcessBtn").prop("disabled", true).text("PROCESSING...");
                $(".process-check, #plant, #month").prop("disabled", true);
            },

            success: function(res){
                let total = res.logs.length, current = 0;
                res.logs.forEach(function(log){
                    current++;
                    updateProgress(Math.round((current/total)*100));
                    addLog(log.message, log.status);
                });

                $("#runProcessBtn").prop("disabled", false).text("RUN SELECTED PROCESS");
                $(".process-check, #plant, #month").prop("disabled", false);
            },
            error: function(){
                addLog("Server error occurred.", "error");
                $("#runProcessBtn").prop("disabled", false).text("RUN SELECTED PROCESS");
                $(".process-check, #plant, #month").prop("disabled", false);
            }
        });
    });
</script>
