<?php
	date_default_timezone_set('Asia/Ho_Chi_Minh');

	require_once ("Database.php");
	$conn = getConnection();
	$conn2 = getConnection('au_avery');


	function getAutomailUpdated()
    {

		$_conn = getConnection('au_avery');
        $result = 'loading...';
        $data = MiQuery("SELECT `STATUS`, `CREATEDDATE` FROM autoload_log ORDER BY ID DESC LIMIT 1;", $_conn );
        if (!empty($data) ) {
			$data = $data[0];
			
            $status = $data['STATUS'];
            $created_date = $data['CREATEDDATE'];

            if ($status == 'OK' ) {
                $result = $created_date;
            } else {
				
                $dataOK = MiQuery("SELECT `STATUS`, `CREATEDDATE` FROM autoload_log WHERE `STATUS`='OK' ORDER BY ID DESC LIMIT 1;", $_conn );
                $created_date_OK = '';
                if (!empty($dataOK) ) {
					$dataOK = $dataOK[0];
                    $created_date_OK = $dataOK['CREATEDDATE'];
                }

                // 01: Không save được
				if ($status == 'ERR_01' ) {
					$result = "$created_date_OK. (ERR 01 (UPDATE) lúc $created_date)";
				} else if ($status == 'ERR_02' ) { // có rỗng dữ liệu PACKING,...
					$result = "$created_date_OK. (ERR 02 (EMPTY DATA) lúc $created_date)";
				} else if ($status == 'ERR_03' ) { // File không đọc được
					$result = "$created_date_OK. (ERR 03 (File Lỗi) lúc $created_date)";
				} 
            }
            
        }

		if ($_conn ) mysqli_close($_conn);

        return $result;
    }


	function getUser() 
    {
        $email = isset($_COOKIE["VNRISIntranet"]) ? trim($_COOKIE["VNRISIntranet"]) : "";
        return $email;
    }

    function planning_user_statistics($email, $program )
    {
        if (!empty($email) ) {
            $table = 'planning_user_statistics';
            $ip = $_SERVER['REMOTE_ADDR'];

            $url = "http://" .$_SERVER["SERVER_ADDR"] .$_SERVER["REQUEST_URI"];

            $METADATA = "HTTP_COOKIE: " . $_SERVER["HTTP_COOKIE"]. "PATH: " .$_SERVER["PATH"]. "SERVER_ADDR" .$_SERVER["SERVER_ADDR"]. "SERVER_PORT" .$_SERVER["SERVER_PORT"]. "DOCUMENT_ROOT" .$_SERVER["DOCUMENT_ROOT"]. "SCRIPT_FILENAME" .$_SERVER["SCRIPT_FILENAME"];
            $METADATA = mysqli_real_escape_string(getConnection("au_avery"), $METADATA);

            // update data
            $key = $email . $program;
            $updated = date('Y-m-d H:i:s');
            $check = MiQuery("SELECT `email` FROM $table WHERE CONCAT(`email`,`program`) = '$key';", getConnection('au_avery') );
            if (!empty($check) ) {
                $sql = "UPDATE $table SET `ip` = '$ip', `url` = '$url', `METADATA` = '$METADATA', `updated` = '$updated'  WHERE `email` = '$email' AND `program` = '$program';";
            } else {
                // Thêm mới. Tự động nên không trả về kết quả
                $sql = "INSERT INTO $table (`email`, `program`, `ip`, `url`, `METADATA`, `updated`) VALUE ('$email', '$program', '$ip',  '$url', '$METADATA', '$updated');";
            }

            return MiNonQuery2( $sql,getConnection("au_avery"));
            
        }
        
        
    }

	$email = getUser();
	

	// check role
	$rowsResult = MiQuery("SELECT * FROM user", $conn);
	if ($conn ) mysqli_close($conn);
	
	if(!empty($rowsResult)){
		foreach ($rowsResult as $row){
			$arrayRole[]=$row['EMAIL'];
		}
	}else{
		$arrayRole = ['anhthu.pham','uyen.le','trang.huynhphuong','thang.bui','hieu.tran', 'tan.doan', 'truong.hoang', 'beo.nguyen'];
	}
	if(!empty($_COOKIE["VNRISIntranet"])){
		$user = $_COOKIE["VNRISIntranet"];
		if(!empty($user)&&in_array($user,$arrayRole)){
			$deleteNO = 1;
		}else{
			$deleteNO = 0;
		}
	}else{
		$deleteNO = 0;
	}
	
	

?>
<!DOCTYPE html>
<html>
<head>
    <title>THERMAL PFL Planning</title>
	<link rel="icon" href="./Module/images/Logo.ico" type="image/x-icon">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="google" content="notranslate" />
	<link rel="STYLESHEET" type="text/css" href="./Module/dhtmlx/skins/skyblue/dhtmlx.css">
	
	<link rel="stylesheet" type="text/css" href="./Module/fontawesome/css/font-awesome.min.css">

	<script src="./Module/dhtmlx/codebase/dhtmlx.js" type="text/javascript"></script>
	<script src="./Module/dhtmlx/codebase/Date.format.min.js" type="text/javascript"></script>
	<script src="./Module/JS/jquery-1.10.1.min.js"></script>

<style>
    html, body {
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
		font-family: "Source Sans Pro","Helvetica Neue",Helvetica;
		background-repeat: no-repeat;
		background-size: 100%;
    }
    .formShow input,.formShow select{ 
            font-size:12px !important; 
            font-weight:bold;
    }
    @media only screen and (max-width: 1600px) {
        
    }
	.dhxwin_active .objbox td a:visited,td.hight_light_cell a{
		color:red!important;
	}
</style>
<script>
var deleteOK = <?php echo $deleteNO;?>;
var LayoutMain;
var MainMenu;
var ToolbarMain;
var RootPath = '<?php echo $_SERVER['REQUEST_URI'];?>';  
var RootDataPath = RootPath  + 'data/'
var SoGrid;
var MaterialGrid;
var InkGrid;
var SoGridLoad = RootDataPath+'grid_so.php';
var checkSo = '';
var checkID = '';
var check_gg = 0;
<?php
	// get CREATEDDATE
	$automail_updated = 'Automail updated: ' .getAutomailUpdated();
	if (strpos($automail_updated, 'ERR') !== false ) {
		$automail_updated = '<span style=\"color: red; font-weight: bold;\">'.$automail_updated.'</span>';
	}
	
    if(!isset($_COOKIE["VNRISIntranet"])) {
        echo 'var HeaderTile = "'.$automail_updated.'<a style=\'color:blue;font-style:italic;padding-left:10px\'>Hi Guest | <a href=\"./Module/Login/index.php?URL=TPFL\">Login</a></a>";var UserVNRIS = "";';
    } else {
        echo 'var HeaderTile = "'.$automail_updated.'<a style=\'color:blue;font-style:italic;padding-left:10px\'>Hi '.$_COOKIE["VNRISIntranet"].' | <a href=\"./Module/Login/Logout.php\">Logout</a></a>";var UserVNRIS = "'.$_COOKIE["VNRISIntranet"].'";';
    }
