<?php

function modal_start(array $config) : void 
{
    $id = $config['id'] ?? 'mainModal';
    $title = $config['title'] ?? 'Información';
    $size = $config['size'] ?? ''; // modal-sm, modal-lg, modal-xl

    echo "
        <div class='modal fade' id='{$id}' tabindex='-1' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered {$size}'>
                <div class='modal-content'>
                    <div class='modal-header border-0'>
                        <h5 class='modal-title text-subtitle'>{$title}</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>";
}

function modal_end(array $buttons = []) : void 
{
    echo "      
                    </div>
                        <div class='modal-footer border-0'>
                            <button type='button' class='btn btn-cancel' data-bs-dismiss='modal'>Cancelar</button>";
    
                    foreach ($buttons as $btn) {
                        $type  = $btn['type'] ?? 'button';
                        $class = $btn['class'] ?? 'btn-save';
                        $attr  = $btn['attr'] ?? '';
                        $text  = $btn['text'] ?? 'Aceptar';
                        echo "<button type='{$type}' class='btn {$class}' {$attr}>{$text}</button>";
                    }

    echo "      </div>
            </div>
        </div>
    </div>";
}

?>