<?php
$script = basename($_SERVER['PHP_SELF']);
$urlRoot = str_replace($script, '', $_SERVER['PHP_SELF']);

require_once("Database.php");
$conn = getConnection();
$conn2 = getConnection('au_avery');

// @tandoan: 20200907 - remark NHAN CAP
function checkNhancap($RBO, $BILL_TO, $SHIP_TO)
{
	$check = false;
	// tách ra từng điều kiện để sau này có thể thay đổi
	// rbo PUMA AG
	if (strpos(strtoupper($RBO), 'PUMA') !== false) {
		// bill to TSG INTL LTD
		if ((strpos(strtoupper($BILL_TO), 'TSG INTL') !== false) || (strpos(strtoupper($BILL_TO), 'TSG INTERNATIONAL') !== false) || (strtoupper($BILL_TO) == 'CONG TY TNHH WORLDON (VIET NAM)')) {
			// ship to CTY TNHH MAY MAC LEADING STAR
			if ((strpos(strtoupper($SHIP_TO), 'LEADING STAR') !== false) || (strtoupper($SHIP_TO) == 'CONG TY TNHH WORLDON (VIET NAM)')) {
				$check = true;
			}
		}
	}

	return $check;
}
// get remark
function remarkNhancap2($SO_LINE, $QTY, $CUST_PO_NUMBER, $conn, $check)
{
	$remark = '';
	if ($check == true) {
		$SO_LINE_ARR = explode('-', $SO_LINE);
		$LINE_NUMBER = isset($SO_LINE_ARR[1]) ? $SO_LINE_ARR[1] : '';

		if (!empty($LINE_NUMBER)) {

			$results = array();
			// query
			$sql = "SELECT ORDER_NUMBER, LINE_NUMBER, QTY, CUST_PO_NUMBER  FROM vnso WHERE ORDER_NUMBER = $SO_LINE_ARR[0] ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC; ";
			$query = mysqli_query($conn, $sql);
			if (mysqli_num_rows($query) > 1) {
				$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
			} else {
				$sql = "SELECT ORDER_NUMBER, LINE_NUMBER, QTY, CUST_PO_NUMBER  FROM vnso_total WHERE ORDER_NUMBER = $SO_LINE_ARR[0] ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC; ";
				$query = mysqli_query($conn, $sql);
				if (mysqli_num_rows($query) > 1) {
					$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
				}
			}

			// check !empty
			if (!empty($results)) {
				foreach ($results as $key => $item) {
					// get data
					$QTY_TMP = (int)$item['QTY'];
					$CUST_PO_NUMBER_TMP = $item['CUST_PO_NUMBER'];
					$LINE_NUMBER_TMP = $item['LINE_NUMBER'];

					// check continue: Trường hợp line check là line đang muốn kiểm tra
					if ($LINE_NUMBER_TMP == $LINE_NUMBER) continue;

					// check 
					if (($QTY_TMP == $QTY) && ($CUST_PO_NUMBER_TMP == $CUST_PO_NUMBER)) {
						$remark = "NHÃN CẶP";
						break;
					}
				}
			}
		}
	}

	return $remark;
}

// get remark
function remarkNhancap($SO_LINE, $QTY, $CUST_PO_NUMBER, $conn, $check)
{
	$remark = '';
	$lineCombine = '';

	if ($check == true) {

		$remark = "Combine Line: ";

		$SO_LINE_ARR = explode('-', $SO_LINE);
		$LINE_NUMBER = isset($SO_LINE_ARR[1]) ? $SO_LINE_ARR[1] : '';

		if (!empty($LINE_NUMBER)) {

			$results = array();
			// query
			$sql = "SELECT ORDER_NUMBER, LINE_NUMBER, QTY, CUST_PO_NUMBER  FROM vnso WHERE ORDER_NUMBER = $SO_LINE_ARR[0] ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC; ";
			$query = mysqli_query($conn, $sql);
			if (mysqli_num_rows($query) > 1) {
				$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
			} else {
				$sql = "SELECT ORDER_NUMBER, LINE_NUMBER, QTY, CUST_PO_NUMBER  FROM vnso_total WHERE ORDER_NUMBER = $SO_LINE_ARR[0] ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC; ";
				$query = mysqli_query($conn, $sql);
				if (mysqli_num_rows($query) > 1) {
					$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
				}
			}

			// check !empty
			if (!empty($results)) {
				foreach ($results as $key => $item) {
					// get data
					$QTY_TMP = (int)$item['QTY'];
					$CUST_PO_NUMBER_TMP = $item['CUST_PO_NUMBER'];
					$LINE_NUMBER_TMP = $item['LINE_NUMBER'];

					// check continue: Trường hợp line check là line đang muốn kiểm tra
					if ($LINE_NUMBER_TMP == $LINE_NUMBER) {
						$lineCombine .= "$LINE_NUMBER_TMP+";
						continue;
					}

					// check 
					if (($QTY_TMP == $QTY) && ($CUST_PO_NUMBER_TMP == $CUST_PO_NUMBER)) {
						$lineCombine .= "$LINE_NUMBER_TMP+";
						// break;
					}
				}
			}
		}
	}

	if (!empty($lineCombine)) {
		$lineCombine = substr($lineCombine, 0, -1);
		$lineCombine = rtrim($lineCombine, '+');
	}

	return ($remark . $lineCombine);
}

