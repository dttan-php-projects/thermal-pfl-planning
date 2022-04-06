<?php

	header("Content-Type: application/json");
	if(!empty($_POST['data'])){
		$NO = $_POST['data'];
		
		//connect to database
			require_once("../Database.php");
			$conn = getConnection();
		// query
			$save_item  = "DELETE FROM save_item WHERE NUMBER_NO='$NO'";
			$save_material	= "DELETE FROM save_material WHERE ID_SAVE_ITEM='$NO'";
		// execute
			$check_1 = $conn->query($save_item);
			$check_2 = $conn->query($save_material);
		// check 
			if($check_1&&$check_2){
				$response = [
						'status' => true,
						'mess' =>''  
					];
			}else{
				$response = [
						'status' => false,
						'mess' =>  ''
					];
			}
		// close db
			mysqli_close($conn);
			
		// results
			echo json_encode($response);
	}