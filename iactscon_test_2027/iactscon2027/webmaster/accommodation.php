<?php 
include_once("includes/source.php");
include_once('includes/init.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
// $cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>General Registration</h2>
       <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Accomdation</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Booking Manager</li>
                    </ol>
                </nav>
                <h2>Hotel & Room Allotment</h2>
                <h6>Manage hotel inventories and delegate room assignments.</h6>
            </div>
            

        </div>
        <ul class="regi_data_grid_ul accm_data_grid_ul mb-3">
             <?
              $sqlFetchHotel = array();
              $sqlFetchHotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . " WHERE `status` = ?";
              $sqlFetchHotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
              $resultFetchHotel = $mycms->sql_select($sqlFetchHotel);
                if (count($resultFetchHotel)) {
                foreach ($resultFetchHotel as $k => $val) {
                    $sqlRoomLimit = array();
                    $sqlRoomLimit['QUERY'] = "SELECT CAST(seat_limit AS DECIMAL(10,2)) as seat_limit 
                                            FROM `rcg_accommodation_seat_limit`
                                            WHERE hotel_id = ? 
                                            AND status = 'A'
                                            ";
                    
                    $sqlRoomLimit['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 'i');
                    
                    $resultRoom = $mycms->sql_select($sqlRoomLimit);
                    
                    if (!empty($resultRoom) && $resultRoom[0]['seat_limit'] > 0) {
                        $totalLimit = (float)$resultRoom[0]['seat_limit'];
                    } else {
                        // Fallback to hotel-level limit
                        $sqlHotelLimit = array();
                        $sqlHotelLimit['QUERY'] = "SELECT CAST(seat_limit AS DECIMAL(10,2)) as seat_limit 
                                                FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
                                                WHERE hotel_id = ? 
                                                LIMIT 1";
                        
                        $sqlHotelLimit['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $val['id'], 'TYP' => 'i');
                        
                        $resultHotel = $mycms->sql_select($sqlHotelLimit);
                        $totalLimit = !empty($resultHotel) ? (float)$resultHotel[0]['seat_limit'] : 0;
                    }

                    $sqlBooked = array();
                    $sqlBooked['QUERY'] = "SELECT COALESCE(SUM(CAST(booking_quantity AS DECIMAL(10,2))), 0) as total_booked
                                        FROM " . _DB_REQUEST_ACCOMMODATION_ . "
                                        WHERE hotel_id = '".$val['id']."'
                                        AND status IN ('A', 'P')
                                        ";
                    
                    
                    $result = $mycms->sql_select($sqlBooked);
                    // FIX: Return as float, not int
                    $bookedSeats = !empty($result) ? (float)$result[0]['total_booked'] : 0;
                    
                    // Step 3: Calculate available seats (as float)
                    $available = $totalLimit - $bookedSeats;
                    
                    // Return as float with 2 decimal places
                   $availableSeat = round(max(0, $available), 2);
                   if ($totalLimit > 0) {
                        $occupancyPercentage = round(($bookedSeats / $totalLimit) * 100);
                    } else {
                        $occupancyPercentage = 0;
                    }
              ?>
            <li>
                <span class="badge_primary"><?php hotel(); ?></span>
                <g><?= $val['hotelRatings'] ?> Star</g>
                <div>
                    <h4><?= $val['hotel_name'] ?></h4>
                    <h6 class="text-muted"><?= $val['hotel_address'] ?></h6>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-value">Occupancy<n><?=$occupancyPercentage?>%</n>
                    </div>
                    <div class="progress">
                        <div class="progress-done bg_primary" data-progress="<?=$occupancyPercentage?>"></div>
                    </div>
                </div>
                <ol>
                    <li><i>Total</i><?=$totalLimit?></li>
                    <li><i>Booked</i><?=$bookedSeats?></li>
                    <li style="color: var(--success2);"><i>Avail</i><?=$availableSeat?></li>
                </ol>
            </li>
            <? } } ?>
           
        </ul>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                  <input id="searchInput" name="q" placeholder="Search by  Name , Registration Id or Dates..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <a href="checkin.checkout.time.excel.php"><?php export(); ?>Export</a>
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newbooking"><?php add(); ?>New Booking</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
             <form method="post" name="frmSearch">
            <div class="filter_body">
                <div class="filter_div">
                    <label>User Name</label>
                    <input type="text"  name="user_full_name" id="user_full_name" value="<?= $_REQUEST['user_full_name'] ?>">
                </div>
                <div class="filter_div">
                    <label>Hotel Name</label>
                    <?php
                     $sql_hotel = array();
                    $sql_hotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . "
                                        WHERE `status` = ? ORDER BY `id` ASC";
                    $sql_hotel['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
                    $row_hotel = $mycms->sql_select($sql_hotel);
                    ?>
                    <select name="id" id="id">
                         <option value="">Select Hotel</option>
                        <?  foreach ($row_hotel as $H => $rowhotel) { ?>
                        <option value="<?=$rowhotel['id']?>" <?= ($rowhotel['id'] == trim($_REQUEST['id'])) ? 'selected="selected"' : '' ?>><?=$rowhotel['hotel_name']?></option>
                        <? } ?>
                    </select>
                </div>
                <div class="filter_div">
                    <label>Room Type</label>
                    <?php
                    $sqlRoom = array();
                    $sqlRoom['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  
                                        WHERE status='A' AND purpose='room' ORDER BY `id` ASC";
                    $queryRoom = $mycms->sql_select($sqlRoom, false);
                    ?>
                    <select  name="roomTypeId" id="roomTypeId">
                        <option value="">Select Room Type</option>
                        <?  foreach ($queryRoom as $k => $rowRoom) { ?>
                        <option value="<?=$rowRoom['id']?>"  <?= ($rowRoom['id'] == trim($_REQUEST['roomTypeId'])) ? 'selected="selected"' : '' ?>><?=$rowRoom['accessories_name']?></option>
                        <? } ?>
                    </select>
                </div>
                <div class="filter_div">
                    <label>Pakcage Name</label>
                    <select  name="package_id" id="package_id">
                        <option value="">Select Package</option>
                        <option value="Individual" <?= (trim($_REQUEST['roomTypeId'])=='Individual') ? 'selected="selected"' : '' ?>>Individual</option>
                        <option value="Sharing" <?= (trim($_REQUEST['roomTypeId'])=='Sharing') ? 'selected="selected"' : '' ?>>Sharing</option>
                    </select>
                </div>
                <div class="filter_div">
                    <label>Payment Status</label>
                    <select  name="payment_status" id="payment_status">
                        <option value="">Select Package</option>
                        <option value="Paid" <?= (trim($_REQUEST['payment_status'])=='Paid') ? 'selected="selected"' : '' ?>>Paid</option>
                        <option value="Unpaid" <?= (trim($_REQUEST['payment_status'])=='Unpaid') ? 'selected="selected"' : '' ?>>Unpaid</option>
                    </select>
                </div>
                <div class="filter_div">
                    <label>Check In date</label>
                    <input type="date" name="checkin_date" id="checkin_date" value="<?= $_REQUEST['checkin_date'] ?>">
                </div>
                <div class="filter_div">
                    <label>Check Out date</label>
                    <input type="date" name="checkout_date" id="checkout_date" value="<?= $_REQUEST['checkout_date'] ?>">
                </div>
            </div>
            <div class="filter_bottom span_4">
                <button onclick="clearFilters();"><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
          </form>
        </div>

        <div class="table_wrap">
            <table>
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Hotel & Room</th>
                        <th>Stay Dates</th>
                        <th>Occupancy</th>
                        <th class="action">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php		
                    	@$searchCondition       = "";		
                        if($_REQUEST['id']!="")
                            {
                                $searchCondition      .= " AND req.hotel_id = '".$_REQUEST['id']."'";
                            }
                         if($_REQUEST['user_full_name']!="")
                            {
                                $searchCondition      .= " AND delegate.user_full_name LIKE '%".$_REQUEST['user_full_name']."%'";
                            }	
                        if($_REQUEST['payment_status']!="")
                            {
                                $searchCondition      .= " AND inv.payment_status = '".$_REQUEST['payment_status']."'";
                            }	
                         if($_REQUEST['checkin_date']!="")
                            {
                                $searchCondition      .= " AND req.checkin_date = '".$_REQUEST['checkin_date']."'";
                            }	
                          if($_REQUEST['checkout_date']!="")
                            {
                                $searchCondition      .= " AND req.checkout_date = '".$_REQUEST['checkout_date']."'";
                            }	
                          if($_REQUEST['roomTypeId']!="")
                            {
                                $searchCondition      .= " AND req.roomTypeId = '".$_REQUEST['roomTypeId']."'";
                            }
                         if($_REQUEST['package_id']!="")
                            {
                                $searchCondition      .= " AND pckg.package_name LIKE '%".$_REQUEST['package_id']."%'";
                            }	
						if (!empty($_GET['q'])) {

                            $q = trim($_GET['q']);
                            $words = preg_split('/\s+/', $q);

                            $wordConditions = [];

                            foreach ($words as $word) {

                                $word = addslashes($word); // you can later upgrade to prepared

                                $wordConditions[] = "(
                                    delegate.user_full_name LIKE '%$word%' 
                                    OR delegate.user_registration_id LIKE '%$word%' 
                                    OR inv.payment_status LIKE '%$word%' 
                                    OR req.booking_quantity LIKE '%$word%'
                                     OR req.checkin_date LIKE '%$word%'
                                     OR req.checkout_date LIKE '%$word%'

                                )";
                            }

                            // Each word must match somewhere
                            $searchCondition .= " AND (" . implode(" AND ", $wordConditions) . ")";
                        }
						$delegateCounter              = 0;
						 $sqlFetchWorkshopBooking = array();
						 $sqlFetchWorkshopBooking['QUERY'] 	  = "SELECT req.*, delegate.user_full_name, delegate.user_unique_sequence, 
																   delegate.user_registration_id, delegate.user_mobile_no, delegate.user_email_id,pckg.package_name,inv.payment_status
																    FROM "._DB_REQUEST_ACCOMMODATION_." req
																	LEFT OUTER JOIN "._DB_ACCOMMODATION_PACKAGE_." pckg
																		ON pckg.id = req.package_id
																	INNER JOIN "._DB_INVOICE_." inv 
																		ON req.refference_invoice_id = inv.id
																	INNER JOIN "._DB_USER_REGISTRATION_." delegate
																		ON delegate.id = req.user_id
																		WHERE delegate.status = 'A'
																			 AND inv.status = 'A'
																			 AND inv.service_type IN ('DELEGATE_ACCOMMODATION_REQUEST','DELEGATE_RESIDENTIAL_REGISTRATION') 
                                                                             ".$searchCondition."  ORDER BY req.checkin_date";
					
                        $resultWorkshopBooking        =  $mycms->sql_select_paginated('R001',$sqlFetchWorkshopBooking,50);
                       $perPage = 50; // IMPORTANT: must match SQL LIMIT

                        $pageIndex = isset($_GET['_pgnR001_'])
                            ? (int)$_GET['_pgnR001_']
                            : 0;

                        $offset = $pageIndex * $perPage;
						$counter = 0;
                        if($resultWorkshopBooking)
                        {
                            $delegateCounter=1;
                            foreach($resultWorkshopBooking as $key=>$rowfetch)
                            {
                                   $countertest = $offset + $key + 1;
								 $counter      = $countertest;
                                    $sqlRoom = array();
                                    $sqlRoom['QUERY'] = "SELECT `accessories_name` FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
                                                        WHERE `id` = ? AND status='A' AND purpose='room' ORDER BY `id` ASC";
                                    $sqlRoom['PARAM'][] = array('FILD' => 'id', 'DATA' => $rowfetch['roomTypeId'], 'TYP' => 's');
                                     $querySlider = $mycms->sql_select($sqlRoom, false);
                        ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-start">
                                <div class="regi_img_circle">
                                    <!-- <img src="" alt="" class="w-100 h-100"> -->
                                    <span><?=$counter?></span>
                                </div>
                                <div>
                                    <div class="regi_name"><?=strtoupper($rowfetch['user_full_name'])?></div>
                                    <div class="regi_contact mt-0">
                                        <span><?=strtoupper($rowfetch['user_registration_id'])?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-start">
                                <div>
                                    <div class="regi_name"><?=getHotelNameByID($rowfetch['hotel_id'])?></div>
                                    <div class="regi_contact mt-0">
                                        <? if($rowfetch['roomTypeId']!="" && $rowfetch['roomTypeId']!='0' ){?>
                                        <span><? hotel(); ?></i><?=$querySlider[0]['accessories_name']?></span>
                                        <? } ?>
                                         <? if($rowfetch['package_id']!="" && $rowfetch['package_id']!='0' ){?>
                                        <span><i class="fal fa-bed-alt"></i><?=$rowfetch['package_name']?></span>
                                         <? } ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php calendar(); ?>
                            <n class="badge_padding badge_dark mx-1"><?=strtoupper($rowfetch['checkin_date'])?></n>
                            <i class="fal fa-long-arrow-right"></i>
                            <n class="badge_padding badge_dark ml-1"><?=strtoupper($rowfetch['checkout_date'])?></n>
                        </td>

                        <td>
                            Adult: <?=$rowfetch['booking_quantity']?><br>
                            <small class="text_primary">In Any</small>
                        </td>

                        <td class="action">
                            <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                <? if(strtoupper($rowfetch['payment_status']) =='PAID'){
                                ?>
                                <span class="badge_padding badge_success w-max-con text-uppercase">Confirmed</span>
                                <? }else{?>
                                 <span style="color: #ffd34d !important;" class="badge_padding badge_success w-max-con text-uppercase">Pending</span>

                               <? } ?>
                            </div>
                        </td>
                    </tr>
                    <? $delegateCounter++; }  } else
                        {
                        ?>
                            <tr>
                                <td colspan="7" align="center">
                                    <span class="mandatory">No Record(s) Found !!</span>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
            
                </tbody>
            </table>
        </div>
         <div class="bbp-pagination">
            <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo('R001') ?></div>
            <span class="paginationDisplay">
                <div class="pagination"><a><?= $mycms->paginate('R001', 'pagination') ?></a></div>
            </span>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>
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

function clearFilters() {
    // Get the form
    var form = document.forms['frmSearch'];

    // Clear selects
    form.user_full_name.value = "";
    form.id.value = "";
    form.roomTypeId.value = "";
    form.package_id.value = "";
    form.payment_status.value = "";
    form.checkin_date.value = "";
    form.checkout_date.value = "";
  
    // Clear date input
    form.querySelector('input[type="date"]').value = "";

    // Optional: clear hidden act value (if you want a clean GET)
    // form.act.value = "";

    // Submit the form to PHP with empty values
    form.submit();
}
</script>