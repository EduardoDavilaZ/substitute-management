document.addEventListener('DOMContentLoaded', function () {
    const dtable = $('table#groups-table').DataTable({
        ajax: {
            url: BASE_URL + 'group/get-classes-enabled',
            dataSrc: 'data'
        },
        responsive: true,
        autoWidth: false,
        columns: [
            { data: 'code' },
            { data: 'name' },
            {
                data: 'stage',
                className: 'text-center',
                render: function (data) {
                    return `<span class="badge-status badge-blue">${data}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (row) {
                    return `
                        <div class="boxButton center">
                            <button type="button" class="btn-edit mod-group fs-6" data-mod-id="${row.id}" title="Editar grupo">
                                <i class="bi bi-pencil-square"></i>
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
                        <div class="boxButton center">
                            <button type="button" class="btn-delete fs-6" data-del-id="${row.id}" title="Eliminar grupo">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ],
        lengthChange: false,
        info: false,
        searching: true,
        layout: {
            topEnd: {
                search: {
                    placeholder: 'Buscar grupo...'
                },
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Exportar a Excel',
                        className: 'btn btn-excel mx-1',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    DataTablesPdfTheme.pdfButton(
                        {
                            text: '<i class="bi bi-file-earmark-pdf"></i> Descargar pdf',
                            className: 'btn btn-pdf mx-1',
                            exportOptions: {
                                columns: [0, 1, 2],
                                stripHtml: true
                            }
                        },
                        { title: 'Gestión de grupos' }
                    )
                ]
            },
            bottomStart: null,
            bottomEnd: 'paging'
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        columnDefs: [
            {
                targets: '_all',
                orderable: true,
                orderSequence: ['asc', 'desc']
            },
            {
                targets: [3, 4],
                orderable: false
            }
        ],
        order: [[0, 'asc']]
    });

    $('#btnNewGroup').on('click', function (e) {
        e.preventDefault();
        Modal.show(`${BASE_URL}group/group-form-modal/0`);
    });

    $('#groups-table').on('click', '.mod-group', function (e) {
        e.preventDefault();
        const id = $(this).data('mod-id');
        Modal.show(`${BASE_URL}group/group-form-modal/${id}`);
    });

    $('#groups-table').on('click', '.btn-delete', function () {
        const btn = $(this);
        let $tr = btn.closest('tr');

        if ($tr.hasClass('child')) {
            $tr = $tr.prev('.parent');
        }

        const id = btn.data('del-id');

        swal({
            title: '¿Estás seguro?',
            text: 'El grupo se dará de baja y dejará de mostrarse en el listado.',
            icon: 'warning',
            buttons: ['Cancelar', 'Sí, eliminar'],
            dangerMode: true
        }).then(function (willDelete) {
            if (!willDelete) return;

            $.ajax({
                url: BASE_URL + 'group/delete-group-by-id',
                type: 'POST',
                data: { class_id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        dtable.row($tr).remove().draw(false);
                        swal('¡Eliminado!', response.message, 'success', {
                            timer: 1500,
                            buttons: false
                        });
                    } else {
                        swal('Error', response.message, 'error');
                    }
                },
                error: function () {
                    swal('Error', 'No se pudo completar la petición de borrado.', 'error');
                }
            });
        });
    });

    $(document).on('click', '#btnSubmitGroup', function () {
        $('#formGroup').trigger('submit');
    });

    $(document).on('submit', '#formGroup', function (e) {
        e.preventDefault();

        const $form = $(this);
        const id = parseInt($form.data('id'), 10) || 0;
        const formData = new FormData(this);

        if (id > 0) {
            formData.append('id', id);
        }

        const endpoint = id > 0 ? 'group/update-group' : 'group/add-group';

        $.ajax({
            url: BASE_URL + endpoint,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    swal('¡Hecho!', response.message, 'success', {
                        timer: 1500,
                        buttons: false
                    });

                    dtable.ajax.reload(null, false);

                    $('.modal').modal('hide');
                    $('#modal-container').empty();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                } else {
                    swal('Error', response.message, 'error');
                }
            },
            error: function () {
                swal('Error', 'No se pudo guardar el grupo.', 'error');
            }
        });
    });
});
