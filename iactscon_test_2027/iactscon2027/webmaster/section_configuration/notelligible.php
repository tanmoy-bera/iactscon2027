<?php 
	include_once('includes/init.php');
	page_header("Improper Access");
?>
	<div class='container'>		
		<?php
		webmaster_notElligible_display();
		?>
	</div>
<?php 
	page_footer();
?> 