<?php

final class EventController extends Controller 
{
    public function eventFormModal(int $id = 0) : array
    {
        $this->view = 'admin/modals/event_form_modal';
        $this->layout = null;

        $eventModel = new Event();
        $eventData = ($id > 0) ? $eventModel->getEventWithClassesAndTeachers($id) : [];
        
        return [
            'selectedId'    => $id,
            'event'         => $eventData,
            'classes'       => (new Classes())->getClasses(),
            'periods'       => (new Period())->getPeriods(),
            'teachers'      => (new Teacher())->getTeachers(), // Para rellenar los selectores
        ];
    }

    public function addEvent() : void 
    {
        $this->handleForm();
    }

    public function updateEvent() : void 
    {
        $id = (int)($_POST['id'] ?? 0);
        $this->handleForm($id);
    }

    private function handleForm(int $id = 0) : never  
    {
        $eventData = [
            'title'       => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'start_date'  => $_POST['start_date'] ?? '',
            'end_date'    => $_POST['end_date'] ?? ''
        ];

        $classIds  = $_POST['class_ids'] ?? [];
        $periodIds = $_POST['period_ids'] ?? [];
        $teacherIds = $_POST['teacher_ids'] ?? []; // Capturamos los profesores asistentes

        if (empty($eventData['title']) || empty($eventData['start_date']) || empty($eventData['end_date'])) {
            json_error("Faltan datos obligatorios.");
        }

        $model = new Event();
        if ($model->saveEvent($eventData, $classIds, $periodIds, $teacherIds, $id)) {
            json_success($id > 0 ? "Evento actualizado con éxito." : "Evento creado con éxito.");
        } else {
            json_error("No se pudo guardar el evento.");
        }
    }

    public function deleteEvent() : never 
    {
        $id = $_POST['id'] ?? 0;

        if ($id <= 0) {
            json_error("ID de evento no válido.");
        }

        $model = new Event();
        $result = $model->deleteEvent($id);

        if ($result) {
            json_success("El evento ha sido eliminado correctamente.");
        } else {
            json_error("No se pudo eliminar el evento. Es posible que no exista.");
        }
    }
}   
