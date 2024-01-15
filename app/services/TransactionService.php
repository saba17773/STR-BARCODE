<?php

namespace App\Services;

use App\Components\Database;

class TransactionService
{
	
	public function  checkbarcode($barcode	='')
	{
		$conn = Database::connect();
		$barcode = (int)substr($barcode, 1);
		$result = "SELECT TOP 1 * FROM BarcodePrinting
				   WHERE CONVERT(nvarchar, SUBSTRING(StartBarcode, 2, 11)) <= ?
				 AND CONVERT(nvarchar, SUBSTRING(FinishBarcode, 2, 11)) >= ?";
	    $params = array($barcode,$barcode);
		$query = sqlsrv_query($conn,$result,$params);

		
		return  sqlsrv_has_rows($query);
	}
	public function  checktransaction($barcode	='')
	{
	    $conn = Database::connect();
		$result = "SELECT *
		FROM INVENTTABLE T
		WHERE T.Barcode = '$barcode'";
		$query = sqlsrv_query($conn, $result);

		
		$row_count = sqlsrv_has_rows($query);
		
		return  $row_count;
	}
	public function  checkstatus($barcode	='',$status = 0)
	{
	    $conn = Database::connect();
		$result = "SELECT *
		FROM INVENTTABLE T
		WHERE T.Barcode = ?
		AND T.Status = ?";
		$params = array($barcode,$status);
		$query = sqlsrv_query($conn, $result,$params);

		$row_count = sqlsrv_has_rows($query);
		
		return  $row_count;
	}
	
	public function checkonhand($codeid  = '',$warehouseid ='',$locationid='' , $batch='')
	{
		$conn = Database::connect();
		$sql ="SELECT SUM(QTY)[QTY]
				FROM Onhand O
				WHERE O.CodeID =?
				AND O.WarehouseID= ?
				AND O.LocationID = ?";
				//AND (O.Batch = ? OR  O.Batch IS NULL)
				//GROUP BY O.CodeID,O.WarehouseID,O.LocationID,O.Batch
		$params = array($codeid,$warehouseid,$locationid);
		$query = sqlsrv_query($conn,$sql,$params);
		$onhand = sqlsrv_fetch_object($query);

		if($onhand->QTY === null)
		{
			return 0;
		}
		else
		{
			return $onhand->QTY;
		}
	}
	public function checkonhandinventtrans($barcode='',$codeid  = '',$warehouseid ='',$locationid='' , $batch = NULL)
	{
		$conn = Database::connect();
		$result ="SELECT SUM(QTY)[QTY]
				FROM InventTrans T
				WHERE T.Barcode = ?
				AND T.CodeID =?
				AND T.WarehouseID= ?
				AND T.LocationID = ?
				AND (T.Batch = ? OR  T.Batch IS NULL)
				GROUP BY T.Barcode,T.CodeID,T.WarehouseID,T.LocationID,T.Batch";
		$params = array($barcode,$codeid,$warehouseid,$locationid,$batch);
		$query = sqlsrv_query($conn,$result,$params);
		$onhand = sqlsrv_fetch_object($query);
		
		if($onhand->QTY === null)
		{
			return 0;
		}
		else
		{
			return $onhand->QTY;
		}
	}
	public function checkWhLocation($codeid  = '',$warehouseid ='',$locationid='' , $Batch='')
	{
		$conn = Database::connect();
		$result = "SELECT  *
		FROM Onhand O
		WHERE O.CodeID = '$codeid'
		AND O.WarehouseID = '$warehouseid'
		AND O.LocationID  = '$locationid'
		AND (O.Batch = '$Batch' OR O.Batch IS NULL) ";
		//GROUP BY O.CodeID,O.WarehouseID,O.LocationID,O.LocationID,O.Batch";
		$query = sqlsrv_query($conn,$result);
		$row_count = sqlsrv_has_rows($query);
		return  $row_count;
	}
	public function generateTransId($barcode)
   	{
		return $barcode.substr(date("YmdHis"),2).round(microtime(true) * 1000);
   	}
 
