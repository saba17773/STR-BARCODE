<?php

namespace App\Controllers;

use App\Services\InventService;

class InventController
{
	private $invent = null;

	public function __construct()
	{
		$this->invent = new InventService();
	}

	public function allInventTable()
	{
		echo $this->invent->allInventTable();
	}

	public function transDetail($barcode)
	{
		echo $this->invent->transDetail($barcode);
	}

	public function countReceiveToWarehouseFromFinal($product_group)
	{
		echo json_encode([
			"count" => count(json_decode($this->invent->countReceiveToWarehouseFromFinal($product_group)))
		]);
	}

	public function countFinalToWh($Id)
	{
		echo json_encode([
			"count" => count(json_decode($this->invent->countFinalToWh($Id)))
		]);
	}
}
