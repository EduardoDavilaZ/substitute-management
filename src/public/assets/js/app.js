/* ---------------------------------------------------------------------
    MODAL COMPONENT
------------------------------------------------------------------------ */

const Modal = {
    show: (url, param = null, callback = null) => {
        if (typeof jQuery === 'undefined') return console.error("Modal.show: jQuery no está cargado.");
        if (typeof bootstrap === 'undefined') return console.error("Modal.show: Bootstrap JS no está cargado.");
        if (!url) return console.error("Modal.show: La URL es obligatoria.");

        $('.modal-backdrop').remove();
        $('.modal').remove();
        $('body').removeClass('modal-open').css('padding-right', '');

        let $container = $('#modal-container');
        if ($container.length === 0) {
            $container = $('<div id="modal-container"></div>').appendTo('body');
        }
        $container.empty();

        let finalUrl = url.replace(/\/$/, "");
        if (param !== null && param !== undefined && param !== '') {
            finalUrl += `/${param}`;
        }

        $.ajax({
            url: finalUrl,
            method: 'GET',
            success: (html) => {
                $container.html(html);
                const modalEl = $container.find('.modal').get(0);

                if (!modalEl) {
                    return console.error(`Modal.show: No existe .modal en la respuesta de ${finalUrl}`);
                }

                try {
                    const bModal = new bootstrap.Modal(modalEl, {
                        backdrop: 'static',
                        keyboard: true
                    });

                    if (typeof callback === 'function') callback(modalEl);

                    bModal.show();

                    $(modalEl).one('hidden.bs.modal', () => {
                        bModal.dispose();
                        $container.empty();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');
                    });

                } catch (err) {
                    console.error("Modal.show: Error de Bootstrap Modal", err);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error(`Modal.show: Error en ${finalUrl}`, {
                    status: jqXHR.status,
                    textStatus,
                    errorThrown
                });

                const msg = "Error al cargar el formulario.";
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', msg, 'error');
                } else {
                    alert(msg);
                }
            }
        });
    }
};