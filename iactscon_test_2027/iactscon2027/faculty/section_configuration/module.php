<?php
	include_once('includes/init.php');
	include_once('../../includes/function.module.php');
	
	page_header("Module");
	
	includeModuleJavascript($cfg, $mycms);
?>
	<div class="container">
	<?php 
		switch($show){
			// show insert window
			case 'add':	
				addModuleFormLayout($cfg, $mycms);
				break;	
			// show edit window	
			case 'edit':
				editModuleFormLayout($cfg, $mycms);
				break;						
			//show all record	
			default:	
				moduleListingDisplay($cfg, $mycms); 
				break;				
		} 
	?>
	</div>
<?php
	page_footer();
?>