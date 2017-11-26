<?php
/**
 * @property CI_Session $session
 */
class Login extends MX_Controller {

  function __construct() {
    parent::__construct();
  }
  
  function index() {
    $username = $this->input->post("username");
    $password = $this->input->post("password");
    $dt['error_message'] = "";
    if ($username <> '') {
      $query = $this->db->query("select * from user where username='{$username}' and password = SHA2('{$password}', '256') and admin = 1");
      if ($query->num_rows() > 0) {
        $user = $query->row();
         $this->session->user_uid   = $user->id;
         $this->session->user_name  = $user->username;
         $this->session->user_admin = $user->admin;
        header("Location: /admin");
        exit;
      }else {
        $dt['error_message'] = "Неверные логин/пароль";
      }
    }
    $this->load->view("login.php", $dt);
  }
  
  
}
?>