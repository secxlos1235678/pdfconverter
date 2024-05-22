<?php
print_r($_POST);die();
$FileName = $_FILES['pdf']['name'];
$ExName = $_FILES['pdf']['tmp_name'];

// tentukan lokasi file akan dipindahkan
$dirUpload = "output/";

// pindahkan file
$terupload = move_uploaded_file($ExName, $dirUpload.$FileName);
require 'vendor/autoload.php'; // Require the autoload file to load Guzzle HTTP client.

use GuzzleHttp\Client; // Import the Guzzle HTTP client namespace.
use GuzzleHttp\Psr7\Request; // Import the PSR-7 Request class.
use GuzzleHttp\Psr7\Utils; // Import the PSR-7 Utils class for working with streams.
$apikey = 'b0505527-d3c7-4e22-8d89-2aee2814ebd1';
$res = pdfrest($_FILES,$apikey);

$file_name = date('YmdHHmmss').'.xlsx';
$response = json_decode($res->getBody()->getContents());
// Locate.
if($response->outputUrl==''){
	$apikey = '03e53b60-159c-4360-a37f-9e467a937033';
	$res = pdfrest($_FILES,$apikey);
	$response = json_decode($res->getBody()->getContents());
	
}

$file_url = $response->outputUrl;
// Configure.
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"".$file_name."\"");

// Actual download.
readfile($file_url);

// Finally, just to be sure that remaining script does not output anything.
exit; // Output the response body, which contains the Excel document.

function pdfrest($files,$apikey){
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

$request = new Request('POST', 'https://api.pdfrest.com/excel', $headers); // Create a new HTTP POST request with the API endpoint and headers.

$res = $client->sendAsync($request, $options)->wait(); // Send the asynchronous request and wait for the response.
return $res;
}
?>
