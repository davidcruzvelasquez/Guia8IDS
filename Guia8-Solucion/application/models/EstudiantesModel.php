<?php

defined('BASEPATH') or exit('No direct script access allowed');

class EstudiantesModel extends CI_Model
{

    public function construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $query = $this->db->get("estudiantes");
        $records = $query->result();
        return $records;
    }

    public function insert($data)
    {
        $this->db->insert("estudiantes", $data);
        $rows = $this->db->affected_rows();
        return $rows;
    }

    public function delete($id)
    {
        if ($this->db->delete("estudiantes", "idestudiante='" . $id . "'")) {
            return true;
        }
    }
    public function getById($id)
    {
        return $this->db->get_where("estudiantes", array("idestudiante" => $id))->row();
    }
    public function update($data, $id)
    {
        $this->db->set($data);
        $this->db->where("idestudiante", $id);
        $this->db->update("estudiantes", $data);
        $rows = $this->db->affected_rows();
        return $rows;
    }
}
