<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('Auth','','Auth');
	}
	public function index()
	{
		$this->load->view('welcome_message');
		print_r($this->Auth->get('Employee'));		
		print_r($this->Auth->Employee);		
	}

	public function login()
	{

		$r = $this->Auth->attempt('Employee',array('email'=>'aditya@fischer.ae','pass'=>'changepassword'));
		if($r){
			redirect('to_your_after_login_page');
		}else{
			redirect('login_page_again');
		}
	}



	public function logout()
	{
		$this->Auth->logout('Employee');

		print_r($this->Auth->Employee);
		echo $this->Auth->is_logged_in('Employee');
	}

	function registration(){
		
		$arr = $this->input->post();

		$arr['emp_name'] = "Edff";
		$arr['email'] = "Edff";
		$arr['pass'] = "123456";
		var_dump($this->Auth->save('Employee',$arr));
	}

	function update(){

		$arr['id'] = "74";
		$arr['emp_name'] = "Edffdsas";
		$arr['email'] = "Edff";
		$arr['pass'] = "123456";
		var_dump($this->Auth->save('Employee',$arr));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */