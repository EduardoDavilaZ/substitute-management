const EventManager = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        $(document).on('click', '.btn-edit', (e) => this.handleModalOpen(e));
        $(document).on('click', '.btn-delete', (e) => this.delete(e));
        $(document).on('click', '.btn-create', (e) => this.handleModalOpen(e));
        
        $(document).on('submit', '#formEvent', (e) => this.handleSubmit(e));
        $(document).on('click', '#btnSubmitEvent', () => $('#formEvent').submit());
        $(document).on('click', '#btnAddClassSelector', () => this.addClassSelector());
$(document).on('click', '.btnRemoveClass', (e) => this.removeClassSelector(e));
    },

    handleModalOpen: function(e) {
        e.preventDefault();
        const id = $(e.currentTarget).data('id') || 0;
        const url = `${BASE_URL}event/event-form-modal/${id}`;
        Modal.show(url);
    },
    
    handleSubmit: function(e) {
        e.preventDefault();
        const $form = $(e.target);
        const formData = new FormData(e.target);
        const id = parseInt($form.data('id'));

        if (id > 0) formData.append('id', id);

        const url = (id > 0) ? 'event/update-event/' : 'event/add-event/';
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
                    text: res.message || 'Operación completada.' 
                }).then(() => location.reload());
            },
            error: (xhr) => {
                swal({ 
                    icon: 'error', 
                    title: 'Error', 
                    text: 'No se pudo procesar la solicitud.' 
                });
            }
        });
    },

    delete: function(e) {
        const id = $(e.currentTarget).data('id');
        swal({ 
            title: "¿Eliminar evento?", 
            text: "Esta acción no se puede deshacer.", 
            icon: "warning", 
            buttons: ["Cancelar", "Borrar"], 
            dangerMode: true 
        }).then((willDelete) => {
            if (willDelete) {
                this.ajaxRequest('event/delete-event/', { id: id }, false);
            } 
        });
    },
    
    addClassSelector: function() {
    const template = $('#classSelectorTemplate').html();
    // Añadimos el selector al contenedor de "nuevos"
    $('#newClassesSelectors').append(template);
    
    // Opcional: Hacer scroll automático hacia abajo para ver el nuevo selector
    const container = $('#classesContainer');
    container.scrollTop(container[0].scrollHeight);
},

removeClassSelector: function(e) {
    // Eliminamos la fila o el selector completo
    $(e.currentTarget).closest('.class-selector-item').fadeOut(200, function() {
        $(this).remove();
    });
},
};

EventManager.init();