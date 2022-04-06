<?php
	$NUMBER_NO = $result_item['NUMBER_NO']?$result_item['NUMBER_NO']:"";
	$NUMBER_NO_ARR = explode('-',$NUMBER_NO);
	if(!empty($NUMBER_NO_ARR[2])){ // FR
		$NUMBER_NO = $NUMBER_NO_ARR[0];
		if($NUMBER_NO_ARR[1]>=1000){
			if(strlen($NUMBER_NO_ARR[1])==6){
				$NUMBER_NO.="-".ltrim($NUMBER_NO_ARR[1],0)."-".$NUMBER_NO_ARR[2];
			}else{
				$NUMBER_NO.="-".$NUMBER_NO_ARR[1]."-".$NUMBER_NO_ARR[2];
			}		
		}else{
			if(strlen($NUMBER_NO_ARR[1])==6){
				$NUMBER_NO.="-".substr($NUMBER_NO_ARR[1],2)."-".$NUMBER_NO_ARR[2];
			}else{
				$NUMBER_NO.="-".$NUMBER_NO_ARR[1]."-".$NUMBER_NO_ARR[2];
			}		
		}
	}else{
		$NUMBER_NO = $NUMBER_NO_ARR[0];
		if($NUMBER_NO_ARR[1]>=1000){
			if(strlen($NUMBER_NO_ARR[1])==6){
				$NUMBER_NO.="-".ltrim($NUMBER_NO_ARR[1],0);
			}else{
				$NUMBER_NO.="-".$NUMBER_NO_ARR[1];
			}		
		}else{
			if(strlen($NUMBER_NO_ARR[1])==6){
				$NUMBER_NO.="-".substr($NUMBER_NO_ARR[1],2);
			}else{
				$NUMBER_NO.="-".$NUMBER_NO_ARR[1];
			}		
		}	
	}
	$SAVE_DATE = $result_item['CREATE_DATE']?$result_item['CREATE_DATE']:"";	
	$SAVE_DATE = date('d-M-y',strtotime($SAVE_DATE));
	$PD 	   = $result_item['PD']?$result_item['PD']:"";
	$PD = date('d-M-y',strtotime($PD));
	$REQ 	   = $result_item['REQ']?$result_item['REQ']:"";
	$REQ = date('d-M-y',strtotime($REQ));
	$ORDERED 	   = $result_item['ORDER']?$result_item['ORDER']:"";
	$ORDERED = date('d-M-y',strtotime($ORDERED));
	$ITEM_NUMBER = $result_item['ITEM_NUMBER']?$result_item['ITEM_NUMBER']:"";
	$sql_printing = "SELECT PRINTING FROM master_item WHERE ITEM_NUMBER='$ITEM_NUMBER'";	
	$print_method = MiQuery($sql_printing,$conn);
	if(empty($print_method)){
		$print_method = '';
	}
	$ITEM_CODE = $result_item['ITEM_CODE']?$result_item['ITEM_CODE']:"";
	$CUT_TYPE = $result_item['CUT_TYPE']?$result_item['CUT_TYPE']:"";
	$CUT_TYPE_NOTE='';
	if(strpos($CUT_TYPE,'CENTER')!==false || strpos($CUT_TYPE,'BOOKLET')!==false){
		$CUT_TYPE_NOTE = 'Con nhãn sau khi in Thermal xong chuyển qua PFL để cắt, gấp';
	}
	$QTY = $result_item['QTY']?$result_item['QTY']:"";

	
	$MACHINE = $result_item['MACHINE']?$result_item['MACHINE']:"";
	$REMARK = $result_item['REMARK']?$result_item['REMARK']:"";
	$REMARK_2 = $result_item['REMARK_2']?$result_item['REMARK_2']:"";
	$NOTE_ITEM = $result_item['NOTE_ITEM']?$result_item['NOTE_ITEM']:"";
	$SO_MAT_IN = $result_item['SO_MAT_IN']?$result_item['SO_MAT_IN']:"";
	$SO_LUONG_PO = $result_item['SO_LUONG_PO']?$result_item['SO_LUONG_PO']:"";
	$KICH_THUOC_IN = $result_item['KICH_THUOC_IN']?$result_item['KICH_THUOC_IN']:"";
	$VAT_TU_THUC_TE = $result_item['VAT_TU_THUC_TE']?$result_item['VAT_TU_THUC_TE']:"";
	$PCS_MAU = $result_item['15_PCS_MAU']?$result_item['15_PCS_MAU']:0;
	$BU_HAO_PO = $result_item['BU_HAO_PO']?$result_item['BU_HAO_PO']:0;
	$TI_LE_BU_HAO = $result_item['TI_LE_BU_HAO']?$result_item['TI_LE_BU_HAO']:0;
	$TI_LE_BU_HAO.="%";
	if($QTY){
		$QTY = round($QTY);
	}

	

	$SO_MAY = $result_item['SO_MAY']?$result_item['SO_MAY']:"";
	$STT = $result_item['STT']?$result_item['STT']:"";
	$PLAN = $result_item['PLAN']?$result_item['PLAN']:"";
	$EMAIL = $result_item['EMAIL']?$result_item['EMAIL']:"";
	$NICK_NAME = '';
	if(!empty($EMAIL)){
		$sql_nick_name = "SELECT NOTE FROM user WHERE EMAIL='$EMAIL'";	
		$result_nick_name = MiQuery($sql_nick_name,$conn);
		if(!empty($result_nick_name)){
			$NICK_NAME = $result_nick_name;
		}else{
			$NICK_NAME = $EMAIL;
		}
	}

	//@tandoan: để check array, remark: MLA //@tandoan: set trường hợp RBO này thì remark:  MLA
	$RBO_CHECK = $result_item['RBO']?$result_item['RBO']:"";

	//$array_RBO_check_MLA = array();
	$array_RBO_check_MLA = [
		'PUMA',
		'ADIDAS',
		'NIKE',
		'H&M',
		'PRIMARK',
		'DECATHLON',
		'UNIQLO',
		'UNDER ARMOUR',
		'AMAZON',
		'INDITEX',
		'TARGET',
		'COLUMBIA',
		'MUJI'
	];

	foreach($array_RBO_check_MLA as $key => $value){
		$REMARK_MLA = '';
		if( strpos( strtoupper($RBO_CHECK), strtoupper($value) )!==FALSE) {
			$REMARK_MLA = 'MLA';
			break;
		}

	}


	$SHIP_TO_CUSTOMER = trim($result_item['SHIP_TO_CUSTOMER']);
	$CUT_MACHINE = trim($result_item['CUT_MACHINE']);
	$BRAND_PROTECTION = trim($result_item['BRAND_PROTECTION']);
	if (empty($BRAND_PROTECTION) ) $BRAND_PROTECTION = 'NO'; // default