// Remark combine của đơn hàng RBO là Under Armour và 3 ship to: ECLAT TEXTILE, COLLTEX GARMENT, TAI-YUAN GARMENTS
function remarkCombineUA($SO_LINE, $checkUA, $conn)
{
	$remark = '';
	$lineCombine = '';

	if ($checkUA == true) {

		$remark = "Combine Line: <br/>";

		$SO_LINE_ARR = explode('-', $SO_LINE);
		$LINE_NUMBER = isset($SO_LINE_ARR[1]) ? $SO_LINE_ARR[1] : '';

		if (!empty($LINE_NUMBER)) {

			$results = array();
			// query
			$sql = "SELECT ORDER_NUMBER, LINE_NUMBER  FROM vnso WHERE ORDER_NUMBER = $SO_LINE_ARR[0] ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC; ";
			$query = mysqli_query($conn, $sql);
			if (mysqli_num_rows($query) > 1) {
				$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
			} else {
				$sql = "SELECT ORDER_NUMBER, LINE_NUMBER  FROM vnso_total WHERE ORDER_NUMBER = $SO_LINE_ARR[0] ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC; ";
				$query = mysqli_query($conn, $sql);
				if (mysqli_num_rows($query) > 1) {
					$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
				}
			}
		}

		// check !empty
		if (!empty($results)) {
			// get data
			$count = count($results) - 1;
			$lineBegin = $results[0]['LINE_NUMBER'];
			$lineLast = $results[$count]['LINE_NUMBER'];
			$lineCombine = "$lineBegin-$lineLast";
		}

		if (!empty($lineCombine)) {
			$remark .= $lineCombine;
		}
	}

	return $remark;
}

// Cho đơn hàng NIKE và Shipto = WORLDON
function remarkCombineNike($SO_LINE, $checkNike, $conn)
{
	$remark = '';
	$lineCombine = '';

	if ($checkNike == true) {

		$remark = "Combine Line: <br/>";

		$SO_LINE_ARR = explode('-', $SO_LINE);
		$LINE_NUMBER = isset($SO_LINE_ARR[1]) ? $SO_LINE_ARR[1] : '';

		if (!empty($LINE_NUMBER)) {

			$results = array();
			// query
			$sql = "SELECT ORDER_NUMBER, LINE_NUMBER, SHIP_TO_CUSTOMER  FROM vnso WHERE ORDER_NUMBER = $SO_LINE_ARR[0] ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC; ";
			$query = mysqli_query($conn, $sql);
			if (mysqli_num_rows($query) > 1) {
				$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
			} else {
				$sql = "SELECT ORDER_NUMBER, LINE_NUMBER, SHIP_TO_CUSTOMER  FROM vnso_total WHERE ORDER_NUMBER = $SO_LINE_ARR[0] ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC; ";
				$query = mysqli_query($conn, $sql);
				if (mysqli_num_rows($query) > 1) {
					$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
				}
			}
		}

		// check !empty
		if (!empty($results)) {
			// get data
			$count = count($results) - 1;
			$lineBegin = $results[0]['LINE_NUMBER'];
			$lineLast = $results[$count]['LINE_NUMBER'];
			$lineCombine = "$lineBegin-$lineLast";
		}

		if (!empty($lineCombine)) {
			$remark .= $lineCombine;
		}
	}

	return $remark;
}

// @tandoan: 20200704 - remark item special (Hoanh.Nguyen)
function remarkSpecialItem($itemCheck)
{

	$conn = getConnection("au_avery_oe");
	$remarkSpecialItem = '';
	$specialItem_list = '';

	// get special item  array
	$sql = "SELECT ITEM FROM oe_special_remark ORDER BY ID ASC ";
	$query = mysqli_query($conn, $sql);
	if (mysqli_num_rows($query) > 0) {
		$specialItem_list = mysqli_fetch_all($query, MYSQLI_ASSOC);

		foreach ($specialItem_list as $specialItem) {
			if ($itemCheck == $specialItem['ITEM']) {
				$remarkSpecialItem = 'HÀNG ĐẶC BIỆT';
				break;
			}
		}
	}

	if ($conn) mysqli_close($conn);

	return $remarkSpecialItem;
}

