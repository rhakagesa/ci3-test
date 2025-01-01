<?php

class User extends CI_Controller {
    private $validationAddUser = array(
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required|max_length[50]',
        ),
        array(
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'required|max_length[50]|is_unique[user.username]',
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required|min_length[8]',
        ),
        array(
            'field' => 'role',
            'label' => 'Role',
            'rules' => 'required|in_list[admin,user]',
        )
    );

    private $validationUpdateUser = array(
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'max_length[50]',
        ),
        array(
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'max_length[50]',
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'min_length[8]',
        ),
        array(
            'field' => 'role',
            'label' => 'Role',
            'rules' => 'in_list[admin,user]',
        )
    );


    public function __construct(){
        parent::__construct();

        $this->load->model('User_model', 'user');
    }

    public function addUser(){
    
       $this->form_validation->set_rules($this->validationAddUser);

       if($this->form_validation->run() == FALSE){

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(
                    json_encode(
                        array(
                            'success' => FALSE,
                            'error' => array('message' => validation_errors())
                        )));
           
        } else {

            $data = array(
                'name' => $this->input->post('name'),
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'role' => $this->input->post('role')
            );

            $result = $this->user->addUser($data);

            if($result == FALSE){

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(
                        json_encode(
                            array(
                                'success' => FALSE,
                                'error' => array('message' => 'Server error, while adding user')
                            )));
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(
                    json_encode(
                        array(
                            'success' => TRUE,
                            'data' => array('message' => 'User added successfully')
                            )));
        }
    
    }

    public function getAllUsers(){

        $result = $this->user->getAllUsers();

        if($result == FALSE){

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(
                    json_encode(
                        array(
                            'success' => FALSE,
                            'error' => array('message' => 'Users not found')
                        )));
        } 
        
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(
                json_encode(
                    array(
                        'success' => TRUE,
                        'data' => $result
                    )));
        
    }

    public function getUser($id){

        $result = $this->user->getUserById($id);

        if($result == FALSE){

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(
                    json_encode(array(
                        'success' => FALSE,
                        'error' => array('message' => 'User not found')
                    )));
           
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(
                json_encode(array(
                    'success' => TRUE,
                    'data' => $result
                )));
        

    }

    public function updateUser($id){
      
        $this->form_validation->set_rules($this->validationUpdateUser);

        if($this->form_validation->run() == FALSE || $id == NULL){

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(
                    json_encode(
                        array(
                            'success' => FALSE,
                            'error' => array('message' => $id == NULL? 'User id is required' : validation_errors())
                        )));
        } else {

            $data = array(
                'name' => $this->input->post('name'),
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'role' => $this->input->post('role')
            );

            $this->getUser($id);
            $result = $this->user->updateUser($id, $data);

            if($result == FALSE){
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(
                        json_encode(array(
                            'success' => FALSE,
                            'error' => array('message' => 'Server error, while updating user')
                            )));
               
            } 
                
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(
                    json_encode(array(
                            'success' => TRUE,
                            'data' => array('message' => 'User updated successfully')
                    )));
        }
    }
    
    

    public function deleteUser($id){
       
        if($id == NULL){

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(
                    json_encode(array(
                        'success' => FALSE,
                        'error' => array('message' => 'User id is required')
                    )));

        } else {

            $existsUser = $this->user->getUserById($id);
            $result = $this->user->deleteUser($id);

            if($result == FALSE || $existsUser == FALSE){
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header($result == FALSE ? 500 : 404)
                    ->set_output(
                        json_encode(array(
                            'success' => FALSE,
                            'error' => array('message' => $result == FALSE ? 'Server error, while deleting user' : 'User not found')
                        )));
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(
                    json_encode(array(
                        'success' => TRUE,
                        'data' => array('message' => 'User deleted successfully')
                    )));
        }
    }
}
