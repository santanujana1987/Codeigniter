<?php
class CommonModel extends CI_Model{

	protected $table = '';
	protected $primaryKey = "";
	function set($table,$primaryKey='id'){
		$this->table = $table;
		$this->primaryKey = $primaryKey;
	}

	function fetch($type="all",$params=array('fields'=>array('*'),'conditions'=>array(),'order'=>array(),'group'=>'','limit'=>'','hasMany'=>array(),'belongsTo'=>array())){
		$return;
		$select = array($this->table.".*");
		$this->db->from($this->table);

		if(isset($params['fields']) && count($params['fields'])){
			//$select=$params['fields'];
			foreach ($params['fields'] as $key => $select) {
				if($select=='*'){
					$fields = $this->db->list_fields($this->table);
					foreach ($fields as $key => $value) {
						$this->db->select($this->table.".".$value." as `".$this->table.".".$value."`");	
					}
					if(isset($params['bind']['belongsTo']) && count($params['bind']['belongsTo'])){
						foreach ($params['bind']['belongsTo'] as $k => $leftjoin) {
							if($this->join_array_check($leftjoin)){
								$fields = $this->db->list_fields($leftjoin['table']);
								foreach ($fields as $key => $value) {
									$this->db->select($leftjoin['table'].".".$value." as `".$leftjoin['table'].".".$value."`");	
								}
							}
							
						}
					}
				}elseif(strpos($select,'.')){
					list($tbl,$fld) = explode('.', $select);
					if($fld=='*'){
						$fields = $this->db->list_fields($tbl);
						foreach ($fields as $key => $value) {
							$this->db->select($tbl.".".$value." as `".$tbl.".".$value."`");	
						}
					}else{
						$this->db->select($tbl.".".$fld." as `".$tbl.".".$fld."`");	
					}
				}
			}
			
		}
		//$this->db->select($select);
		if(isset($params['custom_conditions']) && $params['custom_conditions']!=''){
			$this->db->where($params['custom_conditions']);
		}

		if(isset($params['conditions']) && count($params['conditions'])){
			foreach ($params['conditions'] as $method => $data) {
				if(strtolower($method)=='where' || strtolower($method)=='or_where'){
					$this->db->$method($data);					
				}
				else
				{
					foreach ($data as $col => $value) {
						$this->db->$method($col,$value);
					}
				}
			}
		}



		if(isset($params['having']) && count($params['having'])){
			$this->db->having($params['having']);	
		}
		if(isset($params['or_having']) && count($params['or_having'])){
			$this->db->or_having($params['or_having']);	
		}

		
		
		if(isset($params['group']) && $params['group']!=''){
			$this->db->group_by($params['group']);
		}

		if(isset($params['order']) && count($params['order'])){
			foreach ($params['order'] as $col => $order) {
				$this->db->order_by($col,$order);
			}
		}
		if(isset($params['bind']['belongsTo']) && count($params['bind']['belongsTo'])){
			foreach ($params['bind']['belongsTo'] as $k => $leftjoin) {
				//pr($leftjoin);
				if($this->join_array_check($leftjoin)){
					$tbl = $leftjoin['table'];
					if(isset($leftjoin['as'])){
						$tbl = $tbl .=" ".$leftjoin['as'];
					}else{
						$leftjoin['as'] = $leftjoin['table'];
					}
					$this->db->join($tbl,$leftjoin['on'],'left');	
					if(isset($leftjoin['fields']) && count($leftjoin['fields'])){
						//pr($leftjoin['fields']);
						foreach ($leftjoin['fields'] as $key => $fld) {
							if($fld=='*' || $fld == $leftjoin['as'].".*"){
								$fields = $this->db->list_fields($leftjoin['table']);
								foreach ($fields as $key => $value) {
									$this->db->select($leftjoin['as'].".".$value." as `".$leftjoin['as'].".".$value."`");	
								}
							}else{
								if(strpos($fld,'.')){
									$this->db->select($fld." as `".$fld."`");	
								}else{
									$this->db->select($leftjoin['as'].".".$fld." as `".$leftjoin['as'].".".$fld."`");		
								}
							}
						}
					}else{
						$fields = $this->db->list_fields($leftjoin['table']);
						foreach ($fields as $key => $fld) {
							$this->db->select($leftjoin['as'].".".$fld." as `".$leftjoin['as'].".".$fld."`");	
						}
					}
					
				}
				
			}
		}
		if(isset($params['pagination']) && count($params['pagination'])){
			$this->db->limit($params['pagination']['limit'],isset($params['pagination']['skip'])?$params['pagination']['skip']:0);
		}
		
		if($type=='count'){
			$return = $this->db->count_all_results();	
			$this->last_query = $this->db->last_query();
			return $return;	
		}
		if($type=='max'){
			if(isset($params['fields']) && count($params['fields'])==1){	
				$return=  $this->db->select_max($params['fields'][0])->get()->result_array()[0][$params['fields'][0]];	
				$this->last_query = $this->db->last_query();
				return $return;
			}	
		}
		if($type=='min'){
			if(isset($params['fields']) && count($params['fields'])==1){	
				$return = $this->db->select_min($params['fields'][0])->get()->result_array()[0][$params['fields'][0]];	
				$this->last_query = $this->db->last_query();
				return $return;
			}	
		}
		if($type=='sum'){
			if(isset($params['fields']) && count($params['fields'])==1){	
				$return = $this->db->select_sum($params['fields'][0])->get()->result_array()[0][$params['fields'][0]];	
				$this->last_query = $this->db->last_query();
				return $return;
			}	
		}
		if($type=='avg'){
			if(isset($params['fields']) && count($params['fields'])==1){	
				$return = $this->db->select_avg($params['fields'][0])->get()->result_array()[0][$params['fields'][0]];		
				$this->last_query = $this->db->last_query();
				return $return;
			}	
		}
		
		if($type=='first'){
			$row = $this->db->get()->first_row('array');	
			$this->last_query = $this->db->last_query();
			$new_return = array();
			foreach ($row as $col => $value) {
				if(strpos($col,'.')){
					list($tbl,$fld) = explode('.', $col);
					$new_return[$tbl][$fld] = $value;
				}
			}	
			return $new_return;
		}
		if($type=='last'){
			$row = $this->db->get()->last_row('array');	
			$this->last_query = $this->db->last_query();
			$new_return = array();
			foreach ($row as $col => $value) {
				if(strpos($col,'.')){
					list($tbl,$fld) = explode('.', $col);
					$new_return[$tbl][$fld] = $value;
				}
			}	
			return $new_return;
		}
		if($type=='next'){
			$row =  $this->db->get()->next_row('array');	
			$this->last_query = $this->db->last_query();	
			$new_return = array();
			foreach ($row as $col => $value) {
				if(strpos($col,'.')){
					list($tbl,$fld) = explode('.', $col);
					$new_return[$tbl][$fld] = $value;
				}
			}	
			return $new_return;
		}
		if($type=='previous'){
			$row = $this->db->get()->previous_row('array');
			$this->last_query = $this->db->last_query();
			$new_return = array();
			foreach ($row as $col => $value) {
				if(strpos($col,'.')){
					list($tbl,$fld) = explode('.', $col);
					$new_return[$tbl][$fld] = $value;
				}
			}	
			return $new_return;	
		}

		if($type=='list'){
			if(count($params['fields'])){
				$rows= $this->db->get()->result_array();
				//pr($rows);
				$this->last_query = $this->db->last_query();
				$list=array();
				if(isset($params['empty'])){
					$list = $params['empty'];
				}
				foreach ($rows as $key => $value) {
					$list[$value[$params['fields'][0]]]=$value[isset($params['fields'][1])?$params['fields'][1]:$params['fields'][0]];
				}

				return $return = $list;
			}			
		}

		if($type=='all' || $type=='' || $type==null){			
			$return = $this->db->get()->result_array();
		}

		$this->last_query = $this->db->last_query();
		
		$new_return = array();
		foreach ($return as $key => $row) {
			
			foreach ($row as $col => $value) {
				if(strpos($col,'.')){
					list($tbl,$fld) = explode('.', $col);
					$new_return[$key][$tbl][$fld] = $value;
				}
			}
		if(isset($params['bind']['hasMany']) && count($params['bind']['hasMany'])){
				//pr($params['bind']['hasMany']);
				foreach ($params['bind']['hasMany'] as $k => $many) {
					if($this->join_array_check($many)){
						$many_select = array();
						
						//pr($many_select);
						$hmtbl = $many['table'];
						if(isset($many['as'])){
							$hmtbl = $hmtbl .=" ".$many['as'];
						}else{
							$many['as'] = $many['table'];
						}


						if(isset($many['fields']) && count($many['fields'])){							
							//$many_select=$many['fields'];
							

							foreach ($many['fields'] as $hmfk => $hmfv) {

								if($hmfv=='*' || $hmfv == $many['as'].".*"){
									//pr($hmfv);die;
									$fields = $this->db->list_fields($many['table']);
									foreach ($fields as $fkey => $value) {
										$this->db->select($many['as'].".".$value." as `".$many['as'].".".$value."`");	
									}
								}else{
									if(strpos($hmfv,'.')){
										$this->db->select($hmfv." as `".$hmfv."`");	
									}else{
										$this->db->select($many['as'].".".$hmfv." as `".$many['as'].".".$hmfv."`");		
									}
								}
							}

						}
						

						$this->db->select($many_select);
						$this->db->where($many['on'],$row[$this->table.".".$this->primaryKey]);
						if(isset($many['conditions']) && count($many['conditions'])){
							foreach ($many['conditions'] as $mmethod => $mdata) {
								if(strtolower($mmethod)=='where' || strtolower($mmethod)=='or_where'){
									$this->db->where($mdata);					
								}
								else
								{
									$this->db->$mmethod(key($mdata),$mdata[key($mdata)]);
								}
							}
						}
						if(isset($many['belongsTo']) && count($many['belongsTo'])){

							foreach ($many['belongsTo'] as $k => $leftjoin) {

								if($this->join_array_check($leftjoin)){

									$tbl = $leftjoin['table'];
									if(isset($leftjoin['as'])){
										$tbl = $tbl .=" ".$leftjoin['as'];
									}else{
										$leftjoin['as'] = $leftjoin['table'];
									}
									$this->db->join($tbl,$leftjoin['on'],'left');	

									if(isset($leftjoin['fields']) && count($leftjoin['fields'])){
										foreach ($leftjoin['fields'] as $hmbkey => $hmbfld) {
											if($hmbfld=='*' || $hmbfld == $leftjoin['as'].".*"){
												$fields = $this->db->list_fields($leftjoin['table']);
												foreach ($fields as $fkey => $value) {
													$this->db->select($leftjoin['as'].".".$value." as `".$leftjoin['as'].".".$value."`");	
												}
											}else{
												if(strpos($hmbfld,'.')){
													$this->db->select($hmbfld." as `".$hmbfld."`");	
												}else{
													$this->db->select($leftjoin['as'].".".$hmbfld." as `".$leftjoin['as'].".".$hmbfld."`");		
												}
											}
										}
									}else{
										$fields = $this->db->list_fields($leftjoin['table']);
										foreach ($fields as $hmbkey => $hmbfld) {
											$this->db->select($leftjoin['as'].".".$hmbfld." as `".$leftjoin['as'].".".$hmbfld."`");	
										}
									}
									
								}
								
							}
						}

						$hasManyrows = $this->db->get($hmtbl)->result_array();
						foreach ($hasManyrows as $hasManyKey => $hasManyRow) {
							foreach ($hasManyRow as $hasManyCol => $hasManyValue) {
								if(strpos($hasManyCol,'.')){
									list($hmtbl,$hmfld) = explode('.', $hasManyCol);
									$new_return[$key][$many['as']][$hasManyKey][$hmtbl][$hmfld] = $hasManyValue;
								}
							}
							
						}
					}
				}
			}
		}
		//pr($new_return);
		//$this->set('','');
		return $new_return;

	}

