<?php

	header("Content-Type: application/json");
	require_once("../Database.php");
	$conn = getConnection();

	$SO_LINE = $_GET['SO_LINE'];

	if(strpos($SO_LINE,"-")===false){
		$response = [
			'status' => false,
			'mess' =>  'VUI LÒNG NHẬP ĐÚNG SO LINE',
		];
		echo json_encode($response);die;
	}else{
		if(!is_numeric(str_replace("-","",$SO_LINE))){
			$response = [
				'status' => false,
				'mess' =>  'VUI LÒNG NHẬP ĐÚNG SO LINE',
			];
			echo json_encode($response);die;
		}
	}

	$table = "save_material";

	// check soline đã làm lệnh chưa
	$checkSave = MiQuery("SELECT SO_LINE FROM $table where SO_LINE='$SO_LINE';", $conn);

	if(empty($checkSave)){
		$response = [
			'status' => false,
			'mess' =>  " $SO_LINE CHƯA LÀM LỆNH SẢN XUẤT. \n $SO_LINE CHƯA LÀM LỆNH SẢN XUẤT. \n $SO_LINE CHƯA LÀM LỆNH SẢN XUẤT. \n $SO_LINE CHƯA LÀM LỆNH SẢN XUẤT. \n $SO_LINE CHƯA LÀM LỆNH SẢN XUẤT. "
		];	
	} else {
		$sql_get_no = "SELECT t2.ID_SAVE_ITEM FROM so_may t1 join $table t2 on t1.SO_LINE=t2.SO_LINE where t1.SO_LINE='$SO_LINE';";
		$rows_get_no = MiQuery($sql_get_no, $conn);
		
		if(!empty($rows_get_no)){
			$response = [
				'status' => true,
				'mess' =>  "",
				'data' => $rows_get_no
			];	
		}else{
			$response = [
				'status' => false,
				'mess' =>  "$SO_LINE CHƯA CÓ KẾ HOẠCH",
			];
		}
	}

	mysqli_close($conn);
	echo json_encode($response);
