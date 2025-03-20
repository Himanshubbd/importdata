<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function process_login() {
        // Hardcoded users array
        $valid_users = [
            'tarun.k@studyin-uk.com' => '$2y$10$XrBosyaydKywUrxvr5kOPe6rEQtT1ijLHEqgOdt3wP1YxQ9fAyES6',
            'himanshu.v@studyin-uk.com' => '$2y$10$zrOUQybSBvOEXU6gZfaZ7.u3UCXqBIf66eZOrFHAAWIGXqueoUXx2',
        ];

        // Get credentials from query parameters
        $username = strtolower($this->input->get('username'));
        $password = $this->input->get('password');
        // Validate credentials
        if (array_key_exists($username, $valid_users) && password_verify($password, $valid_users[$username])) {
            // Set session data
            $this->session->set_userdata('logged_in', TRUE);
            $this->session->set_userdata('username', $username);
            echo "<script>alert('Login successful! Welcome, $username');</script>";
            echo "<script>window.location.href = '" . base_url() . "';</script>";
        } else {
            echo "<script>alert('Invalid credentials. Please try again.');</script>";
            echo "<script>window.location.href = '" . base_url('auth/login') . "';</script>";
        }
    }

    public function login() {
        // JavaScript prompt for username and password
        echo "<script>
            var username = prompt('Enter Username:');
            var password = prompt('Enter Password:');
            if (username && password) {
                window.location.href = '".base_url('auth/process_login')."?username=' + username + '&password=' + password;
            } else {
                alert('Login is required to access this page!');
                window.location.href = '".base_url('auth/login')."';
            }
        </script>";
        exit;
    }

    public function logout() {
        // Destroy session
        $this->session->sess_destroy();
        echo "<script>alert('You have been logged out.');</script>";
        echo "<script>window.location.href = '" . base_url() . "';</script>";
    }
}