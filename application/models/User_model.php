<?php

class User_model extends CI_Model {
    public function addUser($data){
        $result = $this->db->insert('user', $data);

        return $result ? TRUE : FALSE;
    }
    
    public function getUserById($id){
        $result = $this->db->select('name, username, role')->where('id', $id)->get('user');
        
        if($result->num_rows() == 0){
            return FALSE;
        }

        return $result->row();

    }

    public function getAllUsers(){
        $result = $this->db->select('name, username, role')->get('user');

        if($result->num_rows() == 0){
            return FALSE;
        }

        return $result->result_array();
    }

    public function updateUser($id, $data){
        $result = $this->db->where('id', $id)->update('user', $data);

        return $result ? TRUE : FALSE;
    }

    public function deleteUser($id){
        $result = $this->db->where('id', $id)->delete('user');

        return $result ? TRUE : FALSE;
    }

}