<?php

final class EventController extends Controller 
{
    public function eventFormModal(int $id = 0) : array
    {
        $this->view = 'admin/modals/event_form_modal';
        $this->layout = null;

        $eventModel = new Event();
        $eventData = ($id > 0) ? $eventModel->getEventWithClasses($id) : [];
        return [
            'selectedId'    => $id, // Cambiamos el nombre para evitar colisiones con la variable $id
            'event'         => $eventData,
            'classes'       => (new Classes())->getClasses(),
            'periods'       => (new Period())->getPeriods(),
        ];
    }

    // public function addEvent() : void 
    // {
    //     $fields = validate_fields(['title', 'description', 'start_date', 'end_date']);
    //     if (!$fields) {
    //         json_error("Faltan campos obligatorios.");
    //     }
        
    //     $result = (new Event())->createEvent($fields, $scheduleIds);
        
    //     if ($result) {
    //         json_success("Evento creado correctamente.");
    //     } else {
    //         json_error("No se pudo crear el evento.");
    //     }
    // }
}   

?>