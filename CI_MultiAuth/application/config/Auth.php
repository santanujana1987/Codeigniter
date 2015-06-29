<?php
$config['Auth']['Logins']['Employee'] = array('table'=>'employee','primary_key'=>'id','password_field'=>'pass','filter'=>array('status'=>1));
$config['Auth']['Logins']['Admin'] = array('table'=>'admin','primary_key'=>'id','password_field'=>'admin_pass','filter'=>array('status'=>1));
$config['Auth']['EncryptionKey'] = 'geotech09';