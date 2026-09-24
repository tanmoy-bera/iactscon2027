<?php
include_once('includes/frontend.init.php');

function ajax_upload_file() {
	global $cfg, $mycms;
	 $file_path   = $cfg['FILES.TEMP']. $_POST['file']; 
	$file_data   = decode_chunk( $_POST['file_data'] );
	
	if ($file_data == false ) {
		echo '{"status":"failure"}';
	}
	
	file_put_contents( $file_path, $file_data, FILE_APPEND );
	
	echo '{"status":"success"}';
}



function decode_chunk( $data ) {
	global $cfg,$mycms;
	$data = explode( ';base64,', $data );
	
	if ( ! is_array( $data ) || ! isset( $data[1] ) ) {
		return false;
	}
	
	$data = base64_decode( $data[1] );
	if ( ! $data ) {
		return false;
	}
	
	return $data;
}

ajax_upload_file();
?>