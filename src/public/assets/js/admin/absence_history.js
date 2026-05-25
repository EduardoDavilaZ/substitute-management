document.addEventListener('DOMContentLoaded', function () {
    $('table#absence-history-table').DataTable({
        ajax: {
            url: BASE_URL + "absence/get-absences-history",
            dataSrc: 'data'
        },
        responsive: true,
        columns: [
            { data: 'absent' },
            { data: 'absent_hours', className: 'text-center' },
            { data: 'date_absence', className: 'text-center' },
            { data: 'reason' },
            {
                data: 'justify',
                className: 'text-center',
                render: function (data) {
                    return data == 1
                        ? '<span class="badge bg-success">Justificada</span>'
                        : '<span class="badge bg-danger">Sin justificar</span>';
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (row) {
                    return `<div class="boxButton">
                        <button type="button" class="btn btn-info btn-sm view-details" data-id="${row.id}" title="Ver detalles completos">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>`;
                }
            }
        ],
        columnDefs: [
            {
                targets: '_all',
                defaultContent: '<i class="text-muted">(no asignado)</i>',
                orderable: true,
                orderSequence: ['asc', 'desc']
            },
            {
                targets: 5,
                orderable: false
            }
        ],
        order: [[2, 'desc']],
        lengthChange: false,
        info: false,
        searching: true,
        layout: {
            topStart: null,
            topEnd: {
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Exportar a Excel',
                        className: 'btn btn-excel mx-1'
                    },
                    DataTablesPdfTheme.pdfButton(
                        {
                            text: '<i class="bi bi-file-earmark-pdf"></i> Descargar pdf',
                            className: 'btn btn-pdf mx-1',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4],
                                stripHtml: true,
                            },
                        },
                        { title: 'Historial de ausencias', orientation: 'landscape' }
                    )
                ]
            },
            bottomStart: null,
            bottomEnd: 'paging'
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
    });

    $('table#absence-history-table').on('click', '.view-details', function () {
        var id = $(this).data('id');
        Modal.show(BASE_URL + 'absence/get-absence-by-id/' + id);
    });
});
