<?php

namespace App\Controllers;

use App\Services\ImportService;

class ImportController
{
	public function __construct()
	{
		$this->import = new ImportService;
	}

	public function importTopTurn()
	{
		renderView("page/import_topturn");
	}

	public function saveImportTopturn()
	{
		$fileExcel = str_replace(" ", "_", $_FILES["import_topturn"]["name"]);
		$type = pathinfo($fileExcel, PATHINFO_EXTENSION);

		$fileExcelRenamed = "topturn." . $type;
		$target_dir = "./resources/topturn/";
		$target_file = $target_dir . $fileExcelRenamed;
		$uploadOk = 1;

		// if ($type === 'ods') {
		// 	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
		// 	$spreadsheet = $reader->load("05featuredemo.ods");
		// }

		if ($type !== "xlsx" && $type !== "ods") {
			echo "File type incorrect! (Please upload only speadsheet file) ";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<br>Sorry, your file was not uploaded.";
			// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["import_topturn"]["tmp_name"], $target_file)) {
				// echo "The file ". basename( $_FILES["import_topturn"]["name"]). " has been uploaded.";
				if (!file_exists($target_file)) {
					echo "File not found.";
				} else {
					if ($type === 'xlsx') {
						$result = self::updateTopTurn(new \SpreadsheetReader($target_file));
					} else {
						$reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
						$result = self::updateTopTurnODS($reader->load($target_file));
						echo '<pre>' . print_r($result, true) . '</pre>';
					}
					// exit;
					if ($result["result"] === true) {
						header("Location: " . APP_ROOT . "/import/topturn?r=success&total=" . $result["total"] .
							"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
					} else {
						header("Location: " . APP_ROOT . "/import/topturn?r=failed&total=" . $result["total"] .
							"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
					}
				}
			} else {
				echo "<br>Sorry, there was an error uploading your file.";
			}
		}
	}

	public function updateTopTurnODS($data)
	{
		$worksheet = $data->getActiveSheet();
		$rows = [];

		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;

		foreach ($worksheet->getRowIterator() as $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
			$cells = [];
			foreach ($cellIterator as $cell) {
				$cells[] = $cell->getValue();
			}
			$rows[] = $cells;
		}

		if (count($rows) <= 1) {
			$errors += 1;
		}

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {
				if ($this->import->isCureTireExist($row[0]) === false) {
					$errors += 1;
				}

				if ($row[1] === "" || $row[1] === null) {
					$row[1] = 0;
				}
				if ($row[2] === "" || $row[2] === null) {
					$row[2] = 0;
				}

				$result = $this->import->updateTopTurn($row[0], $row[1], $row[2]);
				if ($result["result"]  === true) {
					$import += 1;
				} else {
					$not_import += 1;
				}
			}
			$skipHeader++;
		}

		return [
			"result" => true,
			"total" => count($rows) - 2,
			"import" => (count($rows) - 2) - $errors,
			"not_import" => (int) $errors
		];
	}

	public function updateTopTurn($rows)
	{
		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {

				if ($this->import->isCureTireExist($row[0]) === false) {
					$errors += 1;
				} else if ($row[1] <= 0 || $row[2] <= 0) {
					$errors += 1;
				}
			}
			$skipHeader++;
		}

