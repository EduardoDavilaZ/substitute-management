document.addEventListener('DOMContentLoaded', function () {
    var dtable = $('table#teachers-table').DataTable({
        ajax: {
            url: BASE_URL + "teacher/get-tec-enabled",
            dataSrc: 'data'
        },
        responsive: true,
        columns: [
            { data: 'full_name' },
            { data: 'email' },
            {
                data: 'phone',
                className: 'text-center',
                render: function (data) {
                    if (!data || data.trim() === '') {
                        return '<span class="text-muted fst-italic" style="font-size: 0.85rem;">Sin teléfono registrado</span>';
                    }
                    return data;
                }
            },
            { data: 'substitution_counter', className: 'text-center' },
            // { data: 'tutor'},
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (row) {
                    return `
                        <div class="boxButton d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-action" data-mod-id="${row.id}">
                                <i class="bi bi-pencil-square fs-4 text-primary"></i>
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
                        <div class="boxButton d-flex justify-content-center align-items-center">
                            <button class="btn btn-action btn-delete" data-del-id="${row.id}"><i class="bi bi-trash fs-4"></i></button>
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
                    placeholder: 'Buscar profesor...'
                },
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Exportar a Excel',
                        className: 'btn btn-excel mx-1'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> Descargar pdf',
                        className: 'btn btn-pdf mx-1'
                    }
                ]
            },
            bottomStart: null,
            bottomEnd: 'paging'
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
    $('#teachers-table').on('click', '.btn-delete', function () {
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
                    url: BASE_URL + "teacher/delete-teacher-by-id",
                    type: "POST",
                    data: {
                        teacher_id: id
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
});
