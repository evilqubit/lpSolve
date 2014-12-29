<?php
/**
* Read JSON File content using CURL
*
* @param string $json_file_path
* @return array|string array from JSON data or empty string
*/
function getJSONFile ($data){
	
	$output = '';
	$fp = '';
	
	$json_file_path = isset($data['path']) ? $data['path'] : '';
	$json_file_url = isset($data['url']) ? $data['url'] : '';
	
	// Open JSON File (create if not exists 'w')
	if (!file_exists($json_file_path)) {
		$fp = fopen( $json_file_path, 'w');
		fclose($fp);
		chmod ($json_file_path, 0777);
	}
	
	// prevent caching
	$json_file_path_no_cache = $json_file_url.'?_='.time();
	
	// Get JSON file contents
	$curl = curl_init();
	if (FALSE === $curl)
		throw new Exception('failed to initialize');
		
	curl_setopt ($curl, CURLOPT_URL, $json_file_path_no_cache);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

	$json_file_content = curl_exec($curl);

	if($json_file_content === false)
		return '';

	if ( isset ($json_file_content) && !empty ($json_file_content) ){
		// fetch JSON content as array
		$json_result = json_decode ($json_file_content, true);
		if ( isset ($json_result) ){
			$output = $json_result;
		}
	}
	curl_close($curl);
	
	return $output;
}