<?php
function manager_employment_application() {

//must check that the user has the required capability 
    if (!current_user_can('edit_posts'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	?>
	<h2>Employment Application Manager</h2>
	<?php 
	
	if( !class_exists( 'WP_Http' ) )
	{
		include_once( ABSPATH . WPINC. '/class-http.php' );
	}

	$validation = '';
	$status = '';
	$message = '';
	$back_url = '';

	if ($_POST['submitted'] == "true"){
	$em_app = $_POST['em_app'];
	if ($em_app['app_type'] == 'By AppId'){
	$req_params = array('app_id');
	$presence_validation = checkPresence($req_params, $em_app);
	} else if ($em_app['app_type'] == 'By Email'){
	$req_params = array('email');
	$presence_validation = checkPresence($req_params, $em_app);
	} else if ($em_app['app_type'] == 'Print'){
	$presence_validation['overall_status'] = 'Ok';
	} else {
	$presence_validation['overall_status'] = 'error';
	}

	if ($presence_validation['overall_status'] == 'error'){
		$validation = 'fail';
	}
	else
	{
		$request = new WP_Http;
		$appType = $em_app['app_type'];
		$url = '';
		if ($appType == 'By AppId')
		{
			$appCode = $em_app['app_id'];
			$url = "http://api-b-unique.bubba-online.com/getEmployeeApplication?mode=byId&app_code=".$appCode;
			$result = $request->request( $url);
		} 
		else if ($appType == 'By Email')
		{
			$email = $em_app['email'];
			$url = "http://api-b-unique.bubba-online.com/getEmployeeApplication?mode=byEmail&email=".$email;
			$result = $request->request( $url);
		}
		else if ($appType == 'Print')
		{
			$back_url = $_POST['back_url'];
			$status = 'success';
			session_start();
			$_SESSION['em_app'] = $_POST['em_app'];
			echo '<script>
			window.open("http://bubba-online.com/print-employment-app")
			</script>';
		}
		
		// test $result['response'] and if OK do something with $result['body']
			
			if ( $url != ''&& !is_wp_error($result) )
			{
				if ($result['response']['message'] == 'OK')
				{
					$body = json_decode($result['body'], true);
					if ($body['status'] == 0)
					{
						$response = $body['response'];
						$status = 'success';
						if($appType == 'update') 
						{
							$message = 'Application was successfully updated';
						} 
						else
						{
							$message = count($response).' result(s) found';
						}
					}
					else if ($body['status'] == 220)
					{
						$status = 'success';
						$message = 'No results Found';
					}
					else
					{
						$status = 'error';
						$message = 'Error occurred. Please try again';
					}
				}
				else
				{ 
					$status = 'error';
					$message = 'Error. Please try again';
				}
			} else if ($appType != 'Print'){ $status = 'error'; $message = 'Error occurred. Please try again';}

	}

}
?>

<form class="" action="" method="post">
	<?php if ($validation == 'fail'){ echo '<div class="error">Please Fill in the Required fields</div>';}
	else if ($status == 'error') { echo '<div class="error">'. $message .'</div>';}?>
    
	<label>Search By Application ID</label><br/>
	<div id="byAppId">
	<label class="label">Application ID<span class="red"> *</span></label>
	<input class="<?php echo $presence_validation['app_id'] == 'fail'? 'invalid-input' :''; ?>" type="text" size="18" name="em_app[app_id]" value="<?php echo $em_app['app_id']; ?>" size="25" maxlength="45">
	<input type="submit" name="em_app[app_type]" value="By AppId">
    </div><br/>
	
	<label>Search By Email</label><br/>
	<div id="byEmail">
	<label class="label">Email<span class="red"> *</span></label>
	<input class="<?php echo $presence_validation['email'] == 'fail'? 'invalid-input' :'';?>" type="email" name="em_app[email]" value="<?php echo $em_app['email']; ?>" size="25" maxlength="45">
    <input type="submit" name="em_app[app_type]" value="By Email">
	</div>
	<input type="hidden" name="submitted" value="true"> 
	
</form>
<br/>
<?php if ($status == 'success' && $appType != 'View' && $appType != 'Update') { echo '<div class="success">'. $message .'</div>';?> 
<table><tr><th>Application ID</th><th>First Name</th><th>Last Name</th><th>Applied For</th><th>Offered Employment</th><th></th></tr>
<?php foreach($response as $resp) {?>
<form class="" action="" method="post">
<tr>
<td><input type="text" size="20" readonly name="em_app[app_code]" value="<?php echo $resp['application_code'];?>"></td>
<td><input type="text" readonly name="em_app[first_name]" value="<?php echo $resp['first_name'];?>"></td>
<td><input type="text" readonly name="em_app[last_name]" value="<?php echo $resp['last_name'];?>"></td>
<td><input type="text" readonly name="em_app[applying_for]" value="<?php echo $resp['applying_for'];?>"></td>
<td><input type="text" readonly name="em_app[accepted]" value="<?php echo $resp['accepted'];?>"></td>
<td><input type="hidden" name="em_app[dob]" value="<?php echo $resp['dob'];?>">
<input type="hidden" name="em_app[gender]" value="<?php echo $resp['gender'];?>">
<input type="hidden" name="em_app[marital_status]" value="<?php echo $resp['marital_status'];?>">
<input type="hidden" name="em_app[ssn]" value="<?php echo $resp['ssn'];?>">
<input type="hidden" name="em_app[email]" value="<?php echo $resp['email'];?>">
<input type="hidden" name="em_app[home_phone]" value="<?php echo $resp['home_phone'];?>">
<input type="hidden" name="em_app[mobile_phone]" value="<?php echo $resp['mobile_phone'];?>">
<input type="hidden" name="em_app[other_phone]" value="<?php echo $resp['other_phone'];?>">
<input type="hidden" name="em_app[address]" value="<?php echo $resp['address'];?>">
<input type="hidden" name="em_app[city]" value="<?php echo $resp['city'];?>">
<input type="hidden" name="em_app[state]" value="<?php echo $resp['state'];?>">
<input type="hidden" name="em_app[zipcode]" value="<?php echo $resp['zipcode'];?>">
<input type="hidden" name="em_app[education]" value="<?php echo $resp['education'];?>">
<input type="hidden" name="em_app[dl_number]" value="<?php echo $resp['dl_number'];?>">
<input type="hidden" name="em_app[dl_issue]" value="<?php echo $resp['dl_issue'];?>">
<input type="hidden" name="em_app[student_type]" value="<?php echo $resp['employment_type'];?>">
<input type="hidden" name="em_app[period]" value="<?php echo $resp['period'];?>">
<input type="hidden" name="em_app[employer_name]" value="<?php echo $resp['employer_name'];?>">
<input type="hidden" name="em_app[employer_phone]" value="<?php echo $resp['employer_phone'];?>">
<input type="hidden" name="em_app[employer_address]" value="<?php echo $resp['employer_address'];?>">
<input type="hidden" name="em_app[employer_city]" value="<?php echo $resp['employer_city'];?>">
<input type="hidden" name="em_app[employer_state]" value="<?php echo $resp['employer_state'];?>">
<input type="hidden" name="em_app[employer_zipcode]" value="<?php echo $resp['employer_zipcode'];?>">
<input type="hidden" name="em_app[emergency_name]" value="<?php echo $resp['emergency_name'];?>">
<input type="hidden" name="em_app[emergency_relationship]" value="<?php echo $resp['emergency_relationship'];?>">
<input type="hidden" name="em_app[emergency_mobile_number]" value="<?php echo $resp['emergency_mobile_number'];?>">
<input type="hidden" name="em_app[emergency_home_number]" value="<?php echo $resp['emergency_home_number'];?>">
<input type="hidden" name="em_app[emergency_address]" value="<?php echo $resp['emergency_address'];?>">
<input type="hidden" name="em_app[emergency_city]" value="<?php echo $resp['emergency_city'];?>">
<input type="hidden" name="em_app[emergency_state]" value="<?php echo $resp['emergency_state'];?>">
<input type="hidden" name="em_app[emergency_zipcode]" value="<?php echo $resp['emergency_zipcode'];?>">
<input type="hidden" name="em_app[physical_disability]" value="<?php echo $resp['physical_disability'];?>">
<input type="hidden" name="em_app[medication]" value="<?php echo $resp['medication'];?>">
<input type="hidden" name="em_app[summary]" value="<?php echo $resp['summary'];?>">
<input type="hidden" name="em_app[remarks]" value="<?php echo $resp['remarks'];?>">
<input type="hidden" name="em_app[paid_on]" value="<?php echo $resp['paid_on'];?>">
<input type="hidden" name="em_app[submitted_on]" value="<?php echo $resp['submitted_on'];?>"></td>
<td><input type="hidden" name="em_app[old_app_type]" value="<?php echo $appType;?>">
<input type="hidden" name="submitted" value="true">
<input type="hidden" name="back_url" value="<?php echo $url;?>">
<!--<input type="submit" name="em_app[app_type]" value="View">-->
<input type="submit" name="em_app[app_type]" value="Print"></td>
</tr>
</form>
<?php }?>
</table>
<?php } ?>

<?php }

?>