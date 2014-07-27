<?php
	
//This function checks for the presence of the required fields
function checkPresence($req_params, $post_params){
	$result['overall_status'] = 'ok';
	foreach ($req_params as $param){
		if (empty ($post_params[$param])) {$result['overall_status'] = 'error'; $result[$param] = 'fail';}else{$result[$param] = 'pass';}
	}
	return $result;
}

//This function calculates the academic sessions that are available for applying
function calculateSessions(){
	$dayOfYear = date('z');
  $thisYear = date('Y');
  
  $sessions = array(0 =>"", 1 =>"", 2=>"", 3 =>"");
  if ($dayOfYear >= 351)
    {
    $sessions[0] = ($thisYear +1)." - April Admission";
    $sessions[1] = ($thisYear +1)." - July Admission";
	$sessions[2] = ($thisYear +1)." - October Admission";
	$sessions[3] = ($thisYear +2)." - January Admission";
    }
  else if ($dayOfYear >= 259)
	{
	$sessions[0] = ($thisYear +1)." - January Admission";
	$sessions[1] = ($thisYear +1)." - April Admission";
    $sessions[2] = ($thisYear +1)." - July Admission";
    $sessions[3] = ($thisYear +1)." - October Admission";
    }
  else if ($dayOfYear >= 167)
    {
    $sessions[0] = $thisYear." - October Admission";
	$sessions[1] = ($thisYear +1)." - January Admission";
    $sessions[2] = ($thisYear +1)." - April Admission";
    $sessions[3] = ($thisYear +1)." - July Admission";
    }
  else if ($dayOfYear >= 76)
    {
    $sessions[0] = $thisYear." - July Admission";
    $sessions[1] = $thisYear." - October Admission";
    $sessions[2] = ($thisYear +1)." - January Admission";
    $sessions[3] = ($thisYear +1)." - April Admission";
	}
  else if ($dayOfYear < 76)
    {
    $sessions[0] = $thisYear." - April Admission";
    $sessions[1] = $thisYear." - July Admission";
    $sessions[2] = $thisYear." - October Admission";
    $sessions[3] = ($thisYear +1)." - January Admission";
	}
  
  return $sessions;
}

//This function makes the API call
function makeAPICall($api_method, $params, $url)
{
	
}

?>