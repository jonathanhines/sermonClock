<?php
function printTable($data, $includeForm = false) {
	// Print the headings
	if($includeForm) {
		echo "<form method='post' action=''>";
	}
	echo "<table class='table'><tr>";
		echo "<th>Name</th>";
		echo "<th>Type</th>";
		echo "<th>Length</th>";
	foreach($data['service_start_times'] as $service_start_time) {
		echo "<th>" . date("g:i a",$service_start_time) . "</th>";
	}
	if($includeForm) {
		echo "<th>" . "<input onChange='toggleSelectAll()' id='selectAllItems' type='checkbox'";
		if(count($data['active_items']) === count($data['items'])) {
			echo " checked='checked' ";
		}
		echo ">";
	}
	echo "</tr>";

	// Now print the table cells
	foreach($data['items'] as $item) {
		echo "<tr>";
		echo "<td>" . $item['title'] . "</td>";
		echo "<td>" . $item['type'] . "</td>";
		echo "<td>" . gmdate("i:s", intval($item['length']) ) . "</td>";
		foreach($item['startTimes'] as $start_time) {
			echo "<td>" . date("g:i:s a",$start_time) . "</td>";
		}
		if($includeForm) {
			echo "<td>" . "<input class='itemCheckbox'" . (in_array($item['id'], $data['active_items']) ? "checked='checked'" : "") . " type='checkbox' value='". $item['id'] . "' name='activeItems[]'>" . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
	if($includeForm) {
		echo "<input class='btn btn-success' type='submit' value='Save'>";
		echo "</form>";
	}
}
?>
