How to Install
--------------------------------------------

Step 1: Copy Application/config/Auth.php to "your_project/Application/config/" folder

Step 2: Configer "your_project/Application/config/Auth.php" 

	Suppose you have two type of user(like:Employee,Admin)
	then your configaration should like followings

	$config['Auth']['Logins']['Employee'] = array(
							'table'=>'employee',		// required
							'primary_key'=>'id',		// required
							'password_field'=>'pass',	// required
							'filter'=>array('status'=>1)	// optional
						);
	$config['Auth']['Logins']['Admin'] = array(
							'table'=>'admin',		// required
							'primary_key'=>'admin_id',	// required
							'password_field'=>'adminpass',	// required
							'filter'=>array('deleted'=>0)	// optional
						);

	$config['Auth']['EncryptionKey'] = 'test_encryption_key';



Step 4:	Copy "Application/libraries/Auth.php" to "your_project/Application/libraries/" folder

Step 5: Use in Controller "Controller/welcome.php"
	
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

	

