<?php

define("SERVICE_FILE_STORAGE_PATH", dirname(__FILE__) . '/../data/service.json');
function getStoredServiceData() {
	if( file_exists( SERVICE_FILE_STORAGE_PATH )) {
	  return json_decode(file_get_contents(SERVICE_FILE_STORAGE_PATH),true);
	} else {
	  return [];
	}
}

function putStoredServiceData($data) {
	if(file_put_contents(SERVICE_FILE_STORAGE_PATH, json_encode($data))) {
    return true;
  } else {
		return false;
	}
}

?>
