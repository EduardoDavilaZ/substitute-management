<?php
    final class AbsenceController extends Controller
    {

        public function getAbsencesDetail()
        {
            return json(['data' => (new Absence())->getAbsencesDetails()]);
        }
    }
?>