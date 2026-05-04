$(document).on('click', '.btn-edit, .btn-add-slot', function(e) {
    e.preventDefault();
    
    const $btn = $(e.currentTarget);
    
    const id = $btn.data('id') || ''; 
    const info = $btn.data('info') || ''; 

    const url = `${BASE_URL}schedule/guard-schedule-assignment/${id}?info=${encodeURIComponent(info)}`;

    Modal.show(url);
});