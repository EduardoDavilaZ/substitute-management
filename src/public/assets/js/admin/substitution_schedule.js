document.addEventListener('DOMContentLoaded', () => {
    const periods = ['1ª hora (8:15-9:10)', '2ª hora (9:10-10:05)', '3ª hora (10:05-11:00)', 'Recreo (11:00-11:30)', '4ª hora (11:30-12:25)', '5ª hora (12:25-13:20)', '6ª hora (13:20-14:15)'];
    const days = ['L', 'M', 'X', 'J', 'V'];
    const assignments = [
        { d: 'L', p: 0, n: 'Pedro López', c: 6, cl: 'blue' },
        { d: 'L', p: 0, n: 'Ana Martínez', c: 15, cl: 'emerald' },
        { d: 'M', p: 1, n: 'Juan Rivas', c: 8, cl: 'blue' },
        { d: 'X', p: 0, n: 'Marta Sánchez', c: 12, cl: 'blue' },
        { d: 'X', p: 0, n: 'Roberto Cano', c: 9, cl: 'emerald' },
        { d: 'V', p: 4, n: 'Beatriz Peña', c: 5, cl: 'blue' }
    ];

    const body = document.getElementById('schedule-body');
    if (!body) return;

    body.innerHTML = periods.map((h, p) => `
        <tr class="${h.includes('Recreo') ? 'bg-light-subtle' : ''}">
            <td class="p-3 hora-col ${h.includes('Recreo') ? 'fw-bold' : ''}">${h}</td>
            ${days.map(d => {
                const assigned = assignments.filter(a => a.d === d && a.p === p);
                return `<td class="p-2" style="min-width: 200px;"><div class="d-flex flex-column gap-1">
                    ${[0, 1].map(i => assigned[i] ? `
                        <div class="teacher-slot slot-${assigned[i].cl}">
                            <i class="bi bi-person-fill me-1"></i>
                            <span class="text-truncate" style="max-width: 90px;">${assigned[i].n}</span>
                            <span class="badge-count bg-${assigned[i].cl}-badge">${assigned[i].c}</span>
                            <div class="ms-1 d-flex gap-1">
                                <button class="btn-slot-action" data-bs-toggle="modal" data-bs-target="#addTeacherModal"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn-slot-action text-danger"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>` : `
                        <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addTeacherModal"><i class="bi bi-plus"></i> Añadir P${i + 1}</button>`
                    ).join('')}
                </div></td>`;
            }).join('')}
        </tr>`).join('');
});
