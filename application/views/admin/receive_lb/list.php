<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">RECEIVE LIVE BIRD - INVENTORY</h5>

            <!-- SEARCH + ADD -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari receive..." />
                </div>
                <div class="col-md-4 text-end mt-2 mt-md-0">
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#receiveLbAdd">
                        <i class="ti ti-plus"></i> Tambah Receive
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th data-order="PLANT" style="text-align:center;">Plant</th>
                            <th data-order="RECEIVE_DATE" style="text-align:center;">Tgl Receive</th>
                            <th data-order="RECEIVE" style="text-align:center;">Receive</th>
                            <th data-order="DO" style="text-align:center;">DO</th>
                            <th data-order="SUPPLIER" style="text-align:center;">Supplier</th>
                            <th data-order="QTY" style="text-align:center;">Qty</th>
                            <th data-order="WEIGHT" style="text-align:center;">Berat</th>
                            <th data-order="RECEIVE_AMOUNT" style="text-align:center;">Amount</th>
                            <th style="text-align:center;">#</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>

<style>
    .flex-inline {
        padding: 2px 10px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        align-content: center;
        flex-wrap: nowrap;
        flex-direction: row;
    }
    label {
        width: 35%;
    }
    .space-line {
        border-bottom: 5px double black;
        margin-bottom: 10px
    }
</style>

