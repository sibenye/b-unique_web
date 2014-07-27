<?php


class rednao_phone_renderer extends rednao_base_elements_renderer {


    public function GetString($formElement,$entry)
    {
        return htmlspecialchars($entry["area"].'-'.$entry["phone"]);
    }
}