<?php

namespace App\Components;

use App\V2\Database\Connector;
use App\V2\Database\Handler;

class Database
{
	private $db = null;
	private $handler = null;

	public function __construct()
	{
		$this->db = new Connector();
		$this->handler = new Handler();
	}

	public static function connect()
	{
		return (new Connector())->dbConnect();
	}

	public static function errors()
	{
		return (new Handler())->dbError();
	}

	public static function connectDeviceMOrmont()
	{
		return (new Connector())->connectMormont();
	}

	public static function connectDeviceWMSSTR()
	{
		return (new Connector())->connectWMSSTR();
	}
}