		// if ($errors > 0) {
		// 	// failed
		// 	return [
		// 		"result" => false,
		// 		"total" => count($rows) - 1,
		// 		"import" => $import,
		// 		"not_import" => $not_import
		// 	];
		// }

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {

				if ($row[1] === "" || $row[1] === null) {
					$row[1] = 0;
				}
				if ($row[2] === "" || $row[2] === null) {
					$row[2] = 0;
				}

				$result = $this->import->updateTopTurn($row[0], $row[1], $row[2]);
				$result2 = $this->import->insertTopTurnLog($row[0], $row[1], $row[2]);
				if ($result["result"]  === true) {
					$import += 1;
				} else {
					$not_import += 1;
				}

				// if ($this->import->isTopTurnChange($row[0], $row[1], $row[2]) === false) {
				// 	$result = $this->import->updateTopTurn($row[0], $row[1], $row[2]);
				// 	if ($result["result"]  === true) {
				// 		$import += 1;
				// 	}
				// } else {
				// 	$not_import += 1;
				// }
			}
			$skipHeader++;
		}

		return [
			"result" => true,
			"total" => count($rows) - 2,
			"import" => (count($rows) - 2) - $errors,
			"not_import" => (int) $errors
		];
	}

	public function importCureCode()
	{
		renderView("page/import_curecode");
	}


	public function saveImportCureCode()
	{
		$fileExcel = str_replace(" ", "_", $_FILES["import_curecode"]["name"]);
		$type = pathinfo($fileExcel, PATHINFO_EXTENSION);
		$fileExcelRenamed = "curecode." . $type;
		$target_dir = "./resources/curecode/";
		$target_file = $target_dir . $fileExcelRenamed;
		$uploadOk = 1;

		if ($type !== "xlsx") {
			echo "File type incorrect! (Please upload only excel file) ";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<br>Sorry, your file was not uploaded.";
			// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["import_curecode"]["tmp_name"], $target_file)) {
				// echo "The file ". basename( $_FILES["import_topturn"]["name"]). " has been uploaded.";
				if (!file_exists($target_file)) {
					echo "File not found.";
				} else {
					$result = self::updateCureCode(new \SpreadsheetReader($target_file));
					if ($result["result"] === true) {
						header("Location: " . APP_ROOT . "/import/curecode?r=success&total=" . $result["total"] .
							"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
					} else {
						header("Location: " . APP_ROOT . "/import/curecode?r=failed&total=" . $result["total"] .
							"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
					}
				}
			} else {
				echo "<br>Sorry, there was an error uploading your file.";
			}
		}
	}

	public function updateCureCode($rows)
	{
		$import = 0;
		$not_import = 0;
		foreach ($rows as $row) {
			if ($this->import->isCureTireExist($row[0]) === true) {
				$result = $this->import->updateCureCode($row[0], $row[1], $row[2], $row[3]);
				if ($result === false) {
					break;
					return [
						"result" => false,
						"total" => count($rows) - 1,
						"import" => $import,
						"not_import" => $not_import
					];
				} else {
					$import += 1;
				}
			} else {
				$new = $this->import->createNewCureCode($row[0], $row[1], $row[2], $row[3]);
				$not_import += 1;
			}
		}
		return [
			"result" => true,
			"total" => count($rows) - 1,
			"import" => $import,
			"not_import" => $not_import
		];
	}

	//rate build
	public function importBuildSchedule()
	{
		renderView("page/import_schbuild");
	}

	public function saveImportBuildSchedule()
	{
		$fileExcel = str_replace(" ", "_", $_FILES["import_schbuild"]["name"]);
		$type = pathinfo($fileExcel, PATHINFO_EXTENSION);

		$fileExcelRenamed = "schbuild." . $type;
		$target_dir = "./resources/schbuild/";
		$target_file = $target_dir . $fileExcelRenamed;
		$uploadOk = 1;

		// if ($type === 'ods') {
		// 	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
		// 	$spreadsheet = $reader->load("05featuredemo.ods");
		// }

		if ($type !== "xlsx" && $type !== "ods") {
			echo "File type incorrect! (Please upload only speadsheet file) ";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<br>Sorry, your file was not uploaded.";
			// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["import_schbuild"]["tmp_name"], $target_file)) {
				// echo "The file ". basename( $_FILES["import_topturn"]["name"]). " has been uploaded.";
				if (!file_exists($target_file)) {
					echo "File not found.";
				} else {
					if ($type === 'xlsx') {
						$result = self::updateBuildSchedule(new \SpreadsheetReader($target_file));
					} else {
						$reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
						$result = self::updateBuildScheduleODS($reader->load($target_file));
						echo '<pre>' . print_r($result, true) . '</pre>';
					}
					//  exit;
					if ($result["result"] === true) {
						header("Location: " . APP_ROOT . "/import/schbuild?r=success&total=" . $result["total"] .
							"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
					} else {
						header("Location: " . APP_ROOT . "/import/schbuild?r=failed&total=" . $result["total"] .
							"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
					}
				}
			} else {
				echo "<br>Sorry, there was an error uploading your file.";
			}
		}
	}

	public function updateBuildSchedule($rows)
	{
		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {

				if ($this->import->isSchBuildExist($row[0], $row[1], $row[2], $row[3]) === false) {

					if (($row[0] === "" || $row[0] === null) ||
						($row[2] === "" || $row[2] === null) ||
						($row[1] === "" || $row[1] === null || $row[1] === "1900-01-01 00:00:00.000") ||
						($row[3] === "" || $row[3] === null) ||
						($row[4] === "" || $row[4] === null)
					) {
						$not_import += 1;
					} else {
						$new = $this->import->createNewBuildSch($row[0], $row[1], $row[2], $row[3], $row[4]);
						if ($new === true) {
							$import += 1;
						} else {
							$errors += 1;
						}
					}
				} else {
					if (($row[0] === "" || $row[0] === null) ||
						($row[2] === "" || $row[2] === null) ||
						($row[1] === "" || $row[1] === null || $row[1] === "1900-01-01 00:00:00.000") ||
						($row[3] === "" || $row[3] === null) ||
						($row[4] === "" || $row[4] === null)
					) {
						$not_import += 1;
					} else {
						$result = $this->import->updateBuildSch($row[0], $row[1], $row[2], $row[3]);
						if ($result === false) {
							$errors += 1;
						} else {
							$new = $this->import->createNewBuildSch($row[0], $row[1], $row[2], $row[3], $row[4]);
							if ($new === true) {
								$import += 1;
							} else {
								$errors += 1;
							}
						}
					}
				}
			}
			$skipHeader++;
		}

		return [
			"result" => true,
			"total" => (int) $import + (int) $errors,
			"import" => $import,
			"not_import" => (int) $errors
		];
	}

	public function updateBuildScheduleODS($data)
	{
		$worksheet = $data->getActiveSheet();
		$rows = [];

		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;



		foreach ($worksheet->getRowIterator() as $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
			$cells = [];
			foreach ($cellIterator as $cell) {
				$cells[] = $cell->getValue();
			}
			$rows[] = $cells;
		}

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {
				// $date="15/02/2010";
				// list($d,$m,$y)=explode("/",$row[2]);
				// $daterow2=$y."-".$m."-".$d;

				if ($this->import->isSchBuildExist($row[0], $row[1], $row[2], $row[3]) === false) {
					if (($row[0] === "" || $row[0] === null) ||
						($row[2] === "" || $row[2] === null) ||
						($row[1] === "" || $row[1] === null || $row[1] === "1900-01-01 00:00:00.000") ||
						($row[3] === "" || $row[3] === null) ||
						($row[4] === "" || $row[4] === null)
					) {
						$not_import += 1;
					} else {
						// var_dump($row);
						// echo "1   ".$row[0] . " " . $row[1] . " " . $row[2] ." ".$row[3]." ".$row[4] ."</br>";
						$new = $this->import->createNewBuildSch($row[0], $row[1], $row[2], $row[3], $row[4]);
						if ($new === true) {
							$import += 1;
						} else {
							$errors += 1;
						}
					}
				} else {
					// var_dump($row);
					// echo "2   ".$row[0] . " " . $row[1] . " " . $row[2] ." ".$row[3]." ".$row[4] ."</br>";
					if (($row[0] === "" || $row[0] === null) ||
						($row[2] === "" || $row[2] === null) ||
						($row[1] === "" || $row[1] === null || $row[1] === "1900-01-01 00:00:00.000") ||
						($row[3] === "" || $row[3] === null) ||
						($row[4] === "" || $row[4] === null)
					) {
						$not_import += 1;
					} else {
						// echo "2   ".$row[0] . " " . $row[1] . " " . $row[2] ." ".$row[3]." ".$row[4] ."</br>";
						$result = $this->import->updateBuildSch($row[0], $row[1], $row[2], $row[3]);
						if ($result === false) {
							$errors += 1;
						} else {
							$new = $this->import->createNewBuildSch($row[0], $row[1], $row[2], $row[3], $row[4]);
							if ($new === true) {
								$import += 1;
							} else {
								$errors += 1;
							}
						}
					}
				}
			}
			$skipHeader++;
		}

		//    exit;

		return [
			"result" => true,
			"total" => (int) $import + (int) $errors,
			"import" => $import,
			"not_import" => (int) $errors
		];
	}

	//saba import cure
	public function importCureSchedule()
	{
		renderView("page/import_schcure");
	}

	public function saveImportCureSchedule()
	{
		$fileExcel = str_replace(" ", "_", $_FILES["import_schcure"]["name"]);
		$type = pathinfo($fileExcel, PATHINFO_EXTENSION);

		$fileExcelRenamed = "schcure." . $type;
		$target_dir = "./resources/schcure/";
		$target_file = $target_dir . $fileExcelRenamed;
		$uploadOk = 1;


		// if ($type === 'ods') {
		// 	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
		// 	$spreadsheet = $reader->load("05featuredemo.ods");
		// }

		if ($type !== "xlsx" && $type !== "ods") {
			echo "File type incorrect! (Please upload only speadsheet file) ";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<br>Sorry, your file was not uploaded.";
			// if everything is ok, try to upload file
		} else {
			// print_r($_FILES);
			// exit();
			if (move_uploaded_file($_FILES["import_schcure"]["tmp_name"], $target_file)) {

				// echo "The file ". basename( $_FILES["import_topturn"]["name"]). " has been uploaded.";
				if (!file_exists($target_file)) {
					echo "File not found.";
				} else {
					if ($type === 'xlsx') {

						//$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
						$checkdata = self::CheckreSchedule(new \SpreadsheetReader($target_file));
						// echo "<pre>";
						// print_r($checkdata);
						// echo "</pre>";
						// exit();
						if ($checkdata == 'true') {

							//$result = self::updateCureSchedule($reader->load($target_file));
							$result = self::updateCureSchedule(new \SpreadsheetReader($target_file));
							echo '<pre>' . print_r($result, true) . '</pre>';
						} else {
							echo "NO upload";
						}
					} else {

						$reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
						$checkdata = self::CheckreScheduleODS($reader->load($target_file));
						// echo $checkdata;
						// exit();
						if ($checkdata == 'true') {

							$result = self::updateCureScheduleODS($reader->load($target_file));
							echo '<pre>' . print_r($result, true) . '</pre>';
						} else {
							echo "NO upload";
						}
					}



					if ($result["result"] === true) {
						header("Location: " . APP_ROOT . "/import/schcure?r=success&total=" . $result["total"] .
							"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
					} else {
						header("Location: " . APP_ROOT . "/import/schcure?r=failed&total=" . $result["total"] .
							"&import=" . $result["import"] . "&not_import=" . $result["not_import"]);
					}
				}
			} else {
				echo "<br>Sorry, there was an error uploading your file.";
			}
		}
	}

	public function updateCureSchedule($rows)
	{
		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {

				// $new = $this->import->createNewCureSch($row[0], $row[1], $row[2], $row[3]);
				// if ($new === true) {
				// 	$import += 1;
				// } else {
				// 	$errors += 1;
				// }

				//if ($this->import->isSchBuildExist($row[0], $row[1], $row[2], $row[3]) === false) {

				if (($row[0] === "" || $row[0] === "1900-01-01 00:00:00.000" || $row[0] === null) ||
					($row[2] === "" || $row[2] === null) ||
					($row[1] === "" || $row[1] === null)  ||
					($row[3] === "" || $row[3] === null)

				) {
					$not_import += 1;
				} else {
					$new = $this->import->createNewCureSch($row[0], $row[1], $row[2], $row[3]);
					if ($new === true) {
						$import += 1;
					} else {
						$errors += 1;
					}
				}
				//} else {
				// 	if (($row[0] === "" || $row[0] === null) ||
				// 		($row[2] === "" || $row[2] === null) ||
				// 		($row[1] === "" || $row[1] === null || $row[1] === "1900-01-01 00:00:00.000") ||
				// 		($row[3] === "" || $row[3] === null) ||
				// 		($row[4] === "" || $row[4] === null)
				// 	) {
				// 		$not_import += 1;
				// 	} else {
				// 		$result = $this->import->updateBuildSch($row[0], $row[1], $row[2], $row[3]);
				// 		if ($result === false) {
				// 			$errors += 1;
				// 		} else {
				// 			$new = $this->import->createNewBuildSch($row[0], $row[1], $row[2], $row[3], $row[4]);
				// 			if ($new === true) {
				// 				$import += 1;
				// 			} else {
				// 				$errors += 1;
				// 			}
				// 		}
				// 	}
				// }
			}
			$skipHeader++;
		}

		// $worksheet = $data->getActiveSheet();
		// $rows = [];

		// $import = 0;
		// $not_import = 0;
		// $errors = 0;
		// $skipHeader = 0;





		// foreach ($worksheet->getRowIterator() as $row) {
		// 	$cellIterator = $row->getCellIterator();
		// 	$cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
		// 	$cells = [];
		// 	foreach ($cellIterator as $cell) {
		// 		$cells[] = $cell->getValue();
		// 	}
		// 	$rows[] = $cells;
		// }

		// foreach ($rows as $row) {
		// 	if ($skipHeader >= 1) {
		// 		$new = $this->import->createNewCureSch($row[0], $row[1], $row[2], $row[3]);
		// 		if ($new === true) {
		// 			$import += 1;
		// 		} else {
		// 			$errors += 1;
		// 		}
		// 	}
		// 	$skipHeader++;
		// }


		return [
			"result" => true,
			"total" => (int) $import + (int) $errors,
			"import" => $import,
			"not_import" => (int) $errors
		];
	}



	public function updateCureScheduleODS($data)
	{
		$worksheet = $data->getActiveSheet();
		$rows = [];

		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;



		foreach ($worksheet->getRowIterator() as $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
			$cells = [];
			foreach ($cellIterator as $cell) {
				$cells[] = $cell->getValue();
			}
			$rows[] = $cells;
		}



		foreach ($rows as $row) {
			if ($skipHeader >= 1) {

				$new = $this->import->createNewCureSch($row[0], $row[1], $row[2], $row[3]);
				if ($new === true) {
					$import += 1;
				} else {
					$errors += 1;
				}
			}
			$skipHeader++;
		}


		//    exit;

		return [
			"result" => true,
			"total" => (int) $import + (int) $errors,
			"import" => $import,
			"not_import" => (int) $errors
		];
	}

	public function CheckreScheduleODS($data)
	{
		$worksheet = $data->getActiveSheet();
		$rows = [];

		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;





		foreach ($worksheet->getRowIterator() as $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
			$cells = [];
			foreach ($cellIterator as $cell) {
				$cells[] = $cell->getValue();
			}
			$rows[] = $cells;
		}

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {
				if ($this->import->isSchCheckCureExist($row[0], $row[1], $row[2], $row[3]) === false) {




					$check =  "false";
					break;
				} else {
					$check =  "true";
				}
			}
			$skipHeader++;
		}

		return $check;
	}

	public function CheckreSchedule($rows)
	{
		$import = 0;
		$not_import = 0;
		$errors = 0;
		$skipHeader = 0;

		foreach ($rows as $row) {
			if ($skipHeader >= 1) {
				if (($row[0] === "" || $row[0] === "1900-01-01 00:00:00.000" || $row[0] === null) ||
					($row[2] === "" || $row[2] === null) ||
					($row[1] === "" || $row[1] === null)  ||
					($row[3] === "" || $row[3] === null)

				) {
					$not_import += 1;
				} else {
					if ($this->import->isSchCheckCureExist($row[0], $row[1], $row[2], $row[3]) === false) {
						$check =  "false";
						break;
					} else {
						$check =  "true";
					}
				}
			}
			$skipHeader++;
		}

		return $check;
	}
}
