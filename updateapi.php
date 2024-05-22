<?php
print_r($_GET);
if($_GET['act']=='updatelimit'){
	updatemonthlylimit();
}elseif($_GET['act']=='insertkey'){
	insertnewapikey($_GET['apikey']);
}
function updatemonthlylimit(){
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "pdfconverter";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}

	$sql = "update apikey a set
			limits = '300'";
	$result = $conn->query($sql);
}
function insertnewapikey($apikey){
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "pdfconverter";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}

	$sql = "insert into apikey(apikey,limits) values('$apikey','300')";
	$result = $conn->query($sql);
}
?>
