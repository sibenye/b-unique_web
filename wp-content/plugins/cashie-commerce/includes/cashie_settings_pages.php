<?php 
$origin = ((empty($_SERVER['HTTPS'])?"http://":"https://").$_SERVER['HTTP_HOST']); 
$returnURL = ($origin . $_SERVER['REQUEST_URI']);
?>
<div class="wrap">
    <?php

    if ($this->update)
    { ?>
        <div id="notify-update"><span>Settings have been successfully saved...</span></div> <?php
    } ?>
    
   <iframe src="<?php echo $this->cashie_url; ?>/account/configurepages?<?php echo $this->cashie_url_vars; ?>&origin=<?php echo urlencode($origin); ?>&returnURL=<?php echo urlencode($returnURL); ?>&plugin_version=<?php echo cashie_get_version(); ?>" width="930" height="1200" frameborder="0"></iframe>
</div> <!-- End of wrap -->