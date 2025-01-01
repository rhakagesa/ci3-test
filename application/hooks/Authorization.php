<?php

class Authorization {

    public function __construct() {
        $this->CI = &get_instance();
    }

    public function authorize() {
        $routes = [
            'users' => ['POST', 'DELETE']
        ];


        $currentRoute = $this->CI->uri->segment(1);
        $currentMethod = strtoupper($this->CI->input->method());
    
        
        $isMatchRoute = $currentRoute === array_key_first($routes);
        $isMatchMethod = in_array($currentMethod, $routes['users']);
    
        if ($isMatchRoute && $isMatchMethod) {
            if ($this->CI->session->has_userdata('role') === FALSE) {
                return $this->CI->output
                    ->set_content_type('application/json')
                    ->set_status_header(401)
                    ->set_output(
                        json_encode(['success' => FALSE, 'error' => ['message' => 'You must login first']])
                    );
            }
    
            if ($this->CI->session->userdata('role') !== 'admin') {
                return $this->CI->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(
                        json_encode(['success' => FALSE, 'error' => ['message' => 'You are not authorized to access this route']])
                    );
            }
        }
    }    
}