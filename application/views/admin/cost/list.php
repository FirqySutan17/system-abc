    <div class="container-fluid">
        <div class="card w-100">
            <div class="card-body">

                <h5 class="card-title fw-semibold mb-4">COST - BASE DATA</h5>

                <!-- SEARCH + ADD ROW -->
                <div class="row mb-3" style="margin-top: 20px">
                    <div class="col-md-8 col-sm-12">
                        <input id="search" type="text" class="form-control" placeholder="Cari cost..." />
                    </div>
                    <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                        <div class="btn-group ">
                            <!-- <button class="btn btn-outline-secondary" id="exportExcel">Export Excel</button>
                            <button class="btn btn-outline-secondary" id="exportPdf">Export PDF</button> -->
                        </div>
                        <button id="btnAdd" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#costAdd">
                            <i class="ti ti-plus"></i> Tambah
                        </button>
                    </div>
                </div>

                <!-- table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-nowrap mb-0 align-middle" id="mainTable" style="border-collapse: collapse; border-radius: 10px; overflow: hidden;">
                        <thead class="text-dark fs-3">
                            <tr>
                                <th  style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="cost">Cost <span class="sort-icon"></span></th>
                                <th  style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="cost_name">Nama <span class="sort-icon"></span></th>
                                <th  style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="account_code">Account <span class="sort-icon"></span></th>
                                <th  style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="class">Class <span class="sort-icon"></span></th>
                                <th  style="text-align: center; vertical-align: middle" class="border-bottom-0" data-order="remark">Deskripsi</th>
                                <th  style="text-align: center; vertical-align: middle" class="border-bottom-0"></th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="fs-3" style="border: 2px solid #fff;">
                            <!-- filled by AJAX -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div id="info"></div>
                    <div id="pagination"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADD -->
    <div class="modal fade" id="costAdd" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="fcostAdd">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cost - Tambah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-1">
                            <label class="form-label">Cost *</label>
                            <input name="COST" class="form-control" placeholder="Input disini.." required>
                        </div>

                        <div class="col-md-6 mb-1">
                            <label class="form-label">Nama *</label>
                            <input name="COST_NAME" class="form-control" placeholder="Input disini.." required>
                        </div>

                        <div class="col-md-6 mb-1">
                            <label class="form-label">Account *</label>
                            <select id="accountAddSelect" class="form-control" required></select>
                            <input type="hidden" id="hiddenAccountAdd" name="ACCOUNT_CODE">
                        </div>

                        <div class="col-md-6 mb-1">
                            <label class="form-label">Class *</label>
                            <select id="classAddSelect" class="form-control" name="CLASS" required>
                                <option value="MF">MF</option>
                                <option value="AD">AD</option>
                                <option value="SE">SE</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2 mb-1">
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <input name="REMARK" class="form-control" placeholder="Input disini..">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="costEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="fcostEdit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cost - Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    
                    <input type="hidden" name="orig_cost" id="orig_cost">

                    <div class="row g-2">
                        <div class="col-md-6 mb-1">
                            <label class="form-label">Cost *</label>
                            <input name="COST" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-1">
                            <label class="form-label">Nama *</label>
                            <input name="COST_NAME" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-1">
                            <label class="form-label">Account *</label>
                            <select id="accountEditSelect" class="form-control" required></select>
                            <input type="hidden" id="hiddenAccountEdit" name="ACCOUNT_CODE">
                        </div>

                        <div class="col-md-6 mb-1">
                            <label class="form-label">Class *</label>
                            <select id="classEditSelect" class="form-control" name="CLASS" required>
                                <option value="MF">MF</option>
                                <option value="AD">AD</option>
                                <option value="SE">SE</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2 mb-1">
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <input name="REMARK" class="form-control">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </div>
            </form>
        </div>
    </div>


    <script>
        var state = {
            page: 1,
            limit: 10,
            order: 'COST',
            dir: 'ASC',
            search: ''
        };

        $(function(){
            // debounce search input
            let timer = null;
            $('#search').on('keyup', function(){
                clearTimeout(timer);
                timer = setTimeout(function(){
                    state.search = $('#search').val();
                    state.page = 1;
                    loadPage(1);
                }, 350);
            });

            // initial load
            loadPage(1);

            // INIT ADD SELECT2
            $('#accountAddSelect').select2({
                placeholder: 'Pilih ACCOUNT',
                width: '100%',
                dropdownParent: $('#costAdd'),
                ajax: {
                    url: '<?php echo base_url("cost/get_account"); ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    }
                }
            });

            $('#classAddSelect').select2({
                placeholder: 'Pilih CLASS',
                width: '100%',
                dropdownParent: $('#costAdd'),
                minimumResultsForSearch: Infinity
            });

            // handle header sort click
            $('#mainTable thead').on('click', 'th[data-order]', function(){
                var col = $(this).data('order');
                if (state.order === col) {
                    state.dir = state.dir === 'ASC' ? 'DESC' : 'ASC';
                } else {
                    state.order = col;
                    state.dir = 'ASC';
                }
                loadPage(1);
            });

            // submit add
            $('#fcostAdd').on('submit', function(e){
                e.preventDefault();

                var data = {
                    COST: $('input[name="COST"]').val(),
                    COST_NAME: $('input[name="COST_NAME"]').val(),
                    ACCOUNT_CODE: $('#accountAddSelect').val(),   // <-- PENTING
                    CLASS: $('#classAddSelect').val(),            // <-- PENTING
                    REMARK: $('input[name="REMARK"]').val()
                };

                if(!data.COST || !data.COST_NAME){
                    showToast('error', 'cost dan cost name wajib diisi');
                    return;
                }

                $.post("<?= base_url('cost/create'); ?>", data)
                .done(function(resp){
                    var r = typeof resp === 'string' ? JSON.parse(resp) : resp;

                    if(r.status){
                        showToast('success', r.message);
                        $('#costAdd').modal('hide');
                        $('#fcostAdd')[0].reset();

                        $('#accountAddSelect, #classAddSelect').val(null).trigger('change');
                        loadPage(1);
                    } else {
                        showToast('error', r.message);
                    }
                })
                .fail(function(xhr){
                    showToast('error', 'Terjadi kesalahan pada server!');
                    console.log(xhr.responseText);
                });
            });

            $('#accountEditSelect').select2({
                dropdownParent: $('#costEdit'),
                placeholder: "Pilih account...",
                width: '100%',
                allowClear: true,
                ajax: {
                    url: "<?= base_url('cost/get_account'); ?>",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data };   // <-- TIDAK DI-MAP ULANG
                    }
                }
            });

            // Saat select berubah, simpan ke hidden
            $('#accountEditSelect').on('change', function() {
                $("#hiddenAccountEdit").val($(this).val());
            });

            // submit edit
            $("#fcostEdit").on("submit", function(e) {
                e.preventDefault();

                $.post("<?= base_url('cost/update'); ?>", $(this).serialize())
                    .done(function(resp) {
                        if (typeof resp === "string") resp = JSON.parse(resp);

                        showToast(resp.status ? 'success' : 'error', resp.message);

                        if (resp.status) {
                            $("#costEdit").modal("hide");
                            loadPage(state.page);
                        }
                    })
                    .fail(function(xhr) {
                        showToast('error', 'Server error');
                        console.log(xhr.responseText);
                    });
            });

            // export
            $('#exportExcel').on('click', function(){
                var url = "<?= base_url('code/export_excel'); ?>?search="+encodeURIComponent(state.search)+"&order="+state.order+"&dir="+state.dir;
                window.location = url;
            });
            $('#exportPdf').on('click', function(){
                var url = "<?= base_url('code/export_pdf'); ?>?search="+encodeURIComponent(state.search)+"&order="+state.order+"&dir="+state.dir;
                window.location = url;
            });

        });

        // global function used by pagination links
        function loadPage(page) {
            state.page = page;
            $('#table-body').html('<tr><td colspan="6" style="text-align: center">Loading...</td></tr>');
            $.get("<?= base_url('cost/load_data'); ?>", {
                page: page, limit: state.limit, search: state.search, order: state.order, dir: state.dir
            }, function(res){
                var data = res;
                if (typeof res === 'string') data = JSON.parse(res);
                renderTable(data.rows);
                $('#pagination').html(data.pagination);
                $('#info').text('Menampilkan '+ data.rows.length + ' dari '+ data.total);
                // update sort icons
                $('#mainTable thead th[data-order]').each(function(){
                    var col = $(this).data('order');
                    $(this).find('.sort-icon').text(col === state.order ? (state.dir === 'ASC' ? ' ▲' : ' ▼') : '');
                });
            }, 'json');
        }

        function renderTable(rows){
            var html = '';
            if (!rows || rows.length === 0) {
                html = '<tr><td colspan="6" style="text-align: center">Tidak ada data</td></tr>';
            } else {
                rows.forEach(function(r){
                    html += '<tr>';
                    html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.COST)+'</td>';
                    html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.COST_NAME)+'</td>';
                    html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.ACCOUNT_CODE)+'</td>';
                    html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.CLASS)+'</td>';
                    html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.REMARK || '')+'</td>';
                    html += '<td style="text-align: center">';
                    html += '<button class="btn btn-sm btn-warning me-1" onclick="showEdit(\''+r.COST+'\')">Edit</button>';
                    html += '<button class="btn btn-sm btn-danger" onclick="doDelete(\''+r.COST+'\')">Delete</button>';
                    html += '</td>';
                    html += '</tr>';
                });
            }
            $('#table-body').html(html);
        }

        function showDetail(head, code) {
            $('#detailBody').html('Loading...');
            $('#modalDetail').modal('show');
            $.get("<?= base_url('code/detail'); ?>", { head: head, code: code }, function(res){
                var r = res;
                if (typeof res === 'string') r = JSON.parse(res);
                if (!r.status) {
                    $('#detailBody').html('Error: '+r.message);
                    return;
                }
                var d = r.data;
                var html = '<table class="table table-bordered">';
                for (var k in d) {
                    html += '<tr><th style="width:30%">'+k+'</th><td>'+escapeHtml(d[k]||'')+'</td></tr>';
                }
                html += '</table>';
                $('#detailBody').html(html);
            }, 'json');
        }

        window.showEdit = function(cost) {
            $.get("<?= base_url('cost/edit'); ?>", { cost: cost }, function(res) {
                if (typeof res === 'string') res = JSON.parse(res);
                if (!res.status) {
                    alert("Data tidak ditemukan!");
                    return;
                }

                let d = res.data;
                let f = $("#fcostEdit");

                // Simpan PK lama
                f.find("#orig_cost").val(d.COST);

                // Isi data umum
                f.find('input[name="COST"]').val(d.COST);
                f.find('input[name="COST_NAME"]').val(d.COST_NAME);
                f.find('input[name="REMARK"]').val(d.REMARK);

                // Select - Class
                f.find('#classEditSelect').val(d.CLASS).trigger('change');

                // Select - Account (Select2)
                let option = new Option(
                    d.ACCOUNT_CODE + " - " + d.ACCOUNT_NAME,
                    d.ACCOUNT_CODE,
                    true,
                    true
                );
                $('#accountEditSelect').append(option).trigger('change');

                $("#hiddenAccountEdit").val(d.ACCOUNT_CODE);

                $("#costEdit").modal("show");

            }, 'json');
        };

        function doDelete(cost) {
            if(!confirm('Yakin ingin dihapus?')) return;

            $.post("<?= base_url('cost/remove'); ?>", { cost: cost }, function(res){
                var r = res;
                if(typeof res === 'string') r = JSON.parse(res);
                alert(r.message);

                if(r.status){
                    loadPage(state.page); // reload tabel cost
                }
            }, 'json');
        }

        // helper
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return $('<div/>').text(text).html();
        }

        function showToast(type, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: type,
                title: message
            });
        }
    </script>

    <script>
    $('#useYnToggleAdd').on('change', function(){
        $('#useYnValueAdd').val(this.checked ? 'Y' : 'N');
        $('#useYnLabelAdd').text(this.checked ? 'AKTIF' : 'NON-AKTIF');
    });

    $('#useYnToggleEdit').on('change', function(){
        $('#useYnValueEdit').val(this.checked ? 'Y' : 'N');
        $('#useYnLabelEdit').text(this.checked ? 'AKTIF' : 'NON-AKTIF');
    });
    </script>