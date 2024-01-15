<?php  

namespace App\Controllers;

use App\Services\PressArmService;

class PressArmController
{
	public function all()
	{
		echo (new PressArmService)->all();
	}
}