<style type="text/css">
	.form-style-10{
		width:90%;
		padding:30px;
		margin:20px auto;
		font: 15px Arial, Helvetica, sans-serif;
	}
	.form-style-10 .inner-wrap{
		padding: 30px;
		border-radius: 6px;
		margin-bottom: 15px;
	}
	.form-style-10 label{
		color: #888;
		margin-bottom: 15px;
		float:left;
		margin-right:2%;
		width:47%;
	}
	.form-style-10 input[type="text"]{
		display: block;
		box-sizing: border-box;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		width: 100%;
		padding: 8px;
		font-size:15px;
		font-weight:bold;
	}

	.form-style-10 .section{
		color: #2A88AD;
		margin-bottom: 5px;
		text-align:center;
		font-weight:bold;
	}
</style>
<?php

	// @tandoan: xử lý lại Prefix NO trong bảng settings
	function updateNOSettings($conn)
	{
		$NUMBER_NO = MiQuery("SELECT `NUMBER_NO` FROM save_item ORDER BY ID DESC LIMIT 1;",$conn);
		$PRE_FIX_NO = MiQuery("SELECT `VALUE` FROM settings where `NAME`='PRE_FIX_NO' LIMIT 1;",$conn);

		if (empty($NUMBER_NO) ) {
			$PRE_FIX = "LRPM" . date('ym');
		} else {
			$PRE_FIX = (strpos($NUMBER_NO, '-') !==false ) ? explode('-', $NUMBER_NO)[0] : ("LRPM" . date('ym') ) ;
		}

		$PRE_FIX_SAVE = $PRE_FIX;

		// Chỉ lấy 4 ký tự (năm tháng)
		$PRE_FIX = substr($PRE_FIX,4,4 );
		$PRE_FIX_NO = substr($PRE_FIX_NO,4,4 );

		// Nếu trong bảng save, nằm tháng NO > năm tháng NO trong bảng settings thì update lại thông tin trong settings
		// mặc định bảng đã có dữ liệu
		if ((int)$PRE_FIX > (int)$PRE_FIX_NO ) {
			mysqli_query($conn, "UPDATE settings SET `VALUE` = '$PRE_FIX_SAVE' WHERE `NAME`='PRE_FIX_NO'; " );
		}
	}

	//connect to database
		require_once("../Database.php");
		$conn = getConnection();

	// @tandoan: update giá trị PREFIX NO trong settings (nếu có)
		updateNOSettings($conn);

	$sql_PRE_FIX_NO = "SELECT `VALUE` FROM settings WHERE `NAME`='PRE_FIX_NO' LIMIT 0,1; ";
	$PRE_FIX_NO = MiQuery($sql_PRE_FIX_NO,$conn);

	mysqli_close($conn);

	// get NO MAX
	$yearMonth = date('ym');
	if(!empty($PRE_FIX_NO)){
		$PREFIX				= $PRE_FIX_NO;
	}else{
		$PREFIX				= "LRPM".$yearMonth;
	}
	$PREFIX 						= $PREFIX."-XXXX";
	$DATE_CREATE 					= date('d-M-Y');
	$ITEM_NUMBER 					= !empty($_POST['ITEM_NUMBER'])?trim($_POST['ITEM_NUMBER']):'';
	$ITEM_CODE 						= !empty($_POST['ITEM_CODE'])?trim($_POST['ITEM_CODE']):'';
	$ORDER 							= !empty($_POST['ORDER'])?trim($_POST['ORDER']):'';
	$REQ 							= !empty($_POST['REQ'])?trim($_POST['REQ']):'';
	$PD 							= !empty($_POST['PD'])?trim($_POST['PD']):'';
	$VAT_TU_THUC_TE 				= !empty($_POST['VAT_TU_THUC_TE'])?trim($_POST['VAT_TU_THUC_TE']):0;
	$PCS_MAU 						= !empty($_POST['PCS_MAU'])?trim($_POST['PCS_MAU']):0;
	$BU_HAO_PO 						= !empty($_POST['BU_HAO_PO'])?trim($_POST['BU_HAO_PO']):0;
	$TI_LE_BU_HAO 					= !empty($_POST['TI_LE_BU_HAO'])?trim($_POST['TI_LE_BU_HAO']):0;
	$CUT_TYPE 						= !empty($_POST['CUT_TYPE'])?trim($_POST['CUT_TYPE']):'';
	$QTY 							= !empty($_POST['QTY'])?trim($_POST['QTY']):'';
	$MACHINE 						= !empty($_POST['MACHINE'])?trim($_POST['MACHINE']):'';
	$SO_MAT_IN 						= !empty($_POST['SO_MAT_IN'])?trim($_POST['SO_MAT_IN']):0;
	$REMARK 						= !empty($_POST['REMARK'])?trim($_POST['REMARK']):'';
	$NOTE_ITEM 						= !empty($_POST['NOTE_ITEM'])?trim($_POST['NOTE_ITEM']):'';
	$WIDTH 							= !empty($_POST['WIDTH'])?trim($_POST['WIDTH']):0;
	$LENGTH 						= !empty($_POST['LENGTH'])?trim($_POST['LENGTH']):0;
	$ORDER_TYPE_NAME 				= !empty($_POST['ORDER_TYPE_NAME'])?trim($_POST['ORDER_TYPE_NAME']):'';
	if(strpos($ORDER_TYPE_NAME,'VN QR')!==false){
		$FR = '-FR';
		$PREFIX=$PREFIX.$FR;
	}
