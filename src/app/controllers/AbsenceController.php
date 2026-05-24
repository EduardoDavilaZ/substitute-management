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
    }
