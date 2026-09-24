<?php
	include_once('includes/init.php');
	switch($action){
		/*************************************************************/
		/*                    GENERATE STATE CONTROL                 */
		/*************************************************************/
		case'getStateControl':
		
			$countryValue            = $_REQUEST['countryValue'];
			$stateControl            = $_REQUEST['stateControl'];
			$statevalue              = $_REQUEST['stateValue'];
			$sequenceVal             = $_REQUEST['sequenceVal'];
			?>
			<p class="frm-head">State</p>
			<select name="<?=$stateControl?>" id="<?=$stateControl?>" style='width:97%;'  forType="state" sequence="<?=$sequenceVal?>" tabindex="12">
				<option value="">-- Select State --</option>
				<?php		
					getSateList($countryValue,$statevalue);
				?>
			</select>
			<?php
			exit();
			break;
		
		case'getBlankStateControl':
		
			$stateControl            = $_REQUEST['stateControl'];			
			$sequenceVal             = $_REQUEST['sequenceVal'];
			?>
						<p class="frm-head">State</p>

			<select name="<?=$stateControl?>" id="<?=$stateControl?>" style='width:97%;'  forType="state" sequence="<?=$sequenceVal?>">
				<option value="">-- Select State --</option>				
			</select>
			<?php
			exit();
			break;
	}
?>