/* GIAO CUỘN */
function remarkGiaoCuon($rbo, $ship_to_customer, $item)
{
	$result = array();
	$ship_to_arr = array('NY HOA VIET', 'AVERY DENNISON RIS KOREA', 'NAMYANG', 'NY SANFELIX');
	$item_arr = array('CB495981A', 'P538168A', 'P526296A', 'CB542737A', 'CB495920A');

	if (strpos(strtoupper($rbo), 'KOHL') !== false) {

		foreach ($ship_to_arr as $shipto) {
			if (strpos(strtoupper($ship_to_customer), $shipto) !== false) {
				foreach ($item_arr as $itemC) {
					if (strpos(strtoupper($item), $itemC) !== false) {
						$result = array(
							'cut_type' => 'GIAO HÀNG DẠNG CUỘN',
							'cut_type_note' => 'Giao cuộn, 1 size/cuộn'
						);
						break;
					}
				}

				break;
			}
		}
	}

	return $result;
}


/* @TanDoan - 20210526: GIAO CUỘN */
function remarkGiaoCuon2($bill_to_customer, $item)
{
	$result = '';
	$bill_to_arr = array('HA HAE');
	$item_arr = array('CB334663');

	foreach ($bill_to_arr as $billto) {
		if (strpos(strtoupper($bill_to_customer), $billto) !== false) {
			foreach ($item_arr as $itemC) {
				if (strpos(strtoupper($item), $itemC) !== false) {
					$result = 'GIAO CUỘN';
					break;
				}
			}

			break;
		}
	}

	return $result;
}


/* @TanDoan - 20220119
	TRƯỜNG HỢP HIỂN THỊ REMARK COMBINE CHO ĐƠN HÀNG RBO=ADIDAS & SHIP TO=WORLDON
	email: Re: Worldon Adidas-Rebook Special Packing Request thể hiện chữ ''COMBINE LINE 1 + 2''
	1. Đơn hàng RBO=ADIDAS & Ship to = WORLDON
	2. Cùng SO#
	3. Cùng Customer PO Number 
	4. Cùng QTY
	5. Các line liên tiếp
*/ 
function remarkCombineAdidas($SO_LINE, $CUST_PO_NUMBER, $QTY, $check, $conn )
{
	if ($check == false ) return '';
	
	$result = '';
	$lineCombine = array();
	$shipto = 'adidas';
	$table = 'vnso';
	$table2 = 'vnso_total';
	
	// tách SOLine
	$so_line_arr = explode('-', $SO_LINE);
	$order_number = isset($so_line_arr[0]) ? $so_line_arr[0] : '';
	$line_number = isset($so_line_arr[1]) ? $so_line_arr[1] : '';

	// chặn lỗi rỗng dữ liệu
	if (empty($order_number) || empty($line_number) ) return '';

	// Lấy dữ liệu Automail của tất cả line trong SO#
	$fiels = 'LINE_NUMBER';
	$where = "`ORDER_NUMBER`='$order_number' AND `SHIP_TO_CUSTOMER` like '%WORLDON%' AND `CUST_PO_NUMBER`='$CUST_PO_NUMBER' AND `QTY`='$QTY' ORDER BY LENGTH(`LINE_NUMBER`), `LINE_NUMBER` ASC;";
	$sql1 = "SELECT $fiels FROM $table WHERE $where ";
	$sql2 = "SELECT $fiels FROM $table2 WHERE $where ";
	
	$data = MiQuery($sql1, $conn );
	if (empty($data) ) {
		$data = MiQuery($sql2, $conn );
	}

	if (empty($data) ) return '';

	// Phân tích dữ liệu
	$result = "Combine Line: <br>";
	// Cùng SO#
	$line_number_tmp = 0;
	foreach ($data as $key => $value ) {
		$line_number_check = (int)$value['LINE_NUMBER'];

		if ($key == 0 ) {
			$line_number_tmp = $line_number_check;
			$lineCombine[] = $line_number_check;
		} else {
			if ($line_number_check == $line_number_tmp ) {
				$lineCombine[] = $line_number_check;
			} else {
				// Nếu không liên tiếp out
				break;
			}
		}

		// Tăng lên 1 đơn vị để giữ vị trí liên tiếp
		$line_number_tmp = $line_number_check+1;
		
	}

		if ( (count($lineCombine) > 1) && in_array($line_number, $lineCombine) ) {

			$result .= implode('+', $lineCombine );
		}
	


	return $result;
}