 	public function  lastrecordbarcode($barcode	='')
	{
		
		$conn = Database::connect();
		$sql = "SELECT TOP 1 TT.TransID[TT_TRANSID],
			TT.Barcode[TT_BARCODE],
			TT.CodeID[TT_CODEID],
			TT.Batch[TT_BATCH],
			TT.DisposalID[TT_DISPOSALID],
			TT.DefectID[TT_DEFECTID],
			TT.WarehouseID[TT_WAREHOUSEID],
			TT.LocationID[TT_LOCATIONID],
			TT.QTY[TT_QTY],
			TT.UnitID[TT_UNITID],
			TT.DocumentTypeID[TT_DOCUMENTTYPEID],
			TT.Company[TT_COMPANY],
			T.ID[T_ID],
			T.Barcode[T_BARCODE],
			T.DateBuild[T_DATEBUILD],
			T.BuildingNo[T_BUILDINGNO],
			T.GT_Code[T_GTCODE],
			T.CuringDate[T_CURINGDATE],
			T.WarehouseID[T_WAREHOUSEID],
			T.LocationID[T_LOCATIONID],
			T.Status[T_STATUS],
			T.Company[T_COMPANY]

			FROM InventTrans TT JOIN InventTable T
								ON T.Barcode = TT.Barcode
								AND T.WarehouseID = TT.WarehouseID
								AND T.LocationID = TT.LocationID
								AND T.Company = TT.Company
			WHERE TT.Barcode = ?
			ORDER BY TT.TransID DESC";
		    $params = array($barcode);
			$query = sqlsrv_query($conn,$sql,$params);
			$record = [];
            while ($res = sqlsrv_fetch_object($query))
            {
            	$record[] = $res;
            }

            return $record;
	}

	public function transfer($barcode	='',	$fromcodeid ='',	$fromwarehouse ='',		$fromlocation ='',		$frombatch ='',
						  	$tocodeid 	='',	$towarehouse='',	$tolocation	   ='',		$tobatch	  ='',
						  	$fromstatus ='',	$tostatus='',
						  	$disposalid ='',    $documenttype_in ='1',$documenttype_out = '2'
						    )
	{
		$conn 			= Database::connect();
		$transid_out 	= self::generateTransId($barcode);
		$res     		= self::lastrecordbarcode($barcode);
		// $tolocation    	= 9;
		$createBy       = $_COOKIE['user_login'];
		
		// $documenttype_in	= 1;
		// $documenttype_out	= 2;
		
		
		if (sqlsrv_begin_transaction($conn) === false ) 
		{
	    	die( print_r( sqlsrv_errors(), true ));
	    }
	    	
	    	//>>ขาลบInventtrans
	         $sql =  "INSERT INTO InventTrans (
	         			TransID,Barcode,CodeID,Batch,DisposalID,DefectID,WarehouseID,
	         			LocationID,QTY,UnitID,DocumentTypeID,Company,CreateBy,CreateDate
	         		 )VALUES(
	         		 	?,?,?,?,?,?,?,?,?,?,?,?,?,GETDATE()
	         		 )";
			$params = array($transid_out,$barcode,$res[0]->TT_CODEID,$res[0]->TT_BATCH,
        		$res[0]->TT_DISPOSALID,$res[0]->TT_DEFECTID,$res[0]->TT_WAREHOUSEID,
        		$res[0]->TT_LOCATIONID,-1,$res[0]->TT_UNITID,$documenttype_out,
        		$res[0]->TT_COMPANY,$createBy 
        		);
        	$inventtrans_out = sqlsrv_query($conn,$sql,$params);
        	if(!$inventtrans_out)
        	{
        		sqlsrv_rollback($conn);
        		return "inventtrans_out error ติดต่อ Admin";
        	}
	      	//<<ขาลบInventtrans
	      	//>>ขาบวกInventtrans
	      	
