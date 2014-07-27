<?php
require_once('../../../wp-config.php');

$data="";
if(isset($_POST["data"]))
	$data= stripslashes($_POST["data"]);



$data=json_decode($data,true);
foreach($data as $key=>$value)
{
	$_POST[$key]=$value;
}
require_once "smart-forms-ajax.php";
rednao_smart_forms_save_form_values();
