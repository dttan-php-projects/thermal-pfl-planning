<?php


$bold_supply = true;
$sql_supply = "SELECT * FROM save_material WHERE id_save_item='$id'";
$result_supply = MiQuery($sql_supply,$conn);
$arr_supply = [];
if(!empty($result_supply)){
    foreach ($result_supply as $key => $supply) {      
        $arr_supply[$key]['MATERIAL_CODE'] 		= $supply['MATERIAL_CODE'];
        $arr_supply[$key]['MATERIAL_DES'] 		= $supply['MATERIAL_DES'];
        $arr_supply[$key]['MATERIAL_QTY'] 		= $supply['MATERIAL_QTY'];
		$arr_supply[$key]['INK_CODE'] 			= $supply['INK_CODE'];
		$arr_supply[$key]['INK_DES'] 			= $supply['INK_DES'];
		$arr_supply[$key]['INK_QTY'] 			= $supply['INK_QTY'];
		$arr_supply[$key]['SO_LINE'] 			= $supply['SO_LINE'];
		$arr_supply[$key]['UNIT'] 				= 'YD';
		if(strpos($arr_supply[$key]['MATERIAL_CODE'],'WIP')!==false){
			$arr_supply[$key]['UNIT'] = 'EA';
		}
    }
}
$SO_LINE = $arr_supply[0]['SO_LINE']?$arr_supply[0]['SO_LINE']:"";
if(!empty($SO_LINE)){
	$sql_so_may = "SELECT * FROM so_may WHERE SO_LINE='$SO_LINE'";
	$result_so_may = MiQuery($sql_so_may,$conn);
	if(!empty($result_so_may)){
		$SO_MAY = $result_so_may[0]['SO_MAY'];
		$STT = $result_so_may[0]['STT'];
		$PLAN = $result_so_may[0]['PLAN'];
		// update
		$sql = "UPDATE save_item SET `SO_MAY`='$SO_MAY',`STT`='$STT',`PLAN`='$PLAN' where NUMBER_NO='$id'";
		$check = $conn->query($sql);
		
		
	}	
}
$BARCODE = '<img style="height:35px;margin-left: -20px;" src="barcode.php?text='.$SO_LINE.'" />';

//$array_SHIP_TO_check_ = array();
$array_SHIP_TO_Clarks_check = [
	'WinMag Shoes',
	'SHoe premier II(CAMBODIA)',
	'COMPLETE HONOUR',
	'TRIPOS INTERNATIONAL (CAMBODIA)',
	'SKY NICE',
	'SHINE MANOR/HO HSIN TAI',
	'HO HSIN', // add 20200310, mail: Job jacket automation PFL-Thermal 2020
	'FORTRESS INTERNATIONAL', // add 20200919, mail: Job jacket automation PFL-Thermal 2020
	'TRIUMPHANT DRAGON (CAMBODIA)' // add 20201014, mail: Job jacket automation PFL-Thermal 2020

];

$remark_bo_coc_200pcs = '';
$remark_20191212 = '';
$remark_SPECIAL_REQUEST = '';
$remark_WORLDON = '';
$SO_LINE_TMP_ARR = explode('-',$SO_LINE);
$SO_BOCOC = $SO_LINE_TMP_ARR[0];
$LINE_BOCOC = $SO_LINE_TMP_ARR[1];
$BILL_TO_CUSTOMER_CHECK = '';
$SHIP_TO_CUSTOMER_CHECK = '';
$PACKING_INSTRUCTIONS = '';

