<?php

function getInvoice($Id)
	{
		global $cfg, $mycms;
			$sqlInvoice['QUERY'] = "SELECT * 
									  FROM  "._DB_SLIP_." 
		                              WHERE  id = '".$Id."' AND status = 'A'";
		$resInvoice = $mycms->sql_select($sqlInvoice);
		return $resInvoice[0];
	}
 
?>