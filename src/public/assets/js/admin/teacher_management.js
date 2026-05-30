document.addEventListener('DOMContentLoaded', function () {
    const dtable = $('table#teachers-table').DataTable({
        ajax: {
            url: BASE_URL + "teacher/get-tec-enabled",
            dataSrc: 'data'
        },
        responsive: true,
        autoWidth: false,
        columns: [
            { data: 'full_name' },
            { data: 'email' },
            {
                data: 'phone',
                className: 'text-center',
                render: function (data) {
                    if (!data || data.trim() === '') {
                        return '<span class="text-muted-custom fst-italic">Sin teléfono</span>';
                    }
                    return data;
                }
            },
            { data: 'substitution_counter', className: 'text-center' },
            {   
                data: 'is_tutor',
                className: 'text-center',
                render: function (data) {
                    return (data === 1) 
                        ? '<span class="badge-status badge-blue">SÍ</span>' 
                        : '<span class="badge-status badge-gray">NO</span>';
                }
            },
            // {
            //     data: null,
            //     orderable: false,
            //     className: 'text-center',
            //     render: function (row) {
            //         return `
            //             <div class="boxButton center">
            //                 <button type="button" class="btn-edit mod-teacher fs-6" data-mod-id="${row.id}" title="Editar Profesor">
            //                     <i class="bi bi-pencil-square"></i>
            //                 </button>
            //             </div>`;
            //     }
            // },
            // {
            //     data: null,
            //     orderable: false,
            //     className: 'text-center',
            //     render: function (row) {
            //         return `
            //             <div class="boxButton center">
            //                 <button type="button" class="btn-delete fs-6" data-del-id="${row.id}" title="Eliminar Profesor">
            //                     <i class="bi bi-trash"></i>
            //                 </button>
            //             </div>`;
            //     }
            // },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (row) {
                    return `
                        <div class="boxButton center">
                            <button type="button" class="btn btn-action btn-charge fs-6" data-schedule-id="${row.id}" title="Cargar Horario">
                                <i class="bi bi-calendar3"></i>
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
                    placeholder: 'Buscar profesor...'
                },
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Exportar a Excel',
                        className: 'btn btn-excel mx-1',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4] 
                        }
                    },
                    DataTablesPdfTheme.pdfButton(
                        {
                            text: '<i class="bi bi-file-earmark-pdf"></i> Descargar pdf',
                            className: 'btn btn-pdf mx-1',
                            exportOptions: {
                                columns: [0, 1, 2, 3],
                                stripHtml: true,
                            },
                            customize: function (doc) {
                                doc.pageMargins = [60, 48, 60, 56];

                                doc.content.forEach(function(element) {
                                    if (element.table) {
                                        element.table.widths = [160, 305, 90, 80];
                                    }
                                });
                            }
                        },
                        { 
                            title: 'Gestión de profesores', 
                            orientation: 'landscape' 
                        }
                    ),
                    {
                        text: '<i class="bi bi-download"></i> Descargar Plantilla',
                        className: 'btn btn-secondary btn-downlo mx-1',
                        action: function () {
                            window.location.href = BASE_URL + 'schedule/download-schedule-template';
                        }
                    }
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
                targets: [5],
                orderable: false
            }
        ],
        order: [[0, 'asc']]
    });
    ///---------------DELETE
    // $('#teachers-table').on('click', '.btn-delete', function () {
    //     var btn = $(this);
    //     var $tr = btn.closest('tr');
        
    //     if ($tr.hasClass('child')) {
    //         $tr = $tr.prev('.parent');
    //     }

    //     var id = btn.data('del-id');

    //     swal({
    //         title: "¿Estás seguro?",
    //         text: "Una vez eliminado, no podrás recuperar este registro.",
    //         icon: "warning",
    //         buttons: ["Cancelar", "Sí, eliminar"],
    //         dangerMode: true,
    //     })
    //     .then((willDelete) => {
    //         if (willDelete) {
    //             $.ajax({
    //                 url: BASE_URL + "teacher/delete-teacher-by-id",
    //                 type: "POST",
    //                 data: {
    //                     teacher_id: id
    //                 },
    //                 dataType: 'json',
    //                 success: function (response) {
    //                     if (response.status === 'success') {
    //                         dtable.row($tr).remove().draw(false);
    //                         swal("¡Eliminado!", response.message, "success", {
    //                             timer: 1500,
    //                             buttons: false
    //                         });
    //                     } else {
    //                         swal("Error", response.message, "error");
    //                     }
    //                 },
    //                 error: function () {
    //                     swal("Error", "No se pudo completar la petición de borrado.", "error");
    //                 }
    //             });
    //         }
    //     });
    // });
    ///---------------MODIFY
    // $('#teachers-table').on('click', '.mod-teacher', function(e) {
    //     e.preventDefault();
    //     var id = $(this).data('mod-id');
    //     Modal.show(`${BASE_URL}teacher/get-teacher-by-id/${id}`);
    // });


    function validateField(input) {
        var name = input.attr('name');
        var value = input.val().trim();
        
        input.siblings('.invalid-feedback').remove();

        if (name === 'nameTeacher') {
            if (value === '') {
                input.addClass('is-invalid').removeClass('is-valid');
                input.after('<div class="invalid-feedback">El nombre es obligatorio.</div>');
            } else if (value.length < 3) {
                input.addClass('is-invalid').removeClass('is-valid');
                input.after('<div class="invalid-feedback">Debe tener al menos 3 caracteres.</div>');
            } else {
                input.addClass('is-valid').removeClass('is-invalid');
            }
        }

        if (name === 'emailTeacher') {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (value === '') {
                input.addClass('is-invalid').removeClass('is-valid');
                input.after('<div class="invalid-feedback">El correo es obligatorio.</div>');
            } else if (!emailRegex.test(value)) {
                input.addClass('is-invalid').removeClass('is-valid');
                input.after('<div class="invalid-feedback">Formato de correo inválido.</div>');
            } else {
                input.addClass('is-valid').removeClass('is-invalid');
            }
        }

        if (name === 'phoneTeacher') {
            if (value === '') {
                input.removeClass('is-invalid is-valid');
                return;
            }
            var phoneRegex = /^[\d\s+\-]+$/;
            if (!phoneRegex.test(value)) {
                input.addClass('is-invalid').removeClass('is-valid');
                input.after('<div class="invalid-feedback">Solo se permiten dígitos, espacios, + y guiones (sin letras).</div>');
            } else if (value.length > 15) {
                input.addClass('is-invalid').removeClass('is-valid');
                input.after('<div class="invalid-feedback">El teléfono no puede superar 15 caracteres.</div>');
            } else {
                input.addClass('is-valid').removeClass('is-invalid');
            }
        }
    }

    $(document).on('input change', '#formModTeacher .input-validate', function() {
        validateField($(this));
    });

    $(document).on('click', '#btnSubmitGuard', function(e) {
        e.preventDefault();

        var $form = $('#formModTeacher');

        var hasFile = $('input[name="profileImage"]')[0].files.length > 0;

        var ajaxConfig = {
            url: BASE_URL + "teacher/update-teacher",
            type: 'POST',
            dataType: 'json',
            success: function(response) {

                if (response.status === 'success') {

                    swal("¡Modificado!", response.message, "success", {
                        timer: 1500,
                        buttons: false
                    });

                    dtable.ajax.reload(null, false);

                    $('.modal').modal('hide');
                    $('#modal-container').empty();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');

                } else {
                    swal("Error", response.message, "error");
                }
            },
            error: function() {
                swal("Error", "No se pudo actualizar el registro.", "error");
            }
        };

        if (hasFile) {

            var formData = new FormData($form[0]);

            formData.append('id', $form.data('id'));

            ajaxConfig.data = formData;
            ajaxConfig.contentType = false;
            ajaxConfig.processData = false;

        } else {

            ajaxConfig.data = {
                id: $form.data('id'),
                nameTeacher: $('input[name="nameTeacher"]').val(),
                emailTeacher: $('input[name="emailTeacher"]').val(),
                phoneTeacher: $('input[name="phoneTeacher"]').val(),
                tutor: $('#tutor').val()
            };
        }

        $.ajax(ajaxConfig);
    });
    $(document).on('click', '.btn-charge', function (e) {
        e.preventDefault();
        var id = $(this).data('schedule-id');
        Modal.show(`${BASE_URL}teacher/get-teacher-by-id-schedule/${id}`);
    });

    $(document).on('click', '#btnSubmitSchedule', function (e) {
        e.preventDefault();

        var $form = $('#formUploadSchedule');
        var teacherId = parseInt($form.data('teacher-id'), 10);

        if (!teacherId) {
            swal('Error', 'Profesor no identificado.', 'error');
            return;
        }

        var fileInput = $form.find('input[name="schedule"]')[0];

        if (!fileInput.files.length) {
            swal('Error', 'Seleccione un archivo Excel.', 'error');
            return;
        }

        var formData = new FormData();
        formData.append('teacher_id', teacherId);
        formData.append('schedule', fileInput.files[0]);

        $.ajax({
            url: BASE_URL + 'schedule/upload-teacher-schedule',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    swal('¡Importado!', response.message, 'success', {
                        timer: 2000,
                        buttons: false
                    });

                    $('.modal').modal('hide');
                    $('#modal-container').empty();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                } else {
                    swal('Error', response.message, 'error');
                }
            },
            error: function (xhr) {
                var text = 'No se pudo importar el horario.';
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.message) {
                        text = res.message;
                    }
                } catch (err) {
                    console.error(err);
                }
                swal('Error', text, 'error');
            }
        });
    });
});
