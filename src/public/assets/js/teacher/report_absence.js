// estos datos vendran de la base de datos
const hours = [
    { id: '1h', label: '1ª hora (8:15-9:10)' },
    { id: '2h', label: '2ª hora (9:10-10:05)' },
    { id: '3h', label: '3ª hora (10:05-11:00)' },
    { id: 'recreo', label: 'Recreo (11:00-11:30)' },
    { id: '4h', label: '4ª hora (11:30-12:25)' },
    { id: '5h', label: '5ª hora (12:25-13:20)' },
    { id: '6h', label: '6ª hora (13:20-14:15)' }
];

const container = document.getElementById('hours-container');

hours.forEach(hour => {
    const block = document.createElement('div');
    block.className = 'bloque-hora';
    block.innerHTML = `
        <input type="checkbox" class="check-hora" name="hours[]" value="${hour.id}" id="check-${hour.id}">
        <label for="check-${hour.id}">${hour.label}</label>
        <div class="contenedor-archivo" style="display: none;">
            <input type="file" class="input-real" name="file_${hour.id}" id="file_${hour.id}" hidden />
            <div class="upload-box" onclick="document.getElementById('file_${hour.id}').click()">
                <span class="icon">↑</span>
                <p class="main-text">Haz clic para subir un material</p>
                <p class="sub-text">PDF</p>
            </div>
        </div>
    `;
    container.appendChild(block);
});
const buttonRemove = document.querySelector('#remove-justification');
buttonRemove.addEventListener('click', ()=>{
    justification = document.querySelector('.custom-upload #justification');
    justification.value = "";
    const labelText = document.querySelector('.custom-upload .main-text');
    if (labelText) {
        labelText.innerText = "Haz clic para subir un justificante";
    }
});
document.querySelectorAll('.check-hora').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const contenedor = this.closest('.bloque-hora').querySelector('.contenedor-archivo');
        contenedor.style.display = this.checked ? 'block' : 'none';
        if(!this.checked){
            const inputFile = contenedor.querySelector('input[type="file"]');
            inputFile.value = "";
            const labelText = contenedor.querySelector('.main-text');
            if (labelText) {
                labelText.innerText = "Haz clic para subir un material";
            }
        }
    });
});

document.querySelectorAll('.input-real').forEach(input => {
    input.addEventListener('change', function() {
        const box = this.parentElement.querySelector('.upload-box');
        const texto = this.files[0] ? "Archivo: " + this.files[0].name : "Haz clic para subir un material";
        box.querySelector('.main-text').innerText = texto;
    });
});

document.getElementById('justification').addEventListener('change', function() {
    const box = this.closest('.custom-upload').querySelector('.upload-box');
    const texto = this.files[0] ? "Archivo: " + this.files[0].name : "Haz clic para subir un justificante";
    box.querySelector('.main-text').innerText = texto;
});