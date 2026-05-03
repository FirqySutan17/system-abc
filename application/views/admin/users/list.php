<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">USERS - SETTINGS</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3" style="margin-top: 20px">
                <div class="col-md-8 col-sm-12">
                    <input id="search" type="text" class="form-control" placeholder="Cari username atau name..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <button id="btnAdd" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#userAdd">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                </div>
            </div>

            <!-- table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-nowrap mb-0 align-middle" id="mainTable" style="border-collapse: collapse; border-radius: 10px; overflow: hidden;">
                    <thead class="text-dark fs-3">
                        <tr>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="username">Username <span class="sort-icon"></span></th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="name">Nama <span class="sort-icon"></span></th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="plant">Plant <span class="sort-icon"></span></th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0"></th>
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
<div class="modal fade" id="userAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="fuserAdd">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Users - Tambah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-6 mb-1">
                        <label class="form-label">Username *</label>
                        <input name="username" class="form-control" placeholder="Input disini.." required>
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label">Nama *</label>
                        <input name="name" class="form-control" placeholder="Input disini.." required>
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label">Plant *</label>
                        <select
                            id="plantAddSelect"
                            name="plant[]"
                            class="form-control"
                            multiple
                            required>
                        </select>
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" placeholder="Input disini.." required>
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
<div class="modal fade" id="userEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="fuserEdit">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Users - Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                
                <input type="hidden" name="orig_id" id="orig_id">

                <div class="row g-2">
                    <div class="col-md-6 mb-1">
                        <label class="form-label">Username *</label>
                        <input name="username" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label">Nama *</label>
                        <input name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label">Plant *</label>
                        <select id="plantEditSelect" class="form-control" multiple required></select>
                        <input type="hidden" id="hiddenPlantEdit" name="plant">
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah">
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
    order: 'username',
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

    // INIT ADD SELECT2 - Plant
    $('#plantAddSelect').select2({
        placeholder: 'Pilih Plant',
        width: '100%',
        multiple: true,
        dropdownParent: $('#userAdd'),
        ajax: {
            url: '<?= base_url("users/get_plant"); ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });

    // INIT EDIT SELECT2 - Plant
    $('#plantEditSelect').select2({
        placeholder: 'Pilih Plant',
        width: '100%',
        dropdownParent: $('#userEdit'),
        ajax: {
            url: '<?= base_url("users/get_plant"); ?>',
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

    $('#plantEditSelect').on('change', function () {
        let val = $(this).val() || [];
        $('#hiddenPlantEdit').val(JSON.stringify(val));
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
    $('#fuserAdd').on('submit', function(e){
        e.preventDefault();

        var data = {
            username: $('input[name="username"]').val(),
            name: $('input[name="name"]').val(),
            plant: $('#plantAddSelect').val(),
            password: $('input[name="password"]').val()
        };

        if(!data.username || !data.name || !data.plant || data.plant.length === 0 || !data.password){
            showToast('error', 'Semua field wajib diisi');
            return;
        }

        $.post("<?= base_url('users/create'); ?>", data)
        .done(function(resp){
            var r = typeof resp === 'string' ? JSON.parse(resp) : resp;

            if(r.status){
                showToast('success', r.message);
                $('#userAdd').modal('hide');
                $('#fuserAdd')[0].reset();
                $('#plantAddSelect').val(null).trigger('change');
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

    // submit edit
    $("#fuserEdit").on("submit", function(e) {
        e.preventDefault();
        $.post("<?= base_url('users/update'); ?>", $(this).serialize())
            .done(function(resp) {
                if (typeof resp === "string") resp = JSON.parse(resp);
                showToast(resp.status ? 'success' : 'error', resp.message);
                if (resp.status) {
                    $("#userEdit").modal("hide");
                    loadPage(state.page);
                }
            })
            .fail(function(xhr) {
                showToast('error', 'Server error');
                console.log(xhr.responseText);
            });
    });

});

// global function used by pagination links
function loadPage(page) {
    state.page = page;
    $('#table-body').html('<tr><td colspan="4" style="text-align: center">Loading...</td></tr>');
    $.get("<?= base_url('users/load_data'); ?>", {
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
        html = '<tr><td colspan="4" style="text-align: center">Tidak ada data</td></tr>';
    } else {
        rows.forEach(function(r){
            html += '<tr>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.username)+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.name)+'</td>';

            // ⬇️ tampilkan plant_name, BUKAN plant (JSON)
            html += '<td style="text-align: center; vertical-align: middle">'+
                        escapeHtml(r.plant_name || '-')+
                    '</td>';

            html += '<td style="text-align: center">';
            html += '<button class="btn btn-sm btn-warning me-1" onclick="showEdit('+r.id+')">Edit</button>';
            html += '<button class="btn btn-sm btn-danger" onclick="doDelete('+r.id+')">Delete</button>';
            html += '</td>';
            html += '</tr>';
        });
    }
    $('#table-body').html(html);
}

window.showEdit = function(id) {
    $.get("<?= base_url('users/edit'); ?>", { id: id }, function(res) {
        if (!res.status) {
            alert("Data tidak ditemukan!");
            return;
        }

        let d = res.data;
        let f = $("#fuserEdit");

        f.find("#orig_id").val(d.id);
        f.find('input[name="username"]').val(d.username);
        f.find('input[name="name"]').val(d.name);
        f.find('input[name="password"]').val('');

        // RESET select2
        $('#plantEditSelect').empty();

        // ISI plant dari cd_code (CODE_NAME)
        if (res.plants && res.plants.length) {
            res.plants.forEach(function(p){
                let option = new Option(p.text, p.id, true, true);
                $('#plantEditSelect').append(option);
            });
            $('#plantEditSelect').trigger('change');
        }

        // hidden JSON
        $("#hiddenPlantEdit").val(
            res.plants.map(p => p.id)
        );

        $("#userEdit").modal("show");
    }, 'json');
};

function doDelete(id) {
    if (!confirm('Yakin ingin dihapus?')) return;
    $.post("<?= base_url('users/remove'); ?>", { id: id }, function(res){
        var r = typeof res === 'string' ? JSON.parse(res) : res;
        showToast(r.status ? 'success' : 'error', r.message);
        if (r.status) loadPage(state.page);
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
