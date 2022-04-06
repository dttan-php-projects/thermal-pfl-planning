<?php

	header("Content-Type: application/json");

	$UPDATED_BY = isset($_COOKIE['VNRISIntranet']) ? $_COOKIE['VNRISIntranet'] : '';
	$UPDATED_DATE = date('Y-m-d H:i:s');

	if(!empty($_POST['data'])){
		$formatData = json_decode($_POST['data'],true);
		if(!empty($formatData)){
			//connect to database
				require_once("../Database.php");
				$conn = getConnection();
				$table = "master_item";
				
			// get data
				$ITEM_NUMBER = !empty($formatData['ITEM_NUMBER'])?addslashes($formatData['ITEM_NUMBER']):'';
				$ITEM_CODE = !empty($formatData['ITEM_CODE'])?addslashes($formatData['ITEM_CODE']):'';
				$ITEM_RM = !empty($formatData['ITEM_RM'])?addslashes($formatData['ITEM_RM']):'';
				$DES_RM = !empty($formatData['DES_RM'])?addslashes($formatData['DES_RM']):'';
				$PRINTING = !empty($formatData['PRINTING'])?addslashes($formatData['PRINTING']):'';
				$RBO = !empty($formatData['RBO'])?addslashes($formatData['RBO']):'';
				$WIDTH = !empty($formatData['WIDTH'])?$formatData['WIDTH']:0;
				$HEIGHT = !empty($formatData['HEIGHT'])?$formatData['HEIGHT']:0;
				$ITEM_INK = !empty($formatData['ITEM_INK'])?addslashes($formatData['ITEM_INK']):'';
				$DES_INK = !empty($formatData['DES_INK'])?addslashes($formatData['DES_INK']):'';
				$SO_MAT_IN = !empty($formatData['SO_MAT_IN'])?$formatData['SO_MAT_IN']:0;
				$MACHINE = !empty($formatData['MACHINE'])?addslashes($formatData['MACHINE']):'';
				$CUT_TYPE = !empty($formatData['CUT_TYPE'])?addslashes($formatData['CUT_TYPE']):'';
				$NOTE = !empty($formatData['NOTE'])?addslashes($formatData['NOTE']):'';
				$CUT_MACHINE = !empty($formatData['CUT_MACHINE'])?addslashes($formatData['CUT_MACHINE']):'';
				$BRAND_PROTECTION = !empty($formatData['BRAND_PROTECTION'])?addslashes($formatData['BRAND_PROTECTION']):'';
				$idUA = $formatData['ITEM_ID'];
			// check 
				if(strpos($idUA,'new_id_')!==false){  // insert
					if($ITEM_NUMBER){
						$sql = "INSERT INTO $table 
						(`ITEM_NUMBER`,`ITEM_CODE`, `ITEM_RM`,`DES_RM`,`PRINTING`,`RBO`,`WIDTH`,`HEIGHT`,`ITEM_INK`,`DES_INK`,`SO_MAT_IN`,`MACHINE`,`CUT_TYPE`,`NOTE`, `CUT_MACHINE`, `BRAND_PROTECTION`, `UPDATED_BY`) VALUES ('$ITEM_NUMBER','$ITEM_CODE','$ITEM_RM','$DES_RM','$PRINTING','$RBO','$WIDTH','$HEIGHT','$ITEM_INK','$DES_INK','$SO_MAT_IN','$MACHINE','$CUT_TYPE','$NOTE','$CUT_MACHINE','$BRAND_PROTECTION', '$UPDATED_BY')";
						$check_1 = $conn->query($sql);
						$insert_id = $conn->insert_id;
					}			
				}else{
					// update
					$sql = "UPDATE $table SET `ITEM_NUMBER`='$ITEM_NUMBER',`ITEM_CODE`='$ITEM_CODE',`ITEM_RM`='$ITEM_RM',`DES_RM`='$DES_RM',`PRINTING`='$PRINTING',`RBO`='$RBO',`WIDTH`='$WIDTH',`HEIGHT`='$HEIGHT',`ITEM_INK`='$ITEM_INK',`DES_INK`='$DES_INK',`SO_MAT_IN`='$SO_MAT_IN',`MACHINE`='$MACHINE',`CUT_TYPE`='$CUT_TYPE',`NOTE`='$NOTE',`CUT_MACHINE`='$CUT_MACHINE',`BRAND_PROTECTION`='$BRAND_PROTECTION',`UPDATED_BY`='$UPDATED_BY',`UPDATED_DATE`='$UPDATED_DATE' where ID='$idUA'";
					$check_1 = $conn->query($sql);
				}
				if($check_1){
					$response = [
						'status' => true,
						'mess' =>'',
						'id' => !empty($insert_id)?$insert_id:''
					];
				}else{
					$response = [
						'status' => false,
						'mess' =>  $conn->error
					];
				}
			echo json_encode($response);
		}
	}