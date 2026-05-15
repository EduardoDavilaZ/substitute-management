<?php 

final class TeacherController extends Controller 
    {
        protected function init() : void
        {
            $this->layout = 'teacher/layout';
        }

        public function home() : void
        {
            $this->view = 'teacher/home';
        }
        public function getTecEnabled(){
            return json(['data' => (new Teacher())->getTeachersEnabled()]);
        }
        public function deleteTeacherById(){
            $id = $_POST["teacher_id"] ?? 0;
            if($id <= 0)
            {
                return json_error("ID de evento no vàlido.");
            }
            $model = new Teacher();
            $result = $model->deleteTeacher($id);
            if($result)
            {
                return json_success("Profesore dado de baja");
            } else {
                return json_error("Error al dar de baja al profesor");
            }
        }
    }

?>