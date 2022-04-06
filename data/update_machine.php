<?php

	header("Content-Type: application/json");

	if(!empty($_POST['data'])){
		$formatData = json_decode($_POST['data'],true);
		if(!empty($formatData)){
			//connect to database
				require_once("../Database.php");
				$conn = getConnection();
				$table = "so_may";
			// get data
				$SO_LINE = !empty($formatData['SO_LINE'])?addslashes($formatData['SO_LINE']):'';
				$SO_MAY = !empty($formatData['SO_MAY'])?addslashes($formatData['SO_MAY']):'';
				$STT = !empty($formatData['STT'])?addslashes($formatData['STT']):0;
				$PLAN = !empty($formatData['PLAN'])?addslashes($formatData['PLAN']):'';
				$idUA = $formatData['ITEM_ID'];
			// check 
			if(strpos($idUA,'new_id_')!==false){  // insert
				if($SO_LINE){
					$sql = "INSERT INTO $table 
					(`SO_LINE`,`SO_MAY`, `STT`,`PLAN`) VALUES ('$SO_LINE','$SO_MAY','$STT','$PLAN')";
					$check_1 = $conn->query($sql);
					$insert_id = $conn->insert_id;
				}			
			}else{
				// update
				$sql = "UPDATE $table SET `SO_LINE`='$SO_LINE',`SO_MAY`='$SO_MAY',`STT`='$STT',`PLAN`='$PLAN' WHERE ID='$idUA'";
				$check_1 = $conn->query($sql);
			}
			if($check_1){
				$response = [
					'status' => true,
					'mess' =>'',
					'id' => !empty($insert_id)?$insert_id:''
				];
			}else{
				$response = [
						'status' => false,
						'mess' =>  $conn->error
					];
			}

			mysqli_close($conn);
			echo json_encode($response);
		}
	}