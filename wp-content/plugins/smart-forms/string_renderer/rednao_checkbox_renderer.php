<?php
/**
 * Created by PhpStorm.
 * User: edseventeen
 * Date: 11/28/13
 * Time: 10:00 PM
 */

class rednao_checkbox_renderer extends  rednao_base_elements_renderer {
    public function GetString($formElement,$entry)
    {
        $rowInformation= "";
        $arrayValues=$entry["selectedValues"];
        foreach($arrayValues as $value)
        {
            $rowInformation.=htmlspecialchars($value["value"]).';';
        }
        return $rowInformation;
    }

}