	function save($data=array()){		
		
		if(!isset($data[$this->primaryKey]) || $data[$this->primaryKey]==0 || $data[$this->primaryKey]==null || $data[$this->primaryKey]==''){
			//insert
			$data['created_by'] = $this->session->userdata('id');
			$data['created_at'] = date('Y-m-d H:i:s');
			$filter_data = db_column_filter($this->table,$data);
			$r = $this->db->insert($this->table,$filter_data);
			$this->effected_row_id = $this->db->insert_id();
			return $r;

		}else{
			//update
			$filter_data['modified_by'] = $this->session->userdata('id');
			$filter_data['modified_at'] = date('Y-m-d H:i:s');
			$filter_data = db_column_filter($this->table,$data);
			$this->effected_row_id = $filter_data[$this->primaryKey];
			return $this->db->where(array($this->primaryKey=>$filter_data[$this->primaryKey]))->update($this->table,$filter_data);
		}
	}

	private function join_array_check($array){
		if((isset($array['table']) && trim($array['table'])!='') && (isset($array['on']) && trim($array['on'])!='') ){
			return true;
		}else{
			return false;
		}
	}

	function boolen_update($field,$id){
		return $this->db->query('UPDATE '.$this->table.' SET `'.$field.'` = IF(`'.$field.'`=1,0,1) WHERE '.$this->primaryKey.'='.$id);
	}
	
	function delete($id){
		return $this->db->where(array($this->primaryKey=>$id))->delete($this->table);
	}
	function deleteAll($conditions){
		return $this->db->where($conditions)->delete($this->table);
	}

	function exe_sql($sql){
		return $this->db->query($sql);
	}

	function insert($data){
		return $this->db->insert_batch($this->table,$data);
	}

	function update($data,$conditions){
		return $this->db->update($this->table,$data,$conditions);
	}



	function __destruct() {
       $this->set('','');
   	}

}
