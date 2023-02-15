<?php
//Avoiding CORS origin error
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

//Getting ID
$url = $_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);
$pathFragments = explode('/', $path);
$row_id = end($pathFragments);

//Getting updated data
$source_data = file_get_contents('php://input');
$data1 = json_decode($source_data);
$update_data = array();
foreach($data1 as $key => $val) {
    if($key != 'isEdit') {
        $update_data[] = $val;
    }
}

//Getting original data
$i = 0;
$newdata = [];
$handle = fopen('./data.csv', 'r');

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {      
    if ($i == $row_id) {
        $newdata[$i][] = $update_data[0];          
        $newdata[$i][] = $update_data[1];
        $newdata[$i][] = $update_data[2];
        $newdata[$i][] = $update_data[3];
        $newdata[$i][] = $update_data[4];
        $newdata[$i][] = $update_data[5];
        $newdata[$i][] = $update_data[6];
        $i++;
        continue;
    }  
    $newdata[$i][] = $data[0];          
    $newdata[$i][] = $data[1];    
    $newdata[$i][] = $data[2];      
    $newdata[$i][] = $data[3];    
    $newdata[$i][] = $data[4];    
    $newdata[$i][] = $data[5];
    $newdata[$i][] = $data[6];
    $i++;    
}

//Writing file
$filename = 'data.csv';
$f = fopen($filename, 'w');

if ($f === false) {
	die('Error opening the file ' . $filename);
}
foreach ($newdata as $row) {
	fputcsv($f, $row);
}
fclose($f);

?>