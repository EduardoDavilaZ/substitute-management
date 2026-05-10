document.addEventListener('DOMContentLoaded', function () {
    var dtable = $('table#substitutions-table').DataTable({
        ajax: {
            url: BASE_URL + "absence/get-absences-detail",
            dataSrc: 'data'
        },
        responsive: true,
        columns: [
            { data: 'absent' },
            { data: 'class' },
            { data: 'date_absence', className: 'text-center' },
            { data: 'name_hour', className: 'text-center' },
            {
                data: 'justify',
                className: 'text-center',
                render: function (data) {
                    return data == 1 ? '<span class="badge bg-success">Justificada</span>' : '<span class="badge bg-danger">Sin justificar</span>';
                }
            },
            {
                data: 'state',
                className: 'text-center',
                render: function (data) {
                    let badgeClass = 'bg-secondary';
                    if (data === 'CONFIRMADO') badgeClass = 'bg-success';
                    if (data === 'PENDIENTE') badgeClass = 'bg-warning';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { data: 'sustitute', className: 'text-center' },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (row) {
                    return `
                        <div class="boxButton">
                            <button type="button" class="btn btn-info btn-sm view-details" data-id="${row.id}" title="Ver detalles completos">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>`;
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (row) {
                    return `
                        <div class="boxButton">
                            <button type="button" class="btn btn-primary btn-sm assign-substitute" data-id="${row.id}">
                                <i class="bi bi-person-plus-fill me-2"></i> Asignar
                            </button>
                        </div>`;
                }
            },
        ],
        columnDefs: [
            {
                targets: '_all',
                defaultContent: '<i class="text-muted">(no asignado)</i>',
                orderable: true,
                orderSequence: ['asc', 'desc']
            },
            {
                targets: [7, 8],
                orderable: false
            }
        ],
        order: [[5, 'desc']],
        lengthChange: false,
        info: false,
        searching: true,
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: null,
            bottomEnd: 'paging'
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        createdRow: function (row, data) {
            if (data.state === 'CONFIRMADO') {
                $(row).addClass('row-confirmed');
            } else if (data.state === 'PENDIENTE') {
                $(row).addClass('row-pending');
            }
        },
        drawCallback: function () {
            var api = this.api();
            $('#record-count').text(api.page.info().recordsDisplay);
        }
    });

    $('#search-teacher').on('keyup', function () {
        dtable.column(0).search(this.value).draw();
    });

    $.fn.dataTable.ext.search.push(
        function (settings, data, index, rowData) {
            var min = $('#date-from').val();
            var max = $('#date-to').val();
            var date = rowData.date_absence;

            if (!min && !max) return true;
            if (!date) return false;

            if (min && max) {
                return (date >= min && date <= max);
            } else if (min) {
                return (date === min);
            } else if (max) {
                return (date === max);
            }

            return true;
        }
    );

    $('#date-from, #date-to').on('change', function () {
        dtable.draw();
    });
});