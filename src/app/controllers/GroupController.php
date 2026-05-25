<?php

final class GroupController extends Controller
{
    public function getClassesEnabled(): never
    {
        json([
            'data' => (new Classes())->getClassesEnabled()
        ]);
    }

    public function groupFormModal(int $id = 0): array
    {
        $this->layout = null;
        $this->view = 'admin/modals/group_form_modal';

        $classData = [];
        if ($id > 0) {
            $classData = (new Classes())->getClass($id);
            if (empty($classData) || (int) ($classData['enabled'] ?? 0) === 0) {
                $classData = [];
                $id = 0;
            }
        }

        return [
            'selectedId' => $id,
            'classData'  => $classData,
            'stages'     => Classes::validStages(),
        ];
    }

    public function addGroup(): void
    {
        $this->handleForm();
    }

    public function updateGroup(): void
    {
        $id = (int) input('id', 0);
        $this->handleForm($id);
    }

    public function deleteGroupById(): void
    {
        $id = (int) input('class_id', 0);

        if ($id <= 0) {
            json_error('ID de grupo no válido.');
        }

        $model = new Classes();
        $class = $model->getClass($id);

        if (empty($class)) {
            json_error('Grupo no encontrado.');
        }

        if ((int) ($class['enabled'] ?? 0) === 0) {
            json_error('El grupo ya está dado de baja.');
        }

        if ($model->deleteClass($id)) {
            json_success('Grupo dado de baja correctamente.');
        }

        json_error('Error al dar de baja el grupo.');
    }

    private function handleForm(int $id = 0): never
    {
        $code  = trim((string) input('code', ''));
        $name  = trim((string) input('name', ''));
        $stage = trim((string) input('stage', ''));

        if ($code === '' || $name === '' || $stage === '') {
            json_error('Código, nombre y etapa son obligatorios.');
        }

        if ($error = validate_length($code, 1, 10, 'El código')) {
            json_error($error);
        }

        if ($error = validate_regex($code, '/^[A-Za-z0-9\-]+$/', 'El código solo puede contener letras, números y guiones.')) {
            json_error($error);
        }

        if ($error = validate_length($name, 2, 50, 'El nombre')) {
            json_error($error);
        }

        if (!Classes::isValidStage($stage)) {
            json_error('La etapa seleccionada no es válida.');
        }

        $model = new Classes();
        $code = strtoupper($code);

        if ($model->codeExists($code, $id)) {
            json_error('Ya existe un grupo con ese código.');
        }

        if ($id > 0) {
            $existing = $model->getClass($id);

            if (empty($existing)) {
                json_error('Grupo no encontrado.');
            }

            if ((int) ($existing['enabled'] ?? 0) === 0) {
                json_error('No se puede modificar un grupo dado de baja.');
            }

            if ($model->updateClass($id, $code, $name, $stage)) {
                json_success('Grupo actualizado correctamente.');
            }

            json_error('Error al actualizar el grupo.');
        }

        $newId = $model->getNextId();

        if ($newId > 255) {
            json_error('No se pueden crear más grupos (límite de ID alcanzado).');
        }

        if ($model->createClass($newId, $code, $name, $stage)) {
            json_success('Grupo creado correctamente.');
        }

        json_error('Error al crear el grupo.');
    }
}