<!-- ================= MODAL ADD ================= -->
<div class="modal fade" id="receiveLbAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="freceiveLbAdd" enctype="multipart/form-data">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">RECEIVE LIVE BIRD - TAMBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-2 mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Receive</label>
                            <input class="form-control" placeholder="Auto Generate" readonly style="background:#efefef">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select id="plantAdd" class="form-control" required></select>
                            <input type="hidden" name="PLANT" id="hiddenPlantAdd">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal Receive *</label>
                            <input id="RECEIVE_DATE" name="RECEIVE_DATE" type="date" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Pembayaran</label>
                            <div style="padding:5px 0px; width: 100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="CASH">
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="TRANSFER">
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Jenis Pembayaran</label>
                            <div style="padding:5px 0px; width: 100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="JENIS_PAY" value="LUNAS">
                                    <label class="form-check-label">LUNAS</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="JENIS_PAY" value="TEMPO">
                                    <label class="form-check-label">TEMPO</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Slip / Nota</label>
                            <input class="form-control" placeholder="Input disini.." name="SLIP_NO" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" style="width: 17%">Attachment</label>
                            <input type="file" name="ATTACHMENT" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx">
                            <small class="text-muted">&nbsp; *Hanya 1 file, akan menimpa jika sudah ada</small>
                        </div>

                        <div class="space-line"></div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Delivery Order *</label>
                            <input name="DO" class="form-control" required placeholder="No. DO ...">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Supplier *</label>
                            <select id="supplierAdd" class="form-control" ></select>
                            <input type="hidden" name="SUPPLIER" id="hiddensupplierAdd">
                        </div>
                        
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Datang *</label>
                            <input id="ARRIVE_SCHEDULE" name="ARRIVE_SCHEDULE" type="datetime-local" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Bongkar *</label>
                            <input id="DEPART_SCHEDULE" name="DEPART_SCHEDULE" type="datetime-local" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Driver *</label>
                            <input name="DRIVER" class="form-control" required placeholder="Input disini.." required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Mobil / Plat *</label>
                            <input name="NO_CAR" class="form-control" required placeholder="Input disini.." required>
                        </div>

                        <div class="space-line"></div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Qty (Ekor) *</label>
                            <input name="QTY" type="text" inputmode="decimal" step="0.01" class="form-control" required placeholder="Input QTY..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Total BW *</label>
                            <input name="WEIGHT" type="text" inputmode="decimal" step="0.01" class="form-control" required placeholder="Input WEIGHT..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Avg BW</label>
                            <input name="AVG_BW" type="text" inputmode="decimal" step="0.01" class="form-control" readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6 flex-inline"></div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Harga</label>
                            <input name="PRICE" class="form-control" required placeholder="Input HARGA.." required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Total Harga</label>
                            <input name="AMOUNT" class="form-control" readonly style="background: #efefef">
                        </div>

                        <div class="space-line"></div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Mati (Ekor)</label>
                            <input name="DEAD" type="text" inputmode="decimal" step="0.01" class="form-control" required placeholder="Input disini..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Mati (Kg)</label>
                            <input name="DEAD_WEIGHT" type="text" inputmode="decimal" step="0.01" class="form-control" readonly style="background: #efefef" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Susut (Kg)</label>
                            <input name="SHRINK" type="text" inputmode="decimal" step="0.01" class="form-control" required placeholder="Input disini..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Total Terima (Kg)</label>
                            <input name="RECEIVE_AMOUNT" type="text" inputmode="decimal" step="0.01" class="form-control" readonly style="background: #efefef">
                        </div>

                        <div class="space-line"></div>

                        <div class="col-md-12">
                            <label class="form-label">Remark</label>
                            <input name="REMARK" class="form-control" placeholder="Keterangan..">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Tutup</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade" id="receiveLbEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fReceiveLbEdit" enctype="multipart/form-data">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">RECEIVE LIVE BIRD - EDIT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2 mb-3">

                        <!-- Plant -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant</label>
                            <input type="text"
                                id="PLANT_TEXT_EDIT"
                                class="form-control"
                                readonly
                                style="background:#efefef">

                            <!-- nilai real untuk backend -->
                            <input type="hidden" name="PLANT" id="PLANT_EDIT">
                        </div>

                        <!-- No. Receive (readonly) -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Receive</label>
                            <input class="form-control" name="RECEIVE" placeholder="Auto Generate" readonly style="background:#efefef">
                        </div>

                        <!-- Tanggal Receive -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal Receive *</label>
                            <input id="RECEIVE_DATE_EDIT" name="RECEIVE_DATE" type="date" class="form-control" required>
                        </div>

                        <!-- Pembayaran -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Pembayaran</label>
                            <div style="padding:5px 0px; width: 100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="CASH">
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="TRANSFER">
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>

                        <!-- Jenis Pembayaran -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Jenis Pembayaran</label>
                            <div style="padding:5px 0px; width: 100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="JENIS_PAY" value="LUNAS">
                                    <label class="form-check-label">LUNAS</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="JENIS_PAY" value="TEMPO">
                                    <label class="form-check-label">TEMPO</label>
                                </div>
                            </div>
                        </div>

                        <!-- Slip / Nota -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Slip / Nota</label>
                            <input class="form-control" name="SLIP_NO" placeholder="Input disini.." required>
                        </div>

                        <!-- Attachment -->
                        <div class="col-md-12">
                            <label class="form-label" style="width: 17%">Attachment</label>
                            <div id="existingAttachment" class="mb-1"></div>
                            <input type="file" name="ATTACHMENT" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx">
                            <input type="hidden" name="OLD_ATTACHMENT_PATH">
                            <small class="text-muted">&nbsp; *Hanya 1 file, akan menimpa jika sudah ada</small>
                        </div>

                        <div class="space-line"></div>

                        <!-- DO & Supplier -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Delivery Order *</label>
                            <input name="DO" class="form-control" required placeholder="No. DO ...">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Supplier *</label>
                            <select id="supplierEdit" class="form-control"></select>
                            <input type="hidden" name="SUPPLIER" id="hiddensupplierEdit">
                        </div>

                        <!-- Schedule -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Datang *</label>
                            <input id="ARRIVE_SCHEDULE_EDIT" name="ARRIVE_SCHEDULE" type="datetime-local" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Bongkar *</label>
                            <input id="DEPART_SCHEDULE_EDIT" name="DEPART_SCHEDULE" type="datetime-local" class="form-control" required>
                        </div>

                        <!-- Driver & Mobil -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Driver *</label>
                            <input name="DRIVER" class="form-control" required placeholder="Input disini..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Mobil / Plat *</label>
                            <input name="NO_CAR" class="form-control" required placeholder="Input disini..">
                        </div>

                        <div class="space-line"></div>

                        <!-- Qty & Weight -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Qty (Ekor) *</label>
                            <input name="QTY" type="text" inputmode="decimal" step="0.01" class="form-control" required placeholder="Input QTY..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Total BW *</label>
                            <input name="WEIGHT" type="text" inputmode="decimal" step="0.01" class="form-control" required placeholder="Input WEIGHT..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Avg BW</label>
                            <input name="AVG_BW" type="text" inputmode="decimal" step="0.01" class="form-control" readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6 flex-inline"></div>

                        <!-- Harga -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Harga</label>
                            <input name="PRICE" class="form-control" required placeholder="Input HARGA..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Total Harga</label>
                            <input name="AMOUNT" class="form-control" readonly style="background: #efefef">
                        </div>

                        <div class="space-line"></div>

                        <!-- Mati & Susut -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Mati (Ekor)</label>
                            <input name="DEAD" type="text" inputmode="decimal" step="0.01" class="form-control" placeholder="Input disini..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Mati (Kg)</label>
                            <input name="DEAD_WEIGHT" type="text" inputmode="decimal" step="0.01" class="form-control" readonly style="background: #efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Susut (Kg)</label>
                            <input name="SHRINK" type="text" inputmode="decimal" step="0.01" class="form-control" placeholder="Input disini..">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Total Terima (Kg)</label>
                            <input name="RECEIVE_AMOUNT" type="text" inputmode="decimal" step="0.01" class="form-control" readonly style="background: #efefef">
                        </div>

                        <div class="space-line"></div>

                        <!-- Remark -->
                        <div class="col-md-12">
                            <label class="form-label">Remark</label>
                            <input name="REMARK" class="form-control" placeholder="Keterangan..">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Tutup</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'RECEIVE', dir: 'DESC' };

    let searchTimer = null;

    $('#search').on('keyup', function(){
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            state.search = $(this).val();
            state.page = 1; // reset ke halaman pertama
            loadPage(1);
        }, 500);
    });

    function initPlantSelect2(selector, modalId){
        $.get("<?= base_url('receive-lb/get_plant_by_user'); ?>", function(resp){

            let plants = resp.results || [];
            let isSingle = resp.single === true;

            let $select = $(selector);

            // Kosongkan dulu
            $select.empty();

            // Isi options
            plants.forEach(p=>{
                let option = new Option(p.text, p.id, false, false);
                $select.append(option);
            });

            // Init select2 TANPA ajax lagi (biar tidak hit server terus)
            $select.select2({
                placeholder: "-- PILIH PLANT --",
                dropdownParent: $(modalId),
                width: "100%"
            });

            // Jika hanya 1 plant → auto pilih & disable
            if(isSingle && plants.length === 1){
                let plant = plants[0];

                let option = new Option(plant.text, plant.id, true, true);
                $select.append(option).trigger('change');

                $('#hiddenPlantAdd').val(plant.id);
                $select.prop('disabled', true);
            }

            // Jika lebih dari 1 → normal behavior
            $select.on('select2:select', function(e){
                $('#hiddenPlantAdd').val(e.params.data.id);
            });

        }, 'json');
    }
    
    function formatDate(dateString){
        if(!dateString) return '-';
        const d = new Date(dateString);
        if(isNaN(d)) return dateString;

        const day = String(d.getDate()).padStart(2,'0');
        const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
        return `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    // Input bebas saat mengetik
    $(document).on('input', `
        input[name="QTY"],
        input[name="WEIGHT"],
        input[name="DEAD"],
        input[name="DEAD_WEIGHT"],
        input[name="SHRINK"],
        input[name="PRICE"]
    `, function(){
        let form = $(this).closest('form');

        // update hitungan
        hitungAvgBW(form);        
        hitungTotalHarga(form);  
        hitungDeadWeight(form);  
        hitungTotalTerima(form); 
    });

    // Format saat user keluar dari input
    $(document).on('blur', `
        input[name="QTY"],
        input[name="WEIGHT"],
        input[name="DEAD"],
        input[name="DEAD_WEIGHT"],
        input[name="SHRINK"]
    `, function(){
        formatDecimalInput(this); // decimal 2 digit
    });

    $(document).on('blur', 'input[name="PRICE"]', function(){
        formatRupiahInput(this); // rupiah
    });

    function formatDecimalInput(el){
        let val = cleanNumber(el.value);
        if(val === 0){
            el.value = '';
            return;
        }

        el.value = val.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatRupiahInput(el){
        let val = cleanNumber(el.value);
        el.value = val ? val.toLocaleString('id-ID') : '';
    }

    function formatDecimal(val, digit = 2){
        let n = cleanNumber(val);
        return n.toLocaleString('id-ID', {
            minimumFractionDigits: digit,
            maximumFractionDigits: digit
        });
    }

    function formatRupiahInt(val){
        let n = cleanNumber(val);
        return n.toLocaleString('id-ID'); // tanpa ,00
    }

    function cleanNumber(val){
        if (val === null || val === '') return 0;

        // kalau sudah number (dari JSON backend)
        if (typeof val === 'number') return val;

        val = val.toString().trim();

        // jika format indonesia (ada koma)
        if (val.includes(',')) {
            // 1.234.567,89 → 1234567.89
            val = val.replace(/\./g,'').replace(',','.');
        }

        // jika format en (100000.00) → BIARKAN
        let n = parseFloat(val);
        return isNaN(n) ? 0 : n;
    }

    function formatRupiah(x){
        let n = cleanNumber(x).toString().split('.');
        return n[0].replace(/\B(?=(\d{3})+(?!\d))/g,'.') + (n[1] ? '.'+n[1] : '');
    }

    function formatDecimalDB(val, digit = 2){
        let n = Number(val);
        if (isNaN(n)) return '0,00';

        return n.toLocaleString('id-ID', {
            minimumFractionDigits: digit,
            maximumFractionDigits: digit
        });
    }

    function hitungDeadWeight(form){
        let deadQty = cleanNumber($(form).find('[name="DEAD"]').val());
        let avgBW   = cleanNumber($(form).find('[name="AVG_BW"]').val());

        let deadWeight = 0;
        if(deadQty > 0 && avgBW > 0){
            deadWeight = deadQty * avgBW;
        }

        $(form)
            .find('[name="DEAD_WEIGHT"]')
            .val(formatDecimal(deadWeight));

        // update total terima setelah dead_weight berubah
        hitungTotalTerima(form);
    }

    $(document).on('input', 'input[name="DEAD"]', function(){
        let form = $(this).closest('form');
        hitungDeadWeight(form);
    });

    let currentRequest = null;
    let isLoading = false;

    function loadPage(page = 1){
        if(isLoading) return;
        isLoading = true;

        state.page = page;

        // ❌ Hentikan request sebelumnya jika masih jalan
        if(currentRequest && currentRequest.readyState !== 4){
            currentRequest.abort();
        }

        currentRequest = $.ajax({
            url: '<?= base_url("receive-lb/load_data"); ?>',
            type: 'GET',
            data: state,
            dataType: 'json',
            success: function(resp){

                let tbody = $('#table-body').empty();

                resp.rows.forEach(row=>{
                    tbody.append(`
                        <tr>
                            <td class="text-center" style="vertical-align:middle"><b>${row.PLANT_NAME}</b></td>
                            <td class="text-center" style="vertical-align:middle">${formatDate(row.RECEIVE_DATE)}</td>
                            <td class="text-center" style="vertical-align:middle"><b>#${row.RECEIVE}</b></td>
                            <td class="text-center" style="vertical-align:middle">#${row.DO ?? '-'}</td>
                            <td class="text-center" style="vertical-align:middle">${row.SUPPLIER_NAME ?? '-'}<br><b>${row.SUPPLIER ?? '-'}</b></td>
                            <td class="text-end" style="vertical-align:middle">${formatDecimalDB(row.QTY)}</td>
                            <td class="text-end" style="vertical-align:middle">${formatDecimalDB(row.WEIGHT)}</td>
                            <td class="text-end" style="vertical-align:middle">${formatDecimalDB(row.RECEIVE_AMOUNT)}</td>
                            <td class="text-center" style="vertical-align:middle">
                                <button class="btn btn-sm btn-primary exportPdf" data-receive="${row.RECEIVE}" data-plant="${row.PLANT}">PDF</button>
                                ${!['adelia','hellen'].includes(CURRENT_USER) ? 
                                `<button class="btn btn-sm btn-warning editBtn" data-receive="${row.RECEIVE}" data-plant="${row.PLANT}">Edit</button>` 
                                : ''}
                                <button class="btn btn-sm btn-danger deleteBtn" data-receive="${row.RECEIVE}" data-plant="${row.PLANT}">Hapus</button>
                            </td>
                        </tr>
                    `);
                });

                $('#pagination').html(resp.pagination);
                $('#info').text(`Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`);
            },
            complete: function(){
                isLoading = false;
            }
        });
    }

    $(document).on("click", ".exportPdf", function () {
        let receive = $(this).data("receive");
        let plant   = $(this).data("plant");

        window.open(
            "<?= base_url('receive-lb/print_pdf'); ?>?receive=" 
            + receive + "&plant=" + plant,
            "_blank"
        );
    });

    /* =========================
    SELECT2
    ========================= */
    function initSupplierSelect2(selector, modal){
        $(selector).select2({
            placeholder:'Pilih Supplier',
            dropdownParent:$(modal),
            width:'100%',
            ajax:{
                url:'<?= base_url("receive-lb/get_supplier"); ?>',
                dataType:'json',
                delay:250,
                data:p=>({q:p.term}),
                processResults:d=>({results:d})
            }
        }).on('select2:select', function(e){
            $(this).closest('form')
                .find('input[name="SUPPLIER"]')
                .val(e.params.data.id);
        });
    }

    function setDefaultSupplier(selector, cust = 'CS000001') {

        let $select = $(selector);

        // kalau sudah ada value, jangan override
        if ($select.val()) return;

        $.get('<?= base_url("receive-lb/get_supplier"); ?>', { q: cust }, function (res) {

            if (!res || !res.length) return;

            let item = res.find(r => r.id === cust);
            if (!item) return;

            let opt = new Option(item.text, item.id, true, true);
            $select.append(opt).trigger('change');

            // set hidden input
            $select.closest('form')
                .find('input[name="SUPPLIER"]')
                .val(item.id);

        }, 'json');
    }

    function initSupplierEdit(){
        $('#supplierEdit').select2({
            placeholder: 'Pilih Supplier',
            dropdownParent: $('#receiveLbEdit'), // ✅ WAJIB INI
            width: '100%',
            ajax: {
                url: '<?= base_url("receive-lb/get-supplier"); ?>',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data })
            }
        }).on('select2:select', function (e) {
            $('#hiddensupplierEdit').val(e.params.data.id);
        });
    }

    /* =========================
    DETAIL ROW
    ========================= */
    function addDetailRow(data={}, table){
        let modal = table.includes('Edit') ? '#receiveEdit' : '#receiveLbAdd';

        let row = `
            <tr>
                <td><select class="form-control material-select"></select></td>
                <td><input class="form-control berat text-end" value="${data.BERAT||''}"></td>
                <td><input class="form-control jumlah text-end" value="${data.QTY||''}"></td>
                <td><input class="form-control harga text-end" value="${data.PRICE||''}"></td>
                <td><input class="form-control total text-end" value="${data.AMOUNT||''}" readonly></td>
                <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
            </tr>
        `;

        $(table+' tbody').append(row);

        let $select = $(table+' tbody tr:last .material-select');
        initMaterialSelect2($select, modal);

        if(data.MATERIAL && data.MATERIAL_NAME){
            let opt = new Option(data.MATERIAL_NAME, data.MATERIAL, true, true);
            $select.append(opt).trigger('change');
        }
    }

    function updateTotalRow(tr){
        let qty = cleanNumber(tr.find('.jumlah').val());
        let harga = cleanNumber(tr.find('.harga').val());
        tr.find('.total').val(formatRupiah((qty*harga).toString()));
    }

    function cleanDecimal(val){
        if(!val) return 0;

        return parseFloat(
            val
                .replace(/\./g, '')   // hapus ribuan
                .replace(',', '.')    // koma → titik
        ) || 0;
    }

    function cleanRupiah(val){
        if(!val) return 0;

        return parseInt(
            val.replace(/[^\d]/g, ''),
            10
        ) || 0;
    }

    /* =========================
    READY
    ========================= */
    $(function(){
        loadPage(1);

        initPlantSelect2('#plantAdd', '#receiveLbAdd');
        initSupplierSelect2('#supplierAdd','#receiveLbAdd');
        setDefaultSupplier('#supplierAdd', 'CS000001');
        
        initSupplierSelect2('#supplierEdit','#receiveLbEdit');

        $('#addDetailRowAdd').click(()=>addDetailRow({},'#receiveDetailTableAdd'));
        $('#addDetailRowEdit').click(()=>addDetailRow({},'#receiveDetailTableEdit'));

        $('#receiveDetailTableAdd, #receiveDetailTableEdit')
            .on('click','.removeRow',function(){ $(this).closest('tr').remove(); })
            .on('input','.jumlah,.harga',function(){ updateTotalRow($(this).closest('tr')); });

        /* =========================
        CREATE
        ========================= */
        $('#freceiveLbAdd').submit(function(e){
            e.preventDefault();

            let formData = new FormData(this); // FormData support file upload

            // Convert semua field decimal / rupiah ke number murni sebelum submit
            const decimalFields = [
                'QTY','WEIGHT','AVG_BW','DEAD','DEAD_WEIGHT','SHRINK','RECEIVE_AMOUNT'
            ];
            const rupiahFields = ['PRICE','AMOUNT'];

            decimalFields.forEach(f=>{
                let el = $(this).find(`[name="${f}"]`);
                if(el.length){
                    formData.set(f, cleanDecimal(el.val()));
                }
            });

            rupiahFields.forEach(f=>{
                let el = $(this).find(`[name="${f}"]`);
                if(el.length){
                    formData.set(f, cleanRupiah(el.val()));
                }
            });

            $.ajax({
                url: '<?= base_url("receive-lb/create"); ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res){
                    alert(res.message);
                    if(res.status){
                        $('#receiveLbAdd').modal('hide');
                        $('#freceiveLbAdd')[0].reset();
                        loadPage(state.page);
                        $('#supplierAdd').val(null).trigger('change');
                        $('#hiddensupplierAdd').val('');
                        $('#plantAdd').prop('disabled', false).val(null).trigger('change');
                        $('#hiddenPlantAdd').val('');
                        initPlantSelect2('#plantAdd', '#receiveLbAdd');
                    }
                }
            });
        });

        $(document).on('click', '.editBtn', function () {
            let receive = $(this).data('receive');
            let plant   = $(this).data('plant');

            if (!receive) { alert('Receive tidak ditemukan'); return; }

            $.get('<?= base_url("receive-lb/edit"); ?>', { receive: receive, plant: plant }, function (resp) {
                if (typeof resp === 'string') resp = JSON.parse(resp);
                if (!resp.status) { alert(resp.message); return; }

                let d = resp.header; // ⚠ gunakan header
                let form = $('#fReceiveLbEdit');

                // 🔑 RECEIVE & PLANT hidden
                form.find('[name="RECEIVE"]').val(d.RECEIVE);

                form.find('[name="PLANT"]').val(d.PLANT);

                form.find('[name="SUPPLIER"]').val(d.SUPPLIER);

                // tanggal
                form.find('[name="RECEIVE_DATE"]').val(d.RECEIVE_DATE);
                form.find('[name="ARRIVE_SCHEDULE"]').val(d.ARRIVE_SCHEDULE);
                form.find('[name="DEPART_SCHEDULE"]').val(d.DEPART_SCHEDULE);

                // radio
                form.find('[name="PEMBAYARAN"]').prop('checked', false);
                if(d.PEMBAYARAN) form.find('[name="PEMBAYARAN"][value="'+d.PEMBAYARAN+'"]').prop('checked', true);

                form.find('[name="JENIS_PAY"]').prop('checked', false);
                if(d.JENIS_PAY) form.find('[name="JENIS_PAY"][value="'+d.JENIS_PAY+'"]').prop('checked', true);

                // text
                form.find('[name="SLIP_NO"]').val(d.SLIP_NO);
                form.find('[name="DO"]').val(d.DO);
                form.find('[name="DRIVER"]').val(d.DRIVER);
                form.find('[name="NO_CAR"]').val(d.NO_CAR);

                // supplier select2
                let supplierOption = new Option(d.SUPPLIER + ' - ' + (d.SUPPLIER_NAME||''), d.SUPPLIER, true, true);
                $('#supplierEdit').empty().append(supplierOption).trigger('change');
                $('#SUPPLIER').val(d.SUPPLIER);

                $('#PLANT_TEXT_EDIT').val(d.AJ_NAME ? `${d.PLANT} - ${d.AJ_NAME}` : d.PLANT);
                $('#PLANT_EDIT').val(d.PLANT);

                // Attachment preview
                if(d.ATTACHMENT_NAME){
                    let fileUrl = '<?= base_url("uploads") ?>/' + d.PLANT + '/' + d.ATTACHMENT_NAME;
                    let ext = d.ATTACHMENT_NAME.split('.').pop().toLowerCase();
                    let preview = '';
                    if(['jpg','jpeg','png'].includes(ext)){
                        preview = `<img src="${fileUrl}" style="max-width:150px;">`;
                    } else {
                        preview = `<a href="${fileUrl}" target="_blank">${d.ATTACHMENT_NAME}</a> <i>(Klik untuk lihat)</i>`;
                    }
                    $('#existingAttachment').html(preview);
                } else {
                    $('#existingAttachment').html('');
                }

                // decimal & rupiah
                form.find('[name="QTY"]').val(formatDecimal(d.QTY));
                form.find('[name="WEIGHT"]').val(formatDecimal(d.WEIGHT));
                form.find('[name="AVG_BW"]').val(formatDecimal(d.AVG_BW));
                form.find('[name="DEAD"]').val(formatDecimal(d.DEAD));
                form.find('[name="DEAD_WEIGHT"]').val(formatDecimal(d.DEAD_WEIGHT));
                form.find('[name="SHRINK"]').val(formatDecimal(d.SHRINK));
                form.find('[name="RECEIVE_AMOUNT"]').val(formatDecimal(d.RECEIVE_AMOUNT));

                form.find('[name="PRICE"]').val(formatRupiahInt(d.PRICE));
                form.find('[name="AMOUNT"]').val(formatRupiahInt(d.AMOUNT));

                form.find('[name="REMARK"]').val(d.REMARK);

                $('#receiveLbEdit').modal('show');
            }, 'json');
        });

        $('#fReceiveLbEdit').submit(function(e){
            e.preventDefault();

            let f = $(this);
            let formData = new FormData(f[0]);

            // bersihkan decimal & rupiah sebelum submit
            const decimalFields = ['QTY','WEIGHT','AVG_BW','DEAD','DEAD_WEIGHT','SHRINK','RECEIVE_AMOUNT'];
            const rupiahFields  = ['PRICE','AMOUNT'];

            decimalFields.forEach(fld=>{
                formData.set(fld, cleanDecimal(f.find(`[name="${fld}"]`).val()));
            });

            rupiahFields.forEach(fld=>{
                formData.set(fld, cleanRupiah(f.find(`[name="${fld}"]`).val()));
            });

            // tambahkan field lain jika perlu
            formData.set('RECEIVE', f.find('input[name="RECEIVE"]').val());
            formData.set('PLANT', f.find('input[name="PLANT"]').val());
            formData.set('RECEIVE_DATE', f.find('input[name="RECEIVE_DATE"]').val());
            formData.set('PEMBAYARAN', f.find('input[name="PEMBAYARAN"]:checked').val() || '');
            formData.set('JENIS_PAY', f.find('input[name="JENIS_PAY"]:checked').val() || '');
            formData.set('SLIP_NO', f.find('[name="SLIP_NO"]').val());
            formData.set('DO', f.find('[name="DO"]').val());
            formData.set('DRIVER', f.find('[name="DRIVER"]').val());
            formData.set('NO_CAR', f.find('[name="NO_CAR"]').val());
            formData.set('SUPPLIER', $('#hiddensupplierEdit').val());
            formData.set('ARRIVE_SCHEDULE', f.find('[name="ARRIVE_SCHEDULE"]').val());
            formData.set('DEPART_SCHEDULE', f.find('[name="DEPART_SCHEDULE"]').val());
            formData.set('REMARK', f.find('[name="REMARK"]').val());

            $.ajax({
                url: '<?= base_url("receive-lb/update"); ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res){
                    if(typeof res === 'string') res = JSON.parse(res);
                    alert(res.message);

                    if(res.status){
                        $('#receiveLbEdit').modal('hide');
                        loadPage(state.page);
                    }
                }
            });
        });
    });
    </script>

    <script>
    $('#receiveLbAdd').on('shown.bs.modal', function () {
        let today = new Date().toISOString().split('T')[0];
        // $('#RECEIVE_DATE').val(today).attr('min', today);
    });

    $('#receiveLbEdit').on('shown.bs.modal', function () {
        if (!$('#supplierEdit').hasClass('select2-hidden-accessible')) {
            initSupplierEdit();
        }
    });

    $(document).on('click', '.deleteBtn', function () {

        let receive = $(this).data('receive');
        let plant   = $(this).data('plant');

        if (!receive || !plant) {
            alert('Key tidak valid');
            return;
        }

        if (!confirm(`Yakin ingin menghapus RECEIVE LB ${receive}?`)) return;

        $.ajax({
            url: "<?= base_url('receive-lb/remove'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                receive: receive,
                plant: plant
            },
            success: function (res) {
                alert(res.message);
                if (res.status) {
                    loadPage(state.page);
                }
            },
            error: function () {
                alert("Gagal menghubungi server");
            }
        });
    });

    function setNowNoBackDateTime(selector) {
        const now = new Date();

        // Format ke yyyy-MM-ddTHH:mm (wajib untuk datetime-local)
        const pad = n => String(n).padStart(2, '0');
        const formatted =
            now.getFullYear() + '-' +
            pad(now.getMonth() + 1) + '-' +
            pad(now.getDate()) + 'T' +
            pad(now.getHours()) + ':' +
            pad(now.getMinutes());

        $(selector)
            .val(formatted)     // auto isi
            .attr('min', formatted); // lock backdate + backtime
    }

    // Saat halaman / modal siap
    $(function () {
        setNowNoBackDateTime('#ARRIVE_SCHEDULE');
        setNowNoBackDateTime('#DEPART_SCHEDULE');
    });

    function hitungAvgBW(form){
        let qty    = cleanNumber($(form).find('[name="QTY"]').val());
        let weight = cleanNumber($(form).find('[name="WEIGHT"]').val());

        let avg = 0;
        if(qty > 0 && weight > 0){
            avg = weight / qty;
        }

        $(form)
            .find('[name="AVG_BW"]')
            .val(formatDecimal(avg));
    }

    function parseQty(val){
        if(!val) return 0;
        return parseFloat(
            val.replace(/\./g,'')
            .replace(',','.')
        );
    }

    function parseRupiah(val){
        if(!val) return 0;
        return parseInt(val.replace(/\./g,''), 10);
    }

    function hitungTotalHarga(form){
        let qty   = parseQty($(form).find('[name="QTY"]').val());
        let price = parseRupiah($(form).find('[name="PRICE"]').val());

        let total = 0;
        if(qty > 0 && price > 0){
            total = qty * price;
        }

        $(form)
            .find('[name="AMOUNT"]')
            .val(formatRupiahInt(total));
    }

    function hitungTotalTerima(form){
        let weight = cleanNumber($(form).find('[name="WEIGHT"]').val());
        let dead   = cleanNumber($(form).find('[name="DEAD_WEIGHT"]').val());
        let shrink = cleanNumber($(form).find('[name="SHRINK"]').val());

        let total = weight - dead - shrink;
        if(total < 0) total = 0;

        $(form)
            .find('[name="RECEIVE_AMOUNT"]')
            .val(formatDecimal(total));
    }

    $(document).on(
        'input',
        `
        input[name="QTY"],
        input[name="WEIGHT"],
        input[name="PRICE"],
        input[name="DEAD_WEIGHT"],
        input[name="SHRINK"]
        `,
        function(){
            let form = $(this).closest('form');

            hitungAvgBW(form);        // AVG_BW
            hitungTotalHarga(form);  // AMOUNT
            hitungTotalTerima(form); // RECEIVE_AMOUNT
        }
    );

</script>

<script>
    const CURRENT_USER = "<?= strtolower($this->session->userdata('username')); ?>";
</script>


