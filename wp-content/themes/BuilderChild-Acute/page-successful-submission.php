<?php
/**
 *	Template for displaying successful submission page.
 *
 */
?>
<?php function render_content() { 
$appId = $_REQUEST['appId'];
?>
<div class="content-title">Confirmation</div>
<div class="content-center">

	<div class="lbox">
		<p>Your have successfully submitted your application. 
			A confirmation email has been sent to your email address.</p><br /><br />
		<span class="appId">APPLICATION ID: <?php echo $appId; ?></span><br /><br /><br />
		Plese take note of your application ID because you will use it to check the status of your application. 
	</div>
	<?php if (substr($appId, 0, 1) == 'S') { ?>
	<div class="instruction">
			Also note that your application will only be processed after you pay the application fee.
		</div>	
		<br /><br />
		<div class="paypal">Click here to pay your application fee</div>
	<?php } ?>
</div>
<?php  } ?>
<?php

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );

?>