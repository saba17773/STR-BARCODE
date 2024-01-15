<?php

namespace App\Controllers;

use App\Components\Security;
use App\Components\Authentication;
use App\Components\Utils;

class PageHandheldController
{

	private $auth;
	private $secure;
	private $utils;

	public function __construct()
	{
		$this->auth = new Authentication;
		$this->secure = new Security;
		$this->utils = new Utils;

		if ($this->auth->isLogin() === false) {
			renderView("page/handheld_login");
			exit;
		}
	}

	public function handheldLogin()
	{
		renderView("page/handheld_login");
	}

	public function curingHandheld()
	{
		renderView("page/handheld_curing");
	}

	public function curingHandheldWithoutSerial()
	{
		renderView("page/handheld_curing_without_serial");
	}

	public function finaltowh()
	{
		renderView("page/finalToWh_mobile");
	}
	public function finaltowhcreate()
	{
		renderView("page/finalToWh_mobile_create");
	}
	//mobile
	public function finaltowhcreate_mb()
	{
		renderView("page/finalToWh_mobile_create_mb");
	}
	public function finaltowh_mb()
	{
		renderView("page/finalToWh_mobile_mb");
	}

	public function curingHandheld_mb()
	{
		renderView("page/handheld_curingMb");
	}

	public function curingHandheldWithoutSerial_mb()
	{
		renderView("page_mobile/handheld_curing_without_serial_Mb");
	}
}
