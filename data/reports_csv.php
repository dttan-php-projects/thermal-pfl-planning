<?php
	// init 
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		ini_set('max_execution_time',300);  // set time 5 minutes
	// GET Method
		$FROM_DATE = $_GET['from_date_value'];
		$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
		$TO_DATE = $_GET['to_date_value'];
		$TO_DATE = date('Y-m-d',strtotime($TO_DATE));
	
	// function get date
		function formatDate($value){ return date('d-M-y',strtotime($value)); }
	
	// connect
		require_once ("../Database.php");
		$conn = getConnection();
		$filename = date("d_m_Y__H_i_s");

	// query
		$fields = 's_i.ID,s_i.CREATE_DATE,NUMBER_NO,ITEM_NUMBER,SO_LINE,REQ,RBO,ITEM_CODE,QTY,MATERIAL_CODE,MATERIAL_DES,MATERIAL_QTY,WIDTH,LENGTH,INK_CODE,INK_DES,INK_QTY,SO_MAT_IN,SO_LUONG_PO';
		// to do process so kho if(type_worst_vertical = 100-SB1) 10,5
		$query = "SELECT $fields 
					FROM save_item AS s_i 
					JOIN save_material AS s_m 
					ON s_m.ID_SAVE_ITEM = s_i.NUMBER_NO 
					WHERE (s_i.CREATE_DATE>='$FROM_DATE' AND s_i.CREATE_DATE<='$TO_DATE') 
					ORDER BY ID ASC
		;";
		
		$rowsResult = MiQuery($query, $conn);
	// close db
		mysqli_close($conn);

	//output data in XML format 
	
		header('Content-Encoding: UTF-8');
		header('Content-Type: text/csv; charset=utf-8');  
		header("Content-type: text/csv");
		header("Cache-Control: no-store, no-cache");
		
		header("Content-Disposition: attachment; filename=$filename.csv");  
		$output = fopen("php://output", "w");  
	// header show
		$header = [
			"DATE","LENH SX","SO#","NGAY GIAO","ITEM CODE","RBO","ORDER ITEM","SO LUONG","MA VAT TU","TEN VAT TU","SO LUONG VAT TU(YD)/(PCS)","CHIEU DAI (MM)","CHIEU RONG (MM)","MA MUC IN","TEN MUC IN","SO LUONG MUC (MET)","SO MAT IN","SO LUONG PO"
		];
	// check error UTF-8
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
	// get data to csv 
		fputcsv($output, $header);  

		if(count($rowsResult)>0){ 
			if(!empty($rowsResult)){		
				foreach ($rowsResult as $row){
					$NUMBER_NO = $row['NUMBER_NO'];	
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
					$SO_LUONG_PO = $row['SO_LUONG_PO']; 
					$SAVE_DATE = $row['CREATE_DATE']; 
					$SAVE_DATE = formatDate($row['CREATE_DATE']);
					$SO_LINE = $row['SO_LINE']; 
					$REQ = $row['REQ'];
					$REQ = formatDate($REQ);
					$ITEM_NUMBER = $row['ITEM_NUMBER'];
					$ITEM_CODE = $row['ITEM_CODE'];
					//$ITEM_CODE = str_replace(',','|',$ITEM_CODE);
					$RBO = $row['RBO']; 
					//$RBO = str_replace(',','|',$RBO);
					$QTY = $row['QTY'];
					$MATERIAL_CODE = $row['MATERIAL_CODE'];	
					//$MATERIAL_CODE = str_replace(',','|',$MATERIAL_CODE);
					$MATERIAL_DES	 = $row['MATERIAL_DES'];
					//$MATERIAL_DES = str_replace(',','|',$MATERIAL_DES);
					$MATERIAL_QTY = $row['MATERIAL_QTY'];
					$WIDTH = $row['WIDTH'];
					$LENGTH = $row['LENGTH'];
					$INK_CODE = $row['INK_CODE'];
					//$INK_CODE = str_replace(',','|',$INK_CODE);
					$INK_DES = $row['INK_DES'];
					//$INK_DES = str_replace(',','|',$INK_DES);
					$INK_QTY = $row['INK_QTY'];
					$SO_MAT_IN = $row['SO_MAT_IN'];	
					$arrayOutputTMP = [$SAVE_DATE,$NUMBER_NO,$SO_LINE,$REQ,$ITEM_NUMBER,$RBO,$ITEM_CODE,number_format($QTY),$MATERIAL_CODE,$MATERIAL_DES,number_format($MATERIAL_QTY),$LENGTH,$WIDTH,$INK_CODE,$INK_DES,number_format($INK_QTY),$SO_MAT_IN,$SO_LUONG_PO];
					fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
					fputcsv($output, $arrayOutputTMP);						
				}
			}
		} 
	
	// close file
		fclose($output);  