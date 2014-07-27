<?php
/**
 * Created by PhpStorm.
 * User: edseventeen
 * Date: 11/28/13
 * Time: 8:58 PM
 */

class rednao_text_area_renderer extends  rednao_base_elements_renderer{

    public function GetString($formElement,$entry)
    {
        return htmlspecialchars($entry["value"]);
    }
} 