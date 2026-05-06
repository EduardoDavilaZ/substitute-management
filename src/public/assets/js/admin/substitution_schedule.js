const GuardSchedule = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        const container = $('.schedule-table');
        container.on('click', '.btn-edit, .btn-add-slot', this.handleModalOpen);
        container.on('click', '.btn-delete', (e) => this.delete(e));
        container.on('click', '.btn-update', (e) => this.update(e));
        
        $(document).on('submit', '#formAddGuardPeriod', (e) => this.handleSubmit(e));
        $(document).on('click', '#btnSubmitGuard', () => $('#formAddGuardPeriod').submit());
    },

    handleModalOpen: function(e) {
        e.preventDefault();
        const btn = $(e.currentTarget);
        const url = `${BASE_URL}schedule/guard-schedule-assignment/${btn.data('id') || 0}/${btn.data('day')}/${btn.data('period')}`;
        Modal.show(url);
    },
    
    handleSubmit: function(e) {
        e.preventDefault();
        const $form = $(e.target);
        const formData = new FormData(e.target);
        const id = parseInt($form.data('id'));

        formData.append('day', $form.data('day'));
        formData.append('period_id', $form.data('period'));
        
        if (id > 0){
            formData.append('id', id);
        } 

        const url = (id > 0) 
            ? 'schedule/update-guard-period/' 
            : 'schedule/set-guard-period/';
        this.ajaxRequest(url, formData);
    },

    ajaxRequest: function(endpoint, data, isFormData = true) {
        $.ajax({
            url: `${BASE_URL}${endpoint}`,
            type: 'POST',
            data: data,
            contentType: isFormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
            processData: isFormData ? false : true,
            dataType: 'json',
            success: (res) => {
                swal({ 
                    icon: 'success', 
                    title: '¡Éxito!', 
                    text: res.message || 'Cambios guardados.' 
                }).then(() => location.reload());
            },
            error: (xhr) => {
                console.error("Error:", xhr.responseText);
                swal({ 
                    icon: 'error', 
                    title: 'Oops...', 
                    text: 'Hubo un error al procesar la petición.' 
                });
            }
        });
    },

    delete: function(e) {
        const id = $(e.currentTarget).data('id');
        swal({ 
            title: "¿Seguro?", 
            text: "No se puede deshacer.", 
            icon: "warning", 
            buttons: ["Cancelar", "Sí"], 
            dangerMode: true 
        }).then((willDelete) => {
                if (willDelete) {
                    this.ajaxRequest('schedule/delete-guard-period/', { 
                        id: id 
                    }, false);
                } 
            });
    },

    update: function(e) {
        const btn = $(e.currentTarget);
        swal({ 
                title: "¿Confirmar?", 
                text: "¿Actualizar el profesor asignado?", 
                icon: "info", 
                buttons: ["Cancelar", "Sí"] 
        }).then((willUpdate) => {
            if (willUpdate){
                this.ajaxRequest('schedule/update-guard-period/', { 
                    id: btn.data('id'), 
                    teacher_id: btn.data('teacher_id') 
                }, false);
            } 
        });
    }
};

$(document).ready(() => GuardSchedule.init());