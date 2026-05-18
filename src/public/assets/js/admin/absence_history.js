document.addEventListener('DOMContentLoaded', function () {

    const table = document.getElementById('absences-table');
    const searchInput = document.getElementById('filter-search');
    const justifiedSelect = document.getElementById('filter-justified');
    const pageSizeSelect  = document.getElementById('page-size');
    const infoText = document.getElementById('table-info');

    if (!table) return;

    let currentPage = 1;

    function getAllRows() {
        return Array.from(table.tBodies[0].rows);
    }

    function normalizeText(text) {
        return text
            .toLowerCase()
            .normalize('NFD')
            .replace(/[̀-ͯ]/g, '');
    }

    function rowMatchesFilters(row) {
        const search    = normalizeText(searchInput.value);
        const justified = justifiedSelect.value;

        if (search && !normalizeText(row.innerText).includes(search)) {
            return false;
        }

        if (justified !== '') {
            const justifiedCell = row.cells[4].innerText.trim().toLowerCase();
            const isJustified   = justifiedCell === 'sí';

            if (justified === '1' && !isJustified) return false;
            if (justified === '0' &&  isJustified) return false;
        }

        return true;
    }

    function render() {
        const allRows = getAllRows();
        const pageSize = parseInt(pageSizeSelect.value);
        const matchedRows = allRows.filter(rowMatchesFilters);

        allRows.forEach(function (row) {
            row.style.display = 'none';
        });

        matchedRows.forEach(function (row, index) {
            const start = (currentPage - 1) * pageSize;
            const end = currentPage * pageSize;
            const visible = pageSize === -1 || (index >= start && index < end);

            if (visible) {
                row.style.display = '';
            }
        });

        renderInfo(matchedRows.length, allRows.length, pageSize);
    }

    function renderInfo(matched, total, pageSize) {
        let from = 0;
        let to   = 0;

        if (matched > 0) {
            from = pageSize === -1 ? 1 : (currentPage - 1) * pageSize + 1;
            to   = pageSize === -1 ? matched : Math.min(currentPage * pageSize, matched);
        }

        infoText.textContent = 'Mostrando ' + from + ' – ' + to + ' de ' + matched + ' registros (' + total + ' en total)';
    }

    function resetAndRender() {
        currentPage = 1;
        render();
    }

    searchInput.addEventListener('input', resetAndRender);
    justifiedSelect.addEventListener('change', resetAndRender);
    pageSizeSelect.addEventListener('change', resetAndRender);

    render();
});
