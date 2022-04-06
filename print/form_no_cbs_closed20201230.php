<!DOCTYPE html>
<html>
<head>
<title>PFL THERMAL</title>
<meta name="google" content="notranslate" />
<link rel="stylesheet" href="<?php echo $urlRoot.'print/css/style.css';?>">
<style> 
    .bottom-remark-box {
        width: 100%;
        min-height: 60px;
        border: 1px solid #92CDDC;
        min-height: 150px;
        
    }

    .bottom-remark-left {
        width: 66%;
        min-height: 110px;
        float:left;
        font-size: 14px;
        /* background-color: rgb(129, 80, 208); */
    }

    .bottom-remark-right {
        width: 32.5%;
        min-height: 60px;
        float: right;
        /* background-color: #92D050; */
        min-height: 110px;
        text-align: center;
        padding-top: 10px;
    }

    .bottom_remark_color {
        background-color: #92D050;
        padding-top:1.5px;
        height:23px;font-size:15px;font-weight:bold;
    }

    .bottom_remark_color-2 {
        background-color: rgb(231, 233, 229);
        padding-top:2px;
        height:23px;font-size:15px;font-weight:bold;
    }

    .worldon {
        width:200px;height:45px;border: 3px solid blue;font-size:28px;font-weight:bold;padding-top:10px;color:blue;
    }

    .remark-special-item {
        width:200px;height:35px;border: 3px solid black;font-size:22px;font-weight:bold;margin-top:3px;padding-top:5px;color:white;background-color:black;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .remark-nhan-cap{
        width:120px;height:35px;border: 2px solid black;font-size:25px;font-weight:bold;margin-top:1px;padding-top:5px;color:white;background-color:black;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
</style>
</head>
<script type="text/javascript">
    window.onload = function() { 
        window.print(); 		  
		setTimeout(function () { window.close(); }, 100);
	  }
</script>
<?php
    require_once ('pro_supply.php');

	$a_pt25_b = 'aleft bold pt25';
	$pt25_b = 'bold pt25';
	$a_pt22_b = 'aleft bold pt22';
	$pt22_b = 'bold pt22';
	$a_pt16_b = 'aleft bold pt16';
	$pt16_b = 'bold pt16';
	$a_pt14_b = 'aleft bold pt14';
	$pt14_b = 'bold pt14';
	$a_pt12_b = 'aleft bold pt12';
	$pt12_b = 'bold pt12';
    $a_pt10_b = 'aleft bold pt10';
    $pt10_b = 'bold pt10';
    $pt8_b = 'bold pt8';
    $a_pt8_b = 'aleft bold pt8';
    $pt6_b = 'bold pt6';
    $a_pt6_b = 'aleft bold pt6';
    $non_border = 'none_border';
    $spacer = '<td class="none_border">&nbsp;</td>';
?>
<body >
	<div style="height:100%; width:100%;"> <!-- fix page break -->
        <table style="width:99%;height:99%;border-collapse:collapse;margin-left:0%;"  cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="" class="<?php echo $non_border;?>">
                        <table class="header padding" style="width:100%;height:100%;border-collapse:collapse;"  cellpadding="0" cellspacing="0"> <!-- table 1-->
                            <tr class="production bold pt14 nen_cam">
                                <td colspan="7">LỆNH SẢN XUẤT PFL -THERMAL /PRODUCTION ORDER</td>
                            </tr>
                            <tr class="production bold pt14">
                                <td class="" colspan="7"><?php echo strtoupper($print_method);?></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="width:35%;text-align:center; color:red;font-size:22px;" class="<?php echo $a_pt10_b;?>">
                                    <?php 
                                        //@tandoan: if in array array_RBO_check_MLA (in file pro_supply ): show remark: MLA
                                        if (!empty($REMARK_MLA) ) {
                                            echo "<span style='border:0 solid; ' >".$REMARK_MLA."</span>";
                                            echo "<br />";
                                        }
                                        echo $BARCODE;
                                    ?>
                                </td>
                                <td colspan="2">&nbsp;</td>
                                <td style="width:15%" colspan="1" class="aleft bold pt8">Người Làm Lệnh:</td>
                                <td style="width:20%" class="bold pt10" colspan="2"><?php echo $NICK_NAME;?></td>
                            </tr>
                            <tr>
                                <td style="width:10%" class="<?php echo $a_pt10_b;?>">No:</td><td class="nen_vang <?php echo $a_pt12_b;?>" style="width:25%"><?php echo $NUMBER_NO;?></td><td colspan="2">&nbsp;</td><td style="width:15%" colspan="1" class="<?php echo $a_pt8_b;?>">Số Máy:</td><td style="width:20%" class="<?php echo $pt25_b;?>" colspan="2"><?php echo $SO_MAY;?></td>
                            </tr>
                            <tr>
                                <td class="<?php echo $a_pt10_b;?>">Ngày/Date:</td>
                                <td class="nen_cam1 <?php echo $a_pt10_b;?>"><?php echo $SAVE_DATE;?></td>
                                <?php
                                    if (!empty($remarkNhancap) ) {
                                        echo '<td colspan="2" rowspan="2" class="remark-nhan-cap">';
                                            echo $remarkNhancap;
                                        echo '</td>';
                                    } else {
                                        echo '<td colspan="2" rowspan="2">';
                                            echo '&nbsp;';
                                        echo '</td>';
                                    }
                                    
                                ?>
                                <td style="width:15%" colspan="1" class="<?php echo $a_pt8_b;?>">Số Thứ Tự:</td>
                                <td style="width:20%" class="<?php echo $pt25_b;?>" colspan="2"><?php echo $STT;?></td>
                            </tr>
                            <tr>
                                <td class="<?php echo $a_pt10_b;?>">Order date:</td>
                                <td class="nen_cam2 <?php echo $a_pt10_b;?>"><?php echo $ORDERED;?></td>
                                <!-- <td colspan="2">&nbsp;</td> -->
                                <td style="width:15%" colspan="1" class="<?php echo $a_pt8_b;?>">Kế Hoạch:</td>
                                <td style="width:20%" class="<?php echo $pt12_b;?>" colspan="2"><?php echo $PLAN;?></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="<?php echo $a_pt10_b;?>">Đơn đặt hàng khách/Customer PO no.:</td><td style="border:2px solid" colspan="3" class="nen_vang pt20 bold"><?php echo $SO_LINE;?></td>
                                <td>&nbsp;<span style="text-align:center; font-size:20px;font-weight:bold;"><?php 
                                        //@tandoan: show order_type_name: VN REPLACEMENT
                                        $SO_LINE_VN_REPLACEMENT = explode('-',$SO_LINE);
                                        
                                        $ORDER_NUMBER_VN_REPLACEMENT = $SO_LINE_VN_REPLACEMENT[0];
                                        $LINE_NUMBER_VN_REPLACEMENT = $SO_LINE_VN_REPLACEMENT[1];
                                        $sql_vn_replacement = "SELECT ORDER_TYPE_NAME FROM vnso_total WHERE ORDER_NUMBER='$ORDER_NUMBER_VN_REPLACEMENT' AND LINE_NUMBER = '$LINE_NUMBER_VN_REPLACEMENT' ";
                                        $result_vn_replacement = mysqli_query($conn138,$sql_vn_replacement);
                                        $NUM_VN_REPLACEMENT = mysqli_num_rows($result_vn_replacement);
                                        
                                        if ( $NUM_VN_REPLACEMENT>0 ) {
                                            $result_vn_replacement = mysqli_fetch_array($result_vn_replacement);
                                            
                                            $ORDER_TYPE_NAME_VN_REPLAEMENT = $result_vn_replacement['ORDER_TYPE_NAME'];
                                            if ($ORDER_TYPE_NAME_VN_REPLAEMENT == 'VN REPLACEMENT') {
                                                    $VN_REPLACEMENT  = 'REPLACEMENT';
                                            } else $VN_REPLACEMENT  = '';
                                        } else $VN_REPLACEMENT = '';

                                        echo $VN_REPLACEMENT; 
                                    ?></span>
                                </td>
                                <?php echo $spacer;?>
                            </tr>
                            <tr>
                                <td colspan="2" class="<?php echo $a_pt10_b;?>">Mã hàng/Item:</td><td style="width:15%" colspan="1" class=""><td colspan="1" class="nen_vang <?php echo $pt12_b;?>"><?php echo $ITEM_NUMBER;?></td><td style="width:10%" colspan="1" class=""><td colspan="2"></td>
                            </tr>
                            <tr>
                                <td style="width:18%" class="<?php echo $a_pt10_b;?>" colspan="1">Mô tả/Description:</td><td class="nen_cam2 <?php echo $pt10_b;?>" colspan="6"><?php echo $ITEM_CODE;?></td>
                            </tr>
                            <tr>
                                <td class="<?php echo $a_pt10_b;?>">Promise date:</td><td class="nen_cam1 <?php echo $a_pt10_b;?>"><?php echo $PD;?></td><td class="" colspan="2">&nbsp;</td>
                                <td colspan="2" style="width:10%;" class="<?php echo $a_pt10_b;?>">Request date:</td><td style="width:12%;" class="nen_cam1 <?php echo $a_pt10_b;?>"><?php echo $REQ;?></td><td class="" colspan="3">&nbsp;</td>
                            </tr>
                        </table>
                </td>
            </tr>
            <tr>
                <td style="padding:0px;" class="<?php echo $non_border;?>">
                    <table class="padding" style="width:100%;height:100%;border-collapse:collapse;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="padding-top:8px;" colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt12_b;?>">KÍCH THƯỚC IN:</td> <td style="padding-top:5px;" class="aleft pt14 bold <?php echo $non_border;?>" colspan="3"><?php echo $KICH_THUOC_IN;?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="<?php echo $non_border;?> <?php echo $a_pt8_b;?>">VẬT TƯ/MATERIAL</td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td> <td class="aleft pt10 bold <?php echo $non_border;?>" colspan="1">Loại mực/Ink type:</td><td class="aleft pt14 bold nen_cam2 <?php echo $non_border;?>" colspan="2">nhãn in <?php echo $SO_MAT_IN;?> mặt</td>
                        </tr>
                        <tr>
                            <td style="width:25%"  class="nen_xanh4 <?php echo $pt10_b;?>"><?php echo !empty($arr_supply[0])?$arr_supply[0]['MATERIAL_DES']:''; ?></td><td style="width:15%" class="nen_vang <?php echo $pt10_b;?>"><?php echo !empty($arr_supply[0])?$arr_supply[0]['MATERIAL_CODE']:''; ?></td><td style="width:8%" class="nen_cam <?php echo $pt10_b;?>"><?php echo !empty($arr_supply[0])?number_format($arr_supply[0]['MATERIAL_QTY']):''; ?></td><td style="width:7%" class="<?php echo $pt10_b;?>"><?php echo !empty($arr_supply[0])?$arr_supply[0]['UNIT']:''; ?></td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td>
                            <td style="width:20%" class="nen_cam <?php echo $pt10_b;?> "><?php echo !empty($arr_supply[0])?$arr_supply[0]['INK_DES']:''; ?></td><td style="width:15%" class="nen_vang <?php echo $pt10_b;?>"><?php echo !empty($arr_supply[0])?$arr_supply[0]['INK_CODE']:''; ?></td><td style="width:14%" class="nen_cam <?php echo $pt10_b;?>"><?php echo !empty($arr_supply[0])?number_format($arr_supply[0]['INK_QTY']):''; ?></td>
                        </tr>
						<!-- so_luong_po -->
						<tr class="so_luong_po">
                            <td	colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">Số lượng PO#:</td>
							<td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>"> <?php echo $SO_LUONG_PO;?> </td>
							<td colspan="1" class="<?php echo $a_pt10_b;?> <?php echo $non_border;?>">&nbsp;</td>
							<td colspan="3" class="<?php echo $pt10_b;?> <?php echo $non_border;?> border_left">Màu</td> 
							<td colspan="1" class="<?php echo $pt10_b;?>">PO #</td>
                            <td colspan="4" class="<?php echo $pt10_b;?>">Ghi chú</td>
                        </tr>
						<!-- vat tu thuc te -->
						<tr class="so_luong_po">
                            <td	colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">Vật tư thực tế:</td>
							<td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">  <?php echo number_format($VAT_TU_THUC_TE);?> </td>
							<td colspan="1" class="<?php echo $pt10_b;?>">Vải</td>
							<td colspan="3" class="<?php echo $pt10_b;?>">&nbsp;</td> 
							<td colspan="1" class="<?php echo $pt10_b;?>">&nbsp;</td>
                            <td colspan="4" class="<?php echo $pt10_b;?>">&nbsp;</td>							
                        </tr>
						<!-- 15 pcs mẫu -->
						<tr class="so_luong_po">
                            <td	colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">15 pcs mẫu:</td>
							<td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">  <?php echo $PCS_MAU;?> </td>
							<td colspan="1" class="<?php echo $pt10_b;?>">Giấy</td>
							<td colspan="3" class="<?php echo $pt10_b;?>">&nbsp;</td> 
							<td colspan="1" class="<?php echo $pt10_b;?>">&nbsp;</td>
                            <td colspan="4" class="<?php echo $pt10_b;?>">&nbsp;</td>	
                        </tr>
						<!-- vat tu thuc te -->
						<tr class="so_luong_po" >
                            <td	colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">Bù hao theo PO#:</td>
							<td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">  <?php echo ($BU_HAO_PO);?> </td>
							<td colspan="1" class="<?php echo $pt10_b;?>">Mực</td>
							<td colspan="3" class="<?php echo $pt10_b;?>">&nbsp;</td> 
							<td colspan="1" class="<?php echo $pt10_b;?>">&nbsp;</td>
                            <td colspan="4" class="<?php echo $pt10_b;?>">&nbsp;</td>
                        </tr>
						<!-- vat tu thuc te -->
						<tr class="so_luong_po">
                            <td	colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">Tỉ lệ bù hao theo PO#:</td><td colspan="3" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">  <?php echo $TI_LE_BU_HAO;?> </td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td>
                            <td colspan="3" class="<?php echo $non_border;?>">&nbsp;</td>
                        </tr>
						<!-- so luong con nhan-->
						<tr style="height:7%">
							<td style="" colspan="1" class="none_border nen_vang bold pt20"><font color="#FF0000">QTY: <?php echo number_format($QTY);?></font></td>
							<td colspan="1" class="none_border aleft bold pt12">PCS</td>
                            <td	colspan="2" class="<?php echo $non_border;?>"></td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td>
                            <td colspan="4" style="width:13%" class="<?php echo $non_border;?> nen_xanh2 <?php echo $pt12_b;?> "> <?php echo $CUT_TYPE;?></td>
                        </tr>
                        <!-- san xuat -->
						<tr class="so_luong_po">
                            <td	colspan="4" class="<?php echo $non_border;?> <?php echo $a_pt12_b;?>">Sản xuất:</td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td>
							<td colspan="4" style="width:13%" class="<?php echo $non_border;?> nen_vang <?php echo $pt8_b;?> "><?php echo $CUT_TYPE_NOTE; // @@@@@?></td>
                        </tr> 
						<!-- nhan vien in -->
						<tr class="nhan_vien_in so_luong_po">
                            <td	colspan="4" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">Nhân viên in (Printed by):</td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td>
                            <td colspan="3" style="width:13%" class="<?php echo $non_border;?> <?php echo $pt8_b;?> "> Ký tên (Signature):</td>
                        </tr>
						<tr class="nhan_vien_in so_luong_po">
                            <td	colspan="4" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">Nhân viên cắt (Cutting by):</td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td>
                            <td colspan="3" style="width:13%" class="<?php echo $non_border;?> <?php echo $pt8_b;?> "> Ký tên (Signature):</td>
                        </tr>
						<tr class="nhan_vien_in so_luong_po">
                            <td	style="padding-top:5px;" colspan="4" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">Nhân viên QC (Quality checked by):</td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td>
                            <td colspan="3" style="width:13%" class="<?php echo $non_border;?> <?php echo $pt8_b;?> "> Ký tên (Signature):</td>
                        </tr>
						<tr class="nhan_vien_in so_luong_po">
                            <td	style="padding-top:5px;" colspan="4" class="<?php echo $non_border;?> <?php echo $a_pt10_b;?>">Nhân viên đóng gói (Packed by):</td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td>
                            <td colspan="3" style="width:13%" class="<?php echo $non_border;?> <?php echo $pt8_b;?> "> Ký tên (Signature):</td>
                        </tr>
						<tr class="so_luong_po">
                            <td	style="padding-top:5px;" colspan="4" class="<?php echo $non_border;?> <?php echo $a_pt12_b;?>">May in/Machine:</td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td>
							<td colspan="3" style="width:13%" class="<?php echo $non_border;?> <?php echo $pt12_b;?> "> <?php echo $MACHINE;?></td>
                        </tr>
						<tr class="so_luong_po">
                            <td	style="padding-top:5px;" colspan="4" class="<?php echo $non_border;?> <?php echo $a_pt12_b;?>">SO CPU:</td><td colspan="1" class="<?php echo $non_border;?> <?php echo $a_pt6_b;?>"></td><td class="<?php echo $non_border;?>" style="width:5%">&nbsp;</td>
							<td colspan="3" style="width:13%" class="<?php echo $non_border;?> <?php echo $pt8_b;?> ">&nbsp;</td>
                        </tr>
                    </table>                
                </td>
            </tr> 
            <tr style="height:15%">
            <td colspan="" class="<?php echo $non_border;?>">
            <div style="text-align:left;font-size:14px;padding-top:8px;">Ghi chú: </div>
                <?php 
                    echo '<div class="bottom-remark-box">';
                        
                        echo '<div class="bottom-remark-left"> ';
                            echo '<div class="bottom_remark_color" style="">';
                                echo $REMARK;
                            echo '</div>';
                            //style="height:25px;font-size:15px;font-weight:bold;"
                            echo '<div class="bottom_remark_color-2" >';
                                echo $REMARK_2;
                            echo '</div>';

                            echo '<div class="bottom_remark_color">';
                                echo $NOTE_ITEM;
                            echo '</div>';

                            echo '<div class="bottom_remark_color-2" >';
                                echo $remark_bo_coc_200pcs;
                            echo '</div>';

                            echo '<div class="bottom_remark_color">';
                                echo $remark_20191212;
                            echo '</div>';
                            echo '<div class="bottom_remark_color-2" >';
                                echo $remark_SPECIAL_REQUEST;
                                
                            echo '</div>';
                        echo '</div>';
                        
                        // note bên phải
                        echo '<div class="bottom-remark-right">';
                            
                            if (!empty($remark_WORLDON) ) {
                                echo '<div class="worldon">';
                                    echo $remark_WORLDON;
                                echo '</div>';
                            }

                            if (!empty($remarkSpecialItem) ) {
                                echo '<div class="remark-special-item">';
                                    echo $remarkSpecialItem;
                                echo '</div>';
                            }

                        echo '</div>';
                        
                    echo '</div>';
                ?>
                <!-- <div style="text-align:center;">Day day day day</div> -->
            </td>
            </tr>
        </table>
    </div>
</body>
</html>