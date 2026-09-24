<?php 
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
include_once('includes/function.php')
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Invoice Cancelation</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Invoice Cancelation</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Refunded Invoice</li>
                    </ol>
                </nav>
                <h2>Refunded Invoice</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>

        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                    <input id="searchInput" name="q" placeholder="Search by Name, Email, Mobile, Reg ID, Unique Sequence ID, Slip No. or Transaction ID..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">           
             </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
             <form name="frmSearch" method="post" action="refunded_invoice.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration" />
           <div class="filter_body">
                 <div>
                    <label>User Name:</label>
                  <input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
                </div>
                <div>
                    <label>Unique Sequence:</label>
                  <input type="text" name="src_access_key" id="src_access_key" 
										 style="text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
                </div>
                <div>
                    <label>Mobile No:</label>
                  <input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
                </div>
                <div>
                    <label>Email Id:</label>
                  <input type="text" name="src_email_id" id="src_email_id" 
										 style="text-transform:uppercase;" value="<?=$_REQUEST['src_email_id']?>" />
                </div>
                <div>
                    <label>Invoice No:</label>
                  <input type="text" name="src_invoice_no" id="src_invoice_no" 
										 style="text-transform:uppercase;" value="<?=$_REQUEST['src_invoice_no']?>" />
                </div>
                  <div>
                    <label>Slip No:</label>
                	<input type="text" name="src_slip_no" id="src_slip_no"
									 	 style="text-transform:uppercase;" value="<?=$_REQUEST['src_slip_no']?>" />
                </div>
                <div>
                    <label>Pay Mode:</label>
                    <select name="src_payment_mode" id="src_payment_mode">
                        <option value="">-- Select Payment Mode --</option>
                        <option value="Online" <?=(trim($_REQUEST['src_payment_mode']=="Online"))?'selected="selected"':''?>>Online PG</option>
                        <option value="Cash" <?=(trim($_REQUEST['src_payment_mode']=="Cash"))?'selected="selected"':''?>>Cash</option>
                        <option value="Card" <?=(trim($_REQUEST['src_payment_mode']=="Card"))?'selected="selected"':''?>>Card</option>
                        <option value="Cheque" <?=(trim($_REQUEST['src_payment_mode']=="Cheque"))?'selected="selected"':''?>>Cheque</option>
                        <option value="Draft" <?=(trim($_REQUEST['src_payment_mode']=="Draft"))?'selected="selected"':''?>>Draft</option>
                        <option value="NEFT" <?=(trim($_REQUEST['src_payment_mode']=="NEFT"))?'selected="selected"':''?>>NEFT/RTGS</option>
                        <option value="Credit" <?=(trim($_REQUEST['src_payment_mode']=="Credit"))?'selected="selected"':''?>>Exhibitor Credit</option>
                    </select>
                </div>
                 <div>
                    <label>Payment status:</label>
                       <select id="src_payment_status" name="src_payment_status">
                            <option value="">-- Select Payment Mode --</option>
                            <option value="PAID" <?=($_REQUEST['src_payment_status']=="PAID"?'selected="selected"':'')?> >Paid</option>
                            <option value="UNPAID" <?=($_REQUEST['src_payment_status']=="UNPAID"?'selected="selected"':'')?>>Unpaid</option>
                        </select>
                </div>
                <div>
                    <label>Start Date:</label>
                       <input type="date" name="dateFrom"  rel="tcal" value="<?=$_REQUEST['dateFrom']?>" />
                </div>
                <div>
                    <label>End Date:</label>
                       <input type="date" name="dateTo"  rel="tcal" value="<?=$_REQUEST['dateTo']?>" />
                </div>
               <div>
                    <label>Delegate Reg Mode:</label>
                      <select id="src_reg_mode" name="src_reg_mode" >
                            <option value="">-- Select Registration Mode --</option>
                            <option value="ONLINE" <?=($_REQUEST['src_reg_mode']=="ONLINE"?'selected="selected"':'')?> >Online</option>
                            <option value="OFFLINE" <?=($_REQUEST['src_reg_mode']=="OFFLINE"?'selected="selected"':'')?>>Offline</option>
                        </select>
                </div>
               <div>
                    <label>Workshop Category:</label>
                     <select name="src_workshop_category" id="src_workshop_category">
                            <option value="">-- Select Category --</option>
                            <?php
                            $sqlFetchClassification 			 =	array();
                            $sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`type` 
                                                                        FROM "._DB_WORKSHOP_CLASSIFICATION_." 
                                                                        WHERE status = ? 
                                                                        AND display= ?";
                            $sqlFetchClassification['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',                'TYP' => 's');
                            $sqlFetchClassification['PARAM'][]   = array('FILD' => 'display',         'DATA' =>'Y',                'TYP' => 's');										   
                            $resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
                            
                            if($resultClassification)
                            {
                                foreach($resultClassification as $key=>$rowClassification) 
                                {
                                ?>
                                    <option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_workshop_category']))?'selected="selected"':''?>>
                                        <?=$rowClassification['classification_title']?>	
                                    </option>
                                <?php
                                }
                            }
                            ?>
                        </select>
                </div>
                <div>
                    <label>Conf. Reg. Cat. :</label>
                     <select name="src_conf_reg_category" id="src_conf_reg_category" >
                        <option value="">-- Select Category --</option>
                        <?php
                        $sqlFetchClassification 			 = array();
                        $sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` 
                                                                    FROM "._DB_REGISTRATION_CLASSIFICATION_." 
                                                                    WHERE status = ?";
                        $sqlFetchClassification['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',                'TYP' => 's');										   
                        $resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
                        
                        if($resultClassification)
                        {
                            foreach($resultClassification as $key=>$rowClassification) 
                            {
                            ?>
                                <option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
                                <?
                                    if($rowClassification['type']=="DELEGATE")
                                    {
                                        echo "CONFERENCE - ".$rowClassification['classification_title'];
                                    }
                                    if($rowClassification['type']=="COMBO")
                                    {
                                        echo "Residential Registration - ".$rowClassification['classification_title'];
                                    }
                                ?>
                                </option>
                            <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="filter_bottom">
                <button onclick="clearFilters();" ><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
            </form>
        </div>
         <div class="spot_listing">
             <?php
                @$searchCondition       = "";								
                if( $_REQUEST['dateFrom']!='' || $_REQUEST['dateTo']!='' )
                {
                    $searchCondition   .= " AND invoice.invoice_date >= '".$_REQUEST['dateFrom']."'
                                            AND invoice.invoice_date <= '".$_REQUEST['dateTo']."'";
                }
                else if($_REQUEST['dateFrom']!='')
                {
                    $searchCondition   .= " AND invoice.invoice_date = '".$_REQUEST['dateFrom']."'";
                }
                else if($_REQUEST['dateTo']!='')
                {
                    $searchCondition   .= " AND invoice.invoice_date = '".$_REQUEST['dateFrom']."'";
                }																
                if($_REQUEST['src_slip_no']!='')
                {
                    $searchCondition   .= " AND slip.slip_number LIKE '%".$_REQUEST['src_slip_no']."%'";
                }								
                if($_REQUEST['src_payment_status']!='')
                {
                        $searchCondition   .= " AND invoice.payment_status = '".$_REQUEST['src_payment_status']."'";
                }
                if($_REQUEST['src_invoice_no']!='')
                {
                    $searchCondition   .= " AND invoice.invoice_number LIKE '%".$_REQUEST['src_invoice_no']."%'";
                }
                if($_REQUEST['src_conf_reg_category']!='')
                {
                    $sqlFetchClassification 			 = 	array();
                    $sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` 
                                                                FROM "._DB_REGISTRATION_CLASSIFICATION_." 
                                                            WHERE status = ?
                                                                AND `id` = ?";
                                                                
                    $sqlFetchClassification['PARAM'][]   = array('FILD' => 'status',  		 'DATA' =>'A', 								  'TYP' => 's');
                    $sqlFetchClassification['PARAM'][]   = array('FILD' => 'id',  			 'DATA' =>$_REQUEST['src_conf_reg_category'], 'TYP' => 's');										  
                    $resultClassification	 = $mycms->sql_select($sqlFetchClassification);
                    $rowClassification		 = $resultClassification[0];
                    if($rowClassification['type']=="DELEGATE")
                    {
                        $searchCondition   .= " AND invoiceUser.registration_classification_id = '".$_REQUEST['src_conf_reg_category']."'
                                                AND invoice.service_type = 'DELEGATE_CONFERENCE_REGISTRATION'";
                    }
                    if($rowClassification['type']=="COMBO")
                    {
                        $searchCondition   .= " AND invoiceUser.registration_classification_id = '".$_REQUEST['src_conf_reg_category']."'
                                                AND invoice.service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION'";
                    }
                }
                if($_REQUEST['src_workshop_category']!='')
                {
                    
                    $join['QUERY']   			= "INNER JOIN "._DB_REQUEST_WORKSHOP_." reqWorkshop
                                                        ON invoice.refference_id = reqWorkshop.id";
                                        
                    $searchCondition   .= " AND reqWorkshop.workshop_id = '".$_REQUEST['src_workshop_category']."'
                                            AND invoice.service_type = 'DELEGATE_WORKSHOP_REGISTRATION'";
                                                
                                                
                    
                }
                if($_REQUEST['src_user_first_name']!='')
                {
                    $searchCondition  .= " AND invoiceUser.user_full_name LIKE '%".$_REQUEST['src_user_first_name']."%'";
                }
                if($_REQUEST['src_access_key']!='')
                {
                    $searchCondition   .= " AND invoiceUser.user_unique_sequence LIKE '%".$_REQUEST['src_access_key']."%'";
                }
                if($_REQUEST['src_mobile_no']!='')
                {
                    $searchCondition   .= " AND invoiceUser.user_mobile_no LIKE '%".$_REQUEST['src_mobile_no']."%'";
                }
                if($_REQUEST['src_email_id']!='')
                {
                    $searchCondition  .= " AND invoiceUser.user_email_id LIKE '%".$_REQUEST['src_email_id']."%'";
                }
                if($_REQUEST['src_reg_mode']!='')
                {
                    $searchCondition   .= " AND invoiceUser.registration_mode LIKE '%".$_REQUEST['src_reg_mode']."%'";
                }
                if($_REQUEST['src_payment_mode']!='')
                {
                    $searchCondition   .= " AND slip.id IN (SELECT slip_id	 FROM " . _DB_PAYMENT_ . " WHERE status = 'A' AND payment_mode = '".$_REQUEST['src_payment_mode']."')";
                }
                if($_REQUEST['src_invoice_status']!='')
                {
                    if($_REQUEST['src_invoice_status']=='A')
                    {
                        $searchCondition .= " AND invoice.status = '".$_REQUEST['src_invoice_status']."'";
                    }
                    else
                    {
                        $searchCondition .= " AND invoice.status = 'C' AND cancl.Refund_status = '".$_REQUEST['src_invoice_status']."'";
                    }
                }	
                if($_REQUEST['src_cancel_invoice']!='')
                {
                    $searchCondition  .= " AND invoice.id IN (SELECT `invoice_id` FROM "._DB_INVOICE_CANCEL_REQUEST_FROM_PROFILE_.")";
                }							
              if (!empty($_GET['q'])) {
                $q = urldecode(trim($_GET['q']));
                $words = explode(' ', $q);

                $subConditions = [];

                foreach ($words as $word) {
                    $word = addslashes($word);

                    $subConditions[] = "(
                        slipUser.user_full_name LIKE '%$word%'
                        OR slipUser.user_email_id LIKE '%$word%'
                        OR slipUser.user_mobile_no LIKE '%$word%'
                        OR slipUser.user_registration_id LIKE '%$word%'
                        OR slipUser.user_unique_sequence LIKE '%$word%'
                        OR slip.slip_number LIKE '%$word%'
                        OR invoice.delegate_id IN (
                            SELECT delegate_id
                            FROM " . _DB_PAYMENT_ . "
                            WHERE status = 'A'
                            AND razorpay_transaction_id LIKE '%$word%'
                        )
                    )";
                }

                $searchCondition .= " AND (" . implode(' AND ', $subConditions) . ")";
            }
             $countertest =0;

                $sqlSlip['QUERY']  		 = "SELECT invoice.invoice_number AS invoiceNumber,
                                            invoice.service_roundoff_price AS amount,
                                            invoice.cgst_price AS cgst_price,
                                            invoice.sgst_price AS sgst_price,
                                            invoice.cgst_int_price AS cgst_int_price,
                                            invoice.sgst_int_price AS sgst_int_price,
                                            invoice.service_product_price AS serviceAmount,
                                            invoice.internet_handling_amount AS intCharge,
                                            invoice.invoice_mode AS invoice_mode,
                                            invoice.id AS invoiceId,
                                            invoice.delegate_id AS delegate_id,
                                            invoice.invoice_date AS invoiceDate,
                                            invoice.status AS invoiceStatus,
                                            invoice.remarks AS invoiceRemarks,	
                                            invoice.service_type AS invoiceFor,
                                            invoice.refference_id AS reqId,
                                            IFNULL(IFNULL(invoice.service_basic_price,service_unit_price),0.0) AS service_basic_price,
                                            IFNULL(IFNULL(invoice.service_basic_int_price,internet_handling_amount),0.0) AS service_basic_int_price,
                                            invoice.payment_status AS paymentStatus,
                                            slip.slip_number AS slipNumber,
                                            slip.id AS slipId,
                                            slipUser.user_full_name AS slipUserName,
                                            invoiceUser.user_full_name AS invoiceUserName,
                                            invoiceUser.user_registration_id AS user_registration_id,
                                            invoiceUser.user_unique_sequence AS user_unique_sequence,
                                            invoiceUser.user_email_id AS user_email_id,
                                            invoiceUser.user_mobile_no AS user_mobile_no,
                                            invoiceUser.status AS invoiceUserStatus,
                                            invoiceUser.registration_classification_id AS registrationClassificationId
                                                
                                            
                                        FROM "._DB_INVOICE_." invoice			  
                            
                                INNER JOIN "._DB_SLIP_." slip
                                        ON invoice.slip_id = slip.id
                                
                                
                                        
                                LEFT OUTER JOIN "._DB_CANCEL_INVOICE_." cancl
                                        ON cancl.invoice_id = invoice.id
                                                
                                INNER JOIN "._DB_USER_REGISTRATION_." invoiceUser
                                        ON invoice.delegate_id = invoiceUser.id
                                        
                                INNER JOIN "._DB_USER_REGISTRATION_." slipUser
                                        ON slip.delegate_id = slipUser.id
                                        ".$join."
                                        WHERE invoice.status IN ('C')
                                        AND slip.status  = 'A'
                                        AND invoiceUser.status = 'A'
                                        AND invoiceUser.registration_request IN ('GENERAL','SPOT')
                                            ".$searchCondition."
                                    ORDER BY invoice.id DESC";
                
                            
               $resSlip   = $mycms->sql_select_paginated('R001', $sqlSlip, 10);
                 $perPage = 10; // IMPORTANT: must match SQL LIMIT

                $pageIndex = isset($_GET['_pgnR001_'])
                    ? (int)$_GET['_pgnR001_']
                    : 0;

                $offset = $pageIndex * $perPage;
                 $counter = $offset;

                $recs = array();
                if($resSlip)
                {
                    foreach($resSlip as $i=>$invoiceDetails) 
                    {
                        $recs[$invoiceDetails['delegate_id']][$invoiceDetails['slipId']][] = $invoiceDetails;
                    }
                }
                else 
                {
                ?>
                    
                 <span class="mandatory">No Record Present.</span>												
                       
                <?php 
                }
                
                $icheck = '';
                $scheck = '';		
                foreach($recs as $ii=>$resSlip)
                {
                
                    foreach($resSlip as $si=>$slipWiseDetails)
                    {
                        $slipRowspan = sizeof($slipWiseDetails);
                        foreach($slipWiseDetails as $i=>$invoiceDetails) 
                        {
                            
                            if($invoiceDetails['invoiceFor']!='DELEGATE_CONFERENCE_REGISTRATION' && $invoiceDetails['invoiceFor']!='DELEGATE_RESIDENTIAL_REGISTRATION'){
                           //    echo "<pre>";
                           //    print_r($invoiceDetails);
                               $counter++;
                            $cancelInvoiceDetails = getCancelInvoiceDetailsInvoiceWise($invoiceDetails['invoiceId']);
                            $returnArray    = discountAmount($invoiceDetails['invoiceId']);
                            $percentage     = $returnArray['PERCENTAGE'];
                            $totalAmount    = $returnArray['TOTAL_AMOUNT'];
                            $basicAmount    = $returnArray['BASIC_AMOUNT'];
                            $cgstAmount     = $returnArray['CGST_AMOUNT'];
                            $sgstAmount     = $returnArray['SGST_AMOUNT'];
                            $discountAmount = $returnArray['DISCOUNT'];	
                            
                            $sqlCancelRequest = array();    
                            $sqlCancelRequest['QUERY']   = "SELECT * FROM "._DB_INVOICE_CANCEL_REQUEST_FROM_PROFILE_."
                                                                WHERE `delegate_id` = ? 
                                                                AND `invoice_id` = ?";
                                                                
                            $sqlCancelRequest['PARAM'][]   = array('FILD' => 'delegate_id',  'DATA' =>$invoiceDetails['delegate_id'],  'TYP' => 's');
                            $sqlCancelRequest['PARAM'][]   = array('FILD' => 'invoice_id',   'DATA' =>$invoiceDetails['invoiceId'],  'TYP' => 's');
                                                    
                            $resultCancelRequest    	= $mycms->sql_select($sqlCancelRequest);
                            $backgroundColor = '';
                            if($resultCancelRequest)
                            {
                                 
                                $backgroundColor = " style='background-color:antiquewhite;'";
                            }
                        ?>
                           
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span><?=$counter?></span>
                        </div>
                        <div>
                            <div class="regi_name"><?=$invoiceDetails['invoiceUserName']?></div>
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?><?=$invoiceDetails['user_mobile_no']?>
                                </span>
                                <span>
                                    <?php email(); ?><?=$invoiceDetails['user_email_id']?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Reg Number</h5>
                            <p><?=$invoiceDetails['user_registration_id']?></p>
                            <!-- <small>NATCON 2025-000334</small> -->
                        </div>
                        <div class="spot_details_box">
                            <h5>Sequence ID</h5>
                            <h6><?php qr(); ?><b><?=$invoiceDetails['user_unique_sequence']?></b></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Inv Details</h5>
                            <p><?=$invoiceDetails['invoiceNumber']?> </p>
                            <small><?php calendar() ?> <?=$invoiceDetails['invoiceDate']?></small>
                        </div>
                        <div class="spot_details_box">
                            <h5>Slip Details</h5>
                            <h6><?=$invoiceDetails['slipNumber']?></h6>
                        </div>
                        <!-- <div class="spot_details_box">
                            <h5>Inv Amount</h5>
                            <h6><i class="fal fa-calendar"></i>19/11/2025</h6>
                        </div>
                        <div class="spot_details_box align-items-end">
                            <h5>Action</h5>
                            <span class="mi-1 pay_status badge_padding badge_danger w-max-con text-uppercase"><?php close() ?>Close</span>
                        </div> -->
                    </div>
                </div>
                <div class="spot_box_bottom accm_bottom" >
                    <div class="spot_box_bottom_left">
                        <h6 class="badge_secondary">Invoice Amount: <n><?php rupee() ?> <?=$invoiceDetails['amount']?></n>
                        </h6>
                         <? if($invoiceDetails['paymentStatus']=='PAID'){ ?>
                        <h6 class="badge_success">Refund Amount: <n><?php rupee() ?> <?=$invoiceDetails['serviceAmount']?></n>
                        </h6>
                        <? }else{ ?>
                         <h6 class="badge_success">Refund Amount: <n><?php rupee() ?>0.00</n>

                        <? } ?>
                    </div>
                    <div class="spot_box_bottom_right ">
                
                        <a href="#" class="drp icon_hover badge_dark action-transparent"><i class="fal fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="spot_service_break accm_tariff">
                    <div class="service_breakdown_wrap mt-0">
                        <h4><i class="fal fa-window-maximize"></i>Service Breakdown</h4>
                        <div class="table_wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Service Name</th>
                                        <th>Reference</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?=$invoiceDetails['invoiceFor']?>
                                        </td>
                                        <td>
                                           <?=$invoiceDetails['invoiceNumber']?>
                                        </td>
                                        <td class="text-right">
                                            ₹ <?=$invoiceDetails['amount']?>
                                        </td>
                                        <td class="text-right">
                                            <? if($invoiceDetails['paymentStatus']=='PAID'){ ?>
                                            <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><i class="far fa-check-circle"></i><?=$invoiceDetails['paymentStatus']?></span>
                                           <? } else { ?>
                                            <span class="mi-1 badge_padding badge_danger  w-max-con text-uppercase"><i class="fal fa-times-circle"></i><?=$invoiceDetails['paymentStatus']?></span>

                                           <? } ?>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?
                            }
											
										}
									}
								} 
								?>
             <div class="bbp-pagination">
              <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo('R001') ?></div>
                <span class="paginationDisplay">
                    <div class="pagination"><a><?= $mycms->paginate('R001', 'pagination') ?></a></div>
                </span>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>

const searchInput = document.getElementById("searchInput");
let typingTimer;
const typingDelay = 800; // wait 0.8s after last keystroke

searchInput.addEventListener("keyup", function() {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        const query = this.value.trim();

        const url = new URL(window.location.href);

        if (query.length > 0) {
            url.searchParams.set('q', query);
        } else {
            url.searchParams.delete('q');
        }

        url.searchParams.delete('_pgnR001_'); // reset pagination
        window.location.href = url.toString(); // reload page with new query
    }, typingDelay);
});

searchInput.addEventListener("keydown", () => clearTimeout(typingTimer));
</script>
</html>
<script>
function clearFilters() {
    var form = document.forms['frmSearch'];

    // reset full form (best simple method)
    form.reset();

    // optional: force clear fields that reset() might miss
    var inputs = form.querySelectorAll("input, select");

    inputs.forEach(el => {
        if (el.type === "text" || el.type === "date") {
            el.value = "";
        }
        if (el.tagName === "SELECT") {
            el.selectedIndex = 0;
        }
    });
}
</script>