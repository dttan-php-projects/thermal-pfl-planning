<?php   

	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";
	//connect to database
		require_once ("../Database.php");
		$conn = getConnection();

	// GET Method
		$FROM_DATE = $_GET['from_date_value'];
		$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
		$TO_DATE = $_GET['to_date_value'];
		$TO_DATE = date('Y-m-d',strtotime($TO_DATE));
		
	// function get date
		function formatDate($value){
			return date('d-M-y',strtotime($value));
		}
	
	// query
		$sql_count = "SELECT COUNT(ID) FROM save_item";
		$rows_count = MiQuery($sql_count, $conn);
	// init var
		$limit = 0;
		$offset = 5000;
		if($rows_count-5000>0){
			$limit = $rows_count-5000;
		}
	// query 
		$fields = 's_i.ID,s_i.CREATE_DATE,NUMBER_NO,ITEM_NUMBER,SO_LINE,REQ,RBO,ITEM_CODE,QTY,MATERIAL_CODE,MATERIAL_DES,MATERIAL_QTY,WIDTH,LENGTH,INK_CODE,INK_DES,INK_QTY,SO_MAT_IN,SO_LUONG_PO';
		$sql = "SELECT $fields 
				FROM save_item AS s_i 
				JOIN save_material AS s_m ON s_m.ID_SAVE_ITEM = s_i.NUMBER_NO 
				WHERE (s_i.CREATE_DATE>='$FROM_DATE' AND s_i.CREATE_DATE<='$TO_DATE') 
				ORDER BY s_i.ID ASC
				-- LIMIT $limit,$offset 
		;";
	
		$rowsResult = MiQuery($sql, $conn);
		mysqli_close($conn);
	
	$cellStart = "<cell><![CDATA[";
	$cellEnd = "]]></cell>";

	if(count($rowsResult)>0){ 
		echo("<rows>");
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
					$REQ= $row['REQ'];
					$REQ = formatDate($REQ);
					$ITEM_NUMBER = $row['ITEM_NUMBER'];
					$ITEM_CODE = $row['ITEM_CODE'];
					$ITEM_CODE = str_replace(',','|',$ITEM_CODE);
					$RBO = $row['RBO']; 
					$RBO = str_replace(',','|',$RBO);
					$QTY = $row['QTY'];
					$MATERIAL_CODE = $row['MATERIAL_CODE'];	
					$MATERIAL_CODE = str_replace(',','/',$MATERIAL_CODE);
					$MATERIAL_DES	 = $row['MATERIAL_DES'];
					$MATERIAL_DES = str_replace(',','/',$MATERIAL_DES);
					$MATERIAL_QTY = $row['MATERIAL_QTY'];
					$WIDTH = $row['WIDTH'];
					$LENGTH = $row['LENGTH'];
					$INK_CODE = $row['INK_CODE'];
					$INK_CODE = str_replace(',','|',$INK_CODE);
					$INK_DES = $row['INK_DES'];
					$INK_DES = str_replace(',','|',$INK_DES);
					$INK_QTY = $row['INK_QTY'];
					$SO_MAT_IN = $row['SO_MAT_IN'];
					echo("<row>");
						echo $cellStart;
							echo($SAVE_DATE);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($NUMBER_NO);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($SO_LINE);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($REQ);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($ITEM_NUMBER);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($RBO);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($ITEM_CODE);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($QTY);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($MATERIAL_CODE);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($MATERIAL_DES);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($MATERIAL_QTY);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($LENGTH);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($WIDTH);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($INK_CODE);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($INK_DES);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($INK_QTY);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($SO_MAT_IN);    //value for price
						echo $cellEnd;
						echo $cellStart;
							echo($SO_LUONG_PO);    //value for price
						echo $cellEnd;
					echo("</row>");	
				}
			}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}
	