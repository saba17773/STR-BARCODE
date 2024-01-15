<?php

namespace App\Controllers;

use App\Services\OnhandService;

class OnhandController
{
	public function all()
	{
		echo (new OnhandService)->all();
	}

	public function getGreentireHold()
	{
		echo (new OnhandService)->getGreentireHold();
	}

	public function getFinalHold($producGroup)
	{
		echo (new OnhandService)->getFinalHold($producGroup);
	}

	public function updateOnhand($item_code, $type)
	{
		if ((new OnhandService)->updateOnhand($item_code, $type) === false) {
			return false;
		} else {
			return true;
		}
	}

	public function getGtCureFinal($producGroup,$locationtype)
	{
		echo (new OnhandService)->getGtCureFinal2($producGroup,$locationtype);
		// echo $locationtype."<=>".$producGroup;
	}
}
