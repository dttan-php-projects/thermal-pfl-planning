<?php

	header("Content-Type: application/json");
	
	if(!empty($_POST['data'])){
		$formatData = json_decode($_POST['data'],true);
		if(!empty($formatData)){
			$listID = implode(',',$formatData);
			if(!empty($listID)){
				//connect to database
					require_once("../Database.php");
					$conn = getConnection();
				// query
					$delete_ms = "DELETE FROM user WHERE id IN ($listID);";
					$check_1 = $conn->query($delete_ms);
				// check 
					if($check_1){
						$response = [
							'status' => true,
							'mess' =>''  
						];
					}else{
						$response = [
							'status' => false,
							'mess' =>  $conn->error
						];
					}
				// close db
					mysqli_close($conn);
				// results
					echo json_encode($response);
			}
		}
	}