<?php
require_once('../../../wp-config.php');

if(!defined('ABSPATH'))
    die('Forbidden');

if(!current_user_can('manage_options'))
    die('Forbidden');


$data=GetPostValue("exportdata");
$json=json_decode($data,true);

header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=Export.csv');

if(count($json)<=0)
    die();
$keys=array_keys($json[0]);
$firstColumn=true;
$numberOfColumns=count($keys);
$count=0;
foreach($keys as $header)
{
	$count++;
	if($numberOfColumns==$count)
		break;
    if(!$firstColumn)
        echo ",";
    $firstColumn=false;
    echo '"'.$header.'"';

}
foreach($json as $row)
{
    $firstColumn=true;
    echo "\r\n";
	$count=0;
    foreach($row as $column)
    {
		$count++;
		if($numberOfColumns==$count)
			break;
        if(!$firstColumn)
            echo ",";
        $firstColumn=false;
        echo '"'.$column.'"';
    }

}

echo "\xEF\xBB\xBF";





