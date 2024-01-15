<?php

define("APP_ROOT", '');
define("APP_NAME", "STR Barcode TBR Tracking");
define("BARCODE_PREFIX", "5");
define("BARCODE_LENGTH", "11");
define("JOURNAL_PCR_PREFIX", "PCR");
define("WH_PALLET", "WHPL");
define("BASE_URL", "http://lanister.deestonegrp.com:3311");
define("BATCH_DSC", "-D5");

// if ((int) date('y') === 20) {
//   define("USE_ITEMQ", true);
// } else {
//   define("USE_ITEMQ", false);
// }

$dateStartItemQ = date('Y-m-d H:i');
if ($dateStartItemQ >= "2019-12-31 20:00") {
  define("USE_ITEMQ", true);
} else {
  define("USE_ITEMQ", false);
}

// disposal
// pallet receive = 25