?>
<div class="form-style-10">
	<form>
		<div class="section">LỆNH SẢN XUẤT/PRODUCTION ORDER</div>
		<div class="inner-wrap">
			<label>NO MAX(4 số chỉ nhập nếu cần thiết):<input type="text" id="NO_MAX" value=""/></label>
			<label>No<input type="text" id="PREFIX" value="<?php echo $PREFIX;?>"/></label>		
			<label>Ngày/Date<input type="text" id="DATE_CREATE" value="<?php echo $DATE_CREATE;?>"/></label>
			<label>Order date<input type="text" id="ORDER" value="<?php echo $ORDER;?>"/></label>
			<label>Mã hàng/Item<input type="text" id="ITEM_NUMBER" value="<?php echo $ITEM_NUMBER;?>"/></label>
			<label>Mô tả/Description<input type="text" id="ITEM_CODE" value="<?php echo $ITEM_CODE;?>"/></label>
			<label>Promise date:<input type="text" id="PD" value="<?php echo $PD;?>"/></label>
			<label>Request date:<input type="text" id="REQ" value="<?php echo $REQ;?>"/></label>
			<label>Kích thước in<input type="text" id="WIDTH_LENGTH" value="<?php echo $WIDTH;?>mm x <?php echo $LENGTH;?>mm"/></label>
			<label>Số lượng:<input type="text" id="QTY" value="<?php echo $QTY;?>"/></label>
			<label>Số mặt in:<input type="text" id="SO_MAT_IN" value="<?php echo $SO_MAT_IN;?>"/></label>
			<label>Vật tư thực tế:<input type="text" id="VAT_TU_THUC_TE" value="<?php echo $VAT_TU_THUC_TE;?>"/></label>
			<label>15 pcs mẫu:<input type="text" id="PCS_MAU" value="<?php echo $PCS_MAU;?>"/></label>
			<label>Bù hao theo PO#:<input type="text" id="BU_HAO_PO" value="<?php echo $BU_HAO_PO;?>"/></label>
			<label>Tỉ lệ bù hao theo PO#:<input type="text" id="TI_LE_BU_HAO" value="<?php echo $TI_LE_BU_HAO;?>"/></label>
			<label>Cut/Fold Type:<input type="text" id="CUT_TYPE" value="<?php echo $CUT_TYPE;?>"/></label>	
			<label>May in/Machine:<input type="text" id="MACHINE" value="<?php echo $MACHINE;?>"/></label>
			<label>Ghi chú 1:<input type="text" id="REMARK" value="<?php echo $REMARK;?>"/></label>
			<label>Ghi chú 2:<input type="text" id="REMARK_2" value=""/></label>
			<label>Ghi chú item:<input type="text" id="NOTE_ITEM" value="<?php echo $NOTE_ITEM;?>"/></label>
			<div style="clear:left"></div>
		</div>
</form>
</div>
