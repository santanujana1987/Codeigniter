<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('dateToDb')){
  function dateToDb($date,$delimiter='/'){
    list($day, $month, $year) = explode($delimiter, $date);
    return $year."-".$month."-".$day;
  }
}

if(!function_exists('pr')){
  function pr($date,$is_die='0'){
    echo "<pre>";print_r($data);echo "</pre>"; 
    if($is_die){
      die;
    }
  }
}

if(!function_exists('now')){
  function now($type=''){   
    if($type=='day_only'){
       return date('Y-m-d');
    }
    else if($type=='time_only'){
      return date('H:i:s');
    }
    else{
      return date('Y-m-d H:i:s');
    }
    
  }
}

if(!function_exists('auth_check')){
  function auth_check($user_type){
    $CI =& get_instance();
    $logged_row = $CI->session->userdata('Auth.'.$user_type);
    return (is_array($logged_row)?true:false);    
  }
}


if(!function_exists('db_last_query')){
  function db_last_query($date,$is_die='0'){
    $CI =& get_instance();
    echo "<pre>";echo $CI->db->last_query();echo "</pre>"; 
    if($is_die){
      die;
    }
  }
}

if(!function_exists('db_column_set_empty')){
  function db_column_set_empty($table=''){
    if($table!=''){
        $reset_data = array();
        $CI =& get_instance();
        $columns = $CI->db->list_fields($table);
        foreach ($columns as $key => $value) {
          $reset_data[$value] = '';       
        }
        return $reset_data;
    }
  }
}

if(!function_exists('db_column_filter')){
  function db_column_filter($table='',$data=array()){
    $filter_data = array();
    if($table!=''){
      $CI =& get_instance();
      $columns = $CI->db->list_fields($table);
      foreach ($columns as $key => $value) {
        if(isset($data[$value])){
          $filter_data[$value] = $data[$value];
        }
        
      }
    }
    return $filter_data;
  }
}

if(!function_exists('make_insert_query')){
  function make_insert_query($table,$data){
    $CI =& get_instance();
    return $CI->db->insert_string($table, $data);   
  }
}

if(!function_exists('make_update_query')){
  function make_update_query($table,$data,$conditions){
    $CI =& get_instance();
    return $CI->db->update_string($table, $data,$conditions);   
  }
}




// by SJ end

