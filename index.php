<?php
	include "class.diff.php";
	$diff = new banco_diff("localhost","root","","astoria");

	$diff->check_table();
	$diff->public_json();

	$diff->db_diff("localhost","root","","astoria");

?>