<?php
	
	header("Content-Type: application/json");
	require_once("../Database.php");
	$conn2 = getConnection('au_avery');

	$SO_LINE = $_GET['SO_LINE'];
	$sql_count = "SELECT COUNT(ID) FROM vnso WHERE concat(order_number,'-',line_number)='$SO_LINE' AND (PROMISE_DATE LIKE '%1970%' OR PROMISE_DATE='')";
	$rows_count = MiQuery($sql_count, $conn2);
	
	// Cos PD empty
	if(!empty($rows_count)){
		$response = [
			'status' => false,
			'mess' =>  'KHÔNG LẤY ĐƯỢC PROMISE DATE, BẠN CÓ MUỐN TẠO LỆNH!!!!',
		];
	}else{
		$response = [
			'status' => true,
			'mess' =>  '',
			'promise_date' => ''
		];
	}

	if($conn2) mysqli_close($conn2);
	echo json_encode($response);
