<?php
class rednao_donation_recurrence extends  rednao_base_elements_renderer{

	public function GetString($formElement,$entry)
	{
		switch($entry["value"])
		{
			case 'OT':
				return 'One time';
			case 'D':
				return 'Daily';
			case 'W':
				return 'Weekly';
			case 'M':
				return 'Monthly';
			case 'Y':
				return 'Yearly';
		}

		return '';

	}
}