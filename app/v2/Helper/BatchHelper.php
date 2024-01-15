<?php

namespace App\V2\Helper;

class BatchHelper
{
  public function isBatchFormat(string $batch)
  {
    if (\strlen($batch) === 7) {
      return true;
    } else {
      return false;
    }
  }
}