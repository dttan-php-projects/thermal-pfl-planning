<?php
clearstatcache();

ini_set('max_execution_time',6000);
ini_set('memory_limit', '-1');
date_default_timezone_set('Asia/Ho_Chi_Minh');

$message = 'Không xử lý được dữ liệu';
$UPDATED_BY = isset($_COOKIE['VNRISIntranet']) ? trim($_COOKIE['VNRISIntranet']) : '';

if (isset($_POST["submit"])) {

	$file_name = 'TPFL_Machine_' . $UPDATED_BY . '_' . date('Y-m-d_H-i-s') . '.xlsx';
	$excelType = ['d/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/vnd.ms-excel.sheet.macroEnabled.12'];
	$fileSize = $_FILES['file']['size'];
	$fileType = $_FILES['file']['type'];

	if ($_FILES['file']['size'] > 1000000) {

		$message = 'File dữ liệu import quá lớn, Vui lòng kiểm tra lại';
	} elseif (!in_array($_FILES['file']['type'], $excelType)) {

		$message = 'File dữ liệu phải là EXCEL, Vui lòng kiểm tra lại';
	} else {

		// database
		include_once("../Database.php");
		$conn = getConnection();
		$table = "so_may";

		// PHPSpreedsheet get file
		include_once ("vendor/autoload.php");
		
		// save to the path
		$targetPath = '../Excel/' . $file_name;

		// hàm move_uploaded_file k sử dụng được (có thể do bị hạn chế quyền của thư mục tmp)
		if (!copy($_FILES['file']['tmp_name'], $targetPath)) {

			$message = 'Không copy được File Import vào hệ thống';

		} else {

			// init PhpSpreadsheet Xlsx
			$Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            
			// get sheet 0 (sheet 1)
			$spreadSheet = $Reader->load($_FILES['file']['tmp_name'])->getSheet(0);
			$allDataInSheet = $spreadSheet->toArray(null, true, true, true);

			 

			// check col name exist
			$createArray = array('SO_LINE', 'SO_MAY', 'STT', 'DATE', 'PLAN');
			$makeArray = array('SO_LINE' => 'SO_LINE', 'SO_MAY' => 'SO_MAY', 'STT' => 'STT', 'DATE' => 'DATE', 'PLAN' => 'PLAN');
			$SheetDataKey = array();
			foreach ($allDataInSheet as $dataInSheet) {
				foreach ($dataInSheet as $key => $value) {
					if (in_array(trim($value), $createArray)) {
						$value = preg_replace('/\s+/', '', $value);
						$SheetDataKey[trim($value)] = $key;
					} else {
					}
				}
			}

			// check data
			$flag = 0;
			$data = array_diff_key($makeArray, $SheetDataKey);
			if (empty($data)) {
				$flag = 1;
			}


			// kiểm tra dữ liệu
			if ( $flag == 1 ) {

				/* truncate ----------------------------------------------------------------------------------*/
					// không sử dụng truncate nữa 20220331
					// mysqli_query($conn, "TRUNCATE $table ;");

				/* ------------------------------------------------------------------------------------------*/

				// get col key
				$so_line_key = $SheetDataKey['SO_LINE'];
				$so_may_key = $SheetDataKey['SO_MAY'];
				$stt_key = $SheetDataKey['STT'];
				$date_key = $SheetDataKey['DATE'];
				$plan_key = $SheetDataKey['PLAN'];
				
				$count = 0;
				for ($i = 2; $i <= count($allDataInSheet); $i++) {

					$SO_LINE = addslashes(trim($allDataInSheet[$i][$so_line_key]));

					if (!empty($SO_LINE) ) {

						$SO_MAY = addslashes(trim($allDataInSheet[$i][$so_may_key]));
						$STT = addslashes(trim($allDataInSheet[$i][$stt_key]));
						$CREATE_DATE = addslashes(trim($allDataInSheet[$i][$date_key]));
						$PLAN = addslashes(trim($allDataInSheet[$i][$plan_key]));

						// check 
						$sql_check = "SELECT COUNT(*) FROM $table WHERE SO_LINE='$SO_LINE'";
						$rowsCheck = MiQuery($sql_check, $conn);					
						if($rowsCheck>0){
							// update
							$sql = "UPDATE $table SET `SO_MAY`='$SO_MAY',`STT`='$STT',`CREATE_DATE`=$CREATE_DATE,`PLAN`=$PLAN, WHERE SO_LINE='$SO_LINE'";
						}else{
							// insert
							$sql = "INSERT INTO $table 
										(`SO_LINE`,`SO_MAY`, `STT`,`CREATE_DATE`,`PLAN`) 
									VALUES 
										('$SO_LINE','$SO_MAY','$STT','$CREATE_DATE','$PLAN')
							;";
						}

						// Nếu lỗi
						if (!mysqli_query($conn, $sql)) {

							$message = 'Có lỗi xảy ra trong quá trình Import. Item: ' . $SO_LINE;
							break;
						} else {
							$count++;
							$message = 'Import thành công ' . $count . ' dòng.';
						}

					}
					
				}

			}
		}
	}
}

?>
<script>
	var message = '<?php echo $message; ?>';
	alert(message);

	window.location = "../";

</script>