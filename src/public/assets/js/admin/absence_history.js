document.addEventListener('DOMContentLoaded', function () {
    var dtable = $('table#absence-history-table').DataTable({
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
                data: 'proof_file_path',
                className: 'text-center',
                orderable: false,
                render: function (data, type, row) {
                    if (row.justify == 1 && data) {
                        return `<a href="${BASE_URL}${data.replace(/^\//, '')}" class="btn btn-sm btn-success" download title="Descargar justificante">
                            <i class="bi bi-download"></i>
                        </a>`;
                    }
                    return '<span class="text-muted">—</span>';
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
                targets: [5, 6],
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
                        className: 'btn btn-excel mx-1',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4],
                            stripHtml: true,
                        },
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
            var justifyFilter = $('#justifies').val();

            var rowJustify = rowData.justify;
            var rowDate = rowData.date_absence;

            if (justifyFilter !== "" && rowJustify != justifyFilter) {
                return false;
            }

            if (min || max) {
                if (!rowDate) return false;
                if (min && max) {
                    if (rowDate < min || rowDate > max) return false;
                } else if (min) {
                    if (rowDate < min) return false;
                } else if (max) {
                    if (rowDate > max) return false;
                }
            }

            return true;
        }
    );

    function toggleResetButton() {
        const hasSearch = $('#search-teacher').val().trim() !== '';
        const hasJustify = $('#justifies').val() !== '';
        const hasDateFrom = $('#date-from').val() !== '';
        const hasDateTo = $('#date-to').val() !== '';

        if (hasSearch || hasJustify || hasDateFrom || hasDateTo) {
            $('#reset').fadeIn(300).css('display', 'flex');
        } else {
            $('#reset').fadeOut(300);
        }
    }

    $('#search-teacher').on('keyup', function () {
        toggleResetButton();
    });

    $('#date-from, #date-to, #justifies').on('change', function () {
        toggleResetButton();
        dtable.draw();
    });

    $('#reset button').on('click', function () {
        $('#search-teacher').val('');
        dtable.column(0).search('').draw();

        $('#justifies').val('').trigger('change');

        $('.select-group').each(function () {
            const $select = $(this).find('select');
            const firstOpt = $select.find('option:first').text();
            $(this).find('.custom-select-trigger').text(firstOpt);
            $(this).find('.custom-option').removeClass('selected');
            $(this).find('.custom-option:first').addClass('selected');
        });

        if (document.querySelector("#date-from")._flatpickr) {
            document.querySelector("#date-from")._flatpickr.clear();
        }
        if (document.querySelector("#date-to")._flatpickr) {
            document.querySelector("#date-to")._flatpickr.clear();
        }

        dtable.draw();
        toggleResetButton();
    });

    const fpConfig = {
        locale: 'es',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        static: true,
        onReady: function (selectedDates, dateStr, instance) {
            $(instance.altInput).attr('placeholder', 'dd/mm/aaaa');
            $(instance.altInput).css({
                'background-color': '#ffffff',
                'background': '#ffffff',
                'color': '#475569',
                'opacity': '1'
            });
        },
        onChange: function () {
            dtable.draw();
        }
    };
    flatpickr("#date-from", fpConfig);
    flatpickr("#date-to", fpConfig);

    $('.select-group').each(function () {
        var $group = $(this);
        var $select = $group.find('select');
        var $options = $select.find('option');

        var $trigger = $('<div class="custom-select-trigger">' + $select.find('option:selected').text() + '</div>');
        $group.append($trigger);

        var $optionsContainer = $('<div class="custom-options"></div>');
        $options.each(function () {
            var $opt = $(this);
            var $customOpt = $('<div class="custom-option" data-value="' + $opt.val() + '">' + $opt.text() + '</div>');
            if ($opt.is(':selected')) $customOpt.addClass('selected');
            $optionsContainer.append($customOpt);
        });
        $group.append($optionsContainer);

        $trigger.on('click', function (e) {
            e.stopPropagation();
            $('.select-group').not($group).removeClass('open');
            $group.toggleClass('open');
        });

        $optionsContainer.on('click', '.custom-option', function () {
            var val = $(this).data('value');
            var text = $(this).text();

            $select.val(val).trigger('change');
            $trigger.text(text);
            $optionsContainer.find('.custom-option').removeClass('selected');
            $(this).addClass('selected');
            $group.removeClass('open');
        });
    });

    $(document).on('click', function () {
        $('.select-group').removeClass('open');
    });

    $('table#absence-history-table').on('click', '.view-details', function () {
        var id = $(this).data('id');
        Modal.show(BASE_URL + 'absence/get-absence-by-id/' + id);
    });
});