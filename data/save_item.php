<?php

	date_default_timezone_set('Asia/Ho_Chi_Minh');
	header("Content-Type: application/json");

	function createNO($maxValue)
	{
		if(!empty($maxValue)){        
			$maxValue++;
			$lenMaxValue = strlen($maxValue);        
			if($maxValue<=99999){
				if($lenMaxValue===1){
					$maxValue = "00000".$maxValue;
				}else if($lenMaxValue===2){
					$maxValue = "0000".$maxValue;
				}else if($lenMaxValue===3){
					$maxValue = "000".$maxValue;
				}
				else if($lenMaxValue===4){
					$maxValue = "00".$maxValue;
				}
				else if($lenMaxValue===5){
					$maxValue = "0".$maxValue;
				}
			}
			return $maxValue;
		}else{
			return "000001";
		}
	}
	
	// check user
	if(empty($_COOKIE["VNRISIntranet"])){
		$response = [
			'status' => false,
			'mess' =>  'VUI LÒNG ĐĂNG NHẬP VÀO HỆ THỐNG ĐỂ TẠO LỆNH!!!'// use to debug code
		];
		echo json_encode($response);
	}

	// get data
		$data = $_POST['data'];
		// $data = '{"item":{"AB1":"WV","ORDER_TYPE":"VN INTERCO","BOARD":"","AD1":"29.2T","SIZE":"SIZE","SCRAP":"6","PD":"16-10-2018","REQ":"16-10-2018","ORDERED":"27-09-2018","ITEM":"WX025250A","item_length":"WX025250A98","RBO":"NIKE","NUMBER_SIZE":0,"NUM_WIRE":"28","NUMBER_PATTERN":"1T24/5219","HEIGHT_BTP":"33","HEIGHT_TP":"33","CHI_DOC_CAN":0,"WATER":0,"glue":0,"TYPE_WORST_VERTICAL":"75-B2","PAPER_SIZE":"5","SO_SOI_DOC":144,"gear_density":"70D","width_BTP":"98","width_TP":"49","folding_type":"CL","laser_cutting":0,"ultra_sonic":1,"glue1":"0","glue2":"0","note1":"0","note2":"0","iron_temp":"90-120","Q44":"38","S44":"39.6T","Q45":"28","S45":"29.2T","PROCESS":[{"name":"Dệt","display":"1"},{"name":"Cắt nóng","display":"0"},{"name":"Xẻ sonic","display":"1"},{"name":"Qua hồ","display":"0"},{"name":"Qua nước","display":"0"},{"name":"Nối đầu","display":"1"},{"name":"Dán keo","display":"0"},{"name":"Cắt gấp: CL","display":"1"},{"name":"Đóng gói","display":"1"}],"SAVE_DATE":"","NUMBER_NO":"","NUMBER_PICKS":1120},"size":[{"size":"","qty":"42014","qty_so_1":"8027","qty_so_2":"15607","qty_so_3":"6094","qty_so_4":"12286","qty_so_5":"","qty_so_6":"","qty_so_7":"","qty_so_8":"","qty_so_9":"","qty_so_10":"","qty_so_11":"","qty_so_12":"","qty_so_13":"","qty_so_14":"","qty_so_15":"","qty_row":"1611","qty_row_1":"309","qty_row_2":"596","qty_row_3":"236","qty_row_4":"470","qty_row_5":"","qty_row_6":"","qty_row_7":"","qty_row_8":"","qty_row_9":"","qty_row_10":"","qty_row_11":"","qty_row_12":"","qty_row_13":"","qty_row_14":"","qty_row_15":"","scrap":"7%"}],"supply":[{"code_supply":"YRCP07009001","density":"70D","number_picks":"602","chieu_dai_chi":"120000","chi_ngang_can":"0"},{"code_supply":"LY070220001053","density":"70D","number_picks":"518","chieu_dai_chi":"128571","chi_ngang_can":"0"}],"so":[{"so_line":"27031435-1","qty":"8027","item":"WX025250A"},{"so_line":"27031435-2","qty":"15607","item":"WX025250A"},{"so_line":"27031435-3","qty":"6094","item":"WX025250A"},{"so_line":"27031435-4","qty":"12286","item":"WX025250A"}]}';
		
	//  get data
	if(!empty($data)){	
		
		$formatData = json_decode($data,true);   

		// save date process after that
		if($formatData){
			// get data
			$itemData = $formatData["item"];  
			$supplyData = $formatData["supply"];  

			//connect to database
			require_once ("../Database.php");
			$conn = getConnection();
			$table = "save_item";

			// save item        
			$check_item = true;
			$check_size = true;
			$check_supply = true;
			$SAVE_DATE = !empty($itemData['SAVE_DATE'])?addslashes($itemData['SAVE_DATE']):'';
			$SAVE_DATE = date("Y-m-d",strtotime($SAVE_DATE));  
			// GET MAX 
			$maxNO = $itemData['MAX_NO'];
			$NO = $itemData['NO'];
			$prefixNOArr = explode('-',$NO);
			$NoCurrent = $prefixNOArr[0];
			if(!empty($maxNO)){
				$max_value = (int)$maxNO;
			}else{
				// SELECT
				$sql_max = "SELECT NUMBER_NO FROM $table where NUMBER_NO like '$NoCurrent%' order by NUMBER_NO desc LIMIT 0,1";
				$max = MiQuery($sql_max,$conn); 
				if(!empty($max)){
					if (is_array($max)){
						$max_value = $max[0]['NUMBER_NO'];
						$value_tmp = explode("-",$max_value);                
						if(!empty($value_tmp[1])){
							$value_tmp[1]=(int)$value_tmp[1];
							$max_value = $value_tmp[1]; 
						}
					}else{
						$max_value = $max;  
						$value_tmp = explode("-",$max_value);                
						if(!empty($value_tmp[1])){
							$value_tmp[1]=(int)$value_tmp[1];
							$max_value = $value_tmp[1]; 
						}              
					}
				}else{
					$max_value=0;
				}
			}   
			$NUMBER_NO = createNO($max_value);
			$NUMBER_NO = $NoCurrent."-".$NUMBER_NO;
			// save session prefix
			if(!isset($_COOKIE['prefix'])){
				if($_COOKIE['prefix']!=$NoCurrent || empty($_COOKIE['prefix']) ){
					$sql_save_supply = "UPDATE settings SET `VALUE` = '$NoCurrent' WHERE (`NAME` = 'PRE_FIX_NO');";
					$check_supply = $conn->query($sql_save_supply);
					setcookie('prefix', $NoCurrent, time() + (86400 * 365), "/"); // 86400 = 1 day
				}
			}		
			if(!empty($prefixNOArr[2])){
				$NUMBER_NO = $NUMBER_NO."-FR";
			}
			// check exist NO
			$sql_exist = "SELECT count(*) FROM $table WHERE NUMBER_NO='$NUMBER_NO'";
			$resultCheck = MiQuery($sql_exist,$conn);
			if(!empty($resultCheck)){
				$response = [
					'status' => false,
					'mess' =>  "Lệnh sản xuất: $NUMBER_NO đã được tạo. Vui lòng kiểm tra lại!!!"// use to debug code
				];
				echo json_encode($response);die;			
			}
			// end check exist NO
			$SO_LINE = !empty($itemData['SO_LINE'])?addslashes($itemData['SO_LINE']):'';  
			$CS = !empty($itemData['CS'])?addslashes($itemData['CS']):'';  
			$PD = !empty($itemData['PD'])?$itemData['PD']:'';      
			if($PD){
				$PD = date("Y-m-d",strtotime($PD));
			}        
			$REQ = !empty($itemData['REQ'])?$itemData['REQ']:'';
			if($REQ){
				$REQ = date("Y-m-d",strtotime($REQ));
			} 
			$ORDER = !empty($itemData['ORDER'])?$itemData['ORDER']:'';
			if($ORDER){
				$ORDER = date("Y-m-d",strtotime($ORDER));
			}  
			$RBO = !empty($itemData['RBO'])?addslashes($itemData['RBO']):'';
			$ITEM_NUMBER = !empty($itemData['ITEM_NUMBER'])?addslashes($itemData['ITEM_NUMBER']):'';
			$ITEM_CODE = !empty($itemData['ITEM_CODE'])?addslashes($itemData['ITEM_CODE']):'';
			$CUT_TYPE = !empty($itemData['CUT_TYPE'])?addslashes($itemData['CUT_TYPE']):'';
			$QTY = !empty($itemData['QTY'])?($itemData['QTY']):0;
			$MACHINE = !empty($itemData['MACHINE'])?($itemData['MACHINE']):'';     
			$REMARK = !empty($itemData['REMARK'])?($itemData['REMARK']):'';
			$REMARK_2 = !empty($itemData['REMARK_2'])?($itemData['REMARK_2']):'';
			$SO_MAT_IN = !empty($itemData['SO_MAT_IN'])?$itemData['SO_MAT_IN']:0;
			$WIDTH = !empty($itemData['WIDTH'])?$itemData['WIDTH']:0;
			$LENGTH = !empty($itemData['LENGTH'])?addslashes($itemData['LENGTH']):0;
			$SO_LUONG_PO = !empty($itemData['SO_LUONG_PO'])?$itemData['SO_LUONG_PO']:0;
			$VAT_TU_THUC_TE = !empty($itemData['VAT_TU_THUC_TE'])?$itemData['VAT_TU_THUC_TE']:0;
			$PCS_MAU = !empty($itemData['PCS_MAU'])?$itemData['PCS_MAU']:0;
			$BU_HAO_PO = !empty($itemData['BU_HAO_PO'])?$itemData['BU_HAO_PO']:0;
			$TI_LE_BU_HAO = !empty($itemData['TI_LE_BU_HAO'])?$itemData['TI_LE_BU_HAO']:0;
			$KICH_THUOC_IN = !empty($itemData['KICH_THUOC_IN'])?$itemData['KICH_THUOC_IN']:'';
			$NOTE_ITEM = !empty($itemData['NOTE_ITEM'])?$itemData['NOTE_ITEM']:'';

			// Ship to customer 
			$SHIP_TO_CUSTOMER = !empty($itemData['SHIP_TO_CUSTOMER'])? addslashes($itemData['SHIP_TO_CUSTOMER']) : '';

			$ORDER_TYPE_NAME = !empty($itemData['ORDER_TYPE_NAME'])?$itemData['ORDER_TYPE_NAME']:'';
			$CUT_MACHINE = !empty($itemData['CUT_MACHINE'])? addslashes($itemData['CUT_MACHINE']) : '';
			$BRAND_PROTECTION = !empty($itemData['BRAND_PROTECTION'])? addslashes($itemData['BRAND_PROTECTION']) : '';


			// Xử lý lại remark Lấy mẫu, email: "Re: [PFL Thermal] Create new JJ 02032022"
			if ($BRAND_PROTECTION == 'YES' ) {
				$REMARK = 'Lấy mẫu 3pcs/size bất kỳ. Đã bù hao vật tư trên đơn hàng.';
			} else {
				
				if(strpos($ORDER_TYPE_NAME,'VN SAM')!==false){
					$REMARK = 'ĐƠN MẪU.';
				}else{
					if ($SO_LUONG_PO < 100 ) {
						$REMARK = 'Lấy mẫu 3pcs/size bất kỳ. Đã bù hao vật tư trên đơn hàng.';
					} else {
						$REMARK = 'Lấy mẫu 15 pcs/size bất kỳ. Đã bù hao vật tư trên đơn hàng.';
					}
					
				}
			}


			$EMAIL = '';
			if(!empty($_COOKIE["VNRISIntranet"])){
				$EMAIL = $_COOKIE["VNRISIntranet"];
			}
			$sql_save_item="INSERT INTO $table 
								(`CREATE_DATE`,`NUMBER_NO`,`ORDER`,`REQ`,`PD`,`RBO`,`ITEM_NUMBER`,`ITEM_CODE`,`CUT_TYPE`,`QTY`,`MACHINE`,`REMARK`,`REMARK_2`,`SO_MAT_IN`,`WIDTH`,`LENGTH`,`SO_LUONG_PO`,`VAT_TU_THUC_TE`,`15_PCS_MAU`,`BU_HAO_PO`,`TI_LE_BU_HAO`,`KICH_THUOC_IN`,`EMAIL`,`NOTE_ITEM`,`SHIP_TO_CUSTOMER`,`CUT_MACHINE`,`BRAND_PROTECTION`) 
							VALUES 
								('$SAVE_DATE','$NUMBER_NO','$ORDER','$REQ','$PD','$RBO','$ITEM_NUMBER','$ITEM_CODE','$CUT_TYPE','$QTY','$MACHINE','$REMARK','$REMARK_2','$SO_MAT_IN','$WIDTH','$LENGTH','$SO_LUONG_PO','$VAT_TU_THUC_TE','$PCS_MAU','$BU_HAO_PO','$TI_LE_BU_HAO','$KICH_THUOC_IN','$EMAIL','$NOTE_ITEM', '$SHIP_TO_CUSTOMER', '$CUT_MACHINE', '$BRAND_PROTECTION')
			;";

			$check_item = $conn->query($sql_save_item);    
			if($check_item){
				$insert_id = $conn->insert_id;
				if($insert_id){
					if(!empty($supplyData)){
						foreach($supplyData as $key=>$value_supply){
							$MATERIAL_CODE = !empty($value_supply['MATERIAL_CODE'])?addslashes($value_supply['MATERIAL_CODE']):'';
							$MATERIAL_DES = !empty($value_supply['MATERIAL_DES'])?addslashes($value_supply['MATERIAL_DES']):'';
							$MATERIAL_QTY = !empty($value_supply['MATERIAL_QTY'])?addslashes($value_supply['MATERIAL_QTY']):0;
							$INK_CODE = !empty($value_supply['INK_CODE'])?addslashes($value_supply['INK_CODE']):'';
							$INK_DES = !empty($value_supply['INK_DES'])?addslashes($value_supply['INK_DES']):'';
							$INK_QTY = !empty($value_supply['INK_QTY'])?addslashes($value_supply['INK_QTY']):0;
							$sql_save_supply = "INSERT INTO `save_material` 
													(`CREATE_DATE`,`ID_SAVE_ITEM`, `SO_LINE`,`MATERIAL_CODE`,`MATERIAL_DES`,`MATERIAL_QTY`,`INK_CODE`,`INK_DES`,`INK_QTY`) 
												VALUES 
													('$SAVE_DATE','$NUMBER_NO', '$SO_LINE','$MATERIAL_CODE','$MATERIAL_DES','$MATERIAL_QTY','$INK_CODE','$INK_DES','$INK_QTY')
							;";
							$check_supply = $conn->query($sql_save_supply);                                           
						}
					} 
					
					// check all 
					if($check_item&&$check_supply){
						$response = [
							'status' => true,
							'mess' =>  '',// use to debug code
							'NUMBER_NO' => $NUMBER_NO
						];
					}else{
						$response = [
							'status' => false,
							'mess' =>  $conn->error// use to debug code
						];
					}                
				}            
				
			}else{
				$response = [
					'status' => false,
					'mess' =>  $conn->error// use to debug code
				];
			}       
		}

		mysqli_close($conn);
		echo json_encode($response);
	}
	