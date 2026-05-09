<?php
    final class SubstitutionController extends Controller {
        public function getSubstitutions()  {
            return json((new Substitution())->getSubstitutionsCalendar());
        }
    }
?>