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
$req_params = array('first_name', 'last_name', 'app_id', 'app_type');
$presence_validation = checkPresence($req_params, $st_app);

if ($presence_validation['overall_status'] == 'error'){
	$validation = 'fail';
}
else
{
$appCode = $st_app['app_id'];
$firstName = $st_app['first_name'];
$lastName = $st_app['last_name'];
$appType = $st_app['app_type'];
$url = '';
if ($appType == 'student'){
$url = "http://api-b-unique.bubba-online.com/getStudentApplication?mode=status&app_code=".$appCode."&first_name=".$firstName."&last_name=".$lastName;
} else if ($appType == 'employment'){
$url = "http://api-b-unique.bubba-online.com/getEmployeeApplication?mode=status&app_code=".$appCode."&first_name=".$firstName."&last_name=".$lastName;
}

			$request = new WP_Http;
			$result = $request->request( $url);
			// test $result['response'] and if OK do something with $result['body']
			
			if ( !is_wp_error($result) )
			{
				if ($result['response']['message'] == 'OK')
				{
					$body = json_decode($result['body'], true);
					if ($body['status'] == 0)
					{
					$appliedFor = $body['response']['appliedFor'];
					$session = $body['response']['session'];
					$submitDate = $body['response']['submitDate'];
					$message = $body['response']['message'];
					$status = 'success';
					}
					else if ($body['status'] == 220)
					{
						$status = 'success';
						$callMessage = '<div class="notice">No results Found</div>';
					}
					else
					{
						$status = 'error';
						$callMessage = '<div class="error">Error occurred during check. Please try again</div>';
					}
				}
				else
				{ 
					$status = 'error';
					$callMessage = '<div class="error">Error occurred during check. Please try again</div>';
				}
			} else { $status = 'error';}
}

}
//load variables

?>
<div class="content-title"><?php the_title(); ?></div>
<div class="content-center">
<form class="" action="" method="post">
	<?php if ($validation == 'fail'){ echo '<div class="error">Please Fill in the Required fields</div>';}
	else if ($status == 'error') { echo $callMessage;}?>
    
	<label class="label">Application Type<span class="red"> *</span></label>
	<input type="radio" name="st_app[app_type]" value="student" checked>Student Application
	<input type="radio" name="st_app[app_type]" value="employment">Employment Application
	
	<br/>
	<label class="label">First Name<span class="red"> *</span></label>
	<input class="<?php echo $presence_validation['first_name'] == 'fail'? 'invalid-input' :'';?>" type="text" name="st_app[first_name]" value="<?php echo $st_app['first_name']; ?>" size="25" maxlength="45">
    
	<br/>
    <label class="label">Last Name<span class="red"> *</span></label>
	<input class="<?php echo $presence_validation['last_name'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[last_name]" value="<?php echo $st_app['last_name']; ?>" size="25" maxlength="45">
    
	<br/>
    <label class="label">Application Id<span class="red"> *</span></label>
	<input class="<?php echo $presence_validation['app_id'] == 'fail'? 'invalid-input' :''; ?>" type="text" name="st_app[app_id]" value="<?php echo $st_app['app_id']; ?>" size="25" maxlength="45">
    
	<br/>
	<input type="hidden" name="submitted" value="true"> 
	<input type="submit" name="submit" value="check">
</form>
<?php if ($status == 'success') { echo $callMessage;?>
<div class="lbox">
<label class="label">Application ID: <?php echo $appCode;?></label><br/>
<label class="label">Submitted On: <?php echo $submitDate;?></label><br/>
<?php if ($appType == 'student') { ?>
<label class="label">Session: <?php echo $session;?></label><br/>
<?php } ?>
<label class="label">Applied For: <?php echo $appliedFor;?></label><br/>
<label class="label">Status: <?php echo $message;?></label><br/>
<label class="label">First Name: <?php echo $firstName;?></label><br/>
<label class="label">Last Name: <?php echo $lastName;?></label>
</div>
<?php } ?>
</div>


<?php  } ?>

<?php

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );

?>