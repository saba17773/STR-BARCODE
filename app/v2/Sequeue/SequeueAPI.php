<?php

namespace App\V2\Sequeue;

use App\V2\Database\Connector;
use Wattanar\Sqlsrv;

class SequeueAPI
{
  public function updateSequeue($seqId) {
    switch ($seqId) {
      case 'journal_pcr':
        return self::updateJournalPCR();
        break;
      
      default:
        return false;
        break;
    }
  }

  public function getLatestSequeue($seqId) {

    switch ($seqId) {
      case 'journal_pcr':
        return self::getLatestSequeuePCR();
        break;
      
      default:
        return false;
        break;
    }

    
  }

  public function getLatestSequeuePCR() {
    $conn = (new Connector)->dbConnect();

    $getSeq = Sqlsrv::queryArray(
      $conn,
      "SELECT SeqValue
      FROM SeqNumber
      WHERE SeqName = ?",
      [
        'journal_pcr'
      ]
    );

    if ( count($getSeq) === 0) {
      return 0;
    } else {
      return JOURNAL_PCR_PREFIX . str_pad($getSeq[0]['SeqValue'], 8, "0", STR_PAD_LEFT);
    }
    
  }

  public function updateJournalPCR() {
    $conn = (new Connector)->dbConnect();

    $update = sqlsrv_query(
      $conn,
      "UPDATE SeqNumber
      SET SeqValue += 1
      WHERE SeqName = 'journal_pcr'"
    );

    if (!$update) {
      return false;
    } else {
      return true;
    }
  }
}