<?php
    final class SubstitutionController extends Controller {
        public function getSubstitutions()  {
            return json((new Substitution())->getSubstitutionsCalendar());
        }
        public function deleteSubstitution(){
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
    }
?>