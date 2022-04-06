<?php

	header("Content-Type: application/json");

	if(!empty($_POST['data'])){
		$formatData = json_decode($_POST['data'],true);
		if(!empty($formatData)){
			$UPDATED_BY  = '';
			$user = $_COOKIE["VNRISIntranet"];
			if(!empty($user)){
				$UPDATED_BY = $user;
			}
			//connect to database
				require_once("../Database.php");
				$conn = getConnection();
				$table = "user";
			// get data
				$EMAIL = !empty($formatData['EMAIL'])?addslashes($formatData['EMAIL']):'';
				$NOTE = !empty($formatData['NOTE'])?addslashes($formatData['NOTE']):'';
				$idUA = $formatData['ITEM_ID'];
			// check 
				if(strpos($idUA,'new_id_')!==false){  // insert
					if($EMAIL){
						$sql = "INSERT INTO $table 
									(`EMAIL`,`NOTE`,`UPDATED_BY`) 
								VALUES 
									('$EMAIL','$NOTE','$UPDATED_BY')
						;";
						
						$check_1 = $conn->query($sql);
						$insert_id = $conn->insert_id;
					}			
				}else{
					// update
					$sql = "UPDATE $table SET `EMAIL`='$EMAIL',`NOTE`='$NOTE',`UPDATED_BY`='$UPDATED_BY' where ID='$idUA'";
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