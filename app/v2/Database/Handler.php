<?php

namespace App\V2\Database;

class Handler
{
  public function dbError()
  {
    $txt = "";
		if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				$txt .= "message: ".$error[ 'message'];
			}
		}
		return $txt;
  }
}