?>
    var widthScreen = screen.width;
    var widthSo = 500;
    var widthSoSelect = 650;
    var widthForm = 810;
    var widthProcess = 250;
    var heightForm = 200;
    var heightProcess = 400;
    var heightSize = 300;
    var heightSupply = 200;
    if(widthScreen<=1600){
        widthSo = 388;
        widthSoSelect = 260;
        widthForm = 760;
        widthProcess = 150;
        heightSupply = 100;
    }
    function initLayout(){
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "4H",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: true, text: "LIST SO", width: widthSo}, 
				{id: "b", header: true, text: "LIST MATERIAL"},               
                {id: "c", header: true, text: "LIST INK", width:widthSoSelect},                
                {id: "d", header: true, text: "NO"}
            ]
        });
    }
	
	function setCookie(cname,cvalue,exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires=" + d.toGMTString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return -1;
	}
	
    function initMenu(){
        MainMenu = new dhtmlXMenuObject({
				parent: "menuObj",
				icons_path: "./Module/dhtmlx/common/imgs_Menu/",
				json: "./Module/menu/Menu.json",
				top_text: HeaderTile
        });
    }	
	

	var dhxWinsImport;
	function importMasterData()
	{
		if(!dhxWinsImport){ dhxWinsImport= new dhtmlXWindows(); }

		var id = "WindowsDetail";
		var w = 400;	var h = 100;	var x = Number(($(window).width()-400)/2);	var y = Number(($(window).height()-50)/2);
		var Popup = dhxWinsImport.createWindow(id, x, y, w, h);
		dhxWinsImport.window(id).setText("Import Master Data");

		Popup.attachHTMLString(
			'<div style="width:500%;margin:20px ">' +
				'<form action="data/item_upload.php" enctype="multipart/form-data" method="post">' +
					'<input id="file" name="file" type="file">' +
					'<input name="submit" type="submit" value="Upload">' +
				'</form>' +
			'</div>'); 
	}


	var dhxWinsMachine;
	function importMachine()
	{
		// Lưu ý dữ liệu sẽ bị truncate hết trước khi thêm mới
		if(!dhxWinsMachine){ dhxWinsMachine= new dhtmlXWindows(); }

		var id = "WindowsDetail";
		var w = 400;	var h = 100;	var x = Number(($(window).width()-400)/2);	var y = Number(($(window).height()-50)/2);
		var Popup = dhxWinsMachine.createWindow(id, x, y, w, h);
		dhxWinsMachine.window(id).setText("Import SOLine-Machine");

		Popup.attachHTMLString(
			'<div style="width:500%;margin:20px ">' +
				'<form action="data/machine_upload.php" enctype="multipart/form-data" method="post">' +
					'<input id="file" name="file" type="file">' +
					'<input name="submit" type="submit" value="Upload">' +
				'</form>' +
			'</div>'); 
	}

	
	function updateMS(){
		MSGrid.attachEvent("onEnter", function(id,ind){
			// your code here
			var url_update = RootDataPath+'update_ms.php';
			var ITEM_NUMBER = MSGrid.cells(id,1).getValue();
			ITEM_NUMBER = ITEM_NUMBER.trim();
			var ITEM_CODE = MSGrid.cells(id,2).getValue();
			ITEM_CODE = ITEM_CODE.trim();
			var ITEM_RM = MSGrid.cells(id,3).getValue();
			ITEM_RM = ITEM_RM.trim();
			var DES_RM = MSGrid.cells(id,4).getValue();
			DES_RM = DES_RM.trim();
			var PRINTING = MSGrid.cells(id,5).getValue();
			PRINTING = PRINTING.trim();
			var RBO = MSGrid.cells(id,6).getValue();
			RBO = RBO.trim();
			var WIDTH = MSGrid.cells(id,7).getValue();
			WIDTH = WIDTH.trim();
			var HEIGHT = MSGrid.cells(id,8).getValue();
			HEIGHT = HEIGHT.trim();
			var ITEM_INK = MSGrid.cells(id,9).getValue();
			ITEM_INK = ITEM_INK.trim();
			var DES_INK = MSGrid.cells(id,10).getValue();
			DES_INK = DES_INK.trim();
			var SO_MAT_IN = MSGrid.cells(id,11).getValue();
			SO_MAT_IN = SO_MAT_IN.trim();
			var MACHINE = MSGrid.cells(id,12).getValue();
			MACHINE = MACHINE.trim();
			var CUT_TYPE = MSGrid.cells(id,13).getValue();
			CUT_TYPE = CUT_TYPE.trim();
			var NOTE = MSGrid.cells(id,14).getValue();
			NOTE = NOTE.trim();			

			var CUT_MACHINE = MSGrid.cells(id,15).getValue();
			CUT_MACHINE = CUT_MACHINE.trim();	
			var BRAND_PROTECTION = MSGrid.cells(id,17).getValue();

			var objUA = {
				ITEM_NUMBER:ITEM_NUMBER,
				ITEM_CODE:ITEM_CODE,
				ITEM_RM:ITEM_RM,
				DES_RM:DES_RM,
				PRINTING:PRINTING,
				RBO:RBO,
				WIDTH:WIDTH,
				HEIGHT:HEIGHT,
				ITEM_INK:ITEM_INK,
				DES_INK:DES_INK,
				SO_MAT_IN:SO_MAT_IN,
				MACHINE:MACHINE,
				CUT_TYPE:CUT_TYPE,
				NOTE:NOTE,
				CUT_MACHINE:CUT_MACHINE,
				BRAND_PROTECTION:BRAND_PROTECTION,
				ITEM_ID:id
			};			
			$.ajax({
				url: url_update,
				type: "POST",
				data: {data: JSON.stringify(objUA)},
				dataType: "json",
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(result) {
					if(result.status){
						// change ID
						if(result.id){
							MSGrid.changeRowId(id,result.id);
						}
						alert('Update dữ liệu thành công!!!!');
					}else{
						alert(result.mess);
					}
				}
			});
		});
	}
	
	function deleteMS(){
		var checkIDs = [];
		MSGrid.forEachRow(function(id){
			if(MSGrid.cells(id,0).getValue()==1){
				checkIDs.push(id);
			}
		});
		if(!checkIDs.length>0){
			alert("Vui lòng chọn dòng để XÓA");
			return false;
		}else{
			confirm_delete = confirm("Bạn có muốn XÓA những item đã chọn!!!");
			if(confirm_delete){
				var url_delete = RootDataPath+'delete_ms.php';
				// get all checkbox
				$.ajax({
					url: url_delete,
					type: "POST",
					data: {data: JSON.stringify(checkIDs)},
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
							x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							// reload	
							for(var i=0;i<checkIDs.length;i++){
								MSGrid.deleteRow(checkIDs[i]);
							}
						}else{
							alert(result.mess);							
						}
					}
				});					
			}
		}
	}
	
	function CSVToArray( strData, strDelimiter ){
        // Check to see if the delimiter is defined. If not,
        // then default to comma.
        strDelimiter = (strDelimiter || ",");

        // Create a regular expression to parse the CSV values.
        var objPattern = new RegExp(
            (
                // Delimiters.
                "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +

                // Quoted fields.
                "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +

                // Standard fields.
                "([^\"\\" + strDelimiter + "\\r\\n]*))"
            ),
            "gi"
            );


        // Create an array to hold our data. Give the array
        // a default empty first row.
        var arrData = [[]];

        // Create an array to hold our individual pattern
        // matching groups.
        var arrMatches = null;


        // Keep looping over the regular expression matches
        // until we can no longer find a match.
        while (arrMatches = objPattern.exec( strData )){

            // Get the delimiter that was found.
            var strMatchedDelimiter = arrMatches[ 1 ];

            // Check to see if the given delimiter has a length
            // (is not the start of string) and if it matches
            // field delimiter. If id does not, then we know
            // that this delimiter is a row delimiter.
            if (
                strMatchedDelimiter.length &&
                strMatchedDelimiter !== strDelimiter
                ){

                // Since we have reached a new row of data,
                // add an empty row to our data array.
                arrData.push( [] );

            }

            var strMatchedValue;

            // Now that we have our delimiter out of the way,
            // let's check to see which kind of value we
            // captured (quoted or unquoted).
            if (arrMatches[ 2 ]){

                // We found a quoted value. When we capture
                // this value, unescape any double quotes.
                strMatchedValue = arrMatches[ 2 ].replace(
                    new RegExp( "\"\"", "g" ),
                    "\""
                    );

            } else {

                // We found a non-quoted value.
                strMatchedValue = arrMatches[ 3 ];

            }


            // Now that we have our value string, let's add
            // it to the data array.
            arrData[ arrData.length - 1 ].push( strMatchedValue );
        }

        // Return the parsed data.
        return( arrData );
    }
		
	var ToolbarMaterial;
    function initToolbar(){		
		if(deleteOK){
			// ToolbarMaterial = LayoutMain.cells("c").attachToolbar({
			// 	icons_path: "./Module/dhtmlx/common/imgs/",
			// 	align: "left",
			// });
			// ToolbarMaterial.addButton("update_item",1, "Update ITEM", "save.gif"); // show button to add length
			// ToolbarMaterial.addButton("view_item",2, "View ITEM", "page_info.gif"); // show button to add length
			// ToolbarMaterial.addButton("update_so_may",3, "Update SO LINE & MACHINE", "save.gif"); // show button to add length
			// ToolbarMaterial.addButton("view_so_may",4, "View SO LINE & MACHINE", "page_info.gif"); // show button to add length
			// ToolbarMaterial.addButton("view_user",5, "View USER", "page_info.gif"); // show button to add length
			// ToolbarMaterial.attachEvent("onClick", function(name)
			// {
			// 	// if(name == "view_user")
			// 	// {
			// 	// 	loadListUser();
			// 	// }
			// });
		} 

		// icons_path: "./Module/dhtmlx/common/imgs/",

        ToolbarMain = new dhtmlXToolbarObject({
            parent: "ToolbarBottom",    
            align: "left",
			icons_size: 18,
            iconset: "awesome"
        });		
        ToolbarMain.addText("", null, "<a style='font-size:20pt;font-weight:bold'>AD THERMAL PFL</a>");
        ToolbarMain.addText("", null, "SO");
        ToolbarMain.addInput("so", null, "",90); // set for test 27210890
		ToolbarMain.addText("", null, "PO#");
		ToolbarMain.addInput("po", null, "", 50); // set for test 27210890     
		ToolbarMain.addText("",null, "LENGTH");
		ToolbarMain.addInput("length",null, "", 70); // set for test 27210890  
		ToolbarMain.addText("",null, "FROM DATE");
        ToolbarMain.addInput("from_date",null,"",60); // set for test 27210890
		ToolbarMain.addText("",null, "TO DATE");
        ToolbarMain.addInput("to_date",null,"",60); // set for test 27210890
		var from_date_input = ToolbarMain.getInput("from_date");
		var to_date_input = ToolbarMain.getInput("to_date");		
		myCalendar = new dhtmlXCalendarObject([from_date_input,to_date_input]);
		myCalendar.setDateFormat("%d-%M-%y");
		ToolbarMain.addText("",null, "PRINT LRPM");
		ToolbarMain.addInput("print_lrpm",null, "",80); // set for test 27210890
		ToolbarMain.addButtonTwoState('auto_save',null, 'AUTO SAVE', "fa fa-magic", null);
        // end 
		if(getCookie('auto_save')==-1){
			setCookie('auto_save',0,365);
		}
		if(getCookie('auto_save')>0){
			ToolbarMain.setItemState('auto_save', true, false); 
		}else{
			ToolbarMain.setItemState('auto_save', false, false); // set default auto print
		}
		ToolbarMain.addSpacer("auto_save");
		ToolbarMain.addButton('export',null, '<span style="color:red;font-weight:bold;background:yellow;padding:5px;">Exports CSV</span>', 'fa fa-download'); 
        ToolbarMain.hideItem("export");
        ToolbarMain.addButton("saveNo",null, "Save No", "fa fa-floppy-o");    
        ToolbarMain.hideItem('saveNo');
        ToolbarMain.addButton("printNo",null, "Print No", "fa fa-print");
        ToolbarMain.hideItem('printNo');
        ToolbarMain.addButton("view_no",null, "View No", "fa fa-list");
        // ToolbarMain.addButton("report_no",null, "Report", "page_info.gif");
        // ToolbarMain.addButton("export_all_no",null, "Export NO", "xlsx.gif");   

		var report_opts = [
            ['report_no', 'obj', 'Report View', 'fa fa-list'],
			['reports_excel', 'obj', 'Report No (File .XLSX)', 'fa fa-list']
        ];
        
        ToolbarMain.addButtonSelect('reports', 25, 'Reports', report_opts, 'fa fa-database');

		var opts = [
            ['view_master_data', 'obj', 'View Master Data', 'fa fa-list'],
            ['import_master_data', 'obj', 'Import Master Data', 'fa fa-upload'],
            ['sample_master_data', 'obj', 'Download File Mẫu', 'fa fa-download']
        ];
        
        ToolbarMain.addButtonSelect('master_data', 25, 'Master Data', opts, 'fa fa-database');

		var machine_opts = [
            ['view_so_may', 'obj', 'View SOLine & Machine', 'fa fa-download'],
            ['update_so_may', 'obj', 'Update SOLine & Machine', 'fa fa-upload']
        ];
        
        ToolbarMain.addButtonSelect('soline_machine', 25, 'Machine', machine_opts, 'fa fa-database');

		ToolbarMain.addButton("users",null, "Users", "fa fa-users");
		
		
        ToolbarMain.attachEvent("onClick", function(name)
        {
            if(name == "printNo")
            {
                saveDatabase(true);

            } else if(name == "view_no")
            {
                viewNO();

            } else if(name == "saveNo"){
                saveDatabase(false);
            } else if(name == "report_no"){
				
				var from_date_value = ToolbarMain.getValue("from_date");
				var to_date_value = ToolbarMain.getValue("to_date");
				if(!from_date_value||!to_date_value){

					dhtmlx.message({
                        text: "Chương trình hiển thị Reports dữ liệu trong ngày",
                        expire:6000,
                        // type:"error"
                    });

					var currentTime = new Date();
					var dd = String(currentTime.getDate()).padStart(2, '0');
					var mm = String(currentTime.getMonth() + 1).padStart(2, '0'); //January is 0!
					var yyyy = currentTime.getFullYear();
					today = yyyy + '-' + mm + '-' + dd;

					var from_date_value = today;
					var to_date_value = today;
				}

				reportNo(from_date_value, to_date_value );


            } else if(name == "export"){

				// // noGrid.enableCSVHeader(true);
                // // noGrid.setCSVDelimiter(',');
				// // var csv = noGrid.serializeToCSV();
				// // if (csv == null) return;
				// // filename = new Date().format('d-m-Y__H:i:s')+'.csv';   //  07-06-2016 06:38:34
				// // if (!csv.match(/^data:text\/csv/i)) {
				// // 	csv = 'data:text/csv;charset=utf-8,' + csv;
				// // }		
				// // // data = csv;
				// // data = encodeURI(csv);
				// // //data = CSVToArray(data,',');
				// // for (var k=0;k<=100;k++){
				// // 	data = data.replace('&amp;','&');
				// // }
				// // link = document.createElement('a');
				// // link.setAttribute('href', data);
				// // link.setAttribute('download', filename);
				// // link.click();

				var from_date_value = ToolbarMain.getValue("from_date");
				var to_date_value = ToolbarMain.getValue("to_date");
				

				if(!from_date_value||!to_date_value) {

					dhtmlx.message({
						text: "Chương trình hiển thị Reports dữ liệu trong ngày",
						expire:6000,
						// type:"error"
					});

					var currentTime = new Date();
					var dd = String(currentTime.getDate()).padStart(2, '0');
					var mm = String(currentTime.getMonth() + 1).padStart(2, '0'); //January is 0!
					var yyyy = currentTime.getFullYear();
					today = yyyy + '-' + mm + '-' + dd;

					var from_date_value = today;
					var to_date_value = today;
				}

				location.href = "data/reports_csv.php?from_date_value="+from_date_value+"&to_date_value="+to_date_value;


            } else if (name == 'reports_excel' ) {

				var from_date_value = ToolbarMain.getValue("from_date");
				var to_date_value = ToolbarMain.getValue("to_date");
				

				if(!from_date_value||!to_date_value) {

					dhtmlx.message({
						text: "Chương trình hiển thị Reports dữ liệu trong ngày",
						expire:6000,
						// type:"error"
					});

					var currentTime = new Date();
					var dd = String(currentTime.getDate()).padStart(2, '0');
					var mm = String(currentTime.getMonth() + 1).padStart(2, '0'); //January is 0!
					var yyyy = currentTime.getFullYear();
					today = yyyy + '-' + mm + '-' + dd;

					var from_date_value = today;
					var to_date_value = today;
				}

				location.href = "data/reports_excel.php?from_date_value="+from_date_value+"&to_date_value="+to_date_value;

			} else if (name == "view_master_data" ) {

				loadListMS();

			} else if (name == "import_master_data" ) {

				// LoadFormUA();
				importMasterData();

			} else if (name == "sample_master_data" ) {
				
				window.open("https://docs.google.com/spreadsheets/d/1QyQKOdCIAnpoIeQ8FOfXlNRVp9mFJYsyl5SuMQ_a6cU/edit?usp=sharing","_blank");

			} else if(name == "view_so_may" ) {

				loadListMachine();

			} else if(name == "update_so_may" ) {

				// LoadFormMachine();
				importMachine();

			} else if (name == "users" ) {

				loadListUser();
			}

        }); 
		// change state		
		ToolbarMain.attachEvent("onStateChange", function(id, state){
			if(id == "auto_save")
            {
				if(state){
					setCookie('auto_save',1,365);
				}else{
					setCookie('auto_save',0,365);
				}								
            }
		});
    }
    var dhxWins;
    var viewNOGrid;
    function viewNO(){		
        if(!dhxWins){
            dhxWins= new dhtmlXWindows();// show window form to add length
        } 		
        if (!dhxWins.isWindow("windowViewNo")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowViewNo = dhxWins.createWindow("windowViewNo", 508,65,1020,703);	
			dhxWins.window("windowViewNo").progressOn();
            windowViewNo.setText("Window View NO");
            /*necessary to hide window instead of remove it*/
            windowViewNo.attachEvent("onClose", function(win){
                if (win.getId() == "windowViewNo") 
                    win.hide();
            });
            viewNOGrid = windowViewNo.attachGrid();
            viewNOGrid.enableSmartRendering(true);
			viewNOGrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");		
			viewNOGrid.enableMultiselect(true);				
            viewNOGrid.init();
			var from_date_value = ToolbarMain.getValue("from_date");
			var to_date_value = ToolbarMain.getValue("to_date");
			viewNOGrid.load(RootDataPath+'view_no.php?from_date_value='+from_date_value+'&to_date_value='+to_date_value,function(){				
				dhxWins.window("windowViewNo").progressOff();
				viewNOGrid.setColumnHidden(2,true);
			});
			viewNOGrid.attachEvent("onFilterEnd", function(elements){
				windowViewNo.setText('Count Item: '+viewNOGrid.getRowsNum());
			});
        }else{
            dhxWins.window("windowViewNo").show(); 
        } 		
    }
	
	var dhxWinsMS;
    var viewMSGrid;
	var MSGrid;
    function loadListMS(){		
        if(!dhxWinsMS){
            dhxWinsMS= new dhtmlXWindows();// show window form to add length
        }
        if (!dhxWinsMS.isWindow("windowViewMS")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowViewMS = dhxWinsMS.createWindow("windowViewMS", 1013,65,680,720);
			dhxWinsMS.window("windowViewMS").progressOn();
            windowViewMS.setText("Window View ITEM");
            /*necessary to hide window instead of remove it*/
            windowViewMS.attachEvent("onClose", function(win){
                if (win.getId() == "windowViewMS") 
                    win.hide();
            });
            MSGrid= windowViewMS.attachGrid();
            MSGrid.enableSmartRendering(true);
			MSGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");	
			var delete_button = '<input type="button" id="DeleteMS" value="DELETE" onclick="deleteMS()">';
			if(!deleteOK){
				delete_button = '';
			}
			MSGrid.setHeader(delete_button+',Item Number (Part # Oracle#),Customer Item Code (Description),Item RM,Description RM,Printing method/Quality,RBO Name,Label Dimension (width),Label Dimension (Length),Item InK,Des Ink,Số mặt in,Machine,Cut/Fold Type,Note,Cut Machine,Brand Protection,Updated by,Updated date');
			MSGrid.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");	
			MSGrid.setInitWidths("90,200,220,225,560,170,105,155,170,95,455,80,145,155,155,155,155,155,155");
			MSGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");
			MSGrid.setColTypes("ch,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,coro,ed,ed");
			MSGrid.setColSorting("na,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");		
            MSGrid.init();  
            MSGrid.load(RootDataPath+'view_ms.php',function(){	
				dhxWinsMS.window('windowViewMS').maximize();
				var state=MSGrid.getStateOfView();
				if(state[2]>0){
					MSGrid.showRow(MSGrid.getRowId(state[2]-1));
				}
				
				//set values for select box in 16th column
                combobox = MSGrid.getCombo(16);
                combobox.put("NO", "NO");
                combobox.put("YES", "YES");

				updateMS();
			}); 
        }else{
            dhxWinsMS.window("windowViewMS").show(); 
        } 
		dhxWinsMS.window("windowViewMS").progressOff();
    }
	
	var dhxWinsListMachine;
    var viewMachineGrid;
	var MachineGrid;
    function loadListMachine(){		
        if(!dhxWinsListMachine){
            dhxWinsListMachine= new dhtmlXWindows();// show window form to add length
        }
        if (!dhxWinsListMachine.isWindow("windowViewMachine")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowViewMachine = dhxWinsListMachine.createWindow("windowViewMachine", 507,65,650,780);
			dhxWinsListMachine.window("windowViewMachine").progressOn();
            windowViewMachine.setText("Window View ITEM");
            /*necessary to hide window instead of remove it*/
            windowViewMachine.attachEvent("onClose", function(win){
                if (win.getId() == "windowViewMachine") 
                    win.hide();
            });
            MachineGrid= windowViewMachine.attachGrid();
            MachineGrid.enableSmartRendering(true);
			MachineGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter");	
			var delete_button = '<input type="button" id="DeleteMachine" value="DELETE" onclick="DeleteMachine()">';
			if(!deleteOK){
				delete_button = '';
			}
			MachineGrid.setHeader(delete_button+',DATE,SO LINE,SO MAY,STT,PLAN');
			MachineGrid.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");	
			MachineGrid.setInitWidths("90,85,100,80,65,195");
			MachineGrid.setColAlign("left,left,left,left,left,left");
			MachineGrid.setColTypes("ch,ed,ed,ed,ed,ed");
			MachineGrid.setColSorting("na,str,str,str,str,str");		
            MachineGrid.init();  
            MachineGrid.load(RootDataPath+'view_machine.php',function(){				
				updateMachine();
			}); 
        }else{
            dhxWinsListMachine.window("windowViewMachine").show(); 
        } 
		dhxWinsListMachine.window("windowViewMachine").progressOff();
    }
	
	var dhxWinsListUser;
    var viewUserGrid;
	var UserGrid;
    function loadListUser(){		
        if(!dhxWinsListUser){
            dhxWinsListUser= new dhtmlXWindows();// show window form to add length
        }
        if (!dhxWinsListUser.isWindow("windowViewMachine")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowViewMachine = dhxWinsListUser.createWindow("windowViewMachine", 507,65,650,455);
			dhxWinsListUser.window("windowViewMachine").progressOn();
            windowViewMachine.setText("Window View USER");
            /*necessary to hide window instead of remove it*/
            windowViewMachine.attachEvent("onClose", function(win){
                if (win.getId() == "windowViewMachine") 
                    win.hide();
            });
            UserGrid= windowViewMachine.attachGrid();
            UserGrid.enableSmartRendering(true);
			UserGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter");	
			var delete_button = '<input type="button" id="DeleteMachine" value="DELETE" onclick="DeleteUser()">';
			if(!deleteOK){
				delete_button = '';
			}
			UserGrid.setHeader(delete_button+',EMAIL,NOTE,UPDATED BY');
			UserGrid.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");	
			UserGrid.setInitWidths("90,150,*,100");
			UserGrid.setColAlign("left,left,left,left");
			UserGrid.setColTypes("ch,ed,ed,ro");
			UserGrid.setColSorting("na,str,str,str");		
            UserGrid.init();  
            UserGrid.load(RootDataPath+'view_user.php',function(){				
				updateUser();
			}); 
        }else{
            dhxWinsListUser.window("windowViewMachine").show(); 
        } 
		dhxWinsListUser.window("windowViewMachine").progressOff();
    }
	
	function updateUser(){
		UserGrid.attachEvent("onEnter", function(id,ind){
			// your code here
			var url_update = RootDataPath+'update_user.php';
			var EMAIL = UserGrid.cells(id,1).getValue();
			EMAIL = EMAIL.trim();
			var NOTE = UserGrid.cells(id,2).getValue();
			NOTE = NOTE.trim();
			var objUA = {
				EMAIL:EMAIL,
				NOTE:NOTE,
				ITEM_ID:id
			};			
			$.ajax({
				url: url_update,
				type: "POST",
				data: {data: JSON.stringify(objUA)},
				dataType: "json",
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(result) {
					if(result.status){
						// change ID
						if(result.id){
							UserGrid.changeRowId(id,result.id);
						}
						alert('Update dữ liệu thành công!!!!');
					}else{
						alert(result.mess);
					}
				}
			});
		});
	}
	
	function DeleteUser(){
		var checkIDs = [];
		UserGrid.forEachRow(function(id){
			if(UserGrid.cells(id,0).getValue()==1){
				checkIDs.push(id);
			}
		});
		if(!checkIDs.length>0){
			alert("Vui lòng chọn dòng để XÓA");
			return false;
		}else{
			confirm_delete = confirm("Bạn có muốn XÓA những item đã chọn!!!");
			if(confirm_delete){
				var url_delete = RootDataPath+'delete_user.php';
				// get all checkbox
				$.ajax({
					url: url_delete,
					type: "POST",
					data: {data: JSON.stringify(checkIDs)},
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
							x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							// reload	
							for(var i=0;i<checkIDs.length;i++){
								UserGrid.deleteRow(checkIDs[i]);
							}
						}else{
							alert(result.mess);							
						}
					}
				});					
			}
		}
	}
	
	function updateMachine(){
		MachineGrid.attachEvent("onEnter", function(id,ind){
			// your code here
			var url_update = RootDataPath+'update_machine.php';
			var SO_LINE = MachineGrid.cells(id,2).getValue();
			SO_LINE = SO_LINE.trim();
			var SO_MAY = MachineGrid.cells(id,3).getValue();
			SO_MAY = SO_MAY.trim();
			var STT = MachineGrid.cells(id,4).getValue();
			STT = STT.trim();
			var PLAN = MachineGrid.cells(id,5).getValue();
			PLAN = PLAN.trim();
			var objUA = {
				SO_LINE:SO_LINE,
				SO_MAY:SO_MAY,
				STT:STT,
				PLAN:PLAN,
				ITEM_ID:id
			};			
			$.ajax({
				url: url_update,
				type: "POST",
				data: {data: JSON.stringify(objUA)},
				dataType: "json",
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(result) {
					if(result.status){
						// change ID
						if(result.id){
							MachineGrid.changeRowId(id,result.id);
						}
						alert('Update dữ liệu thành công!!!!');
					}else{
						alert(result.mess);
					}
				}
			});
		});
	}
	
	function DeleteMachine(){
		var checkIDs = [];
		MachineGrid.forEachRow(function(id){
			if(MachineGrid.cells(id,0).getValue()==1){
				checkIDs.push(id);
			}
		});
		if(!checkIDs.length>0){
			alert("Vui lòng chọn dòng để XÓA");
			return false;
		}else{
			confirm_delete = confirm("Bạn có muốn XÓA những item đã chọn!!!");
			if(confirm_delete){
				var url_delete = RootDataPath+'delete_machine.php';
				// get all checkbox
				$.ajax({
					url: url_delete,
					type: "POST",
					data: {data: JSON.stringify(checkIDs)},
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
							x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							// reload	
							for(var i=0;i<checkIDs.length;i++){
								MachineGrid.deleteRow(checkIDs[i]);
							}
						}else{
							alert(result.mess);							
						}
					}
				});					
			}
		}
	}
	
    var viewUAGrid;
	var UAGrid;	
    function loadListUA(){		
        if(!dhxWinsUA){
            dhxWinsUA= new dhtmlXWindows();// show window form to add length
        }
        if (!dhxWinsUA.isWindow("windowViewUA")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowViewUA = dhxWinsUA.createWindow("windowViewUA", 1013,65,505,720);
			dhxWinsUA.window("windowViewUA").progressOn();
            windowViewUA.setText("Window View UA");
            /*necessary to hide window instead of remove it*/
            windowViewUA.attachEvent("onClose", function(win){
                if (win.getId() == "windowViewUA") 
                    win.hide();
            });
            UAGrid= windowViewUA.attachGrid();
            UAGrid.enableSmartRendering(true);
			UAGrid.attachHeader(",#text_filter,#text_filter,#text_filter");	
			UAGrid.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");	
			UAGrid.setHeader('<input type="button" id="DeleteUA" value="DELETE" onclick="deleteUA()">,ITEM,SIZE,BASE_ROLL');
			UAGrid.setInitWidths("90,125,90,165");
			UAGrid.setColAlign("left,left,left,left");
			UAGrid.setColTypes("ch,ed,ed,ed");
			UAGrid.setColSorting("na,str,str,str");
            UAGrid.init();  
            UAGrid.load(RootDataPath+'view_ua.php',function(){				
				updateUA();
				//UAGrid.setHeader("<div style='width:100%; text-align:left;'>A</div>,B,C");
			}); 
        }else{
            dhxWinsUA.window("windowViewUA").show(); 
        } 
		dhxWinsUA.window("windowViewUA").progressOff();
    }
	
	var dhxWins;
    var dhxWinsReport;
    function reportNo(from_date_value=null, to_date_value=null ){
        if(!dhxWinsReport){
            dhxWinsReport= new dhtmlXWindows();// show window form to add length
        }   
        if (!dhxWinsReport.isWindow("windowReport")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowReportNo = dhxWinsReport.createWindow("windowReport", 506,65,1205,665);
            dhxWinsReport.window("windowReport").progressOn();
            windowReportNo.setText("Window Report NO");
            /*necessary to hide window instead of remove it*/
            windowReportNo.attachEvent("onClose", function(win){
                if (win.getId() == "windowReport") 
                    win.hide();
                    ToolbarMain.hideItem("export");
                    ToolbarMain.showItem("export_all_no");
            });
            formData = [
				{type: "button",value: "Export", offsetLeft: 10, offsetTop: 0},
				{type: "container", name: "reportGrid", label: "", inputWidth: 720,}
			];
            myForm = windowReportNo.attachForm(formData, true);	
            noGrid = windowReportNo.attachGrid();
            // noGrid = new dhtmlXGridObject(myForm.getContainer("reportGrid"));
            noGrid.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
            noGrid.setHeader("DATE,LENH SX,SOLine,NGAY GIAO,ITEM CODE,RBO,ORDER ITEM,SO LUONG,MA VAT TU,TEN VAT TU,SO LUONG VAT TU(YD)/(PCS),CHIEU DAI (MM),CHIEU RONG (MM),MA MUC IN,TEN MUC IN,SO LUONG MUC (MET),SO MAT IN,PO");   //sets the headers of columns
            noGrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
            noGrid.setColumnIds("DATE,LENH SX,SO#,NGAY GIAO,ITEM CODE,RBO,ORDER ITEM,SO LUONG,MA VAT TU,TEN VAT TU,SO LUONG VAT TU(YD)/(PCS),CHIEU DAI (MM),CHIEU RONG (MM),MA MUC IN,TEN MUC IN,SO LUONG MUC (MET),SO MAT IN,PO");         //sets the columns' ids
            noGrid.setInitWidths("90,115,90,90,90,90,345,90,90,420,185,115,135,110,280,145,90,90");   //sets the initial widths of columns
            noGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");     //sets the alignment of columns
            noGrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
            noGrid.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");  //sets the sorting types of columns
            noGrid.enableSmartRendering(true);
            noGrid.init();

			var url_export = RootDataPath+'report_no.php?from_date_value='+from_date_value+'&to_date_value='+to_date_value;
            noGrid.load(url_export, function(){ //takes the path to your data feed							
            ToolbarMain.showItem("export");     
            // hide no all
            ToolbarMain.hideItem("export_all_no");
            dhxWinsReport.window("windowReport").progressOff();
            });
        }else{
            dhxWinsReport.window("windowReport").show();             
            ToolbarMain.showItem("export");
            ToolbarMain.hideItem("export_all_no");
        } 
    }
	
    function saveDatabase(print){
		// stop edit cell
		InkGrid.editStop();
		MaterialGrid.editStop();
		save = 'SAVE';
		if(print){
			save = 'PRINT';
		}     
		if(checkID){
			if(MaterialGrid.getRowsNum()){
				var MATERIAL_CODE_CHECK = MaterialGrid.cellByIndex(0,0).getValue();
				if(!MATERIAL_CODE_CHECK){
					alert("Vui lòng nhập MATERIAL để "+save+"!!!");                                
					return false;
				}
			}else{
				alert("Vui lòng nhập MATERIAL để "+save+"!!!");                                
				return false;
			}
			if(InkGrid.getRowsNum()){
				var INK_CODE_CHECK = InkGrid.cellByIndex(0,0).getValue();
				if(!INK_CODE_CHECK){
					alert("Vui lòng nhập MỰC để "+save+"!!!");                                
					return false;
				}
			}else{
				alert("Vui lòng nhập MỰC để "+save+"!!!");                                
				return false;
			}			                        
			save_supply = [];
			obj_supply = {};
			for (var i=0; i<MaterialGrid.getRowsNum(); i++){
				var MATERIAL_CODE = MaterialGrid.cellByIndex(i,0).getValue();
				var MATERIAL_DES = MaterialGrid.cellByIndex(i,1).getValue();
				var MATERIAL_QTY= MaterialGrid.cellByIndex(i,2).getValue();
				var INK_CODE= InkGrid.cellByIndex(i,0).getValue();
				var INK_DES= InkGrid.cellByIndex(i,1).getValue();
				var INK_QTY= InkGrid.cellByIndex(i,2).getValue();
				obj_supply = {
						MATERIAL_CODE			:	MATERIAL_CODE.trim(),
						MATERIAL_DES			:	MATERIAL_DES.trim(),
						MATERIAL_QTY			:	MATERIAL_QTY.trim(),
						INK_CODE				:	INK_CODE.trim(),
						INK_DES					:	INK_DES.trim(),
						INK_QTY					:	INK_QTY.trim(),
					};
				save_supply.push(obj_supply);               
			}
			SO_LINE = checkSo;
			MAX_NO = $("#NO_MAX").val();
			if(MAX_NO){
				MAX_NO = MAX_NO.trim();
				if(isNaN(MAX_NO)==true){
					alert("Vui lòng nhập số!!!");
					return false;
				}
			}			
			var NO = $('#PREFIX').val();
			NO = NO.trim();
			SAVE_DATE = $("#DATE_CREATE").val();
			SAVE_DATE = SAVE_DATE.trim();
			ORDER = $("#ORDER").val();
			ORDER = ORDER.trim();
			ITEM_NUMBER = $("#ITEM_NUMBER").val();
			ITEM_NUMBER = ITEM_NUMBER.trim();
			ITEM_CODE = $("#ITEM_CODE").val();
			ITEM_CODE = ITEM_CODE.trim();
			PD = $("#PD").val();
			PD = PD.trim();
			if(!PD){
				alert('VUI LÒNG NHẬP PROMISE DATE!!!');
				return false;
			}
			REQ = $("#REQ").val();
			REQ = REQ.trim();
			KICH_THUOC_IN = $("#WIDTH_LENGTH").val();
			KICH_THUOC_IN = KICH_THUOC_IN.trim();
			WIDTH = SoGrid.cells(checkID,11).getValue();
			LENGTH = SoGrid.cells(checkID,12).getValue();
			WIDTH = WIDTH.trim();
			LENGTH = LENGTH.trim();
			QTY = $("#QTY").val();
			QTY = QTY.trim();	
			SO_MAT_IN = $("#SO_MAT_IN").val();
			SO_MAT_IN = SO_MAT_IN.trim();
			VAT_TU_THUC_TE = $("#VAT_TU_THUC_TE").val();
			VAT_TU_THUC_TE = VAT_TU_THUC_TE.trim();
			PCS_MAU = $("#PCS_MAU").val();
			PCS_MAU = PCS_MAU.trim();
			BU_HAO_PO = $("#BU_HAO_PO").val();
			BU_HAO_PO = BU_HAO_PO.trim();
			TI_LE_BU_HAO = $("#TI_LE_BU_HAO").val();
			TI_LE_BU_HAO = TI_LE_BU_HAO.trim();	
			CUT_TYPE = $("#CUT_TYPE").val();								
			CUT_TYPE = CUT_TYPE.trim();
			MACHINE = $("#MACHINE").val();
			MACHINE = MACHINE.trim();
			REMARK = $("#REMARK").val();
			REMARK = REMARK.trim();
			REMARK_2 = $("#REMARK_2").val();
			REMARK_2 = REMARK_2.trim();
			NOTE_ITEM = $("#NOTE_ITEM").val();
			NOTE_ITEM = NOTE_ITEM.trim();
			RBO = SoGrid.cells(checkID,10).getValue();
			RBO = RBO.trim();
			SO_LUONG_PO = SoGrid.cells(checkID,20).getValue();
			SO_LUONG_PO = SO_LUONG_PO.trim();		
			
			// Save mới cột SHIP TO CUSTOMER
			SHIP_TO_CUSTOMER = SoGrid.cells(checkID,31).getValue();

			CUT_MACHINE = SoGrid.cells(checkID,32).getValue();
			BRAND_PROTECTION = SoGrid.cells(checkID,33).getValue();
			var ORDER_TYPE_NAME = SoGrid.cells(checkID,29).getValue();
			
			save_item ={MAX_NO:MAX_NO,NO:NO,SAVE_DATE:SAVE_DATE,SO_LINE:SO_LINE,ORDER:ORDER,ITEM_NUMBER:ITEM_NUMBER,ITEM_CODE:ITEM_CODE,PD:PD,REQ:REQ,WIDTH:WIDTH,LENGTH:LENGTH,WIDTH_LENGTH:WIDTH_LENGTH,QTY:QTY,SO_MAT_IN:SO_MAT_IN,VAT_TU_THUC_TE:VAT_TU_THUC_TE,PCS_MAU:PCS_MAU,BU_HAO_PO:BU_HAO_PO,TI_LE_BU_HAO:TI_LE_BU_HAO,CUT_TYPE:CUT_TYPE,MACHINE:MACHINE,REMARK:REMARK,REMARK_2:REMARK_2,RBO:RBO,SO_LUONG_PO:SO_LUONG_PO,KICH_THUOC_IN:KICH_THUOC_IN,NOTE_ITEM:NOTE_ITEM,SHIP_TO_CUSTOMER:SHIP_TO_CUSTOMER,CUT_MACHINE:CUT_MACHINE,BRAND_PROTECTION:BRAND_PROTECTION,ORDER_TYPE_NAME:ORDER_TYPE_NAME};    
			var jsonObjects = {
				"item": save_item,
				"supply":save_supply
			}; 
			var url_save = RootDataPath+'save_item.php'; 

			$.ajax({
			url: url_save,
				type: "POST",
				data: {data: JSON.stringify(jsonObjects) },
				dataType: "json",
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(result) {
					if(result.status){
						alert("Save DATA Thành Công!!!");
						location.reload();
						if(print){
							var wi = window.open('about:blank', '_blank');
							//$(wi.document.body).html("<p>Please wait while you are being redirected...</p>");
							// http://147.121.59.138/MVPlans/tpfl/
							wi.location.href = RootPath+'print.php?id='+result.NUMBER_NO;
						}                                
					}else{
						alert(result.mess);
						location.reload();
					}
				}
			});            
		}else{
			alert("Vui lòng chọn một NO để "+save+"!!!");
			return false;
		}       
    }

    function initNO(){
        // pass info to form
        var ITEM_NUMBER = SoGrid.cells(checkID,4).getValue();
        var ITEM_CODE = SoGrid.cells(checkID,8).getValue();
        var ORDER = SoGrid.cells(checkID,7).getValue();
        var REQ = SoGrid.cells(checkID,6).getValue();
        var PD = SoGrid.cells(checkID,5).getValue();
        var VAT_TU_THUC_TE = SoGrid.cells(checkID,21).getValue();
        var PCS_MAU = SoGrid.cells(checkID,22).getValue();
        var BU_HAO_PO = SoGrid.cells(checkID,23).getValue();
        var TI_LE_BU_HAO = SoGrid.cells(checkID,24).getValue();
		var CUT_TYPE = SoGrid.cells(checkID,25).getValue();
		var QTY = SoGrid.cells(checkID,3).getValue();
        var MACHINE = SoGrid.cells(checkID,26).getValue(); 																											   
		var SO_MAT_IN = SoGrid.cells(checkID,19).getValue();
		var REMARK = SoGrid.cells(checkID,27).getValue();
		var ORDER_TYPE_NAME = SoGrid.cells(checkID,29).getValue();
		var NOTE_ITEM = SoGrid.cells(checkID,30).getValue();
        var WIDTH = SoGrid.cells(checkID,11).getValue();
		var LENGTH = SoGrid.cells(checkID,12).getValue();
		objForm = {ITEM_NUMBER:ITEM_NUMBER,ITEM_CODE:ITEM_CODE,ORDER:ORDER,REQ:REQ,PD:PD,VAT_TU_THUC_TE:VAT_TU_THUC_TE,PCS_MAU:PCS_MAU,BU_HAO_PO:BU_HAO_PO,TI_LE_BU_HAO:TI_LE_BU_HAO,CUT_TYPE:CUT_TYPE,QTY:QTY,MACHINE:MACHINE,SO_MAT_IN:SO_MAT_IN,REMARK:REMARK,WIDTH:WIDTH,LENGTH:LENGTH,ORDER_TYPE_NAME:ORDER_TYPE_NAME,NOTE_ITEM:NOTE_ITEM};
		LayoutMain.cells("d").attachURL(RootDataPath+'frm_no.php', true,objForm);        
        ToolbarMain.showItem('saveNo');
        ToolbarMain.showItem('printNo');
		// call save
		// firstly add the event
		LayoutMain.attachEvent("onContentLoaded", function(id){
			// page.html id loaded, your code here
			if(id=='d'){
				if(getCookie('auto_save')>0){
					saveDatabase(false);
				}
			}
		});		
    }
    function initSoGrid(){
		//LayoutMain.cells("a").progressOn();
        SoGrid = LayoutMain.cells("a").attachGrid();
        SoGrid.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        SoGrid.setHeader(",SO,LINE,QTY,Item Number (Part # Oracle#),PD,REQ,ORDER,ORDER ITEM,CS,RBO,WIDTH,LENGTH,Description RM,Item RM,QTY RM,Item InK,FORM Des Ink,INK QTY,SO MAT IN,SO LUONG PO#,VAT TU THUC TE,15 PCS,BU HAO PO,TI LE BU HAO,Cut/Fold Type,MACHINE,REMARK,ITEM,ORDER_TYPE_NAME,NOTE_ITEM,SHIP_TO_CUSTOMER,CUT_MACHINE,BRAND_PROTECTION");
        // SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
        SoGrid.setColumnIds(",SO,LINE,QTY,Item Number (Part # Oracle#),PD,REQ,ORDER,ORDER ITEM,CS,RBO,WIDTH,LENGTH,Description RM,Item RM,QTY RM,Item InK,FORM Des Ink,INK QTY,SO MAT IN,SO LUONG PO#,VAT TU THUC TE,15 PCS,BU HAO PO,TI LE BU HAO,Cut/Fold Type,MACHINE,REMARK,ITEM,ORDER_TYPE_NAME,NOTE_ITEM,SHIP_TO_CUSTOMER,CUT_MACHINE,BRAND_PROTECTION");
        SoGrid.setInitWidths("30,70,70,70,185,75,70,70,200,110,70,70,70,265,70,70,70,445,70,80,110,115,65,90,90,145,115,335,120,120,120,200,120,200");
        SoGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");
        SoGrid.setColTypes("ch,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        SoGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
        //SoGrid.enableSmartRendering(true);
        SoGrid.init();        
		/*
        SoGrid.load(SoGridLoad, function(){ //takes the path to your data feed      
			LayoutMain.cells("a").progressOff();
        });  
		*/
        SoGrid.attachEvent("onRowSelect", function(id,ind){ // Fire When user click on row in grid            
            console.log(id);
        });     
        SoGrid.attachEvent("onCheck", function(rId,cInd,state){// fires after the state of a checkbox has been changed     
			processCheckSo(rId,cInd,state);
        });       
    }

    function delete_no(no){
		confirm_delete = confirm("Bạn có muốn XÓA "+no);
		if(confirm_delete){
			var url_delete = RootDataPath+'delete_no.php';
			$.ajax({
			url: url_delete,
				type: "POST",
				data: {data: no},
				dataType: "json",
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(result) {
					if(result.status){
						// reload
						viewNOGrid.forEachRow(function(id){
							
							if(viewNOGrid.cells(id,2).getValue()===no){
								viewNOGrid.deleteRow(id);
							}
						});
					}else{
						alert('Có Lỗi trong quá trình XÓA '+no);
					}
				}
			});
		}else{
			
		}
	}

    function initMaterialGrid(){
        MaterialGrid = LayoutMain.cells("b").attachGrid();
        MaterialGrid.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        MaterialGrid.setHeader("MATERIAL,DESCRIPTION,QTY");   //sets the headers of columns
        // SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
        MaterialGrid.setColumnIds("MATERIAL,DESCRIPTION,QTY");         //sets the columns' ids
        MaterialGrid.setInitWidths("130,*,80");   //sets the initial widths of columns
        MaterialGrid.setColAlign("left,left,left");     //sets the alignment of columns
        MaterialGrid.setColTypes("ed,ed,ed");    //sets the types of columns
        MaterialGrid.setColSorting("str,str,str");  //sets the sorting types of columns
        //MaterialGrid.enableSmartRendering(true);
        MaterialGrid.init();   
		// add to grid
		var uniqueID = MaterialGrid.uid();
		var ITEM_RM = SoGrid.cells(checkID,14).getValue();
		ITEM_RM = ITEM_RM.trim();
		var DES_RM = SoGrid.cells(checkID,13).getValue();
		DES_RM = DES_RM.trim();
		var QTY_RM = SoGrid.cells(checkID,15).getValue();
		QTY_RM = QTY_RM.trim();
		var data_add = [ITEM_RM,DES_RM,QTY_RM];
		
		MaterialGrid.addRow(uniqueID,data_add);
    }
	
	function initInkGrid(){
        InkGrid = LayoutMain.cells("c").attachGrid();
        InkGrid.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        InkGrid.setHeader("INK,DESCRIPTION,QTY");   //sets the headers of columns
        // SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
        InkGrid.setColumnIds("INK,DESCRIPTION,QTY");         //sets the columns' ids
        InkGrid.setInitWidths("130,*,80");   //sets the initial widths of columns
        InkGrid.setColAlign("left,left,left");     //sets the alignment of columns
        InkGrid.setColTypes("ed,ed,ed");    //sets the types of columns
        InkGrid.setColSorting("str,str,str");  //sets the sorting types of columns
        //MaterialGrid.enableSmartRendering(true);
        InkGrid.init();
		// add to grid
		var uniqueID = InkGrid.uid();
		var ITEM_INK = SoGrid.cells(checkID,16).getValue();
		ITEM_INK = ITEM_INK.trim();
		var DES_INK = SoGrid.cells(checkID,17).getValue();
		DES_INK = DES_INK.trim();
		var QTY_INK = SoGrid.cells(checkID,18).getValue();
		QTY_INK = QTY_INK.trim();
		var data_add = [ITEM_INK,DES_INK,QTY_INK];
		
		InkGrid.addRow(uniqueID,data_add);		
    }
	
    function processCheckSo(rId,cInd,state){ 
        if(state){
            if(checkID){
                alert("Bạn chỉ có thể chọn một SO-LINE!!!");
                SoGrid.cells(rId,0).setValue(0);
                return false;
            }                
            var so = SoGrid.cells(rId,1).getValue();
            var line = SoGrid.cells(rId,2).getValue();
            checkSo = so+"-"+line;
            checkID = rId;  

        }else{
            checkSo = '';
            checkID = '';
        }
        if(checkSo){            
			// check for no cbs form type =2
			if(checkID){
				initMaterialGrid();
				initInkGrid();				                
				initNO();
			}				
			// end check for no cbs
        }else{
            // reset 
            resetSO();
        }
    }	
	
    function resetSO(){
        //SoGrid.clearAll();
        MaterialGrid.clearAll();
        InkGrid.clearAll();
        LayoutMain.cells("d").detachObject(); // reset
    }  
	
	var input_so;
	var input_po;
	var input_length;
	var checkSOExist = 0;
    function filterSO(){
        input_so = ToolbarMain.getInput("so");
		input_po = ToolbarMain.getInput("po");
		input_length = ToolbarMain.getInput("length");
        input_po.focus(); // set focus
        input_so.value="";
		input_po.value="1";
		input_po.onkeypress = function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){ // enter on input text 
				input_so.focus(); // set focus
			}
		}
		input_length.onkeypress = function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){ // enter on input text 
				input_so.focus(); // set focus
			}
		}
        input_so.onkeypress = function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){ // enter on input text  
				if(!deleteOK){
					alert('Vui lòng đăng nhập vào hệ thống!');
					return false;
				}
                var text = $(this).val();  
                text = text.trim();        
                SoGrid.clearAll(); //remove all data
                //save query string in global variable (see step 5 for details)
                SoGridLoadTmp = SoGridLoad;
				var PO = ToolbarMain.getValue('po');
				var Length = ToolbarMain.getValue('length');
				if(Length&&isNaN(Length)==true){
					alert("Length phải là số!!!");
					input_length.focus(); // set focus
					return false;
				}
				if(!PO){
					alert("Vui lòng nhập số lượng PO#!!!");
					input_po.focus(); // set focus
					return false;
				}else{
					if(isNaN(PO)==true){
						alert("Số lượng PO# phải là số!!!");
						input_po.focus(); // set focus
						return false;
					}
				}
				// check promise date
				checkPromiseData(text);

				checkOrderTypeName(text);
				
				// CHECK EXIST SO
				checkExistSO(text);
				if(checkSOExist==1){
					alert("SO LINE NÀY ĐÃ ĐƯỢC TẠO LỆNH, VUI LÒNG KIỂM TRA LẠI ĐƠN HÀNG");
					location.reload();
					return false;
				}
                SoGridLoad+= "?name_mask="+text+"&PO="+PO+"&length="+Length+"&promise_data="+promise_data;
                
                SoGrid.load(SoGridLoad, function(){ //takes the path to your data feed  
                    if(!SoGrid.getRowsNum()){
                        alert("SO không tồn tại trên hệ thống hoặc Không có Master Item !!!");
                        return false;
                    }else{
						var item_number = SoGrid.cellByIndex(0,4).getValue();						
						if(!item_number){
							var item = SoGrid.cellByIndex(0,28).getValue();
							alert('ITEM_NUMBER: '+item+' chưa tồn tại trên hệ thống vui lòng cập nhật!');
							SoGrid.clearAll();
							return false;
						}
					}
                    // check if one row
                    var count_row = 0;
                    var idSO = '';
                    SoGrid.forEachRow(function(id){
                        count_row++;
                        idSO = id;
                        // check again item
                        if(checkSo.length&&checkSo.indexOf(id)!=-1){
                            SoGrid.cells(id,0).setValue(1); // Check
                        }
                    })
                    if(count_row===1){
                        SoGrid.cellByIndex(0,0).setValue(1); // Check  
                        // call ham    
						
                        processCheckSo(idSO,0,true);                             
                    }
                });
                SoGridLoad =  SoGridLoadTmp; 
                event.stopPropagation();
            }        
        }
    }
	
	var input_so_print;
	function printLRPM(){
		input_so_print = ToolbarMain.getInput("print_lrpm");
		input_so_print.onkeypress = function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){ // enter on input text  
				if(!deleteOK){
					alert('Vui lòng đăng nhập vào hệ thống!');
					return false;
				}				
                var text = $(this).val();  
                text = text.trim();
				if(!text){
					alert("Vui lòng nhập SO LINE!!!");
					return false;
				}
				// call ajax to check and print 
				var url_check_so = RootDataPath+'check_so_to_print.php?SO_LINE='+text; 
				$.ajax({
				url: url_check_so,
					type: "POST",
					async: false,
					//data: {data: JSON.stringify(jsonObjects) },
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
						x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							// open link to print
							//clear input text
							input_so_print.value = '';
							var wi = window.open('about:blank', '_blank');
							//$(wi.document.body).html("<p>Please wait while you are being redirected...</p>");
							wi.location.href = 'print.php?id='+result.data;
						}else{
							input_so_print.value = '';
							alert(result.mess);
							return false;
						}
					}
				});
                event.stopPropagation();
            }
		}		
	}
	
	function checkExistSO(SO_LINE){
		var url_save = RootDataPath+'check_so_line.php?SO_LINE='+SO_LINE; 
		$.ajax({
		url: url_save,
			type: "POST",
			async: false,
			//data: {data: JSON.stringify(jsonObjects) },
			dataType: "json",
			beforeSend: function(x) {
				if (x && x.overrideMimeType) {
				x.overrideMimeType("application/j-son;charset=UTF-8");
				}
			},
			success: function(result) {
				checkSOExist = result;
			}
		});
	}
	var promise_data = '';
	function checkPromiseData(SO_LINE){
		var url_save = RootDataPath+'check_promise_date.php?SO_LINE='+SO_LINE; 
		$.ajax({
		url: url_save,
			type: "POST",
			async: false,
			//data: {data: JSON.stringify(jsonObjects) },
			dataType: "json",
			beforeSend: function(x) {
				if (x && x.overrideMimeType) {
				x.overrideMimeType("application/j-son;charset=UTF-8");
				}
			},
			success: function(result) {
				if(result.status){
					promise_data = result.promise_date;
				}else{
					confirm_delete = confirm(result.mess);
					if(!confirm_delete){
						location.reload();
						return false;
					}
				}				
			}
		});
	}

	var promise_data = '';
	function checkOrderTypeName(SO_LINE){
		var url_save = RootDataPath+'check_order_type_name.php?SO_LINE='+SO_LINE; 
		$.ajax({
		url: url_save,
			type: "POST",
			async: false,
			//data: {data: JSON.stringify(jsonObjects) },
			dataType: "json",
			beforeSend: function(x) {
				if (x && x.overrideMimeType) {
				x.overrideMimeType("application/j-son;charset=UTF-8");
				}
			},
			success: function(results) {

				if(results.status){
					dhtmlx.confirm({
						type:"confirm",
						text: results.mess,
						callback: function(result){
							if (!result ) {
								location.reload();
								return false;
							}
							
						}
					});

					

				}				
			}
		});
	}
	
	function checkTab(evt) {
	  var evt = (evt) ? evt : ((event) ? event : null);
	  var EnterKey = 13;
	  if(evt.keyCode == EnterKey) {
		input_so.focus();
	  }
	}
	
    $(document).ready(function(){
		var VNRISIntranet = '<?php echo getUser(); ?>';
        
        if (!VNRISIntranet ) {
            var pr = prompt('Nhập tiền tố email trước @. Ví dụ: tan.doan', '');
            pr = pr.trim();
            if (!pr || pr.indexOf('@') !== -1 ) {
                alert('Bạn vui lòng nhập đúng tiền tố email là phần trước @');
            } else {
                // Save email đến bảng thống kê (au_avery.planning_user_statistics)
                setCookie('VNRISIntranet', pr, 30 );
                // setCookie('VNRISIntranet', pr, 30 );
                var VNRISIntranet = '<?php echo getUser(); ?>';
                var pr_s = '<?php echo planning_user_statistics($email, "TPFL_Planning"); ?>';
                
                
                check_gg = 1;
            }
            
           
        }
		
		if (check_gg) location.href = './';

        initLayout();
        initMenu();
        initToolbar(); 
        initSoGrid();
        filterSO();  
		printLRPM();
    });    
</script>
</head>
<body>
    <div style="height: 30px;background:#205670;font-weight:bold">
		<div id="menuObj"></div>
    </div>
    <div style="position:absolute;width:100%;top:35;background:white">
		<div id="ToolbarBottom" ></div>
    </div>
</body>
</html>