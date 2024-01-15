<?php

namespace App\TireGroup_airwaybill;

use App\Common\Database;
use App\Common\Automail;
use Webmozart\Assert\Assert;

class TgaAPI {

	public function __construct() {
		$this->db_ax = Database::connect('ax');
		$this->db_live = Database::connect();
		$this->automail = new Automail;
	}

	public function isSurrender($file) {
		try {
			if (preg_match("/-SURRENDER/i", $file)) {
				return true;
			} else {
				return false;
			}
		} catch (\Exception $e) {
			throw new \Exception('Error: File in correct.');
		}
	}

	public function getLogs($filter) {
		try {
			$data = Database::rows(
				$this->db_live,
				"SELECT TOP 50
				[Message],
				CustomerCode,
				[FileName],
				SendDate
				FROM Logs
				WHERE ProjectID = 13
				AND $filter
				ORDER BY ID DESC"
			);
			return $data;
		} catch (\Exception $e) {
			return [];
		}
	}

	public function getAirWayBillData($time_set){
		$query = Database::rows(
			$this->db_live,
			"SELECT
				SL.DSG_SALESID,
				SL.DSG_PACKINGSLIPID,
				ST.CUSTACCOUNT,
				ST.SALESNAME,
				ST.DSG_SHIPPINGLINEDESCRIPTION [SHIPPINGLINE],
				ST.DSG_TOPORTID [CONDITION],
				(
					SELECT TOP 1 SLL.DSG_AFTERVALUE FROM DSG_SALESLOG SLL
					WHERE SLL.DSG_SALESID = SL.DSG_SALESID
					AND SLL.DSG_SALESLOGCATEGORY = 17
					ORDER BY SLL.CREATEDDATE DESC
				) [AFTERVALUE],
				CT.NAME [CUSTNAME],
				ST.QUOTATIONID,
				ST.CUSTOMERREF,
				ST.DSG_ToPortDesc [TOPORT],
				DA.DSG_DESCRIPTION [AGENT],
				KP.DSG_COURIERNAME,
				CJ.DSG_AWB_NO,
				CJ.DSG_ETD,
				CJ.DSG_ETA,
				CASE WHEN IV.DATAAREAID = 'DSR' THEN 'SVO/'+IV.SERIES +'/' + CONVERT(NVARCHAR(10),IV.VOUCHER_NO)ELSE  UPPER(IV.DATAAREAID) + '/'+ IV.SERIES +'/' + CONVERT(NVARCHAR(10),IV.VOUCHER_NO)  END [INVNO]
				FROM DSG_SALESLOG SL
				LEFT JOIN SALESTABLE ST ON
					SL.DSG_SALESID = ST.SALESID
					AND ST.DATAAREAID = 'dsc'
				LEFT JOIN CustPackingSlipJour CJ ON
					CJ.SALESID = SL.DSG_SALESID
					AND CJ.DATAAREAID = 'dsc'
					AND CJ.INVOICEACCOUNT = ST.CUSTACCOUNT
					AND SL.DSG_PACKINGSLIPID = CJ.PACKINGSLIPID
				LEFT JOIN INVENTPICKINGLISTJOUR IV ON
					IV.ORDERID = SL.DSG_SALESID
					AND IV.DATAAREAID = 'dsc'
					AND IV.CUSTACCOUNT = ST.CUSTACCOUNT
				LEFT JOIN DSG_AGENTTABLE DA ON
					DA.DSG_AGENTID = ST.DSG_PRIMARYAGENTID
				LEFT JOIN CUSTTABLE CT ON
					CT.ACCOUNTNUM = ST.CUSTACCOUNT
					AND CT.DATAAREAID = 'dsc'
				LEFT JOIN DSG_KPICOURIER KP ON
					KP.DSG_COURIERID = CJ.DSG_COURIERID
				WHERE SL.CREATEDDATE >= ?
				AND SL.CREATEDDATE <= ?
				AND CONVERT(time, dateadd(s, SL.CREATEDTIME , '19700101')) >= ?
				AND CONVERT(time, dateadd(s, SL.CREATEDTIME , '19700101')) <= ?
				AND SL.DSG_DATAAREAID = 'dsc'
				AND SL.DSG_SALESLOGCATEGORY = '17'
				AND ST.DLVMODE = 'SHIP'
				AND CJ.DSG_AIRWAYBILLSENTEMAIL = '0'
				AND ST.CUSTACCOUNT = 'C-1089'
				GROUP BY
				SL.DSG_SALESID,
				SL.DSG_PACKINGSLIPID,
				ST.SALESNAME,
				ST.DSG_SHIPPINGLINEDESCRIPTION,
				ST.CUSTACCOUNT,
				ST.DSG_TOPORTID,
				CJ.DSG_VESSEL,
				CJ.DSG_FEEDER,
				CT.NAME,
				ST.QUOTATIONID,
				ST.CUSTOMERREF,
				ST.DSG_ToPortDesc,
				DA.DSG_DESCRIPTION,
				KP.DSG_COURIERNAME,
				CJ.DSG_AWB_NO,
				CJ.DSG_ETD,
				CJ.DSG_ETA,
				CASE WHEN IV.DATAAREAID = 'DSR' THEN 'SVO/'+IV.SERIES +'/' + CONVERT(NVARCHAR(10),IV.VOUCHER_NO)ELSE  UPPER(IV.DATAAREAID) + '/'+ IV.SERIES +'/' + CONVERT(NVARCHAR(10),IV.VOUCHER_NO)  END",
				[
					$time_set['start_date'],
					$time_set['end_date'],
					$time_set['start_time'],
					$time_set['end_time']
				]


		);
		return $query;
	}


}
