
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
    .culling-report-wrap {
        width: 100%;
    }

    .culling-filter-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .culling-report-wrap .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 7px;
    }

    .culling-report-wrap .form-control,
    .culling-report-wrap .form-select {
        min-height: 42px;
        border-radius: 9px;
        font-size: 13px;
    }

    .culling-table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
    }

    .culling-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .culling-table {
        width: 100%;
        min-width: 1250px;
        border-collapse: collapse;
        font-size: 12px;
        margin-bottom: 0;
    }

    .culling-table thead th {
        background: #f3f6fa;
        color: #374151;
        font-weight: 700;
        padding: 13px 12px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .culling-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
        color: #374151;
    }

    .culling-table tbody tr:hover {
        background: #f9fafb;
    }

    .culling-table .text-number {
        text-align: right;
        white-space: nowrap;
    }

    .culling-table .text-center {
        text-align: center;
    }

    .culling-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 16px 20px;
    }

    #cul_pageInfo {
        font-size: 13px;
        color: #6b7280;
    }

    #cul_pagination {
        margin-left: auto;
    }

    #cul_pagination ul {
        margin-bottom: 0;
    }

    #culLoading {
        padding: 35px;
        text-align: center;
        color: #6b7280;
    }

    @media(max-width: 768px) {
        .culling-filter-card {
            padding: 15px;
        }

        .culling-footer {
            align-items: flex-start;
        }
    }
</style>

<div class="culling-report-wrap">

    <!-- FILTER -->
    <div class="culling-filter-card">

        <div class="row g-3">

            <div class="col-md-3">
                <label for="cul_filter_plant" class="form-label">
                    Plant
                </label>

                <select id="cul_filter_plant" class="form-select">
                    <option value="">All Plant</option>

                    <?php foreach (($plants ?? []) as $plant): ?>
                        <?php if ($plant->CODE !== '*'): ?>
                            <option value="<?= html_escape($plant->CODE); ?>">
                                <?= html_escape($plant->CODE_NAME); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="cul_filter_class_out" class="form-label">
                    Kategori
                </label>

                <select id="cul_filter_class_out" class="form-select">
                    <option value="">-- Semua Kategori --</option>

                    <?php foreach (($class_out ?? []) as $class): ?>
                        <option value="<?= html_escape($class->CODE); ?>">
                            <?= html_escape($class->CODE_NAME); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="cul_date_from" class="form-label">
                    Date From
                </label>

                <input
                    type="date"
                    id="cul_date_from"
                    class="form-control"
                >
            </div>

            <div class="col-md-3">
                <label for="cul_date_to" class="form-label">
                    Date To
                </label>

                <input
                    type="date"
                    id="cul_date_to"
                    class="form-control"
                >
            </div>

            <div class="col-md-9">
                <label for="cul_filter_search" class="form-label">
                    Search
                </label>

                <input
                    type="text"
                    id="cul_filter_search"
                    class="form-control"
                    placeholder="Search remark or created by..."
                >
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button
                    type="button"
                    id="cul_btnFilter"
                    class="btn btn-primary w-100"
                >
                    <i class="fas fa-search me-1"></i>
                    Filter
                </button>
            </div>

            <div class="col-md-1 d-flex align-items-end">

                <div class="d-flex justify-content-end">
                    <div class="dropdown">
                        <button
                            type="button"
                            class="btn btn-success dropdown-toggle"
                            id="cul_btnExport"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="fas fa-download me-1"></i>
                            Export
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="cul_btnExport">
                            <li>
                                <button
                                    type="button"
                                    class="dropdown-item"
                                    id="cul_btnExcel"
                                >
                                    <i class="fas fa-file-excel text-success me-2"></i>
                                    Export Excel
                                </button>
                            </li>

                            <li>
                                <button
                                    type="button"
                                    class="dropdown-item"
                                    id="cul_btnPdf"
                                >
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    Export PDF
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- TABLE -->
    <div class="culling-table-card">

        <div id="culLoading" class="d-none">
            <div class="spinner-border spinner-border-sm me-2"
                 role="status"></div>
            Loading data...
        </div>

        <div id="cullingReportWrapper" class="culling-table-scroll">

            <table class="culling-table">
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Plant</th>
                        <th class="text-center">Sales</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-number text-center">Ekor</th>
                        <th class="text-number text-center">Berat</th>
                        <th class="text-number text-center">Harga</th>
                        <th class="text-number text-center">Total</th>
                        <th class="text-center">Remark</th>
                    </tr>
                </thead>

                <tbody id="cul_tableBody">
                    <tr>
                        <td colspan="13" class="text-center py-5 text-muted">
                            Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="culling-footer">
            <div id="cul_pageInfo">Total data: 0</div>
            <div id="cul_pagination"></div>
        </div>

    </div>
</div>

