<?php

namespace App\Controllers;

use App\Models\ItemSet;

class ItemSetController
{
	public function __construct()
	{
		$this->itemset = new ItemSet;
	}

	public function index()
	{
		renderView("page/itemset");
	}

	public function fetchAll()
	{
		echo $this->itemset->fetchAll();
	}

	public function save()
	{
		$item_set_id = filter_input(INPUT_POST, "item_set_id");
		$item_id = filter_input(INPUT_POST, "item_id");

		$t = new ItemSet;
		$t->item_set_id = $item_set_id;
		$t->item_id = $item_id;

		if ($t->save()) {
			return json_encode([
				"result" => true
			]);
		} else {
			return json_encode([
				"result" => false
			]);
		}
	}

	public function update()
	{
		$item_set_id = filter_input(INPUT_POST, "item_set_id");
		$item_id = filter_input(INPUT_POST, "item_id");
		$id = filter_input(INPUT_POST, "id");

		$t = new ItemSet;
		$t->item_set_id = $item_set_id;
		$t->item_id = $item_id;
		$t->id = $id;

		if ($t->update()) {
			return json_encode([
				"result" => true
			]);
		} else {
			return json_encode([
				"result" => false
			]);
		}
	}

	public function printItem($item) 
	{
		renderView("pdf/item_set", ["item" => $item]);
	}

}