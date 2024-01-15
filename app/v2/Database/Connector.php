<?php

namespace App\V2\Database;

use Wattanar\Sqlsrv;

class Connector
{
  public static $svo_tiresorting = null;
  public static $str_barcode = null;
  public static $wms = null;                 
  public static $mormont = null;
  public static $wms_str = null;

  public static function getInstance($server = null)
  {
    try {
      switch ($server) {
        case 'str_barcode':
          if (!isset(self::$str_barcode)) {
            //DEV
            self::$str_barcode = Sqlsrv::connect(
              "xxx",
              "xxx",
              "xx",
              "xxx"
            );
           
            return self::$str_barcode;
          }
          return self::$str_barcode;
          break;

        case 'svo_tiresorting':
          if (!isset(self::$svo_tiresorting)) {
            self::$svo_tiresorting = Sqlsrv::connect(
              "juno\develop",
              "EAconnection",
              "l;ylfu;yo0yomiN",
              "SVO_TIRESORTING_PRD"
            );
            return self::$svo_tiresorting;
          }
          return self::$svo_tiresorting;
          break;
        case 'mormont':
          if (!isset(self::$mormont)) {
            self::$mormont = Sqlsrv::connect(
              "mormont\develop",
              "EAconnection",
              "l;ylfu;yo0yomiN",
              "HardwareManagement"
            );
            return self::$mormont;
          }
          return self::$mormont;
          break;

        case 'wms':
          if (!isset(self::$wms)) {
            self::$wms = Sqlsrv::connect(
              "mormont\interface",
              "EAconnection",
              "l;ylfu;yo0yomiN",
              "WMSInterface"
            );
            return self::$wms;
          }
          return self::$wms;
          break;

        case 'wms_str':
          if (!isset(self::$wms_str)) {
            self::$wms_str = Sqlsrv::connect(
              "grape\develop",
              "EAconnection",
              "l;ylfu;yo0yomiN",
              "WMSInterface_Live"
            );
            return self::$wms_str;
          }
          return self::$wms_str;
          break;

        default:
          return null;
          break;
      }
    } catch (\Exception $e) {
      return "Error: " . $e->getMessage();
    }
  }

  public function dbConnect()
  {
    return self::getInstance('str_barcode');
  }

  public function connectSVO()
  {
    return self::getInstance('svo_tiresorting');
  }

  public function connectWMS()
  {
    return self::getInstance('wms');
  }

  public function connectMormont()
  {
    return self::getInstance('mormont');
  }

  public function connectWMSSTR()
  {
    return self::getInstance('wms_str');
  }
}