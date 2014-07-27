


<?php
include_once(SMART_FORMS_DIR.'smart-forms-license.php');

if(!defined('ABSPATH'))
    die('Forbidden');

if (isset($_GET['action'])) {
    $action=$_GET['action'];
}else
    $action="";
if($action==="add"||$action=="clone"){
    global $wpdb;
    $result=$wpdb->get_var("SELECT count(*) FROM ".SMART_FORMS_TABLE_NAME);
    if($result>=3&&!smart_forms_check_license_with_options($error))
    {
        smart_forms_load_license_manager("Sorry, this version support up to three forms only");
    }else{
        if($action==="add")
        {
            include(SMART_FORMS_DIR.'main_screens/smart-forms-add-new.php');
            return;
        }
    }

}

if($action!=="edit")
{
	echo "<h1>".__("Forms")."</h1>";
	echo sprintf('<h2 ><a style="color:blue; text-decoration: underline;" href="?page=%s&action=%s">'.__("Add New").'</a></h2>',$_REQUEST['page'],'add');
}



if (isset($_GET['id'])) {
    $form_id=$_GET['id'];
}else
    $form_id="";



if($action!=null&&$form_id!=null)
{
    global $wpdb;

    if($action==="delete")
    {
        $wpdb->query($wpdb->prepare("delete from ".SMART_FORMS_TABLE_NAME." WHERE form_id=%d",$form_id));
        delete_transient("rednao_smart_forms_$form_id");
    }

    if($action==="edit"||$action=="clone")
    {
        $result=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".SMART_FORMS_TABLE_NAME." WHERE form_id=%d",$form_id));

        if(count($result)>0)
        {
            $result=$result[0];

            $formOptions=$result->form_options;
            //$formOptions=str_replace("\\r","", str_replace("\\r","",$formOptions));
            $elementOptions=$result->element_options;
            //$elementOptions=str_replace("\\r","", str_replace("\\","\\\\",$elementOptions));

            $formClientOptions=$result->client_form_options;

            if($formClientOptions=="")
                $formClientOptions="{}";

            $formClientOptions=$formClientOptions;
           // $formClientOptions=str_replace("\\r","", str_replace("\\","\\\\",$formClientOptions));

            $script=<<<EOF
                        <script type="text/javascript" language="javascript">
                            var smartFormId="%s";
                            var smartFormsOptions=%s;
                            var smartFormsElementOptions=%s;
                            var smartFormClientOptions=%s
                        </script>
EOF;
            echo sprintf($script,($action=="clone"?"0":$result->form_id),$formOptions,$elementOptions,$formClientOptions);
            include(SMART_FORMS_DIR.'main_screens/smart-forms-add-new.php');
            return;

        }


    }
}


if(!class_exists('WP_LIST_TABLE'))
{
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}


class RednaoForms extends WP_List_Table
{
    function get_columns()
    {
        return array(
          'form_name'=>__('Form Name'),
          'form_id'=>__('Form Id'),
        );
    }

    function prepare_items()
    {
        $this->_column_headers=array($this->get_columns(),array(),$this->get_sortable_columns());
        global $wpdb;
        $savedForms=$result=$wpdb->get_results("SELECT * FROM ".SMART_FORMS_TABLE_NAME);
        foreach($savedForms as $form)
        {
            $form->form_name=esc_html($form->form_name);
            $form->form_id=esc_html($form->form_id);
        }
        $this->items=$savedForms;
    }

    function get_sortable_columns()
    {

    }

    function column_default($item, $column_name)
    {
        return $item->$column_name;
    }

    function column_form_name($item) {
        $actions = array(
            __('edit')      => sprintf('<a href="?page=%s&id=%s&action=%s">Edit</a>',$_REQUEST['page'],$item->form_id,'edit'),
            __('delete')    => sprintf('<a href="?page=%s&id=%s&action=%s">Delete</a>',$_REQUEST['page'],$item->form_id,'delete'),
            __('clone')    => sprintf('<a href="?page=%s&id=%s&action=%s">Clone</a>',$_REQUEST['page'],$item->form_id,'clone'),
        );

        return sprintf('%1$s %2$s', $item->form_name, $this->row_actions($actions) );
    }
}

$donationList=new RednaoForms();
$donationList->prepare_items();
$donationList->display();

?>




