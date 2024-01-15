<?php

namespace App\Controllers;

use App\Services\ShiftService;

class ShiftController
{
	public function getAll()
	{
		echo (new ShiftService)->getAll();
	}
}