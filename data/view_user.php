<?php   

	function formatDate($value){ return date('d-M-y',strtotime($value)); }
	//connect to database
		require_once("../Database.php");
		$conn = getConnection();

	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";
	// query user
		$rowsResult = MiQuery("SELECT * FROM user;", $conn);
		mysqli_close($conn);
		
	// check 
	if(count($rowsResult)>0){ 
		echo("<rows>");
			if(!empty($rowsResult)){ 
				$cellStart = "<cell><![CDATA[";
				$cellEnd = "]]></cell>";
				foreach ($rowsResult as $row){
					$ID = $row['ID'];
					$EMAIL = $row['EMAIL'];
					$NOTE = $row['NOTE'];
					$UPDATED = $row['UPDATED_BY'];
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
							echo($EMAIL);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($NOTE);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($UPDATED);  //value for product name                 
						echo( $cellEnd);
					echo("</row>");
				}
				for($i=1;$i<=7;$i++){
					$ID = 'new_id_'.$i;
					$EMAIL = '';
					$NOTE = '';
					echo("<row id='".$ID."'>");
						echo $cellStart;  // LENGTH
							echo(0);  //value for product name                 
						echo $cellEnd;
						echo( $cellStart);  // LENGTH
							echo($EMAIL);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($NOTE);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo('');  //value for product name                 
						echo( $cellEnd);
					echo("</row>");
				}
			}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}