<?php
require_once __DIR__ .'/../services/EmailService.php';
use App\Services\EmailService as EmailService;
    final class SubstitutionController extends Controller {
        public function getSubstitutions()
        {
            return json((new Substitution())->getSubstitutionsCalendar());
        }
        public function deleteSubstitution()
        {
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
        public function getSubstitutionAssig(int $id) {
            $this->view = 'admin/modals/assign_substitute';
            $this->layout = null;
            $substitutionData = (new Substitution())->getSubstitution($id);
            $substitution = !empty($substitutionData) ? $substitutionData[0] : [];

            if (empty($substitution)) {
                return ['substitution' => [], 'teachersDay' => [], 'teachersFree' => []];
            }

            $teachersDay = (new Schedule())->getIdAndDay($id);
            $teachersFree = (new Schedule())->getTeacherFree($substitution['absent_teacher_id'],$substitution['date']);

                return [
                'substitution' => $substitution,
                'teachersDay' => $teachersDay,
                'teachersFree' => $teachersFree
                ];
        }
        public function assingSubstitute()
        {
            $model = new Substitution();
            $result = $model->assign();

            if ($result) {

                $mensajeSuccess = "Sustitución asignada correctamente";

                try {
                    $data = $this->getDataEmail();
                    $arrayTeacher = [
                        'email' => $data['teacher']['email'],
                        'name'  => $data['teacher']['full_name']
                    ];

                    EmailService::sendMail($arrayTeacher, $data['affair'], $data['body']);
                    
                } catch (\Exception $e) {
                    echo $e->getMessage(); die();
                    $mensajeSuccess = "Sustitución asignada en el sistema, pero no se pudo enviar el correo de notificación.";          
                }

                return json_success($mensajeSuccess);
            } else {
                return json_error("Error al asignar la sustitución en la base de datos.");
            }
        }
        public function getDataEmail(): array
        {
            $modelTeacher = new Teacher();
            $modelSubti = new Absence();

            $substitutionId = $_POST['idSubstitution'] ?? 0;
            $teacherToday = $_POST['teacherToday'] ?? '';
            $teacherFree = $_POST['teacherFree'] ?? '';

            $selectedTeacher = $teacherToday ?: $teacherFree;

            $teacher = $modelTeacher->getTeacher($selectedTeacher);
            $substitutionData = $modelSubti->getAbsencesDetailsById($substitutionId);
            $substitution = !empty($substitutionData) ? $substitutionData[0] : [];
            $dateFormat = (new DateTime($substitution['date_absence']))->format('d-m-Y');
            $affair = "Sustitución asignada";

            $body = "<div style='background-color: #f4f6f9; padding: 30px; font-family: Arial, sans-serif; color: #333333; line-height: 1.6;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 5px solid #1a73e8;'>
                
                <div style='padding: 25px 30px; background-color: #ffffff; border-bottom: 1px solid #eeeeee;'>
                    <table style='width: 100%; border-collapse: collapse; border: 0;'>
                        <tr>
                            <td style='vertical-align: middle; text-align: left;'>
                                <h2 style='margin: 0; color: #1a73e8; font-size: 20px; font-weight: 600;'>Aviso de Guardia</h2>
                            </td>
                            <td style='vertical-align: middle; text-align: right; width: 140px;'>
                                <img src='cid:logo_centro' alt='Logo del Centro' style='max-width: 70px; height: auto; display: inline-block; margin: 0;'>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style='padding: 30px;'>
                    <p style='margin-top: 0; font-size: 16px;'>Estimad@ docente <strong>{$teacher['full_name']}</strong>:</p>
                    
                    <p style='font-size: 15px; color: #555555;'>
                        Le comunicamos que se le ha asignado una guardia con los siguientes detalles:
                    </p>

                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f8f9fa; border-radius: 6px; overflow: hidden;'>
                        <tr>
                            <td style='padding: 12px 15px; border-bottom: 1px solid #e9ecef; font-weight: bold; color: #495057; width: 30%;'>Fecha:</td>
                            <td style='padding: 12px 15px; border-bottom: 1px solid #e9ecef; color: #212529;'>{$dateFormat}</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 15px; border-bottom: 1px solid #e9ecef; font-weight: bold; color: #495057;'>Hora:</td>
                            <td style='padding: 12px 15px; border-bottom: 1px solid #e9ecef; color: #212529;'>{$substitution['name_hour']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 15px; font-weight: bold; color: #495057;'>Clase/Aula:</td>
                            <td style='padding: 12px 15px; color: #212529;'>{$substitution['class']}</td>
                        </tr>
                    </table>

                    <p style='font-size: 15px; color: #555555; margin-bottom: 0;'>
                        Que tenga un buen día y buena guardia.
                    </p>
                </div>

                <div style='background-color: #f8f9fa; padding: 15px 30px; text-align: center; font-size: 12px; color: #777777; border-top: 1px solid #eeeeee;'>
                    Este es un mensaje automático, por favor no responda a este correo.
                </div>

            </div>
        </div>";
            return [
                'teacher' => $teacher,
                'affair'  => $affair,
                'body'    => $body
            ];
        }
    }
?>
