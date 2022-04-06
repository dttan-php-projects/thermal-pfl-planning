<?php  
	
	function formatDate($value){ return date('d-M-y',strtotime($value)); }
	//connect to database
		require_once ("../Database.php");
		$conn = getConnection();
	
	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";
	
	// query
		$sql = "SELECT * FROM so_may ORDER BY CREATE_DATE DESC,ID";
		$rowsResult = MiQuery($sql, $conn);
		mysqli_close($conn);
		
	if(count($rowsResult)>0){ 
		echo("<rows>");
			if(!empty($rowsResult)){ 
				$cellStart = "<cell><![CDATA[";
				$cellEnd = "]]></cell>";
				foreach ($rowsResult as $row){
					$ID = $row['ID'];
					$CREATE_DATE = $row['CREATE_DATE'];
					$CREATE_DATE = formatDate($CREATE_DATE);
					$SO_LINE = $row['SO_LINE'];
					$SO_MAY = $row['SO_MAY'];
					$STT = $row['STT'];
					$PLAN = $row['PLAN'];
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
							echo($CREATE_DATE);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($SO_LINE);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($SO_MAY);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($STT);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($PLAN);  //value for product name                 
						echo( $cellEnd);
					echo("</row>");
				}
			}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}