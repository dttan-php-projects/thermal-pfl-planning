<?php
	// init 
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		ini_set('max_execution_time',300);  // set time 5 minutes

	// GET Method
		$FROM_DATE = $_GET['from_date_value'];
		$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
		$TO_DATE = $_GET['to_date_value'];
		$TO_DATE = date('Y-m-d',strtotime($TO_DATE));
	
	// function get date
		function formatDate($value){ return date('d-M-y',strtotime($value)); }
	
	// connect
		require_once ("../Database.php");
		$conn = getConnection();
		$filename = date("d_m_Y__H_i_s");
	
	// PHPSpreedsheet get file
	include_once ("vendor/autoload.php");

	// query
		$fields = 's_i.ID,s_i.CREATE_DATE,NUMBER_NO,ITEM_NUMBER,SO_LINE,REQ,RBO,ITEM_CODE,QTY,MATERIAL_CODE,MATERIAL_DES,MATERIAL_QTY,WIDTH,LENGTH,INK_CODE,INK_DES,INK_QTY,SO_MAT_IN,SO_LUONG_PO';
		// to do process so kho if(type_worst_vertical = 100-SB1) 10,5
		$query = "SELECT $fields 
					FROM save_item AS s_i 
					JOIN save_material AS s_m 
					ON s_m.ID_SAVE_ITEM = s_i.NUMBER_NO 
					WHERE (s_i.CREATE_DATE>='$FROM_DATE' AND s_i.CREATE_DATE<='$TO_DATE') 
					ORDER BY ID ASC
		;";
		
		$rowsResult = MiQuery($query, $conn);

	// close db
		mysqli_close($conn);

	// create
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

	// set the names of header cells
		// set Header, width
		$columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP');

		// Add some data
		$spreadsheet->setActiveSheetIndex(0);

		// active and set title
		$spreadsheet->getActiveSheet()->setTitle('Reports');

		$headers = [
			"DATE","LENH SX","SO#","NGAY GIAO","ITEM CODE","RBO","ORDER ITEM","SO LUONG","MA VAT TU","TEN VAT TU","SO LUONG VAT TU(YD)/(PCS)","CHIEU DAI (MM)","CHIEU RONG (MM)","MA MUC IN","TEN MUC IN","SO LUONG MUC (MET)","SO MAT IN","SO LUONG PO"
		];

		$id = 0;
		foreach ($headers as $header) {
			for ($index = $id; $index < count($headers); $index++) {
				// width
				$spreadsheet->getActiveSheet()->getColumnDimension($columns[$index])->setWidth(20);

				// headers
				$spreadsheet->getActiveSheet()->setCellValue($columns[$index] . '1', $header);

				$id++;
				break;
			}
		}


		// Font
		$spreadsheet->getActiveSheet()->getStyle('A1:R1')->getFont()->setBold(true)->setName('Arial')->setSize(10);
		$spreadsheet->getActiveSheet()->getStyle('A1:R1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('3399ff');
		$spreadsheet->getActiveSheet()->getStyle('A:R')->getFont()->setName('Arial')->setSize(10);



	// get data
		if(!empty($rowsResult)){
			
        	$rowCount = 1;	

			foreach ($rowsResult as $row){

				$rowCount++;

				// data
					$NUMBER_NO = $row['NUMBER_NO'];	
					$NUMBER_NO_ARR = explode('-',$NUMBER_NO);
					if(!empty($NUMBER_NO_ARR[2])){ // FR
						$NUMBER_NO = $NUMBER_NO_ARR[0];
						if($NUMBER_NO_ARR[1]>=1000){
							if(strlen($NUMBER_NO_ARR[1])==6){
								$NUMBER_NO.="-".ltrim($NUMBER_NO_ARR[1],0)."-".$NUMBER_NO_ARR[2];
							}else{
								$NUMBER_NO.="-".$NUMBER_NO_ARR[1]."-".$NUMBER_NO_ARR[2];
							}		
						}else{
							if(strlen($NUMBER_NO_ARR[1])==6){
								$NUMBER_NO.="-".substr($NUMBER_NO_ARR[1],2)."-".$NUMBER_NO_ARR[2];
							}else{
								$NUMBER_NO.="-".$NUMBER_NO_ARR[1]."-".$NUMBER_NO_ARR[2];
							}		
						}
					}else{
						$NUMBER_NO = $NUMBER_NO_ARR[0];
						if($NUMBER_NO_ARR[1]>=1000){
							if(strlen($NUMBER_NO_ARR[1])==6){
								$NUMBER_NO.="-".ltrim($NUMBER_NO_ARR[1],0);
							}else{
								$NUMBER_NO.="-".$NUMBER_NO_ARR[1];
							}		
						}else{
							if(strlen($NUMBER_NO_ARR[1])==6){
								$NUMBER_NO.="-".substr($NUMBER_NO_ARR[1],2);
							}else{
								$NUMBER_NO.="-".$NUMBER_NO_ARR[1];
							}		
						}	
					}
					$SO_LUONG_PO = $row['SO_LUONG_PO']; 
					$SAVE_DATE = $row['CREATE_DATE']; 
					$SAVE_DATE = formatDate($row['CREATE_DATE']);
					$SO_LINE = $row['SO_LINE']; 
					$REQ = $row['REQ'];
					$REQ = formatDate($REQ);
					$ITEM_NUMBER = $row['ITEM_NUMBER'];
					$ITEM_CODE = $row['ITEM_CODE'];
					//$ITEM_CODE = str_replace(',','|',$ITEM_CODE);
					$RBO = $row['RBO']; 
					//$RBO = str_replace(',','|',$RBO);
					$QTY = $row['QTY'];
					$MATERIAL_CODE = $row['MATERIAL_CODE'];	
					//$MATERIAL_CODE = str_replace(',','|',$MATERIAL_CODE);
					$MATERIAL_DES	 = $row['MATERIAL_DES'];
					//$MATERIAL_DES = str_replace(',','|',$MATERIAL_DES);
					$MATERIAL_QTY = $row['MATERIAL_QTY'];
					$WIDTH = $row['WIDTH'];
					$LENGTH = $row['LENGTH'];
					$INK_CODE = $row['INK_CODE'];
					//$INK_CODE = str_replace(',','|',$INK_CODE);
					$INK_DES = $row['INK_DES'];
					//$INK_DES = str_replace(',','|',$INK_DES);
					$INK_QTY = $row['INK_QTY'];
					$SO_MAT_IN = $row['SO_MAT_IN'];	

				// to save to excel MACY&#039;S INC  str_replace("&#039;", "'",$rbo)
					$spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $SAVE_DATE );
					$spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $NUMBER_NO);
					$spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $SO_LINE);
					$spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $REQ );
					$spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, $ITEM_NUMBER);
					$spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, str_replace("&#039;", "'",$RBO) );
					$spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, $ITEM_CODE );
					$spreadsheet->getActiveSheet()->SetCellValue('H' . $rowCount, $QTY );
					$spreadsheet->getActiveSheet()->SetCellValue('I' . $rowCount, $MATERIAL_CODE );
					$spreadsheet->getActiveSheet()->SetCellValue('J' . $rowCount, $MATERIAL_DES);
					$spreadsheet->getActiveSheet()->SetCellValue('K' . $rowCount, $MATERIAL_QTY );
					$spreadsheet->getActiveSheet()->SetCellValue('L' . $rowCount, $LENGTH );
					$spreadsheet->getActiveSheet()->SetCellValue('M' . $rowCount, $WIDTH);    
					$spreadsheet->getActiveSheet()->SetCellValue('N' . $rowCount, $INK_CODE);    
					$spreadsheet->getActiveSheet()->SetCellValue('O' . $rowCount, $INK_DES);   
					$spreadsheet->getActiveSheet()->SetCellValue('P' . $rowCount, $INK_QTY );   
					$spreadsheet->getActiveSheet()->SetCellValue('Q' . $rowCount, $SO_MAT_IN );   
					$spreadsheet->getActiveSheet()->SetCellValue('R' . $rowCount, $SO_LUONG_PO);  
					
			}
		}

		ob_clean();
	
	// out put
		$filename = 'TPFL_Reports_' . date("Y-m-d");

		// header: generate excel file
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		// writer
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save('php://output');
		