<?php

class Authentication extends CI_Controller {
    private $validationLogin = array(
        array(
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'required|max_length[50]'
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        )
    );

    public function __construct(){
        parent::__construct();
        $this->load->model('User_model', 'user');
    }

    public function login(){
        
        $this->form_validation->set_rules($this->validationLogin);

        $this->session->set_userdata('login_attempts', 0);

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

        } 
            
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        $existingUser = $this->user->getUserByUsername($username);

        $loginAttempts = $this->session->set_userdata('login_attempts');

        if($loginAttempts >= 10){

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(400)
            ->set_output(
                json_encode(array(
                    'success' => FALSE,
                    'error' => array('message' => 'Too many login attempts, please try again later')
                    )));
            
        }

        if($existingUser == FALSE){
                
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(
                    json_encode(array(
                        'success' => FALSE,
                        'error' => array('message' => 'Username does not exist')
                    )));   
                    
        } 
            
        $verifyPassword = password_verify($password, $existingUser->password); 

        if($verifyPassword == FALSE){
            $this->session->set_userdata('login_attempts', $loginAttempts + 1);
                
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(
                        json_encode(array(
                        'success' => FALSE,
                        'error' => array('message' => 'Incorrect password')
                    )));
        } 
                
        $isLoggin = $this->session->has_userdata('user_id');

        if($isLoggin == TRUE){

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(
                    json_encode(array(
                        'success' => FALSE,
                        'error' => array('message' => 'You are already logged in')
                    )));

            }

        $this->session->set_userdata('login_attempts', 0);
        $this->session->set_userdata('user_id', $existingUser->id);
        $this->session->set_userdata('username', $existingUser->username);
        $this->session->set_userdata('role', $existingUser->role);

        $this->user->updateUser($existingUser->id, array('last_login' => date('Y-m-d H:i:s')));
            
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(array(
                    'success' => TRUE,
                    'data' => array(
                            'user_id' => $existingUser->id,
                            'username' => $existingUser->username,
                            'role' => $existingUser->role
                        )
                    )));

    }                   
  
    public function currentUser(){
        
        $isLoggin = $this->session->has_userdata('user_id');

        if($isLoggin == TRUE){
            
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(
                    json_encode(array(
                        'success' => TRUE,
                        'data' => array(
                            'user_id' => $this->session->userdata('user_id'),
                            'username' => $this->session->userdata('username'),
                            'role' => $this->session->userdata('role')
                        )
                        )));
        } 
        
        return $this->output
        ->set_content_type('application/json')
        ->set_status_header(400)
        ->set_output(
            json_encode(array(
                'success' => FALSE,
                'error' => array('message' => 'You are not logged in')
            )));
       
    }

    public function logout(){

        $isLoggin = $this->session->has_userdata('user_id');

        if($isLoggin == TRUE){
            
            $this->db->where('id', $this->session->userdata('user_id'))->update('user', array('last_login' => date('Y-m-d H:i:s')));

            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('username');
            $this->session->unset_userdata('role');

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(
                    json_encode(array(
                        'success' => TRUE,
                        'data' => array('message' => 'Successfully logged out')
                        )));
        } 
        
        return $this->output
        ->set_content_type('application/json')
        ->set_status_header(400)
        ->set_output(
            json_encode(array(
                'success' => FALSE,
                'error' => array('message' => 'You are not logged in')
            )));
    }
}
