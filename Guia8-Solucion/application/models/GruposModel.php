<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GruposModel extends CI_Model
{
    public function construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        /** Consulta personalizada para extraer todos los grupos
         * con sus respectivo nombre de materia y profesor
         */

        $query = $this->db->query("select g.idgrupo, g.num_grupo, g.anio, g.ciclo, 
        m.idmateria, m.materia, p.idprofesor, p.nombre, p.apellido 
        from grupos as g 
        inner join materias as m on m.idmateria = g.idmateria
        inner join profesores as p on p.idprofesor = g.idprofesor");
        $records = $query->result();
        return $records;
    }

    // Funcion que permite obtener el grupo completo por ID

    public function getGrupoCompletoById($id) {

        //  Consulta personalizada para extraer la información del grupo
        $query = $this->db->query("select g.idgrupo, g.num_grupo, g.anio, g.ciclo, 
        m.materia, p.nombre, p.apellido 
        from grupos as g 
        inner join materias as m on m.idmateria = g.idmateria
        inner join profesores as p on p.idprofesor = g.idprofesor
        where g.idgrupo=". $id);
        // Se utiliza el metodo row para obtener solo un registro
        $record = $query->row(); 
        return $record;
    }

    // Funcion que permite obtener los estudiantes por ID del grupo
    public function getEstudiantesByIdGrupo($id){

        // Consulta personalizada para obtener los estudiantes del grupo con sus datos.
        $query = $this->db->query("select ge.idgrupo, ge.idestudiante, concat(e.nombre,' ',e.apellido) as nombrecompleto 
        from grupo_estudiantes as ge 
        inner join estudiantes as e on e.idestudiante = ge.idestudiante
        where ge.idgrupo=". $id);
        $records = $query->result();
        return $records;
    }

    public function getEstudiantesByIdGrupo2($id){

        // Consulta personalizada para obtener los estudiantes del grupo con sus datos.
        $query = $this->db->query("select ge.idgrupo, g.num_grupo, ge.idestudiante, concat(e.nombre,' ',e.apellido) as nombrecompleto 
        from grupo_estudiantes as ge 
        inner join estudiantes as e on e.idestudiante = ge.idestudiante
        inner join grupos as g on g.idgrupo = ge.idgrupo
        where ge.idgrupo=". $id);
        $records = $query->result();
        return $records;
    }

    // Funcion que permite administrar el grupo
    public function adminGrupo($data){

        try{
           // Se inicia la transaccion
           $this->db->trans_start();
           $this->db->delete("grupo_estudiantes", "idgrupo='" .$data["idgrupo"] ."'");
           // Se recorre cada uno de los registros para insertarlos
           if (isset($data["grupo_estudiantes"])){
               foreach($data["grupo_estudiantes"] as $item){
                   $this->db->insert('grupo_estudiantes', $item);
               }
           }     
           // Se completa la transaccion
           $this->db->trans_complete();
           // Se verifica si la transaccion devolvió o arrojó un estado false
           // si es false indica que ocurrió un error
           if ($this->db->trans_status() === false) {
               return false;
           }
           return true;
        }catch(Exception $ex){
            return false;
        }
    }

    //Funcion que me permite traer todos los alumnos incritos en un grupo
    public function todos_los_inscritos($data){

        // Consulta personalizada (raw) para obtener todos los incritos en el grupo solicitado
        $query = $this->db->query("select a.idgrupo, concat(c.nombre,' ',c.apellido) as 'Alumno', a.num_grupo, a.ciclo, a.anio,
        concat(d.nombre,' ',d.apellido) as 'ProfesorGrupo' 
        from grupos a, grupo_estudiantes b, estudiantes c, profesores d 
        where b.idestudiante = c.idestudiante and a.idgrupo = b.idgrupo and d.idprofesor = a.idprofesor and a.idgrupo=" .$data);
        $records = $query->result();
        return $records;
    }

    // Funcion que permite mostrar todos los grupos
    public function todos_los_grupos(){

        // Consulta que permite obtener todos los grupos
        $query = $this->db->query("select a.idgrupo, a.num_grupo, a.anio, a.ciclo from grupos a");
        $records = $query->result();
        return $records;
    }

    //Funcion que permite agregar elementos a la tabla grupos
    public function insert($data){
        $this->db->insert("grupos",$data);
        $rows = $this->db->affected_rows();
        return $rows;
    }
    //Funcion que permite eliminar grupos
    public function delete($id){
        if ($this->db->delete("grupos","idgrupo='" . $id ."'")){
            return true;
        }
    }
    ///Funcion que permite consultar grupos por ID
    public function getById($id){
        return $this->db->get_where("grupos",array("idgrupo" => $id))->row();
    }

    ///Funcio que permite modificar.
    public function update($data,$id){
        try{
            $this->db->where("idgrupo", $id);
            $this->db->update("grupos", $data);
            $rows = $this->db->affected_rows();
            return $rows;
        }catch(Exception $ex){
            return -1;
        }
    }
}
