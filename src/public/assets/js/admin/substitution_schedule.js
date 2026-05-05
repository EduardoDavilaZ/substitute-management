const GuardSchedule = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        const container = $('.schedule-table'); 
        
        container.on('click', '.btn-edit, .btn-add-slot', this.handleModalOpen);
        $(document).on('submit', '#formAddGuardTime', this.handleSubmit);
    },

    handleModalOpen: function(e) {
        e.preventDefault();
        const btn = $(e.currentTarget);
        
        const params = {
            id: btn.data('id') || '0',
            day: btn.data('day'),
            period: btn.data('period')
        };

        const url = `${BASE_URL}schedule/guard-schedule-assignment/${params.id}/${params.day}/${params.period}`;
        Modal.show(url);
    },

    handleSubmit: function(e) {
        e.preventDefault();
        const form = this;
        
        const formData = new FormData(form);
        formData.append('day', $(form).data('day'));
        formData.append('period_id', $(form).data('period'));

        GuardSchedule.setGuardTime(formData);
    },

    setGuardTime: function(formData) {
        $.ajax({
            url: `${BASE_URL}schedule/set-guard-time/`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: (res) => GuardSchedule.onSuccess(res),
            error: (xhr) => GuardSchedule.onError(xhr)
        });
    },

    onSuccess: (res) => {
        swal({ icon: 'success', title: '¡Éxito!', text: res.message || 'Cambios guardados.' })
            .then(() => location.reload());
    },

    onError: (xhr) => {
        console.error("Error:", xhr.responseText);
        swal({ icon: 'error', title: 'Oops...', text: 'Hubo un error al procesar la petición.' });
    }
};

$(document).ready(() => GuardSchedule.init());