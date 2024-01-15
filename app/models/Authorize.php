<?php

namespace App\Models;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class Authorize
{
    public $ID = null;
    public $Description = null;
    public $Unhold_Unrepair_GT = null;
    public $Unhold_Unrepair_Final = null;
    public $Loading = null;
    public $Adjust_GT = null;
    public $Adjust_Final = null;
    public $Adjust_FG = null;
    public $MovementReverse = null;
    public $Unbom = null;

    public function isAuthorize($field_name)
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT * FROM AuthorizeMaster
            WHERE $field_name = 1
            AND ID = ?",
            [
                $this->ID
            ]
        );
    }
}