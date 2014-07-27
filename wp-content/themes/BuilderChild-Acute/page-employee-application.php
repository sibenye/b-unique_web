<?php
/**
 *	Template for displaying student application.
 *
 */
?>
<?php function render_content() { ?>
<?php 
if( !class_exists( 'WP_Http' ) )
{
    include_once( ABSPATH . WPINC. '/class-http.php' );
}
$validation = '';
$status = '';

if ($_POST['submitted'] == "true"){
$st_app = $_POST['st_app'];
$req_params = array('first_name', 'last_name', 'email', 'marital_status', 'dob', 'gender', 'mobile_phone', 'education', 'address', 'city', 'state', 'zipcode', 'country', 'ssn', 'applying_for', 'employment_type', 'period', 'experience', 'emergency_name', 'emergency_mobile_number', 'emergency_relationship', 'emergency_address', 'emergency_city', 'emergency_state', 'emergency_zipcode', 'emergency_country', 'signed');
$presence_validation = checkPresence($req_params, $st_app);

if ($presence_validation['overall_status'] == 'error'){
	$validation = 'fail';
}
else
{
$url = "http://api-b-unique.bubba-online.com/createEmployeeApplication";

$post_param_names = array('first_name', 'last_name', 'email', 'marital_status', 'dob', 'gender', 'home_phone', 'mobile_phone', 'other_phone', 'education', 'address', 'city', 'state', 'zipcode', 'country', 'ssn', 'dl_number', 'dl_issue', 'applying_for', 'employment_type', 'period', 'experience', 'employer_name', 'employer_phone', 'employer_address', 'employer_city', 'employer_state', 'employer_zipcode', 'employer_country', 'emergency_name', 'emergency_home_number', 'emergency_mobile_number', 'emergency_relationship', 'emergency_address', 'emergency_city', 'emergency_state', 'emergency_zipcode', 'emergency_country', 'physical_disability', 'medication', 'summary', 'referrer');
			$value_pair = array();
			
			foreach ($post_param_names as $name)
			{				
				$value_pair[$name]=$st_app[$name];
			}
			$content = array("employee_application" => $value_pair);
			$headers = array('Content-Type: application/json');
			$request = new WP_Http;
			$result = $request->request( $url, array( 'method' => 'POST', 'body' => $content, 'headers' => $headers) );
			// test $result['response'] and if OK do something with $result['body']
			
			if ( !is_wp_error($result) )
			{			
				if ($result['response']['message'] == 'Created')
				{
					$body = json_decode($result['body'], true);
					if ($body['status'] == 0)
					{
					$appId = $body['response']['application_code'];
					?>
					<script type="text/javascript">
					   <!--
						  window.location= <?php echo "'http://bubba-online.com/successful-submission?appId=" . $appId . "'"; ?>;
					   //-->
					</script>		
					
					<?php
					}
					else
					{
						$status = 'error';
					}
				}
				else
				{ 
					$status = 'error';
				}
			} else { $status = 'error';}
}

}
//load variables
$edus = array('A College Graduate','A High School Graduate','Attending College','A GED Graduate','Attending High School','Did not complete High School');
$states = array('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$countries = array('United States');
$diplomas = array('Instructor','Administrator');
$stypes = array('Full_Time_40hr','Quarter_Time_30hr');
$periods = array('DAY');
$referrers = array('Friend','Yellow Pages','Drove By','Other');
$expres = array('None','1-3months','4-6months','1-3years','4-6years','6-10years','11years +');
?>
<div class="content-title"><?php the_title(); ?></div>
<div class="content-center">
<form class="" action="" method="post">
		  
	<table>
	
  <tr>
  <?php if ($validation == 'fail'){ echo '<div class="error">Please Fill in the Required fields</div>';}
	else if ($status == 'error') { echo '<div class="error">Error occurred during submission. Please try again</div>';}?>
  	<td colspan="3">
  		<span>Profile:</span>
  	</td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">First Name<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['first_name'] == 'fail'? 'invalid-input' :'';?>" type="text" name="st_app[first_name]" value="<?php echo $st_app['first_name']; ?>" size="25" maxlength="45">
    
  </td>
  <td class="field">
    <label class="label">Last Name<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['last_name'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[last_name]" value="<?php echo $st_app['last_name']; ?>" size="25" maxlength="45">
    
  </td>
  <td class="field">
    <label class="label">Email<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['email'] == 'fail'? 'invalid-input' :''; ?>" type="email" name="st_app[email]" value="<?php echo $st_app['email']; ?>" size="25" maxlength="45">
    
  </td>
  </tr>
  <tr>
  <td class="field">
  	<label class="label">Marital Status<span class="red"> *</span></label>
  	<br/>
	<select class="<?php echo $presence_validation['marital_status'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[marital_status]">
	<option value="<?php echo $st_app['marital_status']; ?>" name="st_app[marital_status]" OnClick=""><?php echo $st_app['marital_status'];?></option>
	<option value="Married" name="st_app[marital_status]" OnClick="">Married</option>
	<option value="Single" name="st_app[marital_status]" OnClick="">Single</option>
    </select>
	
  </td>
  <td class="field">
    <label class="label">Date of birth<span class="red"> *</span> yyyy/mm/dd</label><br />
	<input class="<?php echo $presence_validation['dob'] == 'fail'? 'invalid-input' :''; ?>" type="date" name="st_app[dob]" value="<?php echo $st_app['dob']; ?>" size="15" maxlength="15">
    
  </td>
  <td class="field">
  	<label class="label">Gender<span class="red"> *</span></label>
  	<br/>
	<select class="<?php echo $presence_validation['gender'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[gender]">
	<option value="<?php echo $st_app['gender']; ?>" name="st_app[gender]" OnClick=""><?php echo $st_app['gender'];?></option>
	<option value="Female" name="st_app[gender]" OnClick="">Female</option>
	<option value="Male" name="st_app[gender]" OnClick="">Male</option>
    </select>
	
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">Home Phone</label><br />
	<input class="<?php echo $home_phone-style; ?>" type="tel" name="st_app[home_phone]" value="<?php echo $st_app['home_phone']; ?>" size="15" maxlength="20">
    
  </td>
  <td class="field">
    <label class="label">Mobile Phone<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['mobile_phone'] == 'fail'? 'invalid-input' :''; ?>" type="tel" name="st_app[mobile_phone]" value="<?php echo $st_app['mobile_phone']; ?>" size="15" maxlength="20">
    
  </td>
  <td class="field">
    <label class="label">Other Phone</label><br />
	<input class="<?php echo $other_phone-style; ?>" type="tel" name="st_app[other_phone]" value="<?php echo $st_app['other_phone']; ?>" size="15" maxlength="20">
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">Education<span class="red"> *</span></label><br />
	<select class="<?php echo $presence_validation['education'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[education]">
	<option value="<?php echo $st_app['education']; ?>" name="st_app[education]" OnClick=""><?php echo $st_app['education'];?></option>
	<?php foreach($edus as $edu): ?>
	<option value="<?php echo $edu; ?>" name="st_app[education]" OnClick=""><?php echo $edu;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  <td class="field">
    <label class="label">Address<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['address'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[address]" value="<?php echo $st_app['address']; ?>" size="40" maxlength="70">
    
  </td>
  <td class="field">
    <label class="label">City<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['city'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[city]" value="<?php echo $st_app['city']; ?>" size="25" maxlength="40">
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">State<span class="red"> *</span></label><br />
	<select class="<?php echo $presence_validation['state'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[state]">
	<option value="<?php echo $st_app['state']; ?>" name="st_app[state]" OnClick=""><?php echo $st_app['state'];?></option>
	<?php foreach($states as $ste): ?>
	<option value="<?php echo $ste; ?>" name="st_app[state]" OnClick=""><?php echo $ste;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  <td class="field">
    <label class="label">ZipCode<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['zipcode'] == 'fail'? 'invalid-input' :''; ?>" type="number" name="st_app[zipcode]" value="<?php echo $st_app['zipcode']; ?>" size="5" maxlength="7">
    
  </td>
  <td class="field">
    <label class="label">Country<span class="red"> *</span></label><br />
	<select class="<?php echo $presence_validation['country'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[country]">
	<option value="<?php echo $st_app['country']; ?>" name="st_app[country]" OnClick=""><?php echo $st_app['country'];?></option>
	<?php foreach($countries as $ctry): ?>
	<option value="<?php echo $ctry; ?>" name="st_app[country]" OnClick=""><?php echo $ctry;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">SSN<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['ssn'] == 'fail'? 'invalid-input' :''; ?>" type="number" name="st_app[ssn]" value="<?php echo $st_app['ssn']; ?>" size="9" maxlength="11">
    
  </td>
  <td class="field">
    <label class="label">Drivers License Number</label><br />
	<input class="<?php echo $dl_number-style; ?>" type="number" name="st_app[dl_number]" value="<?php echo $st_app['dl_number']; ?>" size="15" maxlength="25">
    
  </td>
  <td class="field">
    <label class="label">Issue State</label><br />
	<select class="<?php echo $dl_issue-style; ?>" name="st_app[dl_issues]">
	<option value="<?php echo $st_app['dl_issue']; ?>" name="st_app[dl_issue]" OnClick=""><?php echo $st_app['dl_issue'];?></option>
	<?php foreach($states as $ste): ?>
	<option value="<?php echo $ste; ?>" name="st_app[dl_issue]" OnClick=""><?php echo $ste;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">Position Applying for<span class="red"> *</span></label><br />
	<select class="<?php echo $presence_validation['applying_for'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[applying_for]">
	<option value="<?php echo $st_app['applying_for']; ?>" name="st_app[applying_for]" OnClick=""><?php echo $st_app['applying_for'];?></option>
	<?php foreach($diplomas as $diploma): ?>
	<option value="<?php echo $diploma; ?>" name="st_app[applying_for]" OnClick=""><?php echo $diploma;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  <td class="field">
    <label class="label">Employment Type<span class="red"> *</span></label><br />
	<select class="<?php echo $presence_validation['employment_type'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[employment_type]">
	<option value="<?php echo $st_app['employment_type']; ?>" name="st_app[employment_type]" OnClick=""><?php echo $st_app['employment_type'];?></option>
	<?php foreach($stypes as $stype): ?>
	<option value="<?php echo $stype; ?>" name="st_app[employment_type]" OnClick=""><?php echo $stype;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  <td class="field">
    <label class="label">Shift<span class="red"> *</span></label><br />
	<select class="<?php echo $presence_validation['period'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[period]">
	<option value="<?php echo $st_app['period']; ?>" name="st_app[period]" OnClick=""><?php echo $st_app['period'];?></option>
	<?php foreach($periods as $per): ?>
	<option value="<?php echo $per; ?>" name="st_app[period]" OnClick=""><?php echo $per;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  </tr>
  <tr>
  <td class="field"  colspan="3">
  	<label class="label">Experience<span class="red"> *</span></label><br/>
	<select class="<?php echo $presence_validation['experience'] == 'fail'? 'invalid-input' :''; ?>" name="st_app[experience]">
	<option value="<?php echo $st_app['experience']; ?>" name="st_app[experience]" OnClick=""><?php echo $st_app['experience'];?></option>
	<?php foreach($expres as $exp): ?>
	<option value="<?php echo $exp; ?>" name="st_app[experience]" OnClick=""><?php echo $exp;?></option>
						<?php endforeach; ?>
    </select>
	
  </td>
  </tr>
  <tr>
  	<td colspan="3">
  		<span>Current or Previous Employment:</span>
  	</td>
  </tr>
  <tr>
  <td class="field"  colspan="3">
    <label class="label">Employer Name</label><br />
	<input class="<?php echo $employer_name-style;?>" type="text" name="st_app[employer_name]" value="<?php echo $st_app['employer_name']; ?>" size="45" maxlength="80">
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">Employer Phone</label><br />
	<input class="<?php echo $employer_number-style;?>" type="tel" name="st_app[employer_number]" value="<?php echo $st_app['employer_number']; ?>" size="15" maxlength="20">
    
  </td>
  <td class="field">
    <label class="label">Employer address</label><br />
	<input class="<?php echo $employer_address-style;?>" type="text" name="st_app[employer_address]" value="<?php echo $st_app['employer_address']; ?>" size="45" maxlength="85">
    
  </td>
  <td class="field">
    <label class="label">City</label><br />
	<input class="<?php echo $employer_city-style;?>" type="text" name="st_app[employer_city]" value="<?php echo $st_app['employer_city']; ?>" size="25" maxlength="40">
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">State</label><br />
	<select class="<?php echo $employer_state-style;?>" name="st_app[employer_state]">
	<option value="<?php echo $st_app['employer_state']; ?>" name="st_app[employer_state]" OnClick=""><?php echo $st_app['employer_state'];?></option>
	<?php foreach($states as $ste): ?>
	<option value="<?php echo $ste; ?>" name="st_app[employer_state]" OnClick=""><?php echo $ste;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  <td class="field">
    <label class="label">ZipCode</label><br />
	<input class="<?php echo $employer_zipcode-style; ?>" type="number" name="st_app[employer_zipcode]" value="<?php echo $st_app['employer_zipcode']; ?>" size="5" maxlength="7">
    
  </td>
  <td class="field">
    <label class="label">Country</label><br />
	<select class="<?php echo $employer_country-style;?>" name="st_app[employer_country]">
	<option value="<?php echo $st_app['employer_country']; ?>" name="st_app[employer_country]" OnClick=""><?php echo $st_app['employer_country'];?></option>
	<?php foreach($countries as $ctry): ?>
	<option value="<?php echo $ctry; ?>" name="st_app[employer_country]" OnClick=""><?php echo $ctry;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  </tr>
  <tr>
  	<td colspan="3">
  		<span>Emergency Contact:</span>
  	</td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">Name<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['emergency_name'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[emergency_name]" value="<?php echo $st_app['emergency_name']; ?>" size="25" maxlength="45">
    
  </td>
  <td class="field">
    <label class="label">Home Phone</label><br />
	<input class="<?php echo $emergency_home_number-style; ?>" type="tel" name="st_app[emergency_home_number]" value="<?php echo $st_app['emergency_home_number']; ?>" size="15" maxlength="20">
    
  </td>
  <td class="field">
    <label class="label">Mobile Phone<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['emergency_mobile_number'] == 'fail'? 'invalid-input' :''; ?>" type="tel" name="st_app[emergency_mobile_number]" value="<?php echo $st_app['emergency_mobile_number']; ?>" size="15" maxlength="20">
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">Relationship<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['emergency_relationship'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[emergency_relationship]" value="<?php echo $st_app['emergency_relationship']; ?>" size="15" maxlength="15">
    
  </td>
  <td class="field">
    <label class="label">Address<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['emergency_address'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[emergency_address]" value="<?php echo $st_app['emergency_address']; ?>" size="45" maxlength="85">
    
  </td>
  <td class="field">
    <label class="label">City<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['emergency_city'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[emergency_city]" value="<?php echo $st_app['emergency_city']; ?>" size="25" maxlength="40">
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">State<span class="red"> *</span></label><br />
	<select class="<?php echo $presence_validation['emergency_state'] == 'fail'? 'invalid-input' :'';?>" name="st_app[emergency_state]">
	<option value="<?php echo $st_app['emergency_state']; ?>" name="st_app[emergency_state]" OnClick=""><?php echo $st_app['emergency_state'];?></option>
	<?php foreach($states as $ste): ?>
	<option value="<?php echo $ste; ?>" name="st_app[emergency_state]" OnClick=""><?php echo $ste;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  <td class="field">
    <label class="label">ZipCode<span class="red"> *</span></label><br />
	<input class="<?php echo $presence_validation['emergency_zipcode'] == 'fail'? 'invalid-input' :''; ?>" type="number" name="st_app[emergency_zipcode]" value="<?php echo $st_app['emergency_zipcode']; ?>" size="5" maxlength="7">
    
  </td>
  <td class="field">
    <label class="label">Country<span class="red"> *</span></label><br />
	<select class="<?php echo $presence_validation['emergency_country'] == 'fail'? 'invalid-input' :'';?>" name="st_app[emergency_country]">
	<option value="<?php echo $st_app['emergency_country']; ?>" name="st_app[emergency_country]" OnClick=""><?php echo $st_app['emergency_country'];?></option>
	<?php foreach($countries as $ctry): ?>
	<option value="<?php echo $ctry; ?>" name="st_app[emergency_country]" OnClick=""><?php echo $ctry;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  </tr>
  </table>
  <table>
  <tr>
  	<td colspan="2">
  		Medical Information:<span class="label">Type N/A if it does not apply to you</span>
  	</td>
  </tr>
  <tr>  
  <td class="field">
    <label class="label">List any physical disability</label><br />
	<textarea class="<?php echo $physical_disability-style; ?>" name="st_app[physical_disability]" maxlength="200" rows="5" cols="20"><?php echo $st_app['physical_disability']; ?></textarea>
    
  </td>
  <td class="field">
    <label class="label">List any medication</label><br />
	<textarea class="<?php echo $medication-style; ?>" name="st_app[medication]" maxlength="200" rows="5" cols="20"><?php echo $st_app['medication']; ?></textarea>
    
  </td>
  </tr>
  <tr>
  	<td colspan="2">
  		<span></span>
  	</td>
  </tr>
  <tr>
  <td class="field" colspan="2">
    <label class="label">Brief summary of wanting this career</label><br />
	<textarea class="<?php echo $summary-style; ?>" name="st_app[summary]" maxlength="450" rows="15" cols="50"><?php echo $st_app['summary']; ?></textarea>
    
  </td>
  </tr>
  <tr>
  <td class="field">
    <label class="label">How did you hear of us</label><br />
	<select class="<?php echo $referrer-style;?>" name="st_app[referrer]">
	<option value="<?php echo $st_app['referrer']; ?>" name="st_app[referrer]" OnClick=""><?php echo $st_app['referrer'];?></option>
	<?php foreach($referrers as $ref): ?>
	<option value="<?php echo $ref; ?>" name="st_app[referrer]" OnClick=""><?php echo $ref;?></option>
						<?php endforeach; ?>
    </select>
    
  </td>
  <td class="field">
	
	<input type="hidden" name="submitted" value="true"> 
    
  </td>
  </tr>
  <tr>
  <td class="field"  colspan="2">
  	<span class="<?php echo $presence_validation['signed'] == 'fail'? 'invalid-input' :''; ?>"><input type="checkbox" name="st_app[signed]"></span>
	<label class="label"><span class="red">* </span>I certify that the information given on this application is accurate to the best of my knowledge. I understand that B-Unique Barber Academy will reject my application for false information.</label>
  </td>
   </tr>
   <tr> 
  <td class="actions">
	<input type="submit" name="submit" value="submit">
    
  </td>
  </tr>
  </table>
		</form>
</div>


<?php  } ?>

<?php

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );

?>