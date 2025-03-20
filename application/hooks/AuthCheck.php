<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthCheck {
    public function checkLogin() {
        $CI =& get_instance();
        $CI->load->library('session');

        // Exclude specific routes from the check
        $excluded_routes = ['auth/login', 'auth/process_login','webhook/index','attendance','attendance/index','bulk-attendance','bulkattendance/index','update-level-tag','updateleveltag/index','updateleveltag/updateCourseTag','process-course-tag-data'];
        $current_route = strtolower($CI->router->class . '/' . $CI->router->method);

        // Check if the user is not logged in
        if (!in_array($current_route, $excluded_routes) && !$CI->session->userdata('logged_in')) {
            // Redirect to login URL
            redirect(base_url('auth/login'));
            exit;
        }
    }
}