<?php   
	//connect to database
		require_once("../Database.php");
		$conn = getConnection();
	
	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";
	// query
		$sql = "SELECT * FROM master_item";
		$rowsResult = MiQuery($sql, $conn);
		mysqli_close($conn);
		
	if(count($rowsResult)>0){ 
		echo("<rows>");
		if(!empty($rowsResult)){ 
			$cellStart = "<cell><![CDATA[";
			$cellEnd = "]]></cell>";
			foreach ($rowsResult as $row){
				$ID = $row['ID'];
				$ITEM_NUMBER = $row['ITEM_NUMBER'];
				$ITEM_CODE = $row['ITEM_CODE'];
				$ITEM_RM = $row['ITEM_RM'];
				$DES_RM = $row['DES_RM'];
				$PRINTING = $row['PRINTING'];
				$RBO = $row['RBO'];
				$WIDTH = $row['WIDTH'];
				$HEIGHT = $row['HEIGHT'];
				$ITEM_INK = $row['ITEM_INK'];
				$DES_INK = $row['DES_INK'];
				$SO_MAT_IN = $row['SO_MAT_IN'];
				$MACHINE = $row['MACHINE'];
				$CUT_TYPE = $row['CUT_TYPE'];
				$NOTE = $row['NOTE'];

				$CUT_MACHINE = $row['CUT_MACHINE'];
				$BRAND_PROTECTION = $row['BRAND_PROTECTION'];
				$UPDATED_BY = $row['UPDATED_BY'];
				$UPDATED_DATE = $row['UPDATED_DATE'];
				/*
				if($deleteNO){
					$link  = 'DELETE^javascript:deleteMS('.$ID.');^_self';
				}	
				*/
				echo("<row id='".$ID."'>");
					echo $cellStart;  // LENGTH
						echo(0);  //value for product name                 
					echo $cellEnd;
					echo( $cellStart);  // LENGTH
						echo($ITEM_NUMBER);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ITEM_CODE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ITEM_RM);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($DES_RM);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($PRINTING);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($RBO);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($WIDTH);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($HEIGHT);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ITEM_INK);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($DES_INK);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($SO_MAT_IN);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($MACHINE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($CUT_TYPE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($NOTE);  //value for product name                 
					echo( $cellEnd);

					echo( $cellStart);  // LENGTH
						echo($CUT_MACHINE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($BRAND_PROTECTION);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($UPDATED_BY);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($UPDATED_DATE);  //value for product name                 
					echo( $cellEnd);
				echo("</row>");
			}
			// add 10 
			for($i=1;$i<=10;$i++){
				$ID = 'new_id_'.$i;
				$ITEM_NUMBER = '';
				$ITEM_CODE = '';
				$ITEM_RM = '';
				$DES_RM = '';
				$PRINTING = '';
				$RBO = '';
				$WIDTH = '';
				$HEIGHT = '';
				$ITEM_INK = '';
				$DES_INK = '';
				$SO_MAT_IN = '';
				$MACHINE = '';
				$CUT_TYPE = '';	

				$NOTE = '';	

				$CUT_MACHINE = '';
				$BRAND_PROTECTION = '';
				$UPDATED_BY = '';
				$UPDATED_DATE = '';
				echo("<row id='".$ID."'>");
					echo $cellStart;  // LENGTH
						echo(0);  //value for product name                 
					echo $cellEnd;
					echo( $cellStart);  // LENGTH
						echo($ITEM_NUMBER);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ITEM_CODE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ITEM_RM);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($DES_RM);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($PRINTING);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($RBO);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($WIDTH);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($HEIGHT);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ITEM_INK);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($DES_INK);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($SO_MAT_IN);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($MACHINE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($CUT_TYPE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($NOTE);  //value for product name                 
					echo( $cellEnd);

					echo( $cellStart);  // LENGTH
						echo($CUT_MACHINE);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($BRAND_PROTECTION);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($UPDATED_BY);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($UPDATED_DATE);  //value for product name                 
					echo( $cellEnd);

				echo("</row>");
			}
		}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}