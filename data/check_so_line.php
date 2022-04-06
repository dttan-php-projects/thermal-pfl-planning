<?php
	require_once("../Database.php");
	$conn = getConnection();

	$EXIST_SO = 0;
	$SO_LINE = $_GET['SO_LINE'];
	
	$sql_count = "SELECT COUNT(ID) FROM save_material WHERE SO_LINE='$SO_LINE'";
	$rows_count = MiQuery($sql_count, $conn);

	if(!empty($rows_count)){
		$EXIST_SO  = 1;
	}
	echo $EXIST_SO;
