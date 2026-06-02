document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-upload-file').forEach(btn => {
        btn.addEventListener('click', () => {
            const absenceId = btn.dataset.absenceId;
            const fileInput = document.querySelector(
                `.input-upload-file[data-absence-id="${absenceId}"]`
            );
            if (fileInput) {
                fileInput.click();
            }
        });
    });

    document.querySelectorAll('.input-upload-file').forEach(input => {
        input.addEventListener('change', async () => {
            if (!input.files.length) return;

            const absenceId = input.dataset.absenceId;
            const btn = document.querySelector(
                `.btn-upload-file[data-absence-id="${absenceId}"]`
            );
            const url = btn.dataset.url;

            const formData = new FormData();
            formData.append('absence_id', absenceId);
            formData.append('justification', input.files[0]);

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (!response.ok || result.status !== 'success') {
                    throw new Error(result.message || 'Error al subir el justificante.');
                }

                location.reload();
            } catch (error) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-upload"></i>';
                input.value = '';

                let message = 'Error al subir el justificante.';
                if (error.message) {
                    message = error.message;
                }

                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
                alertDiv.innerHTML = `
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                const panel = document.querySelector('.teacher-panel');
                const existingAlert = panel.querySelector('.alert-danger');
                if (existingAlert) {
                    existingAlert.remove();
                }
                panel.insertBefore(alertDiv, panel.querySelector('.table-responsive'));

                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        });
    });
});
