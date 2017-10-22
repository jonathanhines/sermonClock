<?php

define("STATE_FILE_STORAGE_PATH", dirname(__FILE__) . '/../data/state.json');
function getStoredStateData() {
	if( file_exists( STATE_FILE_STORAGE_PATH )) {
	  return json_decode(file_get_contents(STATE_FILE_STORAGE_PATH),true);
	} else {
	  return [];
	}
}

function putStoredStateData($data) {
	if(file_put_contents(STATE_FILE_STORAGE_PATH, json_encode($data))) {
    return true;
  } else {
		return false;
	}
}

?>
