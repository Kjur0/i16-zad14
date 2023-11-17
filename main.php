<?php

const host = "localhost";
const user = "ODBC"; #user
const pass = ""; #password
const db = "komis";

global $mysqli;
$mysqli = mysqli_connect(host, user, pass, db);

if (!$mysqli || mysqli_errno($mysqli))
	error_log("database error " . mysqli_errno($mysqli) . ": " . mysqli_error($mysqli));

function query($query)
{
	global $mysqli;
	$result = mysqli_query($mysqli, $query);
	if (mysqli_errno($mysqli))
		error_log("database error " . mysqli_errno($mysqli) . ": " . mysqli_error($mysqli));
	return $result;
}

function mysqli_echo($result)
{
	echo "<table>";
	while ($row = mysqli_fetch_row($result)) {
		echo "<tr>";
		foreach ($row as $value)
			echo "<td>$value</td>";
		echo "</tr>";
	}
	echo "</table>";
}

function mysqli_echo_table($table)
{
	global $mysqli;

	$cols = mysqli_query($mysqli, "SHOW COLUMNS FROM $table");

	echo <<<HTML
			<table>
				<caption>
					$table
				</caption>
				<tr>
		HTML;
	while ($col = mysqli_fetch_row($cols))
		echo "<th>$col[0]</th>";

	echo "</tr>";

	$rows = mysqli_query($mysqli, "SELECT * FROM $table");

	while ($row = mysqli_fetch_row($rows)) {
		echo "<tr>";
		foreach ($row as $value)
			echo "<td>$value</td>";
		echo "</tr>";
	}

	echo "</table>";
}

function mysqli_echo_query($query, $table)
{
	$cols = query("SHOW COLUMNS FROM $table");

	echo <<<HTML
			<table>
				<caption>
					$table
				</caption>
				<tr>
		HTML;
	while ($col = mysqli_fetch_row($cols))
		echo "<th>$col[0]</th>";

	echo "</tr>";

	$rows = query($query);

	while ($row = mysqli_fetch_row($rows)) {
		echo "<tr>";
		foreach ($row as $value)
			echo "<td>$value</td>";
		echo "</tr>";
	}

	echo "</table>";
}

function mysqli_echo_all_tables()
{
	global $mysqli;
	$tables = mysqli_query($mysqli, "SHOW TABLES");
	while ($table = mysqli_fetch_array($tables)) {
		echo "<hr>";
		mysqli_echo_table($table[0]);
	}
}

function uniquePost($posted)
{
	// take some form values
	$description = $posted['t_betreff'] . $posted['t_bereich'] . $posted['t_nachricht'];
	// check if session hash matches current form hash
	if (isset($_SESSION['form_hash']) && $_SESSION['form_hash'] == md5($description)) {
		// form was re-submitted return false
		return false;
	}
	// set the session value to prevent re-submit
	$_SESSION['form_hash'] = md5($description);
	return true;
}