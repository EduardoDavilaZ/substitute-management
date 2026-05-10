<?php

final class DashboardController extends Controller {
    public function obtainDashboardData() {

        return json([
            'active_teachers' => (new Teacher())->countActiveTeachers(),
            'today_shifts' => (new Substitution())->countTodaySubstitutions(date('Y-m-d')),
            'active_classes' => (new Classes())->countActiveClasses(),
            'today_absences' => (new Absence())->countAbsencesByDate(date('Y-m-d')),
            'pending_guards' => (new Substitution())->getPendingGuards(date('Y-m-d')),
            'teacher_absences' => (new Absence())->getTeacherAbsencesToday(date('Y-m-d')),
            'weekly_substitutions' => (new Substitution())->getSubstitutionsWeek(date('Y-m-d'))
        ]);
    }
}