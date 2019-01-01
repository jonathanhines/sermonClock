<?php

define("TIME_OFFSET_FILE_STORAGE_PATH", dirname(__FILE__) . '/../data/last-offset.json');
function getStoredOffsetData() {
	if( file_exists( TIME_OFFSET_FILE_STORAGE_PATH )) {
	  return json_decode(file_get_contents(TIME_OFFSET_FILE_STORAGE_PATH),true);
	} else {
		if(defined("DEFAULT_TIME_OFFSET")) {
			return DEFAULT_TIME_OFFSET; 
		}
		return 35;
	}
}

function putStoredOffsetData($data) {
	if(file_put_contents(TIME_OFFSET_FILE_STORAGE_PATH, json_encode($data))) {
    return true;
  } else {
		return false;
	}
}