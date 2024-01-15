<?php

require_once '../../vendor/autoload.php';
require_once "../../variables.php";

use App\V2\Database\Connector;
use Wattanar\SqlsrvHelper;
use Wattanar\Requesty;

$conn = (new Connector)->dbConnect();

$data = (new SqlsrvHelper)->getRows(sqlsrv_query(
	$conn,
	"SELECT 
	V.barcode, 
	V.[status], 
	V.[message], 
	V.create_date, 
	T.WarehouseTransReceiveDate ,
	T.WarehouseReceiveDate
	FROM BarcodeReaderLogs V
	LEFT JOIN InventTable T ON T.barcode = V.barcode
	WHERE T.WarehouseTransReceiveDate is null
	AND V.status = 200
	AND V.create_date > '2018-12-27 00:00:01'
	ORDER BY V.create_date DESC"
));

if (count($data) > 0) {
	foreach ($data as $v) {
		$response = Requesty::post(BASE_URL . '/api/xray/issue/wh',[
			'barcode' => $v['barcode'],
			'from' => 'sys'
		]);

		$result = json_decode($response);
		$msg = $v['barcode'] . ' : ' . $result->status . ' : ' . $result->message;
		echo $msg . PHP_EOL;
		file_put_contents('./logs/auto_resend_to_wh_' . date('Ymd') . '.txt', $msg.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}