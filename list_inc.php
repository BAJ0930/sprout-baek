이전소스

<?php
if (!defined('_CHEONYU_')) exit; // 개별 페이지 접근 불가


/*===============================================
	신규상품 / 인기상품
===============================================*/
$orderMember = fnGetPriceMem("");
if($s2 == "2") $order = " PsellCount DESC, IDX DESC ";
else if($s2 == "3") $order = " Pprice3 ASC, IDX DESC ";
else if($s2 == "4") $order = " Pprice3 DESC, IDX DESC ";
else if($s2 == "5") $order = " Pdiscount".$orderMember." DESC, IDX DESC ";
else $order = " Pregdate DESC, IDX DESC ";

$s3Arr = explode(",",$s3);
$s3Where = "";
for($i = 0; $i < count($s3Arr); $i ++){
    if($i == 0) $s3OR = "";
    else $s3OR = " OR ";
    $s3Where .= $s3OR . " (a.BRIDX = '" . $s3Arr[$i] . "' OR a.BRIDX2 = '" . $s3Arr[$i] . "') ";
}
if($s3Where) $s3Where = "(" . $s3Where . ")";
if($actOn){
    if($actOn == "best"){
        $fnCateIDX = " AND (b.Pcategory2 like '".$cateIDXdb."%' OR b.Pcategory3 like '".$cateIDXdb."%') ";        
        $sql = " SELECT b.* FROM nBest AS a LEFT JOIN 2011_productInfo AS b ON a.pCode = b.IDX ";
        $sql .= " WHERE b.Pshop = '" . $shopID . "' AND b.Pdeleted = 0 AND b.PenglishView = 0 " . $fnCateIDX;
        $sql .= " AND b.Pprice3 > 0 AND b.Pagree = 1 AND b.Pstate = 1 AND b.PstockCount > 0 AND Pweight < '{$checkWeight}' ";
        if($s3) $sql.="  AND (b.BRIDX = '" . $s3 . "' or b.BRIDX2 = '" . $s3 . "') ";
        $sql .= " GROUP BY b.IDX ";
        $order = "  a.pCnt DESC ";
    } else if($actOn == "new"){
        $fnCateIDX = " AND (a.Pcategory2 like '".$cateIDXdb."%' OR a.Pcategory3 like '".$cateIDXdb."%') ";
        $sql = " SELECT a.* FROM 2011_productInfo AS a ";
        $sql .= " WHERE Pshop='" . $shopID . "' AND Pdeleted=0 AND PenglishView = 0 AND Pprice3>0 AND Pstate<10 AND Pagree=1 AND Pweight < '{$checkWeight}' " .$fnCateIDX;
        if($s3) $sql.="  AND (a.BRIDX = '" . $s3 . "' or a.BRIDX2 = '" . $s3 . "') ";
        $order = " Pregdate DESC, IDX DESC ";
    } else if($actOn == "sale"){
        if(!$cIcon) $cIcon = "6,7";
        $fnCateIDX = " AND (a.Pcategory2 like '".$cateIDXdb."%' OR a.P
        category3 like '".$cateIDXdb."%') ";
        $sql = " SELECT a.* FROM 2011_productInfo AS a ";
        $sql .= " WHERE Pshop='" . $shopID . "' AND Pdeleted=0 AND Pprice3>0 AND Pstate<10 AND Pagree=1 AND Picon1 IN (" . $cIcon .  ") " .$fnCateIDX;
        if($s3) $sql.="  AND " . $s3Where;
        if($s4) $sql .= " and Picon1 IN ({$s4}) ";
        if($s5Price1) $sql .= " AND Pprice2 >= {$s5Price1} ";
        if($s5Price2) $sql .= " AND Pprice2 <= {$s5Price2} ";
        $order = " Pregdate DESC, IDX DESC ";
        $order = ($changeOrder) ? $changeOrder : $order;
    }
} else {
    if($s2 == 2) $sql = "SELECT a.* FROM `2011_productInfo` AS a ";
    else $sql = " SELECT a.* FROM `2011_productInfo` AS a  ";
    if($searchKind == "barcode") $sql .= " LEFT JOIN `2011_productOption` AS h ON a.IDX = h.PIDX ";
    $sql .= " WHERE Pshop='" . $shopID . "' AND Pdeleted=0 AND PenglishView = 0 AND Pprice3>0 AND Pstate<10 AND Pagree=1 AND Pweight < '{$checkWeight}' " .$defaultWhere . $where;
    if($s3) $sql.="  AND (a.BRIDX = '" . $s3 . "' or a.BRIDX2 = '" . $s3 . "') ";
    if($searchKind == "barcode") $sql .= " GROUP BY a.IDX ";
}

