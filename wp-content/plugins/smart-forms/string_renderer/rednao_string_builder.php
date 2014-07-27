<?php
/**
 * Created by PhpStorm.
 * User: edseventeen
 * Date: 11/28/13
 * Time: 9:33 PM
 */

class rednao_string_builder {

    public function __construct()
    {
        require_once(SMART_FORMS_DIR."string_renderer/rednao_base_elements_renderer.php");

    }


    public function  GetStringFromColumn($formElement,$entry){


        $renderer=null;
        switch($formElement["ClassName"])
        {
            case "rednaotextinput":
            case "rednaoprependedtext":
            case "rednaoappendedtext":
            case "rednaodatepicker":
            case "rednaoemail":
            case "rednaonumber":
                $renderer=$this->GetRenderer("rednao_text_input_renderer");
                break;

            case "rednaoprependedcheckbox":
            case "rednaoappendedcheckbox":
                $renderer=$this->GetRenderer("rednao_checkbox_input_renderer");
                break;

            case "rednaotextarea":
                $renderer=$this->GetRenderer("rednao_text_area_renderer");
                break;

            case "rednaomultipleradios":
                $renderer=$this->GetRenderer("rednao_radio_renderer");
                break;

            case "rednaomultiplecheckboxes":
                $renderer=$this->GetRenderer("rednao_checkbox_renderer");
                break;

            case "rednaoselectbasic":
                $renderer=$this->GetRenderer("rednao_select_renderer");
                break;

            case "rednaoname":
                $renderer=$this->GetRenderer("rednao_name_renderer");
                break;
            case "rednaoaddress":
                $renderer=$this->GetRenderer("rednao_address_renderer");
                break;
            case "rednaophone":
                $renderer=$this->GetRenderer("rednao_phone_renderer");
                break;
			case "rednaodonationrecurrence":
				$renderer=$this->GetRenderer("rednao_donation_recurrence");
				break;
			case "sfFileUpload":
				$renderer=$this->GetRenderer("rednao_file_upload");
				break;
        }

      return $renderer->GetString($formElement,$entry);
    }

    public function GetRenderer($rendererName)
    {
        require_once(SMART_FORMS_DIR."string_renderer/$rendererName.php");

        if(!isset($this->$rendererName))
            $this->$rendererName=new $rendererName();

        return $this->$rendererName;
    }



} 