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
        
        // Clases
        $(document).on('click', '#btnAddClassSelector', () => this.addClassSelector());
        $(document).on('click', '.btnRemoveClass', (e) => this.removeClassSelector(e));
        $(document).on('change', '.select-class-trigger', (e) => this.handleClassSelection(e));

        // Profesores (Nuevos disparadores)
        $(document).on('click', '#btnAddTeacherSelector', () => this.addTeacherSelector());
        $(document).on('click', '.btnRemoveTeacher', (e) => this.removeTeacherSelector(e));
        $(document).on('change', '.select-teacher-trigger', (e) => this.handleTeacherSelection(e));

        $(document).on('change', 'input[name="start_date"]', (e) => this.handleDateChange('start'));
        $(document).on('change', 'input[name="end_date"]', (e) => this.handleDateChange('end'));
    },

    handleModalOpen: function(e) {
        e.preventDefault();
        const id = $(e.currentTarget).data('id') || 0;
        const url = `${BASE_URL}event/event-form-modal/${id}`;
        Modal.show(url);
    },
    
    handleSubmit: function(e) {
        e.preventDefault();
        
        // Limpiar selectores vacíos de clases antes de enviar
        $('.select-class-trigger').each(function() {
            if (!$(this).val()) { $(this).closest('.class-selector-item').remove(); }
        });

        // Limpiar selectores vacíos de profesores antes de enviar
        $('.select-teacher-trigger').each(function() {
            if (!$(this).val()) { $(this).closest('.teacher-selector-item').remove(); }
        });

        const formData = new FormData(e.target);
        const id = parseInt($(e.target).data('id'));

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
                if (res.status === 'success') { 
                    swal({ icon: 'success', title: '¡Hecho!', text: res.message }).then(() => location.reload());
                } else {
                    swal({ icon: 'error', title: 'Atención', text: res.message });
                }
            },
            error: (xhr) => {
                console.log("Respuesta del servidor:", xhr.responseText);
                swal({ icon: 'error', title: 'Error', text: 'La operación no se pudo procesar.' });
            }
        });
    },

    delete: function(e) {
        const id = $(e.currentTarget).data('id');
        swal({ 
            title: "¿Eliminar evento?", 
            text: "Se eliminarán también las ausencias asociadas a los profesores.", 
            icon: "warning", 
            buttons: ["Cancelar", "Borrar"], 
            dangerMode: true 
        }).then((willDelete) => {
            if (willDelete) { this.ajaxRequest('event/delete-event/', { id: id }, false); } 
        });
    },
    
    // Métodos lógicos para Clases
    addClassSelector: function() {
        const template = $('#classSelectorTemplate').html();
        $('#newClassesSelectors').append(template);
        const container = $('#classesContainer');
        container.scrollTop(container[0].scrollHeight);
    },

    handleClassSelection: function(e) {
        let select = $(e.target);
        let id = select.val();
        if (!id) return;

        let isDuplicate = $('#classesContainer').find(`input[name="class_ids[]"][value="${id}"]`).length > 0;
        if (isDuplicate) {
            swal({ icon: 'warning', title: 'Clase duplicada', text: 'Esta clase ya ha sido añadida.' });
            select.val(''); return;
        }

        let option = select.find(':selected');
        let item = select.closest('.class-selector-item');
        item.find('.class-code-text').text(option.data('code'));
        item.find('.class-name-text').text(option.data('name'));
        item.find('.class-id-input').val(id);
        item.find('.selector-phase').addClass('d-none');
        item.find('.display-phase').removeClass('d-none').hide().fadeIn(300);
    },

    removeClassSelector: function(e) {
        $(e.currentTarget).closest('.class-selector-item').fadeOut(200, function() { $(this).remove(); });
    },

    // Métodos lógicos para Profesores
    addTeacherSelector: function() {
        const template = $('#teacherSelectorTemplate').html();
        $('#newTeachersSelectors').append(template);
        const container = $('#teachersContainer');
        container.scrollTop(container[0].scrollHeight);
    },

    handleTeacherSelection: function(e) {
        let select = $(e.target);
        let id = select.val();
        if (!id) return;

        let isDuplicate = $('#teachersContainer').find(`input[name="teacher_ids[]"][value="${id}"]`).length > 0;
        if (isDuplicate) {
            swal({ icon: 'warning', title: 'Profesor duplicado', text: 'Este profesor ya ha sido añadido.' });
            select.val(''); return;
        }

        let option = select.find(':selected');
        let item = select.closest('.class-selector-item, .teacher-selector-item');
        item.find('.teacher-name-text').text(option.data('name'));
        item.find('.class-id-input, .teacher-id-input').val(id);
        item.find('.selector-phase').addClass('d-none');
        item.find('.display-phase').removeClass('d-none').hide().fadeIn(300);
    },

    removeTeacherSelector: function(e) {
        $(e.currentTarget).closest('.teacher-selector-item').fadeOut(200, function() { $(this).remove(); });
    },

    handleDateChange: function(source) {
        let startDateInput = $('input[name="start_date"]');
        let endDateInput = $('input[name="end_date"]');
        let startVal = startDateInput.val();
        let endVal = endDateInput.val();

        if (!startVal || !endVal) return;
        if (startVal > endVal) {
            if (source === 'start') { endDateInput.val(''); endDateInput[0].showPicker(); } 
            else if (source === 'end') { startDateInput.val(''); startDateInput[0].showPicker(); }
        }
    },
};

EventManager.init();