function gpmData($RBO, $automaiItem )
{

	// init 
	$result = '';

	// get data
	$CUST_PO_NUMBER = trim($automaiItem['CUST_PO_NUMBER']);
	$CUSTOMER_JOB = trim($automaiItem['CUSTOMER_JOB']);
	$INVOICE_LINE_INSTRUCTIONS = trim($automaiItem['INVOICE_LINE_INSTRUCTIONS']);
	$ORDER_SOURCE_NAME = trim($automaiItem['ORDER_SOURCE_NAME']);
	
	// check
	if (strpos(strtoupper($RBO), 'PATAGONIA') !== false ) {

		// Lấy nội dung trước dấu / cột CUST_PO_NUMBER
		if (strpos($CUST_PO_NUMBER, '/') !== false ) {

			// detached
			$detached = explode('/', $CUST_PO_NUMBER);

			// result
			$result = trim($detached[0]);
		}


	} else if (strpos(strtoupper($RBO), 'CLARKS UK') !== false ) {

		// ORDER_SOURCE_NAME là COPY hoặc VIPS Import => Lấy vị trí cuối cùng sau dấu : cột INVOICE_LINE_INSTRUCTIONS (lượt bỏ dấu ^)
		$orderSourceCheck = array('COPY', 'VIPS IMPORT');
		// Trường hợp không nằm trong các trường hợp ORDER SOURCE CHECK và không có dấu : thì bỏ qua
		if (in_array($ORDER_SOURCE_NAME, $orderSourceCheck) && (strpos($INVOICE_LINE_INSTRUCTIONS, ':') !== false ) ) {
			
			// detached
			$detached = explode(':', $INVOICE_LINE_INSTRUCTIONS);
			$detached = str_replace('^', '', $detached);

			// result
			$result = $detached[count($detached)-1];
		}


	} else if (strpos(strtoupper($RBO), 'LEVI STRAUSS') !== false ) {

		// Lấy từ chữ AD đến hết dữ liệu cột CUST_PO_NUMBER. Trường hợp k có chữ AD thì bỏ qua
		if (strpos(strtoupper($CUST_PO_NUMBER), 'AD') !== false ) {
			
			// detached
			$detached = explode('AD', $CUST_PO_NUMBER);

			// result
			$result = "AD". $detached[count($detached)-1];

		}


	} else if (strpos(strtoupper($RBO), 'ANN TAYLOR') !== false ) {


		// Lấy nội dung cuối cùng sau dấu / cột CUST_PO_NUMBER
		if (strpos(strtoupper($CUST_PO_NUMBER), '/') !== false ) {
			
			// detached
			$detached = explode('/', $CUST_PO_NUMBER);

			// result
			$result = "/". $detached[count($detached)-1];

		}


	} else {
		
		// Các trường hợp ngược lại lấy cột CUSTOMER JOB. Lấy CUSOTMER JOB có chuỗi dài = 7 ký tự
		if (strlen($CUSTOMER_JOB) == 7 ) {

			$result = "*" . $CUSTOMER_JOB . "*";

		}

	}

	return $result;
}



if (empty($_GET['id'])) {
	echo 'VUI LÒNG NHẬP LỆNH SẢN XUẤT';
	die;
}
$id = $_GET['id'];
if (!empty($id)) {
	$sql_item = "SELECT * FROM save_item WHERE NUMBER_NO='$id'";
	$result_item = MiQuery($sql_item, $conn);
	if (empty($result_item[0])) {
		echo 'LỆNH SẢN XUẤT KHÔNG TỒN TẠI.';
		die;
	}
	$result_item = $result_item[0];

	// print_r($result_item); 
	
	$pathERP = dirname($_SERVER['SCRIPT_FILENAME']);
	if (!empty($result_item)) {
		// UPDATED 
		$sql_update_PRINTED = "UPDATE save_item SET `PRINTED`='1' WHERE `NUMBER_NO`='$id'";
		$conn->query($sql_update_PRINTED);

		require_once($pathERP . "/print/pro_item.php"); //  xu ly item
		require_once($pathERP . "/print/pro_supply.php"); //  xu ly supply
		require_once($pathERP . "/print/form_no_cbs.php"); //  xu ly supply        
	} else {
		echo 'LỆNH SẢN XUẤT KHÔNG TỒN TẠI.';
		die;
	}
} else {
	echo 'VUI LÒNG NHẬP LỆNH SẢN XUẤT';
}
