<?php

require 'vendor/autoload.php'; // Require the autoload file to load Guzzle HTTP client.

use GuzzleHttp\Client; // Import the Guzzle HTTP client namespace.
use GuzzleHttp\Psr7\Request; // Import the PSR-7 Request class.
use GuzzleHttp\Psr7\Utils; // Import the PSR-7 Utils class for working with streams.
$apikey = getapikey();;
$convertto = $_POST['convertto'];
generate($_FILES,$apikey,$convertto);



function generate($file,$apikey,$convertto){
$FileName = $file['pdf']['name'];
$ExName = $file['pdf']['tmp_name'];

// tentukan lokasi file akan dipindahkan
$dirUpload = "output/";

// pindahkan file
$terupload = move_uploaded_file($ExName, $dirUpload.$FileName);
$apikey = $apikey;
$res = pdfrest($_FILES,$apikey,$convertto);
if($convertto=='excel'){
	$ty = '.xlsx';
}elseif($convertto=='word'){
	$ty = '.docx';
}elseif($convertto=='powerpoint'){
	$ty = '.ppt';
}elseif($convertto=='bmp'){
	$ty = '.bmp';
}
$file_name = date('YmdHHmmss').$ty;
$response = json_decode($res->getBody()->getContents());
// Locate.
$file_url = $response->outputUrl;
updatelimit();
// Configure.
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"".$file_name."\"");

// Actual download.
readfile($file_url);

// Finally, just to be sure that remaining script does not output anything.
exit; // Output the response body, which contains the Excel document.
}


function pdfrest($files,$apikey,$convertto){
	$client = new Client(); // Create a new instance of the Guzzle HTTP client.

$headers = [
  'Api-Key' => $apikey // Set the API key in the headers for authentication.
];

$options = [
  'multipart' => [
    [
      'name' => 'file', // Specify the field name for the file.
      'contents' => Utils::tryFopen('output/'.$_FILES['pdf']['name'], 'r'), // Open the file specified by the '/path/to/file' for reading.
      'filename' => 'output/'.$_FILES['pdf']['name'], // Set the filename for the file to be processed, in this case, '/path/to/file'.
      'headers' => [
        'Content-Type' => '<Content-type header>' // Set the Content-Type header for the file.
      ]
    ],
    [
      'name' => 'output', // Specify the field name for the output option.
      'contents' => 'pdfrest_excel' // Set the value for the output option (in this case, 'pdfrest_excel').
    ]
  ]
];

$request = new Request('POST', 'https://api.pdfrest.com/'.$convertto, $headers); // Create a new HTTP POST request with the API endpoint and headers.

$res = $client->sendAsync($request, $options)->wait(); // Send the asynchronous request and wait for the response.
return $res;
}
function updatelimit(){
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
			limits = case when limits!=0 then limits-1
			else limits end
			where limits = (select min(b.limits) from apikey b
			where b.limits!=0)";
	$result = $conn->query($sql);
}
function getapikey(){
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

$sql = "SELECT id, apikey FROM apikey where limits!=0 order by limits asc";
$result = $conn->query($sql);
$apikey = $result->fetch_assoc();
$apikey = $apikey['apikey'];
return $apikey;
}
?>
