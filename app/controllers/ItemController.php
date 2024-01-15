<?php

namespace App\Controllers;

use App\Services\ItemService;
use App\Models\Item;

class ItemController
{
	private $item = null;
	private $itemService = null;

	public function __construct()
	{
		$this->item = new Item();
		$this->itemService = new ItemService();
	}

	public function all()
	{
		echo $this->itemService->all();
	}

	public function allBrand()
	{
		echo $this->itemService->allBrand();
	}

	public function getItemSet()
	{
		echo $this->item->getItemSet();
	}

	public function getItemNormal()
	{
		echo $this->item->getItemNormal();
	}

	public function getItemGroupSM()
	{
		echo $this->item->getItemGroupSM();
	}

	public function syncItem()
	{
		$sync = $this->itemService->syncItem();

		if ($sync === true) {
			return json_encode([
				'result' => true,
				'message' => 'Sync Success!'
			]);
		} else {
			return json_encode([
				'result' => false,
				'message' => 'Sync Failed!'
			]);
		}
	}
}
