<?php
	include_once('includes/init.php');
	include_once('../../includes/function.pages.php');
	
	page_header("Pages");
	
	includePageJavascript($cfg, $mycms);
?>	
	<div class="container">
	<?php 
		switch($show){
			// show insert window
			case 'add':	
				addPage($cfg, $mycms);
				break;	
			// show edit window	
			case 'edit':
				editPage($cfg, $mycms);
				break;						
			//show all record	
			default:	
				webPageListingDisplay($cfg, $mycms); 
				break;				
		} 
	?>
	</div>
<?php
	page_footer();
?>