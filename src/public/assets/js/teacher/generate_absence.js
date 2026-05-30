document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('absence-form');
    const submitButton = document.getElementById('submit-absence');
    const dateInput = document.getElementById('date');
    const descriptionInput = document.getElementById('description');
    const checkboxes = document.querySelectorAll('.check-hour');
    const selectedPeriodsCount = document.getElementById('selected-periods-count');
    const attachedFilesCount = document.getElementById('attached-files-count');
    const summaryDate = document.getElementById('summary-date');
    const submitButtonContent = submitButton ? submitButton.innerHTML : '';

    const showMessage = (title, text, icon = 'info') => {
        if (typeof swal === 'function') {
            swal(title, text, icon);
            return;
        }

        alert(text || title);
    };

    const setSubmitting = (isSubmitting) => {
        if (!submitButton) return;

        submitButton.disabled = isSubmitting;
        submitButton.innerHTML = isSubmitting
            ? '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Enviando...'
            : submitButtonContent;
    };

    dateInput.addEventListener('change', (e) => {
        if (!summaryDate) return;
        if (e.target.value) {
            const d = new Date(e.target.value + 'T00:00:00');
            summaryDate.textContent = d.toLocaleDateString('es-ES', {
                day: 'numeric', month: 'long', year: 'numeric'
            });
        } else {
            summaryDate.textContent = 'Sin seleccionar';
        }
    });

    const updateSummary = () => {
        if (selectedPeriodsCount) {
            selectedPeriodsCount.textContent = document.querySelectorAll('.check-hour:checked').length;
        }

        if (attachedFilesCount) {
            let totalFiles = 0;

            document.querySelectorAll('.material-input, #justification').forEach(input => {
                totalFiles += input.files ? input.files.length : 0;
            });

            attachedFilesCount.textContent = totalFiles;
        }
    };

    checkboxes.forEach(checkbox => {

        checkbox.addEventListener('change', () => {

            const row = checkbox.closest('.period-row');
            const upload = row.querySelector('.period-row-upload');

            row.classList.toggle('is-selected', checkbox.checked);
            upload.classList.toggle('d-none', !checkbox.checked);

            if (!checkbox.checked) {
                const input = upload.querySelector('.material-input');
                const filesContainer = upload.querySelector('.uploaded-files');
                input.value = '';
                filesContainer.innerHTML = '';
            }

            updateSummary();
        });
    });

    document.querySelectorAll('.material-input').forEach(input => {

        input.addEventListener('change', () => {

            const container = input.closest('.period-row-upload')
                .querySelector('.uploaded-files');

            container.innerHTML = '';

            [...input.files].forEach(file => {

                const badge = document.createElement('div');

                badge.className = 'file-badge';
                badge.innerHTML = `
                    <i class="bi bi-paperclip me-1"></i>
                    ${file.name}
                `;

                container.appendChild(badge);
            });

            updateSummary();
        });
    });

    const justification = document.getElementById('justification');
    const removeJustification = document.getElementById('remove-justification');
    const justificationLabel = document.getElementById('justification-label');

    justification.addEventListener('change', () => {

        const hasFile = justification.files.length > 0;

        justificationLabel.textContent = hasFile
            ? justification.files[0].name
            : 'Subir PDF justificante';

        removeJustification.classList.toggle('d-none', !hasFile);
        updateSummary();
    });

    removeJustification.addEventListener('click', () => {

        justification.value = '';
        justificationLabel.textContent = 'Subir PDF justificante';
        removeJustification.classList.add('d-none');
        updateSummary();
    });

    form.addEventListener('submit', async event => {
        event.preventDefault();

        if (!dateInput.value) {
            showMessage('Fecha requerida', 'Selecciona la fecha de la ausencia.', 'warning');
            return;
        }

        if (!descriptionInput.value.trim()) {
            showMessage('Motivo requerido', 'Indica el motivo de la ausencia.', 'warning');
            return;
        }

        if (document.querySelectorAll('.check-hour:checked').length === 0) {
            showMessage('Horas requeridas', 'Selecciona al menos una hora afectada.', 'warning');
            return;
        }

        setSubmitting(true);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'No se pudo registrar la ausencia.');
            }

            if (typeof swal === 'function') {
                const confirmation = swal('Ausencia enviada', result.message, 'success');

                if (confirmation && typeof confirmation.then === 'function') {
                    confirmation.then(() => {
                        window.location.href = result.data?.redirect || `${BASE_URL}teacher/absences`;
                    });
                    return;
                }

                setTimeout(() => {
                    window.location.href = result.data?.redirect || `${BASE_URL}teacher/absences`;
                }, 800);
                return;
            }

            window.location.href = result.data?.redirect || `${BASE_URL}teacher/absences`;
        } catch (error) {
            showMessage('Error', error.message || 'No se pudo registrar la ausencia.', 'error');
            setSubmitting(false);
        }
    });

    updateSummary();
});
