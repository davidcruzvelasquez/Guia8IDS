<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EstudiantesController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');
        if (!isset($this->session->userdata['logged_in'])) { 
            redirect("/");
        }
    }

    // FUNCIONES QUE CARGAN VISTAS /////////////////////////////////////////////////////////
    public function index()
    {
        $this->load->model('EstudiantesModel');
        $data = array(
            "records" => $this->EstudiantesModel->getAll(),
            "title" => "Estudiantes",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("estudiantes/index", $data);
        $this->load->view("shared/footer");
    }

    public function insertar()
    {
        $this->load->model('CarrerasModel');
        $data = array(
            "carreras" => $this->CarrerasModel->getAll(),
            "title" => "Insertar estudiante",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("estudiantes/add_edit", $data);
        $this->load->view("shared/footer");
    }

    public function modificar($id)
    {
        $this->load->model('CarrerasModel');
        $this->load->model('EstudiantesModel');
        $estudiante = $this->EstudiantesModel->getById($id);
        $data = array(
            "carreras" => $this->CarrerasModel->getAll(),
            "estudiante" => $estudiante,
            "title" => "Modificar estudiante",
        );
        $this->load->view("shared/header", $data);
        $this->load->view("estudiantes/add_edit", $data);
        $this->load->view("shared/footer");
    }
    // FIN - FUNCIONES QUE CARGAN VISTAS /////////////////////////////////////////////////////////

    // FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////
    public function add()
    {

        // Reglas de validaci??n del formulario
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como m??ximo una cantidad determinada de caracteres.
        valid_email: indica que el valor debe ser un correo con formato v??lido.
         */
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules("idestudiante", "Id Estudiante", "required|min_length[12]|max_length[12]|is_unique[estudiantes.idestudiante]");
        $this->form_validation->set_rules("nombre", "Nombre", "required|min_length[3]|max_length[100]");
        $this->form_validation->set_rules("apellido", "Apellido", "required|min_length[3]|max_length[100]");
        $this->form_validation->set_rules("email", "Email", "required|valid_email|max_length[100]|is_unique[estudiantes.email]");
        $this->form_validation->set_rules("usuario", "Usuario", "required|min_length[3]|max_length[100]|is_unique[estudiantes.usuario]");
        $this->form_validation->set_rules("idcarrera", "Carrera", "required|max_length[3]");

        // Modificando el mensaje de validaci??n para los errores
        $this->form_validation->set_message('required', 'El campo %s es requerido.');
        $this->form_validation->set_message('min_length', 'El campo %s debe tener al menos %s caracteres.');
        $this->form_validation->set_message('max_length', 'El campo %s debe tener como m??ximo %s caracteres.');
        $this->form_validation->set_message('valid_email', 'El campo %s no es un correo v??lido.');
        $this->form_validation->set_message('is_unique', 'El campo %s ya existe.');

        // Par??metros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validaci??n de los campos
        if ($this->form_validation->run()) {
            // Si la validaci??n es correcta entra ac??
            try {
                $this->load->model('EstudiantesModel');
                $data = array(
                    "idestudiante" => $this->input->post("idestudiante"),
                    "nombre" => $this->input->post("nombre"),
                    "apellido" => $this->input->post("apellido"),
                    "email" => $this->input->post("email"),
                    "usuario" => $this->input->post("usuario"),
                    "idcarrera" => $this->input->post("idcarrera"),
                );
                $rows = $this->EstudiantesModel->insert($data);
                if ($rows > 0) {
                    $msg = "Informaci??n guardada correctamente.";
                } else {
                    $statusCode = 500;
                    $msg = "No se pudo guardar la informaci??n.";
                }
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurri?? un error." . $ex->getMessage();
            }
        } else {
            // Si la validaci??n da error, entonces se ejecuta ac??
            $statusCode = 400;
            $msg = "Ocurrieron errores de validaci??n.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        // Se asigna el mensaje que llevar?? la respuesta
        $this->data['msg'] = $msg;
        // Se asigna el c??digo de Estado HTTP
        $this->output->set_status_header($statusCode);
        // Se env??a la respuesta en formato JSON
        echo json_encode($this->data);

    }

    public function update()
    {

        // Reglas de validaci??n del formulario
        $this->form_validation->set_error_delimiters('', '');
        /*
        required: indica que el campo es obligatorio.
        min_length: indica que la cadena debe tener al menos una cantidad determinada de caracteres.
        max_length: indica que la cadena debe tener como m??ximo una cantidad determinada de caracteres.
        valid_email: indica que el valor debe ser un correo con formato v??lido.
         */
        $this->form_validation->set_rules("idestudiante", "Id Estudiante", "required|min_length[12]|max_length[12]");
        $this->form_validation->set_rules("nombre", "Nombre", "required|min_length[3]|max_length[100]");
        $this->form_validation->set_rules("apellido", "Apellido", "required|min_length[3]|max_length[100]");
        $this->form_validation->set_rules("email", "Email", "required|valid_email|max_length[100]");
        $this->form_validation->set_rules("usuario", "Usuario", "required|min_length[3]|max_length[100]");
        $this->form_validation->set_rules("idcarrera", "Carrera", "required|max_length[3]");

        // Modificando el mensaje de validaci??n para los errores, en este caso para
        // la regla required, min_length, max_length
        $this->form_validation->set_message('required', 'El campo %s es requerido.');
        $this->form_validation->set_message('min_length', 'El campo %s debe tener al menos %s caracteres.');
        $this->form_validation->set_message('max_length', 'El campo %s debe tener como m??ximo %s caracteres.');
        $this->form_validation->set_message('is_unique', 'El campo %s ya existe.');

        // Par??metros de respuesta
        header('Content-type: application/json');
        $statusCode = 200;
        $msg = "";

        // Se ejecuta la validaci??n de los campos
        if ($this->form_validation->run()) {
            // Si la validaci??n es correcta entra
            try {
                $this->load->model('EstudiantesModel');
                $data = array(
                    "idestudiante" => $this->input->post("idestudiante"),
                    "nombre" => $this->input->post("nombre"),
                    "apellido" => $this->input->post("apellido"),
                    "email" => $this->input->post("email"),
                    "usuario" => $this->input->post("usuario"),
                    "idcarrera" => $this->input->post("idcarrera"),
                );
                $rows = $this->EstudiantesModel->update($data, $this->input->post("PK_estudiante"));
                $msg = "Informaci??n guardada correctamente.";
            } catch (Exception $ex) {
                $statusCode = 500;
                $msg = "Ocurri?? un error." . $ex->getMessage();
            }
        } else {
            // Si la validaci??n da error, entonces se ejecuta ac??
            $statusCode = 400;
            $msg = "Ocurrieron errores de validaci??n.";
            $errors = array();
            foreach ($this->input->post() as $key => $value) {
                $errors[$key] = form_error($key);
            }
            $this->data['errors'] = $errors;
        }
        // Se asigna el mensaje que llevar?? la respuesta
        $this->data['msg'] = $msg;
        // Se asigna el c??digo de Estado HTTP
        $this->output->set_status_header($statusCode);
        // Se env??a la respuesta en formato JSON
        echo json_encode($this->data);
    }

    public function eliminar($id)
    {
        $this->load->model('EstudiantesModel');
        $result = $this->EstudiantesModel->delete($id);
        if ($result) {
            $this->session->set_flashdata('success', "Registro borrado correctamente.");
        } else {
            $this->session->set_flashdata('error', "No se pudo borrar el registro.");
        }
        redirect("EstudiantesController");
    }
    /*================================================================================*/
    /*                  FUNCION PARA GENERAR REPORTES                                 */
    /*================================================================================*/
    public function report_todos_los_estudiantes()
    {
        // Se carga la libreria para generar tablas
        $this->load->library("table");
        //Carga la librer??a Report que acabamos de crear
        $this->load->library('Report');
    
        $pdf = new Report(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->titulo = "Listado de Estudiantes";
        // Informaci??n del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Oscar Rivera');
        $pdf->SetTitle('Listado de Estudiantes');
        $pdf->SetSubject('Reporte generado usando Codeigniter y TCPDF');
        $pdf->SetKeywords('TCPDF, PDF, MySQL, Codeigniter');
    
        // Fuente de encabezado y pie de p??gina
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    
        // Fuente por defecto Monospaced
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
        // Margenes
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(15);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
        // Quiebre de p??gina autom??tico
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    
        // Factor de escala de imagen
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
        // Fuente del contenido
        $pdf->SetFont('Helvetica', '', 10);
    
        // ================================================
    
        // Generar la tabla y su informaci??n
        $template = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1">',
            'heading_cell_start' => '<th style="font-weight: bold; color:white; background-color: #13CDF7">',
        );
    
        $this->table->set_template($template);
    
        $this->table->set_heading('Id Estudiante', 'Nombre', 'Apellido', 'Email', 'Usuario', 'Carrera');
    
        // Cargando la data
        $this->load->model('EstudiantesModel');
        $this->load->model('CarrerasModel');
        // Asignando la data
        $estudiantes = $this->EstudiantesModel->getAll();
        $carreras = $this->CarrerasModel->getAll();
    
        // Iterando sobre la data
        foreach ($estudiantes as $estudiante):
            foreach ($carreras as $carrera):
                if($estudiante->idcarrera == $carrera->idcarrera){
                    $this->table->add_row($estudiante->idestudiante, $estudiante->nombre, $estudiante->apellido, $estudiante->email, $estudiante->usuario, $carrera->carrera);
                }
            endforeach;
        endforeach;
    
       // Generar la informaci??n de la tabla
        $html = $this->table->generate();
      
    
        // A??adir p??gina
        $pdf->AddPage();
    
        // Contenido de salida en HTML
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Reiniciar puntero a la ??ltima p??gina
        $pdf->lastPage();
    
        // Cerrar y mostrar el reporte
        $pdf->Output(md5(time()) . '.pdf', 'I');
    }
    /*==============================================================*/
    /*              FIN DE FUNCTION GENERAR REPORTE                 */
    /*==============================================================*/

    // FIN - FUNCIONES QUE REALIZAN OPERACIONES /////////////////////////////////////////////////////////

}
