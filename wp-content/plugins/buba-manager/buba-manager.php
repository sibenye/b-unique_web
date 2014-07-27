<?php
/*
Plugin Name: BUBA Manager
Description: Enables the management of b-unique academy student and employment applications
Version: 1.0
Author: Silver Ibenye
Author URI: http://slybase.com
*/

$dir = plugin_dir_path( __FILE__ );

// Hook for adding admin menus
add_action('admin_menu', 'add_buba_menus');

// action function for above hook
function add_buba_menus() {

// Add a new top-level menu
    add_menu_page('BUBA Manager', 'BUBA Manager', 'edit_posts', 'buba-manager', 'manager_home' );

// Add a submenu to the custom top-level menu:
    add_submenu_page('buba-manager', 'Start Here', 'BUBA Manager', 'edit_posts', 'buba-manager', 'manager_home');

// Add second submenu to the custom top-level menu:
    add_submenu_page('buba-manager', 'Student Application', 'Student Application Manager', 'edit_posts', 'student_application_manager', 'manager_student_application');
	
	// Add third submenu to the custom top-level menu:
    add_submenu_page('buba-manager', 'Employment Application', 'Employment Application Manager', 'edit_posts', 'employment_application_manager', 'manager_employment_application');
	
	
}

// manager-home() displays the page content for the Start Here submenu
function manager_home() {
    //must check that the user has the required capability 
    if (!current_user_can('edit_posts'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	?>
	<h2>BUBA Manager</h2>
	<?php
}

require_once($dir.'employment-application-manager.php');

function manager_student_application() {
	//must check that the user has the required capability 
    if (!current_user_can('edit_posts'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	?>
	<h2>Student Application Manager</h2>
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
$st_app = $_POST['st_app'];
if ($st_app['app_type'] == 'By AppId'){
$req_params = array('app_id');
$presence_validation = checkPresence($req_params, $st_app);
} else if ($st_app['app_type'] == 'By Session'){
$req_params = array('month', 'year');
$presence_validation = checkPresence($req_params, $st_app);
} else if ($st_app['app_type'] == 'By Email'){
$req_params = array('email');
$presence_validation = checkPresence($req_params, $st_app);
} else if ($st_app['app_type'] == 'View'){
$presence_validation['overall_status'] = 'Ok';
} else if ($st_app['app_type'] == 'Update'){
$presence_validation['overall_status'] = 'Ok';
}else if ($st_app['app_type'] == 'Back'){
$presence_validation['overall_status'] = 'Ok';
} else if ($st_app['app_type'] == 'Print'){
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
$appType = $st_app['app_type'];
$url = '';
if ($appType == 'By AppId'){
$appCode = $st_app['app_id'];
$url = "http://api-b-unique.bubba-online.com/getStudentApplication?mode=byId&app_code=".$appCode;
$result = $request->request( $url);
} else if ($appType == 'By Session'){
$month = $st_app['month'];
$year = $st_app['year'];
$url = "http://api-b-unique.bubba-online.com/getStudentApplication?mode=bySession&month=".$month."&year=".$year;
$result = $request->request( $url);
} else if ($appType == 'By Email'){
$email = $st_app['email'];
$url = "http://api-b-unique.bubba-online.com/getStudentApplication?mode=byEmail&email=".$email;
$result = $request->request( $url);
} else if ($appType == 'View'){
$appCode = $st_app['app_code'];
$back_url = $_POST['back_url'];
$url = "http://api-b-unique.bubba-online.com/getStudentApplication?mode=byId&app_code=".$appCode;
$result = $request->request( $url);
} else if ($appType == 'Back'){
$url = $_POST['back_url'];
$result = $request->request( $url);
} else if ($appType == 'Print'){
$back_url = $_POST['back_url'];
$status = 'success';
session_start();
$_SESSION['st_app'] = $_POST['st_app'];
echo '<script>
window.open("http://bubba-online.com/print-student-app")
</script>';

} else if ($appType == 'Update'){
$status_change = "false";	
$appCode = $st_app['app_code'];
$back_url = $_POST['back_url'];
$old_status = $st_app['accepted_readOnly'];
$new_status = $st_app['accepted'];
if ($old_status != $new_status)
{
$status_change = "true";
}
$url = "http://api-b-unique.bubba-online.com/updateStudentApplication";
$post_param_names = array('id', 'accepted', 'remarks');
$value_pair = array();
			
			foreach ($post_param_names as $name)
			{				
				$value_pair[$name]=$st_app[$name];
			}
$content = array("student_application" => $value_pair, "id" => $st_app['id'], "status_change" => $status_change);
$headers = array('Content-Type: application/json');
$result = $request->request( $url, array( 'method' => 'POST', 'body' => $content, 'headers' => $headers));
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
//load variables
$periods = array('January','April', 'July', 'October');
?>


<form class="" action="" method="post">
	<?php if ($validation == 'fail'){ echo '<div class="error">Please Fill in the Required fields</div>';}
	else if ($status == 'error') { echo '<div class="error">'. $message .'</div>';}?>
    
	<label>Search By Application ID</label><br/>
	<div id="byAppId">
	<label class="label">Application ID<span class="red"> *</span></label>
	<input class="<?php echo $presence_validation['app_id'] == 'fail'? 'invalid-input' :''; ?>" type="text" size="18" name="st_app[app_id]" value="<?php echo $st_app['app_id']; ?>" size="25" maxlength="45">
	<input type="submit" name="st_app[app_type]" value="By AppId">
    </div><br/>
	
	<label>Search By Session</label><br/>
	<div id="bySession">
	<label class="label">Month<span class="red"> *</span></label>
	<select class="<?php echo $presence_validation['month'] == 'fail'? 'invalid-input' :'';?>" name="st_app[month]">
	<option value="<?php echo $st_app['month']; ?>" name="st_app[month]" OnClick=""><?php echo $st_app['month'];?></option>
	<?php foreach($periods as $period): ?>
	<option value="<?php echo $period; ?>" name="st_app[month]" OnClick=""><?php echo $period;?></option>
	<?php endforeach; ?>
    </select>
	<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>	
    <label class="label">Year<span class="red"> *</span></label>
	<input class="<?php echo $presence_validation['year'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[year]" value="<?php echo $st_app['year']; ?>" size="25" maxlength="45">
	<input type="submit" name="st_app[app_type]" value="By Session">
    </div><br/>
	
	<label>Search By Email</label><br/>
	<div id="byEmail">
	<label class="label">Email<span class="red"> *</span></label>
	<input class="<?php echo $presence_validation['email'] == 'fail'? 'invalid-input' :'';?>" type="email" name="st_app[email]" value="<?php echo $st_app['email']; ?>" size="25" maxlength="45">
    <input type="submit" name="st_app[app_type]" value="By Email">
	</div>
	<input type="hidden" name="submitted" value="true"> 
	
</form>
<br/>
<?php if ($status == 'success' && $appType != 'View' && $appType != 'Update') { echo '<div class="success">'. $message .'</div>';?> 
<table><tr><th>Application ID</th><th>First Name</th><th>Last Name</th><th>Session</th><th>Applied For</th><th>Paid</th><th>Offered Admission</th><th></th></tr>
<?php foreach($response as $resp) {?>
<form class="" action="" method="post">
<tr>
<td><input type="text" size="20" readonly name="st_app[app_code]" value="<?php echo $resp['application_code'];?>"></td>
<td><input type="text" readonly name="st_app[first_name]" value="<?php echo $resp['first_name'];?>"></td>
<td><input type="text" readonly name="st_app[last_name]" value="<?php echo $resp['last_name'];?>"></td>
<td><input type="text" readonly name="st_app[session]" value="<?php echo $resp['admission_session'];?>"></td>
<td><input type="text" readonly name="st_app[applying_for]" value="<?php echo $resp['applying_for'];?>"></td>
<td><input type="text" readonly name="st_app[paid]" value="<?php echo $resp['paid'];?>"></td>
<td><input type="text" readonly name="st_app[accepted]" value="<?php echo $resp['accepted'];?>"></td>
<td><input type="hidden" name="st_app[dob]" value="<?php echo $resp['dob'];?>">
<input type="hidden" name="st_app[gender]" value="<?php echo $resp['gender'];?>">
<input type="hidden" name="st_app[marital_status]" value="<?php echo $resp['marital_status'];?>">
<input type="hidden" name="st_app[ssn]" value="<?php echo $resp['ssn'];?>">
<input type="hidden" name="st_app[email]" value="<?php echo $resp['email'];?>">
<input type="hidden" name="st_app[home_phone]" value="<?php echo $resp['home_phone'];?>">
<input type="hidden" name="st_app[mobile_phone]" value="<?php echo $resp['mobile_phone'];?>">
<input type="hidden" name="st_app[other_phone]" value="<?php echo $resp['other_phone'];?>">
<input type="hidden" name="st_app[address]" value="<?php echo $resp['address'];?>">
<input type="hidden" name="st_app[city]" value="<?php echo $resp['city'];?>">
<input type="hidden" name="st_app[state]" value="<?php echo $resp['state'];?>">
<input type="hidden" name="st_app[zipcode]" value="<?php echo $resp['zipcode'];?>">
<input type="hidden" name="st_app[education]" value="<?php echo $resp['education'];?>">
<input type="hidden" name="st_app[dl_number]" value="<?php echo $resp['dl_number'];?>">
<input type="hidden" name="st_app[dl_issue]" value="<?php echo $resp['dl_issue'];?>">
<input type="hidden" name="st_app[student_type]" value="<?php echo $resp['student_type'];?>">
<input type="hidden" name="st_app[period]" value="<?php echo $resp['period'];?>">
<input type="hidden" name="st_app[committment]" value="<?php echo $resp['committment'];?>">
<input type="hidden" name="st_app[employer_name]" value="<?php echo $resp['employer_name'];?>">
<input type="hidden" name="st_app[employer_phone]" value="<?php echo $resp['employer_phone'];?>">
<input type="hidden" name="st_app[employer_address]" value="<?php echo $resp['employer_address'];?>">
<input type="hidden" name="st_app[employer_city]" value="<?php echo $resp['employer_city'];?>">
<input type="hidden" name="st_app[employer_state]" value="<?php echo $resp['employer_state'];?>">
<input type="hidden" name="st_app[employer_zipcode]" value="<?php echo $resp['employer_zipcode'];?>">
<input type="hidden" name="st_app[emergency_name]" value="<?php echo $resp['emergency_name'];?>">
<input type="hidden" name="st_app[emergency_relationship]" value="<?php echo $resp['emergency_relationship'];?>">
<input type="hidden" name="st_app[emergency_mobile_number]" value="<?php echo $resp['emergency_mobile_number'];?>">
<input type="hidden" name="st_app[emergency_home_number]" value="<?php echo $resp['emergency_home_number'];?>">
<input type="hidden" name="st_app[emergency_address]" value="<?php echo $resp['emergency_address'];?>">
<input type="hidden" name="st_app[emergency_city]" value="<?php echo $resp['emergency_city'];?>">
<input type="hidden" name="st_app[emergency_state]" value="<?php echo $resp['emergency_state'];?>">
<input type="hidden" name="st_app[emergency_zipcode]" value="<?php echo $resp['emergency_zipcode'];?>">
<input type="hidden" name="st_app[physical_disability]" value="<?php echo $resp['physical_disability'];?>">
<input type="hidden" name="st_app[medication]" value="<?php echo $resp['medication'];?>">
<input type="hidden" name="st_app[summary]" value="<?php echo $resp['summary'];?>">
<input type="hidden" name="st_app[remarks]" value="<?php echo $resp['remarks'];?>">
<input type="hidden" name="st_app[paid_on]" value="<?php echo $resp['paid_on'];?>">
<input type="hidden" name="st_app[submitted_on]" value="<?php echo $resp['submitted_on'];?>"></td>
<td><input type="hidden" name="st_app[old_app_type]" value="<?php echo $appType;?>">
<input type="hidden" name="submitted" value="true">
<input type="hidden" name="back_url" value="<?php echo $url;?>">
<input type="submit" name="st_app[app_type]" value="View">
<input type="submit" name="st_app[app_type]" value="Print"></td>
</tr>
</form>
<?php } ?>
</table>
<?php } ?>
<?php if ($status == 'success' && ($appType == 'View' || $appType == 'Print' || $appType == 'Update')) { echo '<div class="success">'. $message .'</div>';?>
<div><form class="" action="" method="post">
<input type="hidden" name="back_url" value="<?php echo $back_url;?>">
<input type="hidden" name="st_app[app_type]" value="Back">
<input type="hidden" name="submitted" value="true"> 
<input type="submit" name="submit" value="Go Back To Results">
</form></div>
<?php if ($appType == 'View') {?>
<?php foreach($response as $resp) {?>
<form class="" action="" method="post">
<table>
<tr>
<td>
<fieldset>
<legend><b>Personal Info:</b></legend>
<table>
<tr>
<td><label class="label">Application ID</label></td>
<td><input type="text" size="20" readonly name="st_app[app_code]" value="<?php echo $resp['application_code'];?>"></td>
</tr>
<tr>
<td><label class="label">First Name</label></td>
<td><input type="text" readonly name="st_app[first_name]" value="<?php echo $resp['first_name'];?>"></td>
</tr>
<tr>
<td><label class="label">Last Name</label></td>
<td><input type="text" readonly name="st_app[last_name]" value="<?php echo $resp['last_name'];?>"></td>
</tr>
<tr>
<td><label class="label">Date of Birth</label></td>
<td><input type="text" readonly name="st_app[dob]" value="<?php echo $resp['dob'];?>"></td>
</tr>
<tr>
<td><label class="label">Gender</label></td>
<td><input type="text" readonly name="st_app[gender]" value="<?php echo $resp['gender'];?>"></td>
</tr>
<tr>
<td><label class="label">Marital Status</label></td>
<td><input type="text" readonly name="st_app[marital_status]" value="<?php echo $resp['marital_status'];?>"></td>
</tr>
<tr>
<td><label class="label">Email</label></td>
<td><input type="text" readonly name="st_app[email]" value="<?php echo $resp['email'];?>"></td>
</tr>
<tr>
<td><label class="label">Mobile Phone</label></td>
<td><input type="text" readonly name="st_app[mobile_phone]" value="<?php echo $resp['mobile_phone'];?>"></td>
</tr>
<tr>
<td><label class="label">Home Phone</label></td>
<td><input type="text" readonly name="st_app[home_phone]" value="<?php echo $resp['home_phone'];?>"></td>
</tr>
<tr>
<td><label class="label">Other Phone</label></td>
<td><input type="text" readonly name="st_app[other_phone]" value="<?php echo $resp['other_phone'];?>"></td>
</tr>
<tr>
<td><label class="label">SSN</label></td>
<td><input type="text" readonly name="st_app[ssn]" value="<?php echo $resp['ssn'];?>"></td>
</tr>
<tr>
<td><label class="label">Drivers License Number</label></td>
<td><input type="text" readonly name="st_app[dl_number]" value="<?php echo $resp['dl_number'];?>"></td>
</tr>
<tr>
<td><label class="label">Issue State</label></td>
<td><input type="text" readonly name="st_app[dl_issue]" value="<?php echo $resp['dl_issue'];?>"></td>
</tr>
<tr>
<td><label class="label">Education</label></td>
<td><input type="text" readonly name="st_app[education]" value="<?php echo $resp['education'];?>"></td>
</tr>
<tr>
<td><label class="label">Address</label></td>
<td><input type="text" readonly name="st_app[address]" value="<?php echo $resp['address'];?>"></td>
</tr>
<tr>
<td><label class="label">City</label></td>
<td><input type="text" readonly name="st_app[city]" value="<?php echo $resp['city'];?>"></td>
</tr>
<tr>
<td><label class="label">State</label></td>
<td><input type="text" readonly name="st_app[state]" value="<?php echo $resp['state'];?>"></td>
</tr>
<tr>
<td><label class="label">Zip-Code</label></td>
<td><input type="text" readonly name="st_app[zipcode]" value="<?php echo $resp['zipcode'];?>"></td>
</tr>
</table>
</fieldset>
<br/>
<fieldset>
<legend><b>Emergency Contact:</b></legend>
<table>
<tr>
<td><label class="label">Name</label></td>
<td><input type="text" readonly name="st_app[emergency_name]" value="<?php echo $resp['emergency_name'];?>"></td>
</tr>
<tr>
<td><label class="label">Relationship</label></td>
<td><input type="text" readonly name="st_app[emergency_relationship]" value="<?php echo $resp['emergency_relationship'];?>"></td>
</tr>
<tr>
<td><label class="label">Mobile Phone</label></td>
<td><input type="text" readonly name="st_app[emergency_mobile_number]" value="<?php echo $resp['emergency_mobile_number'];?>"></td>
</tr>
<tr>
<td><label class="label">Home Phone</label></td>
<td><input type="text" readonly name="st_app[emergency_home_number]" value="<?php echo $resp['emergency_home_number'];?>"></td>
</tr>
<tr>
<td><label class="label">Address</label></td>
<td><input type="text" readonly name="st_app[emergency_address]" value="<?php echo $resp['emergency_address'];?>"></td>
</tr>
<tr>
<td><label class="label">City</label></td>
<td><input type="text" readonly name="st_app[emergency_city]" value="<?php echo $resp['emergency_city'];?>"></td>
</tr>
<tr>
<td><label class="label">State</label></td>
<td><input type="text" readonly name="st_app[emergency_state]" value="<?php echo $resp['emergency_state'];?>"></td>
</tr>
<tr>
<td><label class="label">Zip-Code</label></td>
<td><input type="text" readonly name="st_app[emergency_zipcode]" value="<?php echo $resp['emergency_zipcode'];?>"></td>
</tr>
</table>
</fieldset>
</td>
<td>&nbsp;&nbsp;</td>
<td>
<fieldset>
<legend><b>Admission Info:</b></legend>
<table>
<tr>
<td><label class="label">Admission Session</label></td>
<td><input type="text" readonly name="st_app[session]" value="<?php echo $resp['admission_session'];?>"></td>
</tr>
<tr>
<td><label class="label">Applied For</label></td>
<td><input type="text" readonly name="st_app[applying_for]" value="<?php echo $resp['applying_for'];?>"></td>
</tr>
<tr>
<td><label class="label">Paid</label></td>
<td><input type="text" readonly name="st_app[paid]" value="<?php echo $resp['paid'];?>"></td>
</tr>
<tr>
<td><label class="label">Offered Admission</label></td>
<td><input type="text" readonly name="st_app[accepted_readOnly]" value="<?php echo $resp['accepted'];?>"></td>
</tr>
<tr>
<td><label class="label">Will Attend</label></td>
<td><input type="text" readonly name="st_app[student_type]" value="<?php echo $resp['student_type'];?>"></td>
</tr>
<tr>
<td><label class="label">During</label></td>
<td><input type="text" readonly name="st_app[period]" value="<?php echo $resp['period'];?>"></td>
</tr>
<tr>
<td><label class="label">Level of Committment</label></td>
<td><input type="text" readonly name="st_app[committment]" value="<?php echo $resp['committment'];?>"></td>
</tr>
<tr>
<td><label class="label">Why this Career</label></td>
<td><textarea rows="10" cols="50" readonly name="st_app[summary]" value="<?php echo $resp['summary'];?>"></textarea></td>
</tr>
</table>
</fieldset>
<br/>
<fieldset>
<legend><b>Employment History:</b></legend>
<table>
<tr>
<td><label class="label">Name</label></td>
<td><input type="text" readonly name="st_app[employer_name]" value="<?php echo $resp['employer_name'];?>"></td>
</tr>
<tr>
<td><label class="label">Phone</label></td>
<td><input type="text" readonly name="st_app[employer_number]" value="<?php echo $resp['employer_number'];?>"></td>
</tr>
<tr>
<td><label class="label">Address</label></td>
<td><input type="text" readonly name="st_app[employer_address]" value="<?php echo $resp['employer_address'];?>"></td>
</tr>
<tr>
<td><label class="label">City</label></td>
<td><input type="text" readonly name="st_app[employer_city]" value="<?php echo $resp['employer_city'];?>"></td>
</tr>
<tr>
<td><label class="label">State</label></td>
<td><input type="text" readonly name="st_app[employer_state]" value="<?php echo $resp['employer_state'];?>"></td>
</tr>
<tr>
<td><label class="label">Zip-Code</label></td>
<td><input type="text" readonly name="st_app[employer_zipcode]" value="<?php echo $resp['employer_zipcode'];?>"></td>
</tr>
</table>
</fieldset>
<br/>
<fieldset>
<legend><b>Medical Information:</b></legend>
<table>
<tr>
<td><label class="label">Physical Disability</label></td>
<td><textarea rows="5" cols="20" readonly name="st_app[physical_disability]" value="<?php echo $resp['physical_disability'];?>"></textarea></td>
</tr>
<tr>
<td><label class="label">List of Medication</label></td>
<td><textarea rows="5" cols="20" readonly name="st_app[medication]" value="<?php echo $resp['medication'];?>"></textarea></td>
</tr>
</table>
</fieldset>
</td>
<td>&nbsp;&nbsp;</td>
<td>
<fieldset>
<legend><b>Official Remarks/Decision:</b></legend>
<table>
<tr>
<td>
<label>This application was submitted on</label><br/>
<input type="text" readonly name="st_app[submitted_on]" value="<?php echo $resp['submitted_on'];?>"></td>
</td>
</tr>
<tr>
<td>
<label>Payment was made on</label><br/>
<input type="text" readonly name="st_app[paid_on]" value="<?php echo $resp['paid_on'];?>"></td>
</td>
</tr>
<tr>
<td>
<label>Amount paid</label><br/>
<input type="text" readonly name="st_app[amount]" value="<?php echo $resp['amount'];?>"></td>
</td>
</tr>
<tr>
<td>
<label>Official Remark (Reason for Denial/Pending)</label><br/>
<textarea rows="10" cols="50" name="st_app[remarks]" value="<?php echo $resp['remarks'];?>"></textarea>
</td>
</tr>
<tr>
<td>
<label>Official Decision</label><br/>
<?php if ($resp['accepted'] == 'YES') { ?>
<input type="text" size="30" readonly name="decision" value="YES, was offered admission">
<?php } else if ($resp['accepted'] == 'NO'){ ?>
<input type="text" size="30" readonly name="decision" value="NO, admission was denied">
<?php } else { ?>
<input type="radio" name="st_app[accepted]" value="YES">Offer Admission<br/>
<input type="radio" name="st_app[accepted]" value="NO">Deny Admission<br/>
<input type="radio" name="st_app[accepted]" value="PENDING">Pending<br/>
<?php } ?>
</td>
</tr>
<tr>
<td><input type="hidden" name="st_app[old_app_type]" value="<?php echo $oldAppType;?>">
<input type="hidden" name="values_for_print" value="<?php echo $resp;?>">
<input type="hidden" name="back_url" value="<?php echo $back_url;?>">
<input type="hidden" name="submitted" value="true">
<input type="hidden" name="st_app[id]" value="<?php echo $resp['id'];?>">
<input type="submit" name="st_app[app_type]" value="Update">
<input type="submit" name="st_app[app_type]" value="Print">
<input type="submit" name="st_app[app_type]" value="Back"></td>
</tr>
</table>
</fieldset>
</td>
</tr>
</table>

</form>
<?php }}} ?>


	<?php
}

?>