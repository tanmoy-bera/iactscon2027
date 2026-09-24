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
$loggedUserID = $mycms->getLoggedUserId();

$indexVal          = 1;
$pageKey           = "_pgn".$indexVal."_";

$pageKeyVal        = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];

@$searchString     = "";
$searchArray       = array();

$searchArray[$pageKey]                     = $pageKeyVal;	

foreach($searchArray as $searchKey=>$searchVal)
{
    if($searchVal!="")
    {
        $searchString .= "&".$searchKey."=".$searchVal;
    }
}
	
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Workshop Report</h2>
       <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Workshop</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Workshop Report</li>
                    </ol>
                </nav>
                <h2>Breakfast Workshops Overall Report</h2>
                <h6>View attendance and payment details for all workshop sessions.</h6>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <!-- <div class="regi_search">
                <?php search(); ?>
                <input placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div> -->
            <div class="regi_search_wrap_btn_box">
                <!-- <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a> -->
                <a href="download.excel.paid_workshop.php"><?php export(); ?>Export paid excel</a>
                <a  href="download.excel.unpaid_workshop.php"><?php export(); ?>Export unpaid excel</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <div class="filter_body">
                <div>
                    <label>Registration Type</label>
                    <select>
                        <option>All Types</option>
                    </select>
                </div>
                <div>
                    <label>Payment Status</label>
                    <select>
                        <option>All Status</option>
                    </select>
                </div>
                <div>
                    <label>Registration Date</label>
                    <input type="date">
                </div>
            </div>
            <div class="filter_bottom">
                <button><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
        </div>

        <div class="table_wrap">
            <table>
                <thead>
                    <tr>
                        <th class="sl">Sl No</th>
                        <th>Breakfast Session Category</th>
                        <th>Workshop Date</th>
                        <th>Seat Limit</th>
                        <th>Total Applicant(s)</th>
                        <th>Total Paid Delegate(s)</th>
                        <th>Total Zero Val. Delegate(s)</th>
                        <th>Total Unpaid Delegate(s)</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
         		<?php
        $searchCondition               = "";
        
        $workshopCounter               = 0;
        
        
        $sqlFetchWorkshopDetails  	   = WorkshopReportQuery();
                                                                    
        $resultWorkshopDetails         = $mycms->sql_select_paginated (1, $sqlFetchWorkshopDetails, 25, $restrt);	
        
        //echo '<pre>'; print_r($sqlFetchWorkshopDetails);
        if($resultWorkshopDetails)
        {
            foreach($resultWorkshopDetails as $key=>$rowWorkshopDetails) 
            {
                
                if($rowWorkshopDetails['id'])
                {
                
                    $workshopCounter++;
            ?>
                    <tr class="tlisting">
                        <td align="center" valign="top"><?=$workshopCounter + ($_REQUEST['_pgn1_']*25)?></td>
                        <td align="left" valign="top"><?=$rowWorkshopDetails['classification_title']?></td>
                        <td align="left" valign="top"><?=date('d M Y',strtotime($rowWorkshopDetails['workshop_date'])); ?></td>
                        <td align="center" valign="top"><?=$rowWorkshopDetails['seat_limit']?></td>
                        <td align="center" valign="top"><?=$rowWorkshopDetails['applied_for']?> </td>
                        <td align="center" valign="top"><?=$rowWorkshopDetails['paid_For']?> </td>
                        <td align="center" valign="top"><?=$rowWorkshopDetails['zerovalue_pay']?> </td>
                        <td align="center" valign="top"><?=$rowWorkshopDetails['notPaid_For']?> </td>
                        <td class="action">
                            <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a href="workshop_report_view.php?workshopId=<?=$rowWorkshopDetails['id']?>" data-tab="profile" class="icon_hover badge_primary action-transparent"><?php view(); ?>View</a>
                                </ul>
                            </div>
                        </td>
                    </tr>
										
                        <?php
                            }
                        }
                    }
                    else
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
       
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>