<?php
	function getConnection($db=null)
	{
		if ($db == null) $db = "au_avery_thermal_pfl"; // mặc định
		$host = "147.121.56.227";
		$username = "planning";
		$password = "PELS&Auto@{2020}";
		$conn = mysqli_connect($host, $username, $password, $db) or die('Không thể kết nối tới Server ' . $host);
		$conn->query("SET NAMES 'utf8'");

		return $conn;
	}


	function MiQuery($Query, $conn)
	{
		$result = $conn->query($Query);
		if ($result) {
			$results = mysqli_fetch_all($result, MYSQLI_ASSOC);
		} else {
			$results = array();
		}

		if (count($results) == 1) {
			$Value = "";
			$i = 0;
			foreach ($results[0] as $K => $V) {
				$Value = $V;
				$i++;
			}
			if ($i == 1) return $Value;
		}

		return $results;
	}

	function MiNonQuery($Query, $conn)
	{
		$result = $conn->query($Query);
	}

	function toQuery($Query, $conn)
	{

		$results = array();
		$result = mysqli_query($conn, $Query);
		if (!$result) return array();

		$rowCount = mysqli_num_rows($result);
		if ($rowCount > 0) {
			if ($rowCount == 1) {
				$results = mysqli_fetch_array($$result, MYSQLI_ASSOC);
			} else {
				$results = mysqli_fetch_all($$result, MYSQLI_ASSOC);
			}
		}


		return $results;
	}


	// Sử dụng cho đăng nhập
	function MiNonQuery2($Query,$dbMi = null) {
		if($dbMi == null) $dbMi = getConnection();
		$dbMi->query("SET NAMES 'utf8'");
		if(!$dbMi->query($Query)){
			echo $dbMi->error;
			mysqli_close($dbMi);
			return false;
		} else {
			mysqli_close($dbMi);
			return true;
		}
	}
