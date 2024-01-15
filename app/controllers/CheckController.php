<?php

namespace App\Controllers;

use App\Conpnents\Database as DB;
use Wattanar\Sqlsrv;
use App\Models\Barcode;

class CheckController
{
	public function checkBuild()
	{
		renderView('page/check_build');
	}

	public function checkTest()
	{
		renderView('page/check_test');
	}

	public function checkFinalInspect()
	{
		renderView('page/check_final_inspect');
	}

	public function handheld_loginMb()
	{
		renderView('page_mobile/handheld_login');
	}

	public function checkwmsbarcode()
	{
		renderView('page/check_wms_barcode');
	}
}
