<?php   
	function formatDate($value){ return date('d-M-y',strtotime($value)); }
	//connect to database
		require_once("../Database.php");
		$conn = getConnection();

	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";
	// GET method
		$FROM_DATE = $_GET['from_date_value'];
		$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
		$TO_DATE = $_GET['to_date_value'];
		$TO_DATE = date('Y-m-d',strtotime($TO_DATE));
	
	// query
		$table = "save_item";
		$table_material = "save_material";
		$table_somay = "so_may";

		$fields = 's_i.ID,NUMBER_NO,ITEM_NUMBER,RBO,s_m.SO_LINE,s_i.CREATE_DATE,EMAIL,so_may.PLAN,PRINTED';
		$sql = "SELECT $fields FROM $table as s_i JOIN $table_material as s_m ON s_m.ID_SAVE_ITEM = s_i.NUMBER_NO LEFT JOIN $table_somay ON so_may.SO_LINE = s_m.SO_LINE ";
		if($FROM_DATE!='1970-01-01'&&$TO_DATE!='1970-01-01'){
			$sql .= " WHERE (s_m.CREATE_DATE>='$FROM_DATE' AND s_m.CREATE_DATE<='$TO_DATE') ORDER BY s_i.CREATE_DATE DESC,s_i.ID DESC;";
		}else{
			$sql .= " ORDER BY s_i.CREATE_DATE DESC,s_i.ID DESC LIMIT 0,1000;";
		}

		$rowsResult = MiQuery($sql, $conn);
		
	// header show
	$header = '<head>
					<column width="85" type="ed" align="left" sort="str">DATE</column>
					<column width="131" type="ed" align="left" sort="str">NO</column>
					<column width="131" type="ed" align="left" sort="str">NO</column>
					<column width="90" type="ed" align="left" sort="str">SO-LINE</column>
					<column width="140" type="ed" align="left" sort="str">CREATED BY</column>
					<column width="100" type="ed" align="left" sort="str">ITEM NUMBER</column>
					<column width="80" type="ed" align="left" sort="str">RBO</column>
					<column width="*" type="ed" align="left" sort="str">PLAN</column>
					<column width="80" type="ed" align="left" sort="str">PRINTED</column>
					<column width="80" type="link" align="left" sort="str"></column>
					<column width="80" type="link" align="left" sort="str"></column>
				</head>';

	if(count($rowsResult)>0){ 
		
		echo("<rows>");
		
			// check role
			$sql_role = "SELECT * FROM user"; 
			$rowsResultRole = MiQuery($sql_role, $conn);
			if(!empty($rowsResultRole)){
				foreach ($rowsResultRole as $row){
					$arrayRole[]=$row['EMAIL'];
				}
			}else{
				$arrayRole = ['anhthu.pham','uyen.le','trang.huynhphuong','thang.bui','hieu.tran'];
			}
			$user = '';
			if(!empty($_COOKIE["VNRISIntranet"])){
				$user = $_COOKIE["VNRISIntranet"];
				if(in_array($user,$arrayRole)){
					$deleteNO = 1;
				}else{
					$deleteNO = 0;
				}
			}		
			echo $header;
			if(!empty($rowsResult)){  
				$ID = 0;
				$cellStart = "<cell><![CDATA[";
				$cellEnd = "]]></cell>";
				foreach ($rowsResult as $row){
					$SAVE_DATE = $row['CREATE_DATE']; 
					$SAVE_DATE = formatDate($row['CREATE_DATE']);
					$PRINTED = $row['PRINTED'];
					$ID++;
					$NUMBER_NO = $row['NUMBER_NO'];
					$NUMBER_NO_TO_DELETE = $row['NUMBER_NO'];
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
					$SO_LINE = $row['SO_LINE'];
					$EMAIL = $row['EMAIL'];
					$ITEM_NUMBER = $row['ITEM_NUMBER'];
					$RBO = $row['RBO'];
					$PLAN = $row['PLAN'];
					if($deleteNO && ($user == $EMAIL || $user == 'tan.doan'|| $user == 'anhthu.pham')){
						$link  = 'DELETE^javascript:delete_no("'.$NUMBER_NO_TO_DELETE.'");^_self';
					}	
					
					$linkPrint = "./print.php?id=$NUMBER_NO_TO_DELETE";		
					echo("<row id='".$ID."'>");
					echo( $cellStart);  // LENGTH
						echo($SAVE_DATE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($NUMBER_NO);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($NUMBER_NO_TO_DELETE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($SO_LINE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($EMAIL);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ITEM_NUMBER);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($RBO);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($PLAN);  //value for product name                 
					echo( $cellEnd);
					if($PRINTED=='1'){
						echo( $cellStart);
							echo("YES");  //value for product name                 
						echo( $cellEnd);
					}else{
						echo( $cellStart);
							echo("NO");  //value for product name                 
						echo( $cellEnd);
					}
					if($PRINTED=='1'){
						echo("<cell><![CDATA[<font color='red'></front>");  // LENGTH
							echo("Print NO^$linkPrint");  //value for product name                 
						echo("]]></cell>");
					}else{
						echo($cellStart);  // LENGTH
							echo("Print NO^$linkPrint");  //value for product name                 
						echo($cellEnd);
					}			
					if($deleteNO && ($user == $EMAIL || $user == 'tan.doan'|| $user == 'anhthu.pham')){
						echo( $cellStart);  // LENGTH
						echo $link;  //value for product name                 
						echo( $cellEnd);
					}				
					echo("</row>");
				}
			}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}
