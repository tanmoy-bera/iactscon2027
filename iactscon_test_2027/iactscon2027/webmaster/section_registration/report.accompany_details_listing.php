<?php
	include_once('includes/init.php');
	include_once('../../includes/function.accompany.php');
	include_once('../../includes/function.delegate.php');
	include_once("../../includes/function.invoice.php");
	include_once('../../includes/function.dinner.php');
	page_header("Accompany Report");
	
	$pageKey                       		            = "_pgn1_";
	$pageKeyVal                    		            = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString                 		            = "";
	$searchArray                   		            = array();
	
	$searchArray[$pageKey]                          = $pageKeyVal;
	$searchArray['src_delegates_full_name']         = trim($_REQUEST['src_delegates_full_name']);
	$searchArray['src_delegates_access_key']        = trim($_REQUEST['src_delegates_access_key']);
	$searchArray['src_delegates_mobile_no']         = trim($_REQUEST['src_delegates_mobile_no']);
	$searchArray['user_delegates_email_id']         = trim($_REQUEST['user_delegates_email_id']);
	$searchArray['src_accompany_full_name']         = trim($_REQUEST['src_accompany_full_name']);
	$searchArray['src_accompany_registration_id']   = trim($_REQUEST['src_accompany_registration_id']);
	$searchArray['src_from_date']                   = trim($_REQUEST['src_from_date']);
	$searchArray['src_to_date']                     = trim($_REQUEST['src_to_date']);
	
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
?>
	<div class="container">
		<?php 
		viewAllGeneralRegistration($cfg, $mycms);
		?>
	</div>
