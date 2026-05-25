<?php
    final class AbsenceController extends Controller
    {

        public function getAbsencesDetail()
        {
            return json(['data' => (new Absence())->getAbsencesDetail()]);
        }

        public function getAbsencesHistory()
        {
            return json(['data' => (new Absence())->getAbsencesHistory()]);
        }

        public function getAbsenceById(int $id = 0) : array
        {
            $this->view   = 'admin/modals/absence_detail_modal';
            $this->layout = null;

            return ['absence' => (new Absence())->getAbsenceById($id)];
        }
    }
?>