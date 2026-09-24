<?php
	include_once('includes/init.php');
	include_once('../../includes/function.section.php');
	
	page_header("Sections");
	
	$pageKey          = "_pgn_";
	$pageKeyVal       = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString    = "";
	$searchArray      = array();
	
	$searchArray[$pageKey]                   = $pageKeyVal;
	$searchArray['src_filter_text']          = trim($_REQUEST['src_filter_text']);
	$searchArray['src_filter_selection']     = trim($_REQUEST['src_filter_selection']);
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
	
	includeSectionJavascript($cfg, $mycms);
?>
	<div class="container">
		<?php 
		switch($show){
		
			/*******************************************************/
			/*                 SECTION INSERT WINDOW               */
			/*******************************************************/
			case'add':
				
				sectionAddFormLayout($cfg, $mycms);
				break;	
				
			/*******************************************************/
			/*                  SECTION EDIT WINDOW                */
			/*******************************************************/
			case'edit':
			
				sectionEditFormLayout($cfg, $mycms);
				break;
									
			/*******************************************************/
			/*               ALL SECTION LISTING WINDOW            */
			/*******************************************************/
			default:
				
				sectionListingDisplay($cfg, $mycms); 
				break;				
		
		} 
		?>
	</div>
<?php
	page_footer();
?>