<?php

function renderView($path, $data = null)
{
	$templates = new \League\Plates\Engine("./views");
	if (isset($data)) {
		echo $templates->render($path, $data);
	} else {
		echo $templates->render($path);
	}
	return;
}

function _d($log = null)
{
	if ($log === null) {
		$_SESSION['_d'] = "";
	} else {
		$_SESSION['_d'] .= $log . PHP_EOL;
	}
	return;
}

function jsonResult($result, $message, $extra = [])
{
	return [
		'result' => $result,
		'message' => $message,
		'extra' => $extra
	];
}