<script>
window.CullingReport = {

    loaded: false,
    page: 1,
    limit: 10,

    init() {
        if (this.loaded) return;

        this.loaded = true;

        this.initSelect2();
        this.bind();
        this.setDefault();
        this.load();
    },

    initSelect2() {
        if ($.fn.select2) {
            $('#cul_filter_plant').select2({
                width: '100%',
                placeholder: 'Pilih Plant',
                allowClear: true
            });

            $('#cul_filter_class_out').select2({
                width: '100%',
                placeholder: 'Pilih Class Out',
                allowClear: true
            });
        }
    },

    setDefault() {
        const now = new Date();

        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');

        $('#cul_date_from').val(`${yyyy}-${mm}-01`);
        $('#cul_date_to').val(`${yyyy}-${mm}-${dd}`);
    },

    bind() {
        let timer;

        $('#cul_btnFilter').on('click', () => {
            this.page = 1;
            this.load();
        });

        $('#cul_filter_search').on('keyup', () => {
            clearTimeout(timer);

            timer = setTimeout(() => {
                this.page = 1;
                this.load();
            }, 300);
        });

        $(
            '#cul_filter_plant, #cul_filter_class_out, ' +
            '#cul_date_from, #cul_date_to'
        ).on('change', () => {
            this.page = 1;
            this.load();
        });

        $('#cul_btnExcel').on('click', () => {
            this.exportReport('excel');
        });

        $('#cul_btnPdf').on('click', () => {
            this.exportReport('pdf');
        });

        $(document).on(
            'click',
            '#cul_pagination a',
            (e) => {
                e.preventDefault();

                const page = $(e.currentTarget).data('page');

                if (page) {
                    this.load(page);
                }
            }
        );
    },

    exportReport(type) {
        const filters = this.query();

        delete filters.page;
        delete filters.limit;

        const params = new URLSearchParams(filters).toString();

        const baseUrl = type === 'excel'
            ? '<?= base_url("report-inventory/export_excel_culling"); ?>'
            : '<?= base_url("report-inventory/export_pdf_culling"); ?>';

        window.open(baseUrl + '?' + params, '_blank');
    },

    query() {
        return {
            page: this.page,
            limit: this.limit,
            plant: $('#cul_filter_plant').val(),
            class_out: $('#cul_filter_class_out').val(),
            search: $('#cul_filter_search').val(),
            date_from: $('#cul_date_from').val(),
            date_to: $('#cul_date_to').val()
        };
    },

    showLoading() {
        $('#culLoading').removeClass('d-none');
        $('#cullingReportWrapper').hide();
    },

    hideLoading() {
        $('#culLoading').addClass('d-none');
        $('#cullingReportWrapper').show();
    },

    escape(value) {
        return $('<div>').text(value ?? '').html();
    },

    formatNumber(value) {
        const number = Number(value);

        if (!Number.isFinite(number)) return '-';

        return number.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    },

    formatDate(value) {
        if (!value) return '-';

        const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);

        if (match) {
            return `${match[3]}-${match[2]}-${match[1]}`;
        }

        return this.escape(value);
    },

    load(page = null) {
        if (page !== null) {
            this.page = page;
        }

        this.showLoading();

        $.get(
            '<?= base_url("report-inventory/load_culling"); ?>',
            this.query()
        )
        .done((resp) => {
            if (typeof resp === 'string') {
                try {
                    resp = JSON.parse(resp);
                } catch (e) {
                    this.render([]);
                    $('#cul_pageInfo').text('Gagal membaca data laporan.');
                    return;
                }
            }

            if (!resp || resp.status !== true) {
                this.render([]);
                $('#cul_pageInfo').text('Gagal mengambil data laporan.');
                return;
            }

            this.render(resp.rows || []);

            $('#cul_pagination').html(resp.pagination || '');

            $('#cul_pageInfo').text(
                `Total data: ${resp.total || 0}`
            );
        })
        .fail((xhr) => {
            this.render([]);
            $('#cul_pageInfo').text('Terjadi kesalahan saat mengambil data.');

            console.error(
                'Load Culling error:',
                xhr.responseText
            );
        })
        .always(() => {
            this.hideLoading();
        });
    },

    render(rows) {
        const body = $('#cul_tableBody');

        body.empty();

        if (!rows || rows.length === 0) {
            body.html(`
                <tr>
                    <td colspan="13"
                        class="text-center py-5 text-muted">
                        Data tidak ditemukan
                    </td>
                </tr>
            `);

            return;
        }

        const start = (this.page - 1) * this.limit;

        let html = '';

        rows.forEach((r, index) => {
            html += `
                <tr>
                    <td class="text-center">${this.formatDate(r.ymd)}</td>
                    <td class="text-center">${this.escape(r.PLANT_NAME || r.plant || '-')}</td>
                    <td class="text-center">${this.escape(r.SALES || '-')}</td>
                    <td class="text-center">${this.escape(r.CLASS_OUT_NAME || r.class_out || '-')}</td>
                    <td class="text-number">${this.formatNumber(r.EKOR)}</td>
                    <td class="text-number">${this.formatNumber(r.berat)}</td>
                    <td class="text-number">${this.formatNumber(r.HARGA)}</td>
                    <td class="text-number">${this.formatNumber(r.jumlah)}</td>
                    <td class="text-center">${this.escape(r.remark || '-')}</td>
                </tr>
            `;
        });

        body.html(html);
    }
};
</script>
