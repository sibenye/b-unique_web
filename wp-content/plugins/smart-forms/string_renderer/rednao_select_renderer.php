<?php
/**
 * Created by PhpStorm.
 * User: edseventeen
 * Date: 11/28/13
 * Time: 8:59 PM
 */

class rednao_select_renderer extends  rednao_base_elements_renderer{

    public function GetString($formElement,$entry)
    {
        return htmlspecialchars($entry["value"]);
    }

}