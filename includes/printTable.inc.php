<?php
function printTable($data, $formMode = "setup") {
	// Print the headings
	if($formMode === "setup") {
		echo "<form method='post' action=''>";
	}
	echo "<table class='table'><thead><tr>";
		echo "<th>Name</th>";
		echo "<th>Type</th>";
		echo "<th>Length</th>";
	foreach($data['service_start_times'] as $service_start_time) {
		echo "<th>" . date("g:i a",$service_start_time) . "</th>";
	}
	if($formMode === "setup") {
		echo "<th>" . "<input onChange='toggleSelectAll()' id='selectAllItems' type='checkbox'";
		if(count($data['active_items']) === count($data['items'])) {
			echo " checked='checked' ";
		}
		echo "></th>";
	} elseif($formMode === "live") {
		echo "<th>";
		echo "Activate";
		echo "</th>";
	}
	echo "</tr></thead>";

	// Now print the table cells
	foreach($data['items'] as $item) {
		$is_active = in_array($item['id'], $data['active_items']);
		echo "<tr" . ($is_active ? " class='success' ":"") . ">";
		echo "<td>" . $item['title'] . "</td>";
		echo "<td>" . $item['type'] . "</td>";
		echo "<td>" . gmdate("i:s", intval($item['length']) ) . "</td>";
		foreach($item['startTimes'] as $start_time) {
			echo "<td>" . date("g:i:s a",$start_time) . "</td>";
		}
		if($formMode === "setup") {
			echo "<td>" . "<input class='itemCheckbox'" . ($is_active ? "checked='checked'" : "") . " type='checkbox' value='". $item['id'] . "' name='activeItems[]'>" . "</td>";
		} elseif($formMode === "live") {
			echo "<td>" . "<button class='btn btn-success' onClick='setActiveItem(\"". $item['id'] ."\")'>Active</button>" . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
	if($formMode === "setup") {
		echo "<input class='btn btn-success' type='submit' value='Save'>";
		echo "</form>";
	}
}
?>
