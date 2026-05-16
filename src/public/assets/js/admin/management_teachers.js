document.addEventListener('DOMContentLoaded', function () {
    const dtable = $('table#teachers-table').DataTable({
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
                            <button type="button" class="btn btn-action mod-teacher" data-mod-id="${row.id}">
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
    ///---------------DELETE
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
    ///---------------MODIFY
    $('#teachers-table').on('click', '.mod-teacher', function(e) {
        e.preventDefault();
        var id = $(this).data('mod-id');
        Modal.show(`${BASE_URL}teacher/get-teacher-by-id/${id}`);
    });


    function validarCampo(input) {
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

    $(document).on('input change', '#formModTeacher .form-control', function() {
        validarCampo($(this));
    });

    $(document).on('click', '#btnSubmitGuard', function(e) {
        e.preventDefault();
        
        var $form = $('#formModTeacher');
        if ($form.length === 0) return false;

        $form.find('input[name="nameTeacher"], input[name="emailTeacher"], input[name="phoneTeacher"]').each(function() {
            validarCampo($(this));
        });

        if ($form.find('.is-invalid').length > 0) {
            swal("Error", "Por favor, corrige los errores del formulario.", "error");
            return false;
        }

        var formData = new FormData($form[0]);
        var idTeacher = $form.attr('data-id') || $form.data('id');
        formData.append('id', idTeacher);

        $.ajax({
            url: BASE_URL + "teacher/update-teacher",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    swal("¡Modificado!", response.message, "success", { timer: 1500, buttons: false });
                    
                    if (typeof dtable !== 'undefined') {
                        dtable.ajax.reload(null, false);
                    }
                    
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
        });
    });
});
