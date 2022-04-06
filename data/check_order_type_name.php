<?php
	
	header("Content-Type: application/json");
	require_once("../Database.php");
	$conn2 = getConnection('au_avery');

	// init
	$status = false;
	$mess = 'Không lấy được dữ liệu Automail';

	$SO_LINE = $_GET['SO_LINE'];
	$sql = "SELECT ORDER_TYPE_NAME FROM vnso WHERE concat(order_number,'-',line_number)='$SO_LINE' LIMIT 1;";
	$ORDER_TYPE_NAME = MiQuery($sql, $conn2);
	if (empty($ORDER_TYPE_NAME) ) {
		$sql = "SELECT ORDER_TYPE_NAME FROM vnso_total WHERE concat(order_number,'-',line_number)='$SO_LINE' LIMIT 1;";
		$ORDER_TYPE_NAME = MiQuery($sql, $conn2);
	}
	
	// check data
	if(!empty($ORDER_TYPE_NAME)){
		
		if (strpos(strtoupper($ORDER_TYPE_NAME), 'BNH') !== false ) {
			$status = true;
			$mess = 'Đơn hàng BNH, Bạn có muốn tiếp tục làm lệnh?';
		} else {
			$mess = 'Không phải đơn hàng BNH';
		}
	}

	
	// close db
	if($conn2) mysqli_close($conn2);

	// result
	$response = array( 'status' => $status, 'mess' => $mess );
	// render
	echo json_encode($response);
