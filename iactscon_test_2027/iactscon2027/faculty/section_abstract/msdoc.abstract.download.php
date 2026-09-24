<?php
include_once('includes/init.php');
include_once('../../includes/function.abstract.php');
include_once('../../includes/function.delegate.php');

$operation				= addslashes(trim($_REQUEST['operation']));

$abstractId 			= addslashes(trim($_REQUEST['id']));		 
$sqlAbstractDetails		= abstractDetailsQuerySet($abstractId,'');

$resultAbstractDetails  = $mycms->sql_select($sqlAbstractDetails);
$rowAbstractDetails     = $resultAbstractDetails[0];

if($operation=="docdownload")
{
	header("Content-Description: File Transfer");
	header('Content-Disposition: attachment; filename="'.$rowAbstractDetails['tags'].'_'.$rowAbstractDetails['abstract_submition_code'].'.doc"');
	header("Content-type: application/vnd.ms-word;");// charset=Windows-1252
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Expires: 0');//
}
?>
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<head></head>
<body>
<font face="Arial, Helvetica, sans-serif">
<?
/// this is the erstwhile code kept for reference

abstractDetailsWindow($rowAbstractDetails);
?>
</font>
</body>
</html>
<script>
<?
if($operation=="print")
{
?>
setTimeout(function(){
				window.print();
		   },2000);
<?
}
?>
</script>
<?
	function abstractDetailsWindow($rowAbstractDetails)
	{
		global $cfg, $mycms;		
?>
		<table width="100%" class="tborder">			
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%">
						<tr>
							<td colspan="4" align="center" class="thighlight" style="font-size: 32px"><strong><?=ucwords($rowAbstractDetails['tags'])?></strong></td>
						</tr>
						<tr>
							<td colspan="4" align="left" class="thighlight" style="font-size: 24px"><strong>Basic Details</strong></td>
						</tr>
						<tr>
							<td width="20%" align="left"><b>Submission Code</b></td>
							<td width="30%" align="left"><?=strtoupper($rowAbstractDetails['abstract_submition_code'])?></td>
							<td width="20%" align="left"><b>Submission Date</b></td>
							<td width="30%" align="left"><?=setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D")?></td>
						</tr>
						<tr>
							<td align="left"><b>Submission Category</b></td>
							<td align="left"><?=$rowAbstractDetails['abstract_child_type']?></td>
							<td align="left"><b>Submission Type</b></td>
							<td align="left"><?=$rowAbstractDetails['abstract_parent_type']?></td>
						</tr>
					</table>
					<?
					if($rowAbstractDetails['abstract_parent_type']=='ABSTRACT')
					{
						$abstractContent = getAbstractProofContent($rowAbstractDetails['id'], '../../');
					?>
					<table width="100%">
						<tr>
							<td colspan="4" align="left" class="thighlight">
							<br/><br/>
							<h1>Details</h1>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top" width="20%"><strong>TOPIC</strong></td>
						</tr>						
						<tr>
							<td colspan="3" align="left" valign="top"><?=$rowAbstractDetails['abstract_topic']?></td>
						</tr>
						<tr>
							<td align="left" width="20%" valign="top"><strong>Title</strong></td>
						</tr>
						<tr>
							<td colspan="3" align="left" valign="top"><?=$abstractContent['TITLE']?></td>
						</tr>
						
						<?
						if($rowAbstractDetails['abstract_child_type'] == 'VIDEO')
						{
						?>
						<tr>
							<td  align="left" width="20%"><strong>Description</strong></td>
						</tr>
						<tr>
							<td colspan="3"  align="left"><?=nl2br($abstractContent['DESCRIPTION'])?></td>
						</tr>
						<tr>
							<td align="left" valign="top" width="20%"><strong>Video File</strong></td>
						</tr>
						<tr>
							<td colspan="3" align="left" valign="top">
								<a href="<?=_BASE_URL_.$cfg['FILES.ABSTRACT.REQUEST'].$rowAbstractDetails['abstract_video_file']?>" target="_blank" title="<?="Download ".$rowAbstractDetails['abstract_original_file_name']?>">
								<img src="images/invDnld.png"  style="width:20px;" /></a>
								&nbsp;<?=$rowAbstractDetails['abstract_original_file_name']?>
							</td>
						</tr>
						<!--<tr>
							<td  align="left" width="20%"><strong>Aims and Objectives</strong></td>
						</tr>
						<tr>
							<td colspan="3"  align="left"><?=$rowAbstractDetails['abstract_background_aims']?></td>
						</tr>-->
						<?
						}
						else
						{
						?>
						<tr>
							<td  align="left" width="20%"><strong>Background & Aim</strong></td>
						</tr>
						<tr>
							<td colspan="3"  align="left"><?=nl2br($abstractContent['BACKGROUND_N_AIM'])?></td>
						</tr>
						<tr>
							<td align="left" width="20%"><strong>Methods and Material</strong></td>
						</tr>
						<tr>
							<td colspan="3" align="left"><?=nl2br($abstractContent['MATERIAL_N_METHOD'])?></td>
						</tr>
						<tr>
							<td align="left" valign="top" width="20%"><strong>Results</strong></td>
						</tr>
						<tr>
							<td colspan="3" align="left" valign="top"><?=nl2br($abstractContent['RESULT'])?></td>
						</tr>						
						<tr>
							<td align="left" width="20%"><strong>Conclusion</strong></td>
						</tr>
						<tr>
							<td colspan="3" align="left" ><?=nl2br($abstractContent['CONCLUSION'])?></td>
						</tr>
						<?
						}
						?>
					</table>
					<?
					}
					elseif($rowAbstractDetails['abstract_parent_type']=='CASEREPORT')
					{
						$caseContent = getAbstractProofContent($rowAbstractDetails['id'], '../../');
					?>
					<table width="100%">
						<tr>
							<td colspan="4" align="left" class="thighlight">
							<h1>Details</h1>
							</td>
						</tr>
						<tr>
							<td  align="left" width="20%" valign="top"><strong>Title</strong></td>
						</tr>
						<tr>
							<td colspan="3" align="left" valign="top"><?=$caseContent['TITLE']?></td>
						</tr>
						
						<tr>
							<td  align="left" width="20%"><strong>Description</strong></td>
						</tr>
						<tr>
							<td colspan="3"  align="left"><?=nl2br($caseContent['DESCRIPTION'])?></td>
						</tr>
						<tr>
							<td  align="left" width="20%"><strong>Conclusion</strong></td>
						</tr>
						<tr>
							<td colspan="3"  align="left"><?=nl2br($caseContent['CONCLUSION'])?></td>
						</tr>
					</table>					
					<? 
					}
					?>
				</td>
			</tr>
		</table>
	<?php
	}
?>