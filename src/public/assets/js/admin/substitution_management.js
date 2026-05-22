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
            { data: 'date_absence' },
            { data: 'name_hour' },
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
            {
                data:'substitute',
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (row) {
                    return `
                        <div class="boxButton">
                            <button type="button" class="btn btn-info btn-sm view-details" data-detail-id="${row.id}" title="Ver detalles completos">
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
                    const isDisabled = row.state !== 'PENDIENTE'; 
                    
                    return `
                    <div class="boxButton">
                        <button type="button" 
                                class="btn btn-primary btn-sm assign-substitute ${isDisabled ? 'disabled' : ''}" 
                                data-assig-id="${row.id}"
                                ${isDisabled ? 'disabled' : ''}>
                            <i class="bi bi-person-plus-fill me-2"></i> Asignar
                        </button>
                    </div>`;
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (row) {
                    const isDisabled = row.state !== 'PENDIENTE';
                    return `
                        <div class="boxButton d-flex justify-content-center align-items-center ${isDisabled ? 'disabled' : ''}">
                            <button class="btn btn-action btn-delete" data-del-id="${row.id}"${isDisabled ? 'disabled' : ''}><i class="bi bi-trash fs-4"></i></button>
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
                targets: [6, 7, 8],
                orderable: false
            }
        ],
        order: [[4, 'desc']],
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
                                columns: [0, 1, 2, 3, 4, 5,6],
                                stripHtml: true,
                            },
                    },
                    DataTablesPdfTheme.pdfButton(
                        {
                            text: '<i class="bi bi-file-earmark-pdf"></i> Descargar pdf',
                            className: 'btn btn-pdf mx-1',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                stripHtml: true,
                            },
                        },
                        { title: 'Gestión de ausencias y sustituciones', orientation: 'landscape' }
                    )
                ]
            },
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
    /**
     * Search teacher 
     */
    $('#search-teacher').on('keyup', function () {
        dtable.column(0).search(this.value).draw();
    });
    /**
     * Search by date range, state and justification
     */
    $.fn.dataTable.ext.search.push(
        function (settings, data, index, rowData) {
            var min = $('#date-from').val();
            var max = $('#date-to').val();
            var stateFilter = $('#states').val();
            var justifyFilter = $('#justifies').val();

            var rowState = rowData.state;
            var rowJustify = rowData.justify;
            var rowDate = rowData.date_absence;

            if (stateFilter && rowState !== stateFilter) {
                return false;
            }

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
        const hasState = $('#states').val() !== '';
        const hasJustify = $('#justifies').val() !== '';
        const hasDateFrom = $('#date-from').val() !== '';
        const hasDateTo = $('#date-to').val() !== '';

        if (hasSearch || hasState || hasJustify || hasDateFrom || hasDateTo) {
            $('#reset').fadeIn(300).css('display', 'flex');
        } else {
            $('#reset').fadeOut(300);
        }
    }

    $('#search-teacher').on('keyup', function () {
        toggleResetButton();
    });

    $('#date-from, #date-to, #states, #justifies').on('change', function () {
        toggleResetButton();
        dtable.draw();
    });

    /**
     * reset filter
     */
    $('#reset button').on('click', function () {
        $('#search-teacher').val('');
        dtable.column(0).search('').draw();

        $('#states, #justifies').val('').trigger('change');

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
    /*style calendar date*/
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

    $('#substitutions-table').on('click', '.btn-delete', function () {
        var btn = $(this);
        var $tr = btn.closest('tr');
        
        if ($tr.hasClass('child')) {
            $tr = $tr.prev('.parent');
        }

        var id = btn.data('del-id');

        swal({
            title: "¿Estás seguro?",
            text: "Una vez eliminado, no podrás recuperar este registro.",
            icon: "warning",
            buttons: ["Cancelar", "Sí, eliminar"],
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: BASE_URL + "substitution/delete-substitution",
                    type: "POST",
                    data: {
                        substitution_id: id
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            dtable.row($tr).remove().draw(false);
                            swal("¡Eliminado!", response.message, "success", {
                                timer: 1500,
                                buttons: false
                            });
                        } else {
                            swal("Error", response.message, "error");
                        }
                    },
                    error: function () {
                        swal("Error", "No se pudo completar la petición de borrado.", "error");
                    }
                });
            }
        });
    });

    $('#substitutions-table').on('click','.assign-substitute',function(e){
        e.preventDefault();
        var btn = $(this);

        var id = btn.data('assig-id');

        const url = `${BASE_URL}substitution/get-substitution-assig/${id}`;
        Modal.show(url);
    });
    $('#substitutions-table').on('click','.view-details',function(e){
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('detail-id');
        const url = `${BASE_URL}absence/get-data-view/${id}`;
        Modal.show(url);
    });
    $(document).on('click','#btnSubmitAssig',function(e){
        e.preventDefault();
        var form = $('#formAssign');

        if (form.length === 0) {
            swal("Error", "No se pudo encontrar el formulario", "error");
            return;
        }

        const data = new FormData(form[0]);

        $.ajax({
            url: BASE_URL + "substitution/assing-substitute",
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log('Respuesta:', response);
                if (response.status === 'success') {
                    swal("¡Sustitución Asignada!", response.message, "success", {
                        timer: 1500,
                        buttons: false
                    });
                    var dtable = $('table#substitutions-table').DataTable();
                    dtable.ajax.reload();
                    $('#modal-container .modal').modal('hide');
                } else {
                    swal("Error", response.message, "error");
                }
            },
            error: function () {
                swal("Error", "No se pudo completar la petición de asignar.", "error");
            }
        });
    });
});