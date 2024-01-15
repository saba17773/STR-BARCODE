<?php

namespace App\Controllers;

use App\Services\TrackingService;

class TrackingController
{
	public function searchByBarcode()
	{
		echo (new TrackingService)->searchByBarcode();
	}

	public function searchByBarcode2()
	{
		echo (new TrackingService)->searchByBarcode2();
	}

	public function searchByBarcodeSvoPCR()
	{
		echo (new TrackingService)->searchByBarcodeSvoPCR();
	}

	public function searchByBarcodeLine()
	{
		echo (new TrackingService)->searchByBarcodeLine();
	}

	public function searchBybeforelast($datatest)
	{
		echo (new TrackingService)->aa($datatest);

		//echo (new TrackingService)->searchByBarcodeLine();
	}
	public function searchBybeforelastDeafce($datatest)
	{
		echo (new TrackingService)->showDefcet($datatest);

		//echo (new TrackingService)->searchByBarcodeLine();
	}
	public function searchBybeforelastDeafceScrap($datatest)
	{
		echo (new TrackingService)->showDefcetScrap($datatest);

		//echo (new TrackingService)->searchByBarcodeLine();
	}
}
