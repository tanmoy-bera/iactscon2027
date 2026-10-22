<?php
	include_once('includes/init.php');
	include_once('../../includes/function.userProfile.php');
	page_header("User Profile");
?>
    <script language="javascript" src="scripts/CountryStateCityRetriver.js"></script>
	<div class="container">
		<?php 
			switch($show){						
				
				/***************************************************************************/
				/* Name:       viewUserProfile                                             */
				/* Location:   root/includes/function.userProfile.php                      */
				/* Parameters:                                                             */
				/***************************************************************************/
				default:	
				
					viewUserProfile($cfg, $mycms); 
					break;				
			} 
		?>
	</div>
<?php
	page_footer();
?>