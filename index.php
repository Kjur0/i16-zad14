<?php
$doctype = 'html';
$charset = "utf-8";
$author = "Krzysztof Jurkowski";
$lang = "pl";
if (file_exists('main.php'))
	include 'main.php';
else
	error_log("main.php not found");
echo <<<HTML
<!DOCTYPE $doctype>
<html lang=$lang>
<head>
	<meta charset=$charset>
	<meta name="author" content="$author">
	<title>Komis</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<form method="post" id="head">
		<h1>Wybierz co chcesz zrobić:</h1>
		<input type="submit" value="Wyświetl" name="view">
		<input type="submit" value="Szukaj" name="search">
		<input type="submit" value="Dodaj" name="add">
	</form>
HTML;
if (array_key_exists("view", $_POST)) {
	echo "<h1>Wyświetlanie wszystkich tablic</h1>";
	mysqli_echo_all_tables();
} elseif (array_key_exists("search", $_POST)) {
	echo <<<HTML
	<h1>Wyszukiwanie</h1>
	<form name="search" method="post">
	HTML;

	echo "<hr><h1>Wybierz marki:</h1>";
	$markas = query("SELECT DISTINCT marka FROM samochody;");
	for ($i = 0; $marka = mysqli_fetch_row($markas); $i++) {
		echo <<<HTML
				<div>
					<input type="checkbox" name="marki[]" id="marka$i" value="$marka[0]">
					<label for="marka$i">$marka[0]</label>
				</div>
				HTML;
	}

	echo "<hr><h1>Wybierz kolory:</h1>";
	$kolors = query("SELECT DISTINCT kolor FROM samochody;");
	for ($i = 0; $kolor = mysqli_fetch_row($kolors); $i++) {
		echo <<<HTML
				<div>
					<input type="checkbox" name="kolory[]" id="kolor$i" value="$kolor[0]">
					<label for="kolor$i">$kolor[0]</label>
				</div>
				HTML;
	}

	echo <<<HTML
		<hr>
		<input type="submit" name="show" value="Szukaj">
	</form>
	HTML;
} elseif (array_key_exists("show", $_POST)) {
	$query = "SELECT * FROM samochody WHERE 1=1";
	echo "<h1>Wyniki wyszukiwania</h1>";
	if (array_key_exists("marki", $_POST)) {
		$marki = $_POST["marki"];
		$query .= " AND marka IN(";
		echo "<hr><p>Wybrane marki:<ul>";
		foreach ($marki as $marka) {
			echo "<li>$marka</li>";
			$query .= '"' . $marka . '"' . ', ';
		}
		echo "</ul></p>";
		$query .= ')';
	}

	if (array_key_exists("kolory", $_POST)) {
		$kolory = $_POST["kolory"];
		$query .= " AND kolor IN(";
		echo "<hr><p>Wybrane kolory:<ul>";
		foreach ($kolory as $kolor) {
			echo "<li>$kolor</li>";
			$query .= '"' . $kolor . '"' . ', ';
		}
		echo "</ul></p>";
		$query .= ')';
	}

	$query .= ';';
	echo "<hr>";
	$query = str_replace(', )', ')', $query);
	mysqli_echo_query($query, "samochody");
} elseif (array_key_exists("add", $_POST)) {
	echo "<h1>Dodawanie</h1>";
	if (array_key_exists("new", $_POST)) {
		$query = "INSERT INTO samochody (marka, model, rocznik, kolor, stan) VALUES (";
		$query .= '"' . $_POST["marka"] . '"' . ', ';
		$query .= '"' . $_POST["model"] . '"' . ', ';
		$query .= '"' . $_POST["rocznik"] . '"' . ', ';
		$query .= '"' . $_POST["kolor"] . '"' . ', ';
		$query .= '"' . $_POST["stan"] . '"';
		$query .= ");";
		query($query);
		echo "<h2>Dodano!</h2>";
	}
	$current = date("Y");
	echo <<<HTML
	<form name="add" method="post">
		<input type="hidden" name="new" value="true">
		<hr>
		<label for="marka">Marka:</label>
		<input type="text" name="marka" id="marka" required>
		<label for="model">Model:</label>
		<input type="text" name="model" id="model" required>
		<label for="rocznik">Rocznik:</label>
		<input type="number" name="rocznik" id="rocznik" required min="1886" max="$current" step="1" >
		<label for="kolor">Kolor:</label>
		<input type="text" name="kolor" id="kolor" required>
		<label for="stan">Stan:</label>
		<input type="text" name="stan" id="stan" required>
		<hr>
		<input type="submit" name="add" value="Dodaj">
	</form>
	HTML;
}
echo <<<HTML
</body>
</html>
HTML;
