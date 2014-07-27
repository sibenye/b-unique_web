<?php 
	global $cashie_logged_in;
  $origin = ((empty($_SERVER['HTTPS'])?"http://":"https://").$_SERVER['HTTP_HOST']); 
	$returnURL = ($origin . $_SERVER['REQUEST_URI']);
?>
<script type="text/javascript">
var logoutURL = "<?php echo $this->cashie_url; ?>/logout?<?php echo $this->cashie_url_vars; ?>&origin=<?php echo urlencode($origin); ?>&returnURL=<?php echo urlencode($returnURL); ?>&plugin_version=<?php echo cashie_get_version(); ?>";

function confirmLogin()
{
	if (confirm("WARNING: Have you already added Cashie Commerce to another website outside of WordPress? Any existing shopping cart created with your Cashie Commerce account will stop working after you sign in here. To avoid this, you should create a new Cashie Commerce account for this WordPress site. Would you like to continue and login?"))
	{
		document.getElementById("loginarea").style.display = "none";
		document.getElementById("signupframe").src = "<?php echo $this->cashie_url; ?>/login?<?php echo $this->cashie_url_vars; ?>&origin=<?php echo urlencode($origin); ?>&returnURL=<?php echo urlencode($returnURL); ?>&plugin_version=<?php echo cashie_get_version(); ?>";
	}
}
</script>
<style type="text/css">
.existing {
	font-size:18px !important;
	font-weight:bold !important;
	color:#5BA81F !important;
	margin:5px 0px !important;
}
</style>
<div class="wrap">
    <?php if ($this->update) { ?>
        <form id="updateurl_form" name="updateurl_form" method="post" action="<?php echo $this->cashie_url; ?>/api/users/save_urls">
						<input type="hidden" name="cart"  value="<?php echo get_permalink($this->option_values['url_cart']); ?>" /> 
            <input type="hidden" name="checkout" value="<?php echo get_permalink($this->option_values['url_checkout']); ?>" />  
            <input type="hidden" name="success" value="<?php echo get_permalink($this->option_values['url_success']); ?>" />
            <input type="hidden" name="failure"  value="<?php echo get_permalink($this->option_values['url_failure']); ?>" />
            <input type="hidden" name="catalog"  value="<?php echo get_permalink($this->option_values['url_catalog']); ?>" /> 
            <input type="hidden" name="details"  value="<?php echo get_permalink($this->option_values['url_details_dynamic']); ?>" />
            <input type="hidden" name="static_details"  value='<?php echo json_encode($this->option_values['static_details']); ?>' />  
            <input type="hidden" name="returnURL"  value="<?php echo $returnURL."&update=1"; ?>" />          
				</form>   
        <script type="text/javascript">
				  document.updateurl_form.submit();
				</script>
      <?php }  // end if ($this->update)?>
      <?php if (!empty($_GET['update'])) { ?>
      	<div id="notify-update"><span>Your WordPress site has been linked to your Cashie Commerce account and your shopping cart pages have been generated.</span></div>
      <?php } ?>
    <?php if (empty($this->option_values['hash'])) { ?>
    <iframe src="<?php echo $this->cashie_url; ?>/sign_up?<?php echo $this->cashie_url_vars; ?>&origin=<?php echo urlencode($origin); ?>&returnURL=<?php echo urlencode($returnURL); ?>&plugin_version=<?php echo cashie_get_version(); ?>" width="930" height="2000" frameborder="0" name="signupframe" id="signupframe"></iframe>
    <?php } else { ?>
   <iframe src="<?php echo $this->cashie_url; ?>/login?<?php echo $this->cashie_url_vars; ?>&origin=<?php echo urlencode($origin); ?>&returnURL=<?php echo urlencode($returnURL); ?>&plugin_version=<?php echo cashie_get_version(); ?>" width="930" height="1000" frameborder="0" name="signupframe" id="signupframe"></iframe>
   <?php } ?>
   <form id="profile_form" name="profile_form" method="post" action="">
						<input type="hidden" name="hash"  value="" /> 
            <input type="hidden" name="oldhash" value="<?php echo $this->option_values['hash']; ?>" />  
            <input type="hidden" name="details_dynamic" value="" />
            <input type="hidden" name="update_hash"  value="true" /> 
            <input type="hidden" name="update"  value="true" />            
				</form>        
</div> <!-- End of wrap -->

