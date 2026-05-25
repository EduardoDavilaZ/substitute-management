<?php
    final class AbsenceController extends Controller
    {

        public function getAbsencesDetail()
        {
            return json(['data' => (new Absence())->getAbsencesDetails()]);
        }
        public function getDataView(int $id)
        {
            $this->view = 'admin/modals/detail_substitution_modal';
            $results = (new Absence())->getAbsencesDetailsById($id);
            return ['data' => $results[0] ?? []];
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
