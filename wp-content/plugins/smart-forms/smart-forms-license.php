<?php

function smart_forms_check_license($email,$key,&$error)
{
    if($email!=null||$key!=null)
    {
        if(get_transient("smart_forms_check_again"))
            return true;
        if(smart_forms_license_is_valid($email,$key,$error))
        {
            update_option('smart_forms_email',$email);
            update_option('smart_forms_key',$key);

            set_transient("smart_forms_check_again",1,60*60*24*7);
            return true;
        }
    }

    return false;
}

function smart_forms_check_license_with_options(&$error)
{
    $result=apply_filters("smart_forms_lc_is_valid_with_options",array());
	$error=$result["error"];
	return $result["is_valid"];
}

function smart_forms_license_is_valid($email,$key,&$error)
{
	$result = apply_filters( "smart_forms_lc_is_valid", array(
		email=>$email,
		key=>$key,
		is_valid=>false,
		error=>""
	) );

	$error=$result["error"];
	return $result["is_valid"];

}

function smart_forms_load_license_manager($errorMessage)
{
    if(smart_forms_check_license_with_options($error))
    {
        ?><script language="javascript">
            var RedNaoSmartFormLicenseIsValid=true;
            var RedNaoSmartFormLicenseErrorMessage="";
        </script><?php
    }else
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('isolated-slider',SMART_FORMS_DIR_URL.'js/rednao-isolated-jq.js',array('jquery'));
        wp_enqueue_style('smart-forms-Slider',SMART_FORMS_DIR_URL.'css/smartFormsSlider/jquery-ui-1.10.2.custom.min.css');

        ?>
                <script language="javascript">
                    var RedNaoSmartFormLicenseIsValid=false;
                    var RedNaoSmartFormLicenseErrorMessage="<?php echo $errorMessage?>";
                </script>


        <?php
    }
    wp_enqueue_script('rednao-smart-forms-licensing-manager',SMART_FORMS_DIR_URL.'js/licensing/rednao-licensing-manager.js',array('isolated-slider'));

}

