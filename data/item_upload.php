<?php
set_time_limit(6000);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$message = 'Không xử lý được dữ liệu';
$UPDATED_BY = isset($_COOKIE['VNRISIntranet']) ? trim($_COOKIE['VNRISIntranet']) : '';

if (isset($_POST["submit"])) {

	$file_name = 'TPFL_MasterData_' . $UPDATED_BY . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
		$table = "master_item";

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
			$spreadSheet = $Reader->load($targetPath)->getSheet(0);
			$allDataInSheet = $spreadSheet->toArray(null, true, true, true);

			

			// check col name exist
			$createArray = array(
				'Part Number', 
				'Customer Item', 
				'Tape', 
				'Description Tape', 
				'Printing Method',

				'RB',
				'Width',
				'Length',
				'Ink',
				'Description Ink',

				'Sides',
				'Machine',
				'CutFold Type',
				'Note',
				'Cut Machine',
				'Brand Protection'
			);
			$makeArray = array(
				'PartNumber' => 'PartNumber', 
				'CustomerItem' => 'CustomerItem', 
				'Tape' => 'Tape', 
				'DescriptionTape' => 'DescriptionTape', 
				'PrintingMethod' => 'PrintingMethod',

				'RB' => 'RB',
				'Width' => 'Width',
				'Length' => 'Length',
				'Ink' => 'Ink',
				'DescriptionInk' => 'DescriptionInk',

				'Sides' => 'Sides',
				'Machine' => 'Machine',
				'CutFoldType' => 'CutFoldType',
				'Note' => 'Note',
				'CutMachine' => 'CutMachine',

				'BrandProtection' => 'BrandProtection'

			);
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

				$UPDATED_DATE = date('Y-m-d H:i:s');

				// get col key
				$item_number_key = $SheetDataKey['PartNumber'];
				$item_code_key = $SheetDataKey['CustomerItem'];
				$item_rm_key = $SheetDataKey['Tape'];
				$des_rm_key = $SheetDataKey['DescriptionTape'];
				$printing_key = $SheetDataKey['PrintingMethod'];

				$rbo_key = $SheetDataKey['RB'];
				$width_key = $SheetDataKey['Width'];
				$height_key = $SheetDataKey['Length'];
				$item_ink_key = $SheetDataKey['Ink'];
				$des_ink_key = $SheetDataKey['DescriptionInk'];

				$so_mat_in_key = $SheetDataKey['Sides'];
				$machine_key = $SheetDataKey['Machine'];
				$cut_type_key = $SheetDataKey['CutFoldType'];
				$note_key = $SheetDataKey['Note'];
				$cut_machine_key = $SheetDataKey['CutMachine'];

				$brand_protection_key = $SheetDataKey['BrandProtection'];

				// get data
				$count = 0;
				for ($i = 2; $i <= count($allDataInSheet); $i++) {

					$ITEM_NUMBER = addslashes(trim($allDataInSheet[$i][$item_number_key]));

					if (!empty($ITEM_NUMBER) ) {

						$ITEM_CODE = addslashes(trim($allDataInSheet[$i][$item_code_key]));
						$ITEM_RM = addslashes(trim($allDataInSheet[$i][$item_rm_key]));
						$DES_RM = addslashes(trim($allDataInSheet[$i][$des_rm_key]));
						$PRINTING = addslashes(trim($allDataInSheet[$i][$printing_key]));

						$RBO = addslashes(trim($allDataInSheet[$i][$rbo_key]));
						$WIDTH = addslashes(trim($allDataInSheet[$i][$width_key]));
						$HEIGHT = addslashes(trim($allDataInSheet[$i][$height_key]));
						$ITEM_INK = addslashes(trim($allDataInSheet[$i][$item_ink_key]));
						$DES_INK = addslashes(trim($allDataInSheet[$i][$des_ink_key]));

						$SO_MAT_IN = addslashes(trim($allDataInSheet[$i][$so_mat_in_key]));
						$MACHINE = addslashes(trim($allDataInSheet[$i][$machine_key]));
						$CUT_TYPE = addslashes(trim($allDataInSheet[$i][$cut_type_key]));
						$NOTE = addslashes(trim($allDataInSheet[$i][$note_key]));
						$CUT_MACHINE = addslashes(trim($allDataInSheet[$i][$cut_machine_key]));

						$BRAND_PROTECTION = addslashes(trim($allDataInSheet[$i][$brand_protection_key]));

						$sql_check = "SELECT COUNT(*) FROM $table WHERE ITEM_NUMBER='$ITEM_NUMBER'";
						$rowsCheck = MiQuery($sql_check, $conn);
						if ($rowsCheck > 0) {
							// update
							$sql = "UPDATE $table SET `ITEM_CODE`='$ITEM_CODE',`ITEM_RM`='$ITEM_RM',`DES_RM`='$DES_RM',`PRINTING`='$PRINTING',`WIDTH`='$WIDTH',`HEIGHT`='$HEIGHT',`ITEM_INK`='$ITEM_INK',`DES_INK`='$DES_INK',`SO_MAT_IN`='$SO_MAT_IN',`MACHINE`='$MACHINE',`CUT_TYPE`='$CUT_TYPE',`NOTE`='$NOTE',`CUT_MACHINE`='$CUT_MACHINE',`BRAND_PROTECTION`='$BRAND_PROTECTION', `UPDATED_BY`='$UPDATED_BY', `UPDATED_DATE`='$UPDATED_DATE' where ITEM_NUMBER='$ITEM_NUMBER'";
						} else {
							// insert
							$sql = "INSERT INTO $table 
												(`ITEM_NUMBER`,`ITEM_CODE`, `ITEM_RM`,`DES_RM`,`PRINTING`,`RBO`,`WIDTH`,`HEIGHT`,`ITEM_INK`,`DES_INK`,`SO_MAT_IN`,`MACHINE`,`CUT_TYPE`,`NOTE`,`CUT_MACHINE`,`BRAND_PROTECTION`,`UPDATED_BY`) 
											VALUES 
												('$ITEM_NUMBER','$ITEM_CODE','$ITEM_RM','$DES_RM','$PRINTING','$RBO','$WIDTH','$HEIGHT','$ITEM_INK','$DES_INK','$SO_MAT_IN','$MACHINE','$CUT_TYPE','$NOTE','$CUT_MACHINE','$BRAND_PROTECTION','$UPDATED_BY')
									;";
						}

						// Nếu lỗi
						if (!mysqli_query($conn, $sql)) {

							$message = 'Có lỗi xảy ra trong quá trình Import. Item: ' . $ITEM_NUMBER;
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