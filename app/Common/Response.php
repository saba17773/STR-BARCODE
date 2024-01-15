<?php

namespace App\Common;

class Response
{

  public function json($result, $message, $data = [])
  {
    header("Content-Type: application/json;");
    return json_encode([
      "result" => $result,
      "message" => $message,
      "data" => $data
    ]);
  }

  public function array($result, $message, $data = [])
  {
    return [
      "result" => $result,
      "message" => $message,
      "data" => $data
    ];
  }
}
