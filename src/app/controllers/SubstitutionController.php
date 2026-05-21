<?php
    final class SubstitutionController extends Controller {
        public function getSubstitutions()
        {
            return json((new Substitution())->getSubstitutionsCalendar());
        }
        public function deleteSubstitution()
        {
            $id = $_POST["substitution_id"] ?? 0;
            if($id <= 0)
            {
                return json_error("ID de la sustitución no vàlido.");
            }
            $model = new Substitution();
            $result = $model->deleteSubstitutions($id);
            if($result)
            {
                return json_success("Sustitucion eliminada con exito");
            } else {
                return json_error("Error al eliminar la sustitución");
            }
        }
        public function getSubstitutionAssig(int $id) {
            $this->view = 'admin/modals/assign_substitute';
            $this->layout = null;
            $substitutionData = (new Substitution())->getSubstitution($id);
            $substitution = !empty($substitutionData) ? $substitutionData[0] : [];

            if (empty($substitution)) {
                return ['substitution' => [], 'teachersDay' => [], 'teachersFree' => []];
            }

            $teachersDay = (new Schedule())->getIdAndDay($id);
            $teachersFree = (new Schedule())->getTeacherFree($substitution['absent_teacher_id'],$substitution['date']);

                return [
                'substitution' => $substitution,
                'teachersDay' => $teachersDay,
                'teachersFree' => $teachersFree
                ];
        }
        public function assingSubstitute()
        {
            $model = new Substitution();
            $result = $model->assign();
            if($result) {
                return json_success("Sustitución asignada correctamente");
            } else {
                return json_error("Error al asignar la sustitución");
            }
        }
    }
?>