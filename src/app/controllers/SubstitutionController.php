<?php
    final class SubstitutionController extends Controller {
        public function getSubstitutions()  {
            return json((new Substitution())->getSubstitutionsCalendar());
        }
        public function deleteSubstitution(){
            $id = $_POST["substitution_id"] ?? 0;
            if($id <= 0)
            {
                return json_error("ID de evento no vàlido.");
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
            $subtitution = (new Substitution())->getSubstitution($id);
            $idHourScheduleTeachers = (new Schedule())->getIdAndDay($subtitution['schedule_id']);
            $teachersDay =(new Schedule())->getTeachersHour($idHourScheduleTeachers[0]['id'],$idHourScheduleTeachers[0]['day']);
            ////Hay que traer los profesores libres
           
                return [
                'substitution' => $subtitution,
                'teachersDay' => $teachersDay,
                ];
        }
    }
?>