$fiels = 'BILL_TO_CUSTOMER,SHIP_TO_CUSTOMER, PACKING_INSTRUCTIONS, CUST_PO_NUMBER, QTY, CUSTOMER_JOB, INVOICE_LINE_INSTRUCTIONS, ORDER_SOURCE_NAME';
$query_vnso_check = "SELECT $fiels FROM vnso WHERE ITEM = '$ITEM_NUMBER' AND ORDER_NUMBER = '$SO_BOCOC' AND LINE_NUMBER = '$LINE_BOCOC' ORDER BY ID DESC LIMIT 1 ";
$result_vnso_check = MiQuery($query_vnso_check,$conn2);
if(empty($result_vnso_check)) {
	$query_vnso_check = "SELECT $fiels FROM vnso_total WHERE ITEM = '$ITEM_NUMBER' AND ORDER_NUMBER = '$SO_BOCOC' AND LINE_NUMBER = '$LINE_BOCOC' ORDER BY ID DESC LIMIT 1 ";
	$result_vnso_check = MiQuery($query_vnso_check,$conn2);
}
if(!empty($result_vnso_check)) {

	$gpmBarcode = '';
	$gpmData = gpmData($RBO_CHECK, $result_vnso_check[0] );
	if (!empty($gpmData) ) {
		$gpmBarcode = '<img style="height:30px;" src="barcode.php?text='.$gpmData.'" />';
	}
	


	// Khai b??o bill to v?? ship to l???y t??? ??k l?? item, order number v?? line number
	$BILL_TO_CUSTOMER_CHECK = strtoupper($result_vnso_check[0]['BILL_TO_CUSTOMER']);
	$SHIP_TO_CUSTOMER_CHECK = strtoupper($result_vnso_check[0]['SHIP_TO_CUSTOMER']);
	$PACKING_INSTRUCTIONS = strtoupper($result_vnso_check[0]['PACKING_INSTRUCTIONS']);
	
	$CUST_PO_NUMBER = $result_vnso_check[0]['CUST_PO_NUMBER'];
	$QTY = (int)$result_vnso_check[0]['QTY'];


	// @tandoan: 20210204 X??? l?? l???i theo mail: Job jacket automation PFL-Thermal 2020 (Hieu g???i)
		$remarkGiaoCuon = remarkGiaoCuon($RBO_CHECK, $SHIP_TO_CUSTOMER_CHECK, $ITEM_NUMBER );
		if (!empty($remarkGiaoCuon) ) {
			$CUT_TYPE = $remarkGiaoCuon['cut_type'];
			$CUT_TYPE_NOTE = $remarkGiaoCuon['cut_type_note'];
		}

	// @tandoan - 20210526: Giao cu???n. mail: 
		$remarkGiaoCuon2 = remarkGiaoCuon2($BILL_TO_CUSTOMER_CHECK, $ITEM_NUMBER );
		if (!empty($remarkGiaoCuon2) ) {
			$CUT_TYPE = $remarkGiaoCuon2;
		}


	if( strpos($BILL_TO_CUSTOMER_CHECK,'CHUTEX' )!==FALSE && strpos($SHIP_TO_CUSTOMER_CHECK,'CHUTEX') !==FALSE || 
		strpos($BILL_TO_CUSTOMER_CHECK,'KANMAX' )!==FALSE && (strpos($SHIP_TO_CUSTOMER_CHECK,'I APPAREL') !==FALSE || strpos($SHIP_TO_CUSTOMER_CHECK,'IK APPAREL') !==FALSE) || 
		strpos($BILL_TO_CUSTOMER_CHECK,'EPOCH LAY' )!==FALSE && strpos($SHIP_TO_CUSTOMER_CHECK,'EPOCH LAY') !==FALSE ) 
	{
		$remark_bo_coc_200pcs = 'B?? c???c 200pcs/c???c - Nh???p kho theo size';

	} else if( strpos($BILL_TO_CUSTOMER_CHECK,'ECLAT TEXTILE' )!==FALSE && strpos($SHIP_TO_CUSTOMER_CHECK,'CONG TY TNHH SON HA') !==FALSE ) {
		$remark_bo_coc_200pcs = 'Chia 200pcs/c???c, ph??n m??u v?? d??n s??? l?????ng ngo??i b???ch h??ng';
		
	}
	// //20191202. B??? ??i kh??ng s??? d???ng n???a 20200615. 
	// if (strpos($BILL_TO_CUSTOMER_CHECK,'ECLAT TEXTILE' )!==FALSE && $ITEM_NUMBER=='CB495920A') {
	// 	$remark_bo_coc_200pcs = 'B?? c???c 200pcs/c???c';
	// }
	
	//tr?????ng h???p ??i???u ki???n l?? ship to 
	if (strpos(strtoupper($SHIP_TO_CUSTOMER_CHECK),'NAMYANG')!==FALSE) {
		$remark_20191212 = "In ????? s??? l?????ng - Kh??ng FOC";
	} else if (strpos(strtoupper($SHIP_TO_CUSTOMER_CHECK),'FASHION GARMENTS')!==FALSE) {
		$remark_20191212 = "B?? c???c 50pcs/c???c (FASHION GARMENTS)";
	} else if (strpos(strtoupper($SHIP_TO_CUSTOMER_CHECK),'WORLDON')!==FALSE || strpos(strtoupper($SHIP_TO_CUSTOMER_CHECK),'DAQIAN TEXTILE')!==FALSE || strpos(strtoupper($SHIP_TO_CUSTOMER_CHECK),'GAIN LUCKY (VIETNAM) LIMITED')!==FALSE ) {
		$remark_WORLDON = 'WORLDON';
	}  else if (strpos(strtoupper($SHIP_TO_CUSTOMER_CHECK),'NIEN HSING') !== FALSE ) {
		
		if ($ITEM_NUMBER == 'CB334663' || $ITEM_NUMBER == 'CB334615A' ) {
			$remark_20191212 = "X???p 1000pcs/b???c ";
		}

	}

	// Check ??i???u ki???n: (mail: Job jacket automation PFL-Thermal 2020): 
	// RBO 
	$checkUA = false;
	$checkNike = false;
	$checkAdidas = false;
	if ( strpos(strtolower($RBO_CHECK),'columbia')!==FALSE ) {
		if ( strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'viet thai')!==FALSE ) {
			$remark_SPECIAL_REQUEST = '500pcs/ b???ch, ghi s??? th??? t??? l??n b???ch';
		} else if ( strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'yupoong')!==FALSE ) {
			$remark_SPECIAL_REQUEST = '1,000pcs/ b???ch, ghi s??? th??? t??? l??n b???ch';
		}
		
	} else if ( strpos(strtolower($RBO_CHECK),'under armour')!==FALSE ) {

		if ( strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'quang viet')!==FALSE ) {
			$remark_SPECIAL_REQUEST = 'chia 200pcs /c???c, d??n ph??n size ra ngo??i';
		} else if ( (strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'eclat textile')!==FALSE) || (strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'colltex garment')!==FALSE) || (strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'tai-yuan garments')!==FALSE) ) {
			$remark_SPECIAL_REQUEST = '????n h??ng g???p nhi???u line - ????ng g??i trong 1 th??ng';
			$checkUA = true;
		}
		
	} else if ( strpos(strtolower($RBO_CHECK),'clarks')!==FALSE ) {
		foreach ($array_SHIP_TO_Clarks_check as $ship_to_clarks_item) {
			if ( strpos(strtolower($SHIP_TO_CUSTOMER_CHECK), strtolower($ship_to_clarks_item))!==FALSE ) {
				$remark_SPECIAL_REQUEST = 'Gi??? nguy??n ????? v??ng khi process ????n h??ng';
				break;
			}
		}
	} else if ( strpos(strtolower($RBO_CHECK),'aeo')!==FALSE ) {
		if (strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'phong phu')!==FALSE ) {
			$remark_SPECIAL_REQUEST = '1,000 pcs/b???ch, ghi s??? l?????ng ngo??i m???i b???ch';
		} else if (strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'chutex')!==FALSE ) {
			$remark_SPECIAL_REQUEST = '5,000pcs/b???ch, ghi s??? l?????ng v?? Style b??n ngo??i m???i b???ch';
		} else {
			$remark_SPECIAL_REQUEST = '5,000 pcs/b???ch, ghi s??? l?????ng ngo??i m???i b???ch';
		}
	} else if ( strpos(strtolower($RBO_CHECK),'nike')!==FALSE ) {
		if (strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'worldon')!==FALSE ) {
			$checkNike = true;
		}
	} else if ( (strpos(strtolower($RBO_CHECK),'adidas')!==FALSE) || (strpos(strtolower($RBO_CHECK),'reebok')!==FALSE) ) {
		if (strpos(strtolower($SHIP_TO_CUSTOMER_CHECK),'worldon')!==FALSE ) {
			$checkAdidas = true;
		}
	}

	//@tandoan: 20200704 - remark item special
		$remarkSpecialItem = remarkSpecialItem($ITEM_NUMBER );

	// @tandoan: 20200907 - remark NHAN CAP
		$remarkNhancap = remarkNhancap($SO_LINE, $QTY, $CUST_PO_NUMBER, $conn2, checkNhancap($RBO_CHECK, $BILL_TO_CUSTOMER_CHECK, $SHIP_TO_CUSTOMER_CHECK) );
	
	// Tr?????ng h???p UNDER ARMOUR
		if ($checkUA == true ) {
			$remarkNhancap = remarkCombineUA($SO_LINE, $checkUA, $conn2);
		}

	// Tr?????ng h???p NIKE WORLDON
		if ($checkNike == true ) {
			$remarkNhancap = remarkCombineNike($SO_LINE, $checkNike, $conn2);
		}

	// Tr?????ng h???p NIKE WORLDON
		if ($checkAdidas == true ) {
			$remarkNhancap = remarkCombineAdidas($SO_LINE, $CUST_PO_NUMBER, $QTY, $checkAdidas, $conn2);
		}
	

}


?>