<?php

	page_footer();
	
	/****************************************************************************/
	/*                      SHOW ALL GENERAL REGISTRATION                       */
	/****************************************************************************/
	function viewAllGeneralRegistration($cfg, $mycms)
	{
	?>
		
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Accompany Details</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
						<?php /*?><div style="float:right; padding: 0px 15px 0px 0px;">
							<span style="float:right;">
							 <a href="download_excel_accompany_report.php"><img src="../images/Excel-icon.png"  width="20" height="20" /></a>
							</span>
						</div><?php */?>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;"> 
									
						<div class="tsearch" style="display:block;">
							<form name="frmSearch"  method="post">
							<table width="100%">
								<tr>
									<td align="left">Accompany Name:</td>
									<td align="left">
										<input type="text" name="src_accompany_full_name" id="src_accompany_full_name" 
										 style="width:90%;" value="<?=$_REQUEST['src_accompany_full_name']?>" />
									</td>
									<td align="left">Accompany Registration Id:</td>
									<td align="left">
										<input type="text" name="src_accompany_registration_id" id="src_accompany_registration_id" 
										 style="width:90%;" value="<?=$_REQUEST['src_accompany_registration_id']?>" />
									</td>
									<td align="right" rowspan="4">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr> 
									<td align="left">Registration Type:</td>
									<td align="left"> 
										<select name="src_registration_status" id="src_registration_status" style="width:96%;"> 
										<option value="">-- Select Type --</option>
										<option value="GENERAL" <?=$_REQUEST['src_registration_status']=="GENERAL"?'selected="selected"':''?>>-- GENERAL --</option>
										<option value="SPOT" <?=$_REQUEST['src_registration_status']=="SPOT"?'selected="selected"':''?>>-- SPOT --</option>
										</select>
									</td> 
									<td></td>
									<td></td>
								</tr>
							</table>
							</form>
						</div>
								
						<table width="100%">
							<tr class="theader">
								<td width="5%" align="left">Sl No</td>
								<td width="20%">Accompany Details</td>
								<td width="25%" align="left">Registration Id (Accompany)</td>
								<td width="15%" colspan="2">Accompany Payment Status</td>
								<td width="15%" align="right" >Registration Type</td>
								
							</tr>
							<?php
							@$searchCondition     = "";
							@$acompanySearchCondition = "";
							
							if(trim($_REQUEST['src_delegates_full_name'])!='')
							{
								$searchCondition .= " AND user_full_name LIKE '%".trim($_REQUEST['src_delegates_full_name'])."'";
							}
							if(trim($_REQUEST['src_delegates_access_key'])!='')
							{
								$searchCondition .= " AND user_unique_sequence LIKE '%".trim($_REQUEST['src_delegates_access_key'])."%'";
							}
							if(trim($_REQUEST['src_delegates_mobile_no'])!='')
							{
								$searchCondition .= " AND user_mobile_no LIKE '%".trim($_REQUEST['src_delegates_mobile_no'])."%'";
							}
							if(trim($_REQUEST['user_delegates_email_id'])!='')
							{
								$searchCondition .= " AND user__email_id LIKE '%".trim($_REQUEST['user_delegates_email_id'])."%'";
							}
							
							if(trim($_REQUEST['src_accompany_full_name'])!='')
							{
								$acompanySearchCondition .= " AND user_full_name LIKE '%".trim($_REQUEST['src_accompany_full_name'])."%'";
							}
							if(trim($_REQUEST['src_registration_status'])!='')
							{
								$acompanySearchCondition .= " AND operational_area LIKE '%".trim($_REQUEST['src_registration_status'])."%'";
							}
							if(trim($_REQUEST['src_accompany_registration_id'])!='')
							{
								$acompanySearchCondition .= " AND user_registration_id LIKE '%".trim($_REQUEST['src_accompany_registration_id'])."%'";
							}
							if(trim($_REQUEST['src_from_date'])!="" && trim($_REQUEST['src_to_date'])=="")
							{
								$searchCondition .= " AND grandTAB.created_dateTime BETWEEN '".trim($_REQUEST['src_from_date'])."'  AND '3000-01-01'";
							}
							if(trim($_REQUEST['src_from_date'])=="" && trim($_REQUEST['src_to_date'])!="")
							{
								$searchCondition .= " AND grandTAB.created_dateTime BETWEEN '2000-01-01'  AND '".trim($_REQUEST['src_to_date'])."'";
							}
							if(trim($_REQUEST['src_from_date'])!="" && trim($_REQUEST['src_to_date'])!="")
							{
								$searchCondition .= " AND (grandTAB.created_dateTime BETWEEN '".trim($_REQUEST['src_from_date'])."'  
								                                                         AND '".trim($_REQUEST['src_to_date'])."')";
							}
							
							$grandPriceWithTax        = 0;
							 $sqlFetchUserDetails	= array();
						   $sqlFetchUserDetails['QUERY']      = "SELECT * FROM "._DB_USER_REGISTRATION_." 
													
														 WHERE user_type = 'DELEGATE' 
														   AND status = 'A' ".$searchCondition."
														  ";
							$RESFetchUserDetails	= $mycms->sql_select($sqlFetchUserDetails);	
							$counter = 0;				   
													
							if($RESFetchUserDetails)
							{
								$delegateUniqueSequenceNo = '';
								
								$kidVarable=0;
								$teenageVaraible=0;
								$AdultVariable=0;
								$rowTotalAccompanyCount=0;
								
								foreach($RESFetchUserDetails as $i=>$rowFetchUser) 
								{
								 $counter++;
								 $showAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
								 
									$invoiceCounter                 = 0;
									if($showAccompanyCount!=0)
									{
										$sqlaccompany			= array();
										$sqlaccompany['QUERY'] = "SELECT * FROM "._DB_USER_REGISTRATION_." 
															WHERE user_type = 'ACCOMPANY' 
																	AND registration_request = 'GENERAL'
																	AND status = 'A' 
																	AND refference_delegate_id= '".$rowFetchUser['id']."' ".$acompanySearchCondition."";
		
											
										$RESFetchaccompany	= $mycms->sql_select($sqlaccompany);
										$rowFetchaccompany		= $RESFetchaccompany[0];
																			
										
										$grandPriceWithTax += $rowFetchUser['accompanyServiceRoundOffAmount'];
										
										// ACCOMPANY REGISTRATION PAYMENT STATUS
										$regPaymentStatus  = array();
										
										if($rowFetchUser['payment_status']=="ZERO_VALUE")
										{
											$regPaymentStatus['FONT.COLOR']            = "#5E8A26";
											$regPaymentStatus['INVOICE.STATUS']        = "ZERO VALUE";
											$regPaymentStatus['INVOICE.REQUEST.MODE']  = ""; 
											$regPaymentStatus['BANK.TRANSACTION.ID']   = "";      
										}
										else if($rowFetchUser['payment_status']=="COMPLEMENTARY")
										{
											$regPaymentStatus['FONT.COLOR']            = "#5E8A26";
											$regPaymentStatus['INVOICE.STATUS']        = "COMPLEMENTARY";
											$regPaymentStatus['INVOICE.REQUEST.MODE']  = ""; 
											$regPaymentStatus['BANK.TRANSACTION.ID']   = "";      
										}
										else if($rowFetchUser['payment_status']=="PAID")
										{
											$regPaymentStatus['FONT.COLOR']            = "#5E8A26";
											$regPaymentStatus['INVOICE.STATUS']        = "PAID";
											$regPaymentStatus['INVOICE.REQUEST.MODE']  = $rowFetchUser['invoice_mode']; 
											$regPaymentStatus['BANK.TRANSACTION.ID']   = $rowFetchUser['regInvoiceBankTransIds'];     
										}
										else
										{
											if($rowFetchUser['accompanyInvoiceDiscardCount']>0)
											{
												$regPaymentStatus['FONT.COLOR']               = "#C70505";
												$regPaymentStatus['INVOICE.STATUS']           = "DISCARD";
												$regPaymentStatus['INVOICE.REQUEST.MODE']     = ""; 
												$regPaymentStatus['BANK.TRANSACTION.ID']      = ""; 
											}
											else 
											{
												$regPaymentStatus['FONT.COLOR']               = "#C70505";
												$regPaymentStatus['INVOICE.STATUS']           = "UNPAID";
												$regPaymentStatus['INVOICE.REQUEST.MODE']     = ""; 
												$regPaymentStatus['BANK.TRANSACTION.ID']      = ""; 
											}  
										}
										if($rowFetchUser['id']==$rowFetchaccompany['refference_delegate_id'])
										{
								?>
										
										<tr style="background-color:#FF8080;">
											<td colspan="2">
												<h4 style="color: #fff;">
												<b>Delegate's Name :</b> <?=strtoupper($rowFetchUser['user_full_name'])?>
												</h4>		
											</td>
											<td valign="middle">
												<h4 style="color: #fff;">
												<b>Registration ID :</b>  <?=$rowFetchUser['user_registration_id']?>
												</h4>		
											</td>
											<td colspan="4"  valign="middle">
												<h4 style="color: #fff;">
												<b>Unique Sequence :</b> <?=strtoupper($rowFetchUser['user_unique_sequence'])?>
												</h4>		
											</td>
											
										</tr>
										<?
										}
										
										foreach($RESFetchaccompany as $key=>$rowFetchAccompany) 
										{
										if($rowFetchAccompany['user_age']>=1 && $rowFetchAccompany['user_age']<=12)
										{
										 $kidVarable++;
										}
										if($rowFetchAccompany['user_age']>12 && $rowFetchAccompany['user_age']<20)
										{
										$teenageVaraible++;
										}
										if($rowFetchAccompany['user_age']>20)
										{
										$AdultVariable ++;
										}
										$rowTotalAccompanyCount ++;
																				
										$invoiceCounter++;
										$invoiceCounterVal++; 
										$totalAmount = invoiceDetailsOfDelegate($rowFetchAccompany['refference_delegate_id'],$searchCondition.="AND service_type = 'ACCOMPANY_CONFERENCE_REGISTRATION'");
										
										$getTotalAmountAccompany = $totalAmount[0];
										?>
										<tr class="tlisting">
											<td align="left" valign="top"><?=$invoiceCounter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top" >
												<b>Name : </b><?=strtoupper($rowFetchAccompany['user_full_name'])?>
												<!--<br />
												<?
												if($rowFetchAccompany['user_age']!='')
												{
												?>
												<b>Age:</b> <?=strtoupper($rowFetchAccompany['user_age'])?>
												<?
												}
												else
												{
												?>
												<b>Age:</b>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--
												<?
												}
												?>-->
											</td>
											<td align="left" valign="top">
												
												<?php
												if($rowFetchAccompany['payment_status']!="UNPAID")
												{
													echo $rowFetchAccompany['user_registration_id'];	
												}
												else
												{
													echo "-";
												}
												?>
											</td>
											
											<td align="center" valign="top">
											<?
												if($rowFetchAccompany['registration_payment_status']=="UNPAID")
												{
												?>
												<span style="color:red;"><b><?=$rowFetchAccompany['registration_payment_status']?></b></span>
												
												<?	
												}
												else
												{
												?>
													<span style="color:green;"><b><?=$rowFetchAccompany['registration_payment_status']?></b></span>
												<?
												}
												?>
												
											</td>
											<td></td>
											<td  align="right" valign="top"><span style="color:chocolate;"><b><?=$rowFetchAccompany['operational_area']?></b></span></td>
										</tr>
										<?
										}
										?>
								<?php		
									}
								}
							} 
							else 
							{
							?>
								<tr>
									<td colspan="8" align="center">
										<span class="mandatory">No Record Present.</span>												
									</td>
								</tr>  
							<?php 
							} 
							?>
						</table>
					</td> 
				</tr>
				<tr class="tfooter">
					<td colspan="4" class="tfooter">&nbsp;
						<span style="float: left; color:red; font-size:14px">&nbsp;&nbsp;<b>Total Accompany Count&nbsp;-&nbsp;</b></span>
						<span style="float: left; "><b><?=($rowTotalAccompanyCount!=''?$rowTotalAccompanyCount:'NA')?></b></span>
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>
		</table>
	
	<?php
		
		}
	?>

