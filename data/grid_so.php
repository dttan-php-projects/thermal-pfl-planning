<?php

	require_once ("../Database.php");
	require_once ("./class/helper.php");

	$conn = getConnection();
	$conn2 = getConnection('au_avery');

	// get automail
		$sql = "SELECT * FROM vnso ";
		$OrderBy = " ORDER BY ORDER_NUMBER,LENGTH(LINE_NUMBER),LINE_NUMBER";
	
	//query to products table    
		$ORDER_NUMBER = $_GET["name_mask"];    
		$PROMISE_DATE_GET =  $_GET["promise_data"];
		$PROMISE_DATE_GET = date('m/d/Y',strtotime($PROMISE_DATE_GET));
	// check and set automail sql
		if(!empty($ORDER_NUMBER)){
			$ORDER_NUMBER = trim($ORDER_NUMBER);
			$ORDER_ARR = explode('-',$ORDER_NUMBER);        
			if(!empty($ORDER_ARR[1])){
				$sql.=" WHERE ORDER_NUMBER='$ORDER_ARR[0]' AND LINE_NUMBER = '$ORDER_ARR[1]' ";
			}else{
				$sql.=" WHERE ORDER_NUMBER='$ORDER_NUMBER' ";
			}
		}

	// order by
		$sql .= $OrderBy; 
	// query automail
		$res = MiQuery($sql,$conn2);
		if ($conn2) mysqli_close($conn2);
	// check 
		$rows = count($res);

	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";
	
	// set data
	if($rows>0){
		
		$PO_NUMBER = $_GET['PO'];
		  
		echo("<rows>");
			if(!empty($res)){
				// // tắt kiểm tra đơn hàng bắc ninh - AnhThu yeu cầu 30.09.2021
				// if (stripos($res[0]['ORDER_TYPE_NAME'], 'BNH') !==false ) {
				// 	// Đơn hàng Bắc Ninh, trả về trống
				// } else { 
					$cellStart = "<cell><![CDATA[";
					$cellEnd = "]]></cell>";		
					foreach ($res as $row){
						if(!empty(trim($row['PROMISE_DATE']))){
							$PROMISE_DATE = formatDate($row['PROMISE_DATE']);
						}else{
							if(!empty($PROMISE_DATE_GET)){
								$PROMISE_DATE = formatDate($PROMISE_DATE_GET);
							}else{
								$PROMISE_DATE = '';
							}				
						}			
						if(!empty($row['REQUEST_DATE'])){
							$REQUEST_DATE = formatDate($row['REQUEST_DATE']);
						}else{
							$REQUEST_DATE = '';
						}			
						if(!empty($row['ORDERED_DATE'])){
							$ORDERED_DATE = formatDate($row['ORDERED_DATE'],'',0);
						}else{
							$ORDERED_DATE = '';
						}
						$ORDER_NUMBER = trim($row['ORDER_NUMBER']);
						$LINE_NUMBER = trim($row['LINE_NUMBER']);
						$QTY = trim($row['QTY']);
						$ITEM = trim($row['ITEM']);

						// $SHIP_TO_CUSTOMER = htmlentities($row['SHIP_TO_CUSTOMER'], ENT_QUOTES, 'UTF-8');
						$SHIP_TO_CUSTOMER = trim($row['SHIP_TO_CUSTOMER']);
						// $SHIP_TO_CUSTOMER = htmlspecialchars($row['SHIP_TO_CUSTOMER'], ENT_QUOTES, 'UTF-8' );
	
						// get master data
							$sql = "SELECT * FROM master_item WHERE ITEM_NUMBER='$ITEM'; ";
							$query = mysqli_query($conn, $sql );
							$master_item = mysqli_fetch_array($query, MYSQLI_ASSOC);
							mysqli_close($conn);
						// check and get data
							if (!empty($master_item) ) {
	
								$RBO = '';
								$WIDTH = '';
								$HEIGHT = '';
								$DES_RM = '';
								$ITEM_RM = '';
								$ITEM_CODE = '';
								$ITEM_INK = '';
								$DES_INK = '';
								$QTY_INK = '';
								$E18 = '';
								$E19 = '';
								$E20 = '';
								$E21 = '';
								$E22 = '';
								$E24 = '';
								$E34 = '';
								$ORDER_TYPE_NAME = '';
								$SO_MAT_IN = '';
								$QTY_MATERIAL = '';
								$CS = '';
								$REMARK = '';
	
								$ITEM_NUMBER = trim($master_item['ITEM_NUMBER']);
								$NOTE_ITEM = trim($master_item['NOTE']);
								
								$CUT_MACHINE = trim($master_item['CUT_MACHINE']);
								$BRAND_PROTECTION = trim($master_item['BRAND_PROTECTION']);
								if (empty($BRAND_PROTECTION) ) $BRAND_PROTECTION = 'NO';
	
								if(!empty($ITEM_NUMBER)){
									$HEIGHT = trim($master_item['HEIGHT']);
									// automail data
										$ORDER_TYPE_NAME = trim($row['ORDER_TYPE_NAME']);
										$CS = trim($row['CS']);
									// get data
										$LENGTH = $_GET['length'];
										if(!empty($LENGTH)){ $HEIGHT = $LENGTH; }
									
									// master item
										$RBO = trim($master_item['PRINTING']);
										$WIDTH = trim($master_item['WIDTH']);
										
										
										$DES_RM = trim($master_item['DES_RM']);
										$ITEM_CODE = trim($master_item['ITEM_CODE']);
										$ITEM_RM = trim($master_item['ITEM_RM']);
										$ITEM_INK = trim($master_item['ITEM_INK']);
										$DES_INK = trim($master_item['DES_INK']);	
	
									if(strpos($ORDER_TYPE_NAME,'VN SAM')!==false){
										$QTY_INK = (((($QTY*$HEIGHT)/1000)*1.03)*2);
									}else{
										$X = (15*$HEIGHT/1000);
										$QTY_INK =((($QTY*$HEIGHT)/1000)*1.023*2+$X);
									}				
									$E18 = $PO_NUMBER;//Số lượng PO
									$E19 = ($QTY*$HEIGHT/1000/0.914);//Số lượng vật tư thực tế
									if(strpos($ORDER_TYPE_NAME,'VN SAM')!==false){
										$E20 = 0;
									}else{
										$E20 = (15*$HEIGHT/1000/0.914); //15 pcs mẫu
									}
									if(strpos($ORDER_TYPE_NAME,'VN SAM')!==false){
										$E21 = 0;
									}else{				
										
										if($RBO=='ASICS'){
											$E21 = (($QTY*0.023+(1*2)+round_up($QTY/200,0))*$HEIGHT/1000+(1-1)*2)/0.914;//số bù hao
										}else if ($RBO=="KOHL'S"){//@tandoan: nhan them 1.05 them vao 0.023 = 0.073
											$E21 = ((($QTY*0.073+($E18*2)+round_up($QTY/200,0))*$HEIGHT/1000+($E18-1)*2)/0.914);
										}else {
											$E21 = (($QTY*0.023+($E18*2)+round_up($QTY/200,0))*$HEIGHT/1000+($E18-1)*2)/0.914;
										}
										//$E21 = 1234;
									}
									$E22 = ($E21/$E19)*100;
									$E24 = trim($master_item['CUT_TYPE']);
									$E34 = trim($master_item['MACHINE']);				
									$SO_MAT_IN = trim($master_item['SO_MAT_IN']);
									if(strpos($ITEM_RM,'WIP')!==false){
										$QTY_MATERIAL = ($QTY*1.023)+15;
										$QTY_INK =(($QTY*$HEIGHT*1.023)/1000)+6;
									}else{
										$QTY_MATERIAL = $E19+$E20+$E21;
										$QTY_INK =($QTY_MATERIAL*0.914)*$SO_MAT_IN;
									}	
									if($QTY_MATERIAL<1){
										$QTY_MATERIAL = 1;
									}
									if($QTY_INK<1){
										$QTY_INK = 1;
									}
	
									//@tandoan: neu RBO  la "KOHL'S" x 5%
									if ($RBO=="KOHL'S") {
										$QTY_MATERIAL = $E19+$E20+$E21;
										$QTY_INK =($QTY_MATERIAL*0.914)*$SO_MAT_IN;
									}

									if(strpos($ORDER_TYPE_NAME,'VN SAM')!==false){
										$REMARK = 'ĐƠN MẪU.';
									}else{
										$REMARK = 'Lấy mẫu 15 pcs/size bất kỳ. Đã bù hao vật tư trên đơn hàng.';
									}				
								}
	
								echo("<row id='".$row['ID']."'>");
									echo $cellStart;
										echo(0);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($ORDER_NUMBER);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($LINE_NUMBER);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($QTY);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($ITEM_NUMBER);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($PROMISE_DATE);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($REQUEST_DATE);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($ORDERED_DATE);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($ITEM_CODE);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($CS);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($RBO);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($WIDTH);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($HEIGHT);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($DES_RM);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($ITEM_RM);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo(round($QTY_MATERIAL));    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($ITEM_INK);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($DES_INK);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo(round($QTY_INK));    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($SO_MAT_IN);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($E18);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo(round($E19,2));    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo(round($E20,2));    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo(round($E21,2));    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo(round($E22,1));    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($E24);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($E34);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($REMARK);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($ITEM);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($ORDER_TYPE_NAME);    //value for price
									echo $cellEnd;
									echo $cellStart;
										echo($NOTE_ITEM);    //value for price
									echo $cellEnd;	
									echo $cellStart;
										echo($SHIP_TO_CUSTOMER);    // ship to
									echo $cellEnd;	
									
									echo $cellStart;
										echo($CUT_MACHINE);
									echo $cellEnd;
									echo $cellStart;
										echo($BRAND_PROTECTION);
									echo $cellEnd;
								echo("</row>");  
	
							}
	
								
					}
				// } // tắt kiểm tra đơn hàng bắc ninh
				
			}
		echo("</rows>");
	}else{
		echo("<rows>");
		echo("</rows>");
	}    
	die;
	