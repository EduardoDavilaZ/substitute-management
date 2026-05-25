document.addEventListener('DOMContentLoaded', () => {

    const checkboxes = document.querySelectorAll('.check-hour');

    checkboxes.forEach(checkbox => {

        checkbox.addEventListener('change', () => {

            const card = checkbox.closest('.hour-card');
            const upload = card.querySelector('.material-upload');

            upload.classList.toggle('d-none', !checkbox.checked);

            if (!checkbox.checked) {

                const input = upload.querySelector('.material-input');
                const filesContainer = upload.querySelector('.uploaded-files');

                input.value = '';
                filesContainer.innerHTML = '';
            }
        });
    });

    document.querySelectorAll('.material-input').forEach(input => {

        input.addEventListener('change', () => {

            const container = input.closest('.material-upload')
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
        });
    });

    const justification = document.getElementById('justification');

    justification.addEventListener('change', () => {

        const title = document.querySelector('.upload-general .upload-title');

        if (justification.files.length > 0) {

            title.textContent = justification.files[0].name;

        } else {

            title.textContent = 'Haz clic para subir un justificante';
        }
    });

    document
        .getElementById('remove-justification')
        .addEventListener('click', () => {

            justification.value = '';

            document.querySelector(
                '.upload-general .upload-title'
            ).textContent = 'Haz clic para subir un justificante';
        });
});