	      		$transid_in = self::generateTransId($barcode);
		      	while($transid_in == $transid_out)
		      	{
		      		$transid_in = self::generateTransId($barcode);
		      	}
		      	//echo $transid;
		      	$sql1 =  "INSERT INTO InventTrans (
				         			TransID,Barcode,CodeID,Batch,DisposalID,DefectID,WarehouseID,
				         			LocationID,QTY,UnitID,DocumentTypeID,Company,CreateBy,CreateDate
		         		 )VALUES(
		         		 	?,?,?,?,?,?,?,?,?,?,?,?,?,GETDATE()
		         		 )";
				$params1 = array($transid_in,$barcode,$res[0]->TT_CODEID,$res[0]->TT_BATCH,
				        		$disposalid,$res[0]->TT_DEFECTID,$res[0]->TT_WAREHOUSEID,
				        		$tolocation,1,$res[0]->TT_UNITID,$documenttype_in,
				        		$res[0]->TT_COMPANY,$createBy 
				        		);
		        $inventtrans_in = sqlsrv_query($conn,$sql1,$params1);
		        if(!$inventtrans_in)
	        	{
	        		sqlsrv_rollback($conn);
	        		return "inventtrans_in error ติดต่อ Admin";
	        	}
	        
	        // <<ขาบวกInventtrans
	        // >>update_Inventtable
	        $sql2 	="UPDATE InventTable
	                    SET DisposalID = ?,
	                    LocationID = ?,
	                    Status = ?,
	                    UPDATEBY = ?,
	                    UPDATEDATE = GETDATE()
	                    
	                    WHERE barcode =?";
	        $params2 = array($disposalid,$tolocation,$tostatus,$createBy,$barcode);
	        $update_inventtable	 = sqlsrv_query($conn,$sql2,$params2);
	        if(!$update_inventtable)
        	{
        		sqlsrv_rollback($conn);
        		return "update_inventtable error ติดต่อ Admin";
        	}
	        // <<update_Inventtable
	       
	        //>>update onhand ขาบวก
	       
		        if(self::checkWhLocation($tocodeid,$towarehouse,$tolocation,$tobatch))
		        {
			      //   $sql_onhand_in 	="UPDATE Onhand
									// SET QTY= QTY+1
									// FROM Onhand
									// WHERE Onhand.CodeID = ?
									// AND Onhand.WarehouseID = ?
									// AND Onhand.LocationID  = ?
									// AND Onhand.Batch = ? ";
							
		       //  	$params_onhand_in = array($tocodeid,$towarehouse,$tolocation,$tobatch);
		       //  	$update_onhand_in	 = sqlsrv_query($conn,$sql_onhand_in,$params_onhand_in);
		       //  	if(!$update_onhand_in)
		       //  	{
		       //  		sqlsrv_rollback($conn);
		       //  		return "update_onhand_in error ติดต่อ Admin";
		       //  	}
	           	}
	           	else
	           	{
	          //   	$sql_onhand_in = "INSERT INTO ONHAND (CodeID,WarehouseID,LocationID,Batch,QTY,Company)
	          //   					VALUES (?,?,?,?,1,'STR')";
	          //   	$params_onhand_in = array($tocodeid,$towarehouse,$tolocation,$tobatch);
	          //   	$update_onhand_in	 = sqlsrv_query($conn,$sql_onhand_in,$params_onhand_in);
	          //   	if(!$update_onhand_in)
		        	// {
		        	// 	sqlsrv_rollback($conn);
		        	// 	return "update_onhand_in error ติดต่อ Admin";
		        	// }
	       		}
	       	
	        //<<update onhand ขาบวก
	        //>>update onhand ขาบลบ
        	
	       
	    //     $sql_onhand_out 	="UPDATE Onhand
     //                SET QTY = QTY-1
     //                FROM Onhand 
					// WHERE Onhand.CodeID = ?
					// AND Onhand.WarehouseID = ?
					// AND Onhand.LocationID  = ?
					// AND (Onhand.Batch = ? OR  Onhand.Batch IS NULL)";
        	
        	// $params_onhand_out = array($fromcodeid,$fromwarehouse,$fromlocation,$frombatch);
        	// $update_onhand_out	 = sqlsrv_query($conn,$sql_onhand_out,$params_onhand_out);
        	// if(!$update_onhand_out)
        	// {
        	// 	sqlsrv_rollback($conn);
        	// 	return "update_onhand_out error ติดต่อ Admin";
        	// }
	        //<<update onhand ขาลบ
	     //echo $transid_out."-".$transid_in; 
        // if($inventtrans_out)
        // {
        // 	echo "1inventtrans_out";
        // }
        // if($inventtrans_in)
        // {
        // 	echo "2inventtrans_int";
        // }
        // if($update_inventtable)
        // {
        // 	echo "3update_inventtable";
        // }
        // if($update_onhand_in)
        // {
        // 	echo "4sql_onhand_in";
        // }
        // if($update_onhand_out)
        // {
        // 	echo "5sql_onhand_out";
        // }

        // sqlsrv_rollback($conn);
        // 	return $inventtrans_out."-".$inventtrans_in;
        //exit();
	    // if($inventtrans_out && $inventtrans_in && $update_inventtable && $update_onhand_in && $update_onhand_out){
     //    	sqlsrv_commit($conn);
     //    	return 'success Full';
     //    }
     //    else
     //    {
     //    	sqlsrv_rollback($conn);
     //    	return 'fail admin ';
     //    }

        sqlsrv_commit($conn);
        	return 'success Full';



	}
	public function adjust($barcode	='',	$fromcodeid ='',	$fromwarehouse ='',		$fromlocation ='',		$frombatch ='',
						   $tostatus='',
						   $disposalid ='',  
						   $defectid = '0',  
						   $documenttype_in ='1',$documenttype_out = '2'
						   )
	{
		$conn 			= Database::connect();
		$transid_out 	= self::generateTransId($barcode);
		$res     		= self::lastrecordbarcode($barcode);
		$createBy       = 1;
		
		

		if (sqlsrv_begin_transaction($conn) === false ) 
		{
	    	die( print_r( sqlsrv_errors(), true ));
	    }
	    	
	    	//>>ขาลบInventtrans
	          $sql =  "INSERT INTO InventTrans (
	         			TransID,Barcode,CodeID,Batch,DisposalID,DefectID,WarehouseID,
	         			LocationID,QTY,UnitID,DocumentTypeID,Company,CreateBy,CreateDate
	         		 )VALUES(
	         		 	?,?,?,?,?,?,?,?,?,?,?,?,?,GETDATE()
	         		 )";
			$params = array($transid_out,$barcode,$res[0]->TT_CODEID,$res[0]->TT_BATCH,
        		$res[0]->TT_DISPOSALID,$res[0]->TT_DEFECTID,$res[0]->TT_WAREHOUSEID,
        		$res[0]->TT_LOCATIONID,-1,$res[0]->TT_UNITID,$documenttype_out,
        		$res[0]->TT_COMPANY,$createBy 
        		);
        	$inventtrans_out = sqlsrv_query($conn,$sql,$params);
	      	//<<ขาลบInventtrans
	      	
	        // >>update_Inventtable
	        $sql2 	="UPDATE InventTable
	                    SET DisposalID = ?,
	                    LocationID = ?,
	                    Status = ?,
	                    UPDATEBY = ?,
	                    UPDATEDATE = GETDATE()
	                    
	                    WHERE barcode =?";
	        $params2 = array($disposalid,$fromlocation,$tostatus,$createBy,$barcode);
	        $update_inventtable	 = sqlsrv_query($conn,$sql2,$params2);
	        
	        // <<update_Inventtable
	       
	       
	        //>>update onhand ขาลบ
        	
	    //     $sql_onhand_out 	="UPDATE Onhand
     //                SET QTY = QTY-1
     //                FROM Onhand
					// WHERE Onhand.CodeID = ?
					//  AND Onhand.WarehouseID = ?
					//  AND Onhand.LocationID  = ?
					//  AND Onhand.Batch = ?";
     //    	//echo $sql_onhand_out;
     //    	$params_onhand_out = array($fromcodeid,$fromwarehouse,$fromlocation,$frombatch);
     //    	$update_onhand_out	 = sqlsrv_query($conn,$sql_onhand_out,$params_onhand_out);

	        //<<update onhand ขาลบ
	  
	  	  // <<update onhand ขาลบ

	        sqlsrv_commit($conn);
        	return 'success Full';
	    
	    // if($inventtrans_out && $update_inventtable && $update_onhand_out){
     //    	sqlsrv_commit($conn);
     //    	return 'success Full';
     //    }
     //    else
     //    {
     //    	sqlsrv_rollback($conn);
     //    	return 'fail contact admin ';
     //    }



	}

}