$result = sql_query($sql);
$TotalCount = mysqli_num_rows($result);
$PagePerList = 10;
$StartPos = ($page - 1) * $listSize;
$sql .= " ORDER BY " . $order;
if ($listSize > 0) { // $listSize가 0이면 LIMIT 적용 안 함
    $sql .= " LIMIT ".($page - 1) * $listSize.",".$listSize;
}
$result = sql_query($sql);

if($viewType == 1) $div_class = "sum_box";
else if($viewType == 2) $div_class = "list_box";
?>
<div class="<?=$div_class?>">
<?php
while($rs = sql_fetch_array($result)){
    foreach ($rs as $fieldName => $fieldValue){$fieldName = "db" . $fieldName;$$fieldName = $fieldValue;}
    if (in_array($dbIDX, $outIdx)) continue;
    $imgUrl = getImgUrl($dbPsaveFile1);
    $pImg = "<img src='".$imgUrl."' border='0' style=''/>";
    $dbPready = getProductReady($dbIDX);
    $dbPstockCount = $dbPstockCount - $dbPready;
    $getInfo = getListInfo($rs, $dbPstockCount);
    $icon = $getInfo['icon'];
    $icon2 = $getInfo['icon2'];
    $icon3 = $getInfo['icon3'];
    $boxin2 = $getInfo['boxin2'];
    $nCount = $getInfo['nCount'];
    $addCheckMsg = $getInfo['addCheckMsg'];
    $dbPstockCount = $getInfo['dbPstockCount'];
    if($_Minus == 1) $dbPorderMinus = 1;
    $priceInfo = fnCalPrice($dbPprice3,$rs);
    $link = "<a href='/product/view.html?qIDX=" . $dbIDX . $optionLink ."'  title='" . $dbPengName . "' class='pLink'>";
    $dbPname_list = $dbPengName;
    $btnDisabled = "";
    if($dbPstockCount < 1  && $dbPorderMinus != 1) $btnDisabled = " disabled ";
    if($viewType==1){
?>
<div class="box">
    <div style="position:relative">
        <span class="icon boxin"><?= $boxin2 ?></span>
        <?php if ($dbPicon1 > 5): ?>
            <div style="position:absolute;left:3px;top:3px;width:62px;height:67px; background-image: url('/img/circle160_<?=$dbPicon1?>.png?2210'); background-repeat:no-repeat;"></div>
        <?php endif; ?>
        <?=$link?><p class="imgOver"><?=$pImg?></p></a>
    </div>
    <span class="tit"><?=$fnBrandName[$dbBRIDX]?></span>
    <span class="tit2">Item No. <?=$dbIDX?></span>
    <span class="text">
        <?=$link?><?=$dbPengName?></a>
        <?php if ($dbPstockDate && $dbPstockDate > mktime(0, 0, 0, date("m"), date("d"), date("Y"))): ?>
             <span style='color:red; font-size:11px;'>Expectation date of warehoused: <?= date("y-m", $dbPstockDate) ?>
                <?php 
                    if (date("d", $dbPstockDate) < 10) echo "At the beginning of the month";
                    elseif (date("d", $dbPstockDate) < 20) echo "In the middle of the month";
                    else echo "The end of the month";
                ?>
            </span>
        <?php endif; ?>
    </span>
    <? if($MID) { ?>
        <div class="price-dollar">
            <font class="sale">USD <?=$priceInfo["dollar_txt"]?></font>
            <font class="point"><strong><span style="font-size: 13px; font-weight: 400; color: #555555;">USD </span> <?=$priceInfo["dcDollar_txt"]?></strong></font>
        </div>
        <span class="price">
            <?php if ($priceInfo["dcPer"]): ?>
                <font class="percent"><strong><?=$priceInfo["dcPer"]?></strong>%<span class="max"><?=fnGETPricePer($rs); ?></span></font>
            <?php endif; ?>
        </span>
        <? } else { ?>
            <div class="before-price-wrap" style="border: 0;">
                <div class="price-notice">Login To See Price</div>
            </div>
        <? } ?>
    <div class="amount_box">
        <div class="arrow">
            <input type="checkbox" name="inPcheck<?=$addID?>" id="inPcheck<?=$addID?>" class="check" value="<?=$dbIDX?>" <?=$btnDisabled?> <?=$addCheckMsg?> />
            <img src="/img/ico/ico-item-minus.svg" alt="down" id="btn_minus" style="cursor:pointer" onclick="fnPcountPlus(this,-1)" <?=$btnDisabled?>>
            <input name="inPcount" type="text" class="amount" id="inPcount" size="3" maxlength="3" value="<?=$nCount?>" onkeydown="onlyNum()" onblur="fnPcountCheck(this)" <?=$btnDisabled?> />
            <img src="/img/ico/ico-item-plus.svg" alt="up" id="btn_plus" style="cursor:pointer" onclick="fnPcountPlus(this,1)" <?=$btnDisabled?>>
        </div>
        <div class="flex-row-gap6">
            <img src='/img/ico/btn-addToCart.svg' alt="Add to Cart" style="cursor:pointer;" id="btn_addCart" onclick="fnCartIn(<?=$dbIDX?>,this)" <?=$btnDisabled?>>
        </div>
    </div>
    <span class="icon"><?= $icon . $icon2 . $icon3 ?></span>
</div>
<?php
    } else if($viewType==2) {
?>
<div class="box">
    <span class="img">
        <?=$link?>
        <div style="position:relative"> 
            <?php if ($dbPicon1 > 5): ?>
                <div style="position:absolute;left:-8px;top:-6px;width:58px;height:57px;">
                    <img src="/img/line80_<?=$dbPicon1?>.png" border="0">
                </div>
            <?php endif; ?>
            <p><?=$pImg?></p>
        </div>
        </a>
    </span>
    <div class="text_box">
        <?=$link?>
        <span class="tit"><?=$fnBrandName[$dbBRIDX]?></span>
        <span class="text"><?=$dbPname_list?></span>
        <span class="icon"><?=$icon . $icon2 . $icon3 . $boxin2?></span>        
        <?php
        if ($dbPstockDate && $dbPstockDate > mktime(0, 0, 0, date("m"), date("d"), date("Y"))) {
            echo "<span style='color:red;font-size:11px;'>Expectation date of warehoused : " . date("y-m", $dbPstockDate);
            if (date("d", $dbPstockDate) < 10) echo "At the beginning of the month";
            else if (date("d", $dbPstockDate) < 20) echo "In the middle of a month";
            else echo "The end of month";
            echo "</span>";
        }
        ?>
        </a>
    </div>
    <div class="price">
        <font class="sale">USD <?=$priceInfo["dollar_txt"]?></font><br />
        <font class="point"><strong><span style="font-size: 13px; font-weight: 400; color: #555555;">USD</span> <?=$priceInfo["dcDollar_txt"]?></strong></font>
    </div>
    <span class="percent">
        <strong><?=$priceInfo["dcPer"]?></strong>%<span class="max"><?=fnGETPricePer($rs); ?></span>
    </span>
    <span class="amount_box">  
        <input type="checkbox" name="inPcheck<?=$addID?>" id="inPcheck<?=$addID?>" class="check" value='<?=$dbIDX?>' <?php if (!$dbPstockCount && $dbPorderMinus != 1) echo "disabled"; ?> <?=$addCheckMsg?> />
        Qty 
        <input name="inPcount" type="text" class="amount" id="inPcount" size="3" maxlength="3" value='<?=$nCount?>' onkeydown='onlyNum()' onblur="fnPcountCheck(this)" <?php if (!$dbPstockCount && $dbPorderMinus != 1) echo "disabled"; ?> /> 
        <span class="arrow">
            <img src="/img/ico/ico_arrow_up.gif" alt="up" id="btn_plus" style="cursor:pointer" onclick="fnPcountPlus(this,1)">
            <img src="/img/ico/ico_arrow_down.gif" alt="down" id="btn_minus" style="cursor:pointer" onclick="fnPcountPlus(this,-1)">
        </span> 
        <img src="/img/icon_pdlist_cart.svg" alt="ADDTOCART" style="cursor:pointer;" id="btn_addCart" onclick="fnCartIn(<?=$dbIDX?>,this)">
    </span>
</div>
<?php
    }
?>
<input type='hidden' name='inMaxStock' id='inMaxStock' value='<?=$dbPstockCount?>'>
<input type='hidden' name='inPorderMinus' id='inPorderMinus' value='<?=$dbPorderMinus?>'>
<?php
} // end while
?>
</div>
<?php if($listSize > 0) { // 페이지네이션은 $listSize가 0이 아닌 경우에만 표시 ?>
<div align="center" id="paging"><? echo page_nav($TotalCount,$listSize,$PagePerList,$page,$option); ?></div>
<?php } ?>
<script>
$(document).ready(function(){
    $('#ProductCount')[0].innerHTML = "<?=$TotalCount?>";
    $('.imgOver').append('<em></em>');
    $('.sum_box .box').mouseenter(function(){
        $(this).find('.imgOver em').show();
    });
    $('.sum_box .box').mouseleave(function(){
        $(this).find('.imgOver em').hide();
    });
});
</script>