<?php 
class Auth {
	var $config;
	var $CI;
	
	function __construct($config=array()){
		$this->CI = & get_instance();
		$this->CI->load->config('Auth');
		$this->config = $this->CI->config->item('Auth');
		foreach ($this->config['Logins'] as $key => $value) {
			$this->set($key);
		}
				
	}

	public function attempt($user_type='',$conditions=array()){
		$check_has = true;
		$full_condition = array_merge($this->config['Logins'][$user_type]['filter'],$conditions);
		$condition_without_password_field = $full_condition;
		if(isset($this->config['EncryptionKey']) && $this->config['EncryptionKey']!=''){
			if(isset($condition_without_password_field[$this->config['Logins'][$user_type]['password_field']])){
				unset($condition_without_password_field[$this->config['Logins'][$user_type]['password_field']]);
			}
		}
		
		$row = $this->CI->db->where($condition_without_password_field)->get($this->config['Logins'][$user_type]['table'])->result_array();
		if(isset($this->config['EncryptionKey']) && $this->config['EncryptionKey']!=''){
			$check_has = $this->check_has($conditions[$this->config['Logins'][$user_type]['password_field']],$row[0][$this->config['Logins'][$user_type]['password_field']]);
		}
		if(count($row) && $check_has==true){
			$this->CI->session->set_userdata('Auth.'.$user_type,$row[0]);
			$this->$user_type = $row[0];
			return true;
		}else{
			return false;
		}		
	}

	public function check($user_type){
		$logged_row = $this->CI->session->userdata('Auth.'.$user_type);
		return (is_array($logged_row)?true:false);
	}

	public function get($user_type){
		return $this->$user_type;
	}

	private function set($user_type){
		$this->$user_type = $this->CI->session->userdata('Auth.'.$user_type);
	}

	public function logout($user_type){
		$this->$user_type = false;
		return $this->CI->session->unset_userdata('Auth.'.$user_type);
	}
	public function is_logged_in($user_type){
		$logged_row = $this->CI->session->userdata('Auth.'.$user_type);
		return (is_array($logged_row)?true:false);
	}

	

	public function save($user_type,$data){
		$pkey = (isset($this->config['Logins'][$user_type]['primary_key'])?$this->config['Logins'][$user_type]['primary_key']:'id');

		$f = (object)$this->CI->db->list_fields($this->config['Logins'][$user_type]['table']);

		$this->$user_type = new stdClass();
		foreach ($f as $key => $value) {
			if(isset($data[$value])){
				$this->$user_type->$value = $data[$value];
			}			
		}
		if(count($this->$user_type)){
			if(isset($this->$user_type->$pkey) && trim($this->$user_type->$pkey)!=''){	
				$update_row = (array)$this->$user_type;

				if(isset($this->config['EncryptionKey']) && $this->config['EncryptionKey']!='' && isset($update_row[$this->config['Logins'][$user_type]['password_field']])){
					$update_row[$this->config['Logins'][$user_type]['password_field']] = $this->make_has($update_row[$this->config['Logins'][$user_type]['password_field']]);
				}
						
				$this->CI->db
					->where(array($pkey=>$this->$user_type->$pkey))
					->update($this->config['Logins'][$user_type]['table'],$update_row);
				return $this->$user_type->$pkey;
			}else{
				$insert_row = (array)$this->$user_type;

				//echo "<pre>";print_r($insert_row[$this->config['Logins'][$user_type]['password_field']]);die;
				if(isset($this->config['EncryptionKey']) && $this->config['EncryptionKey']!='' && isset($insert_row[$this->config['Logins'][$user_type]['password_field']])){
					$insert_row[$this->config['Logins'][$user_type]['password_field']] = $this->make_has($insert_row[$this->config['Logins'][$user_type]['password_field']]);
				}	
				$this->CI->db->insert($this->config['Logins'][$user_type]['table'],$insert_row);
				return $this->CI->db->insert_id();
			}
		}else{
			return false;
		}		
		
	}

	public function make_has($val){
		$this->CI->load->library('encrypt');
		if(isset($this->config['EncryptionKey'])){
			return $this->CI->encrypt->encode($val, $this->config['EncryptionKey']);
		}else{
			return $this->CI->encrypt->encode($val);
		}
	}

	public function check_has($val,$has){
		$this->CI->load->library('encrypt');
		$decode ='';
		if(isset($this->config['EncryptionKey'])){
			$decode =  $this->CI->encrypt->decode($has,$this->config['EncryptionKey']);
		}else{
			$decode = $this->CI->encrypt->decode($has);
		}

		return ($decode == $val?true:false);
	}



}