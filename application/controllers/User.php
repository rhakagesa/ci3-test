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
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(
                array(
                    'success' => FALSE,
                    'error' => array(
                        'message' => validation_errors()
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
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(array(
                    'success' => FALSE,
                    'error' => array(
                        'message' => 'Server error, while adding user'
                    )
                    ));
            }

            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(array(
                'success' => TRUE,
                'data' => array(
                    'message' => 'User added successfully'
                )
            ));
        }
    
    }

    public function getAllUsers(){
        $result = $this->user->getAllUsers();

        if($result == FALSE){
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(array(
                'success' => FALSE,
                'error' => array(
                    'message' => 'Users not found'
                )
            ));
        } else {
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(array(
                'success' => TRUE,
                'data' => $result
            ));
        }
    }

    public function getUser(){
        $id = $this->uri->segment(3);
        
        $result = $this->user->getUserById($id);

        if($result == FALSE){
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(array(
                'success' => FALSE,
                'error' => array(
                    'message' => 'User not found'
                )
            ));
        } else {
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(array(
                'success' => TRUE,
                'data' => $result
            ));
        }

    }

    public function updateUser(){
        $id = $this->uri->segment(3);

        $this->form_validation->set_rules($this->validationUpdateUser);

        if($this->form_validation->run() == FALSE || $id == NULL){
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(
                array(
                    'success' => FALSE,
                    'error' => array(
                        'message' => $id == NULL? 'User id is required' : validation_errors()
                )));
        } else {
            $data = array(
                'name' => $this->input->post('name'),
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'role' => $this->input->post('role')
            );

            $result = $this->user->updateUser($id, $data);

            if($result == FALSE){
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(array(
                    'success' => FALSE,
                    'error' => array(
                        'message' => 'Server error, while updating user'
                    )
                    ));
            }

            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(array(
                'success' => TRUE,
                'data' => array(
                    'message' => 'User updated successfully'
                )
            ));
        }
    
    }

    public function deleteUser(){
        $id = $this->uri->segment(3);

        if($id == NULL){
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(
                array(
                    'success' => FALSE,
                    'error' => array(
                        'message' => 'User id is required'
                )));
        } else {
            $result = $this->user->deleteUser($id);

            if($result == FALSE){
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(array(
                    'success' => FALSE,
                    'error' => array(
                        'message' => 'Server error, while deleting user'
                    )
                    ));
            }

            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(array(
                'success' => TRUE,
                'data' => array(
                    'message' => 'User deleted successfully'
                )
            ));
        }
    }
}