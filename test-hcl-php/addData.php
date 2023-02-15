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

//Getting new data
$source_data = file_get_contents('php://input');
$data1 = json_decode($source_data);
$add_data = array();
foreach($data1 as $key => $val) {
    if($key != 'isEdit' && $key != 'isSelected') {
        $add_data[] = $val;
    }
}

//Getting original data
$i = 0;
$last_id = 0;
$newdata = [];
$handle = fopen('./stock.csv', 'r');

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $newdata[$i][] = $data[0];          
    $newdata[$i][] = $data[1];    
    $newdata[$i][] = $data[2];      
    $newdata[$i][] = $data[3];    
    $newdata[$i][] = $data[4];    
    $newdata[$i][] = $data[5];
    $newdata[$i][] = $data[6];
    $i++;

    $last_id = (int) $data[0] + 1;
}

$newdata[$i][] = $last_id;          
$newdata[$i][] = $add_data[1];
$newdata[$i][] = $add_data[2];
$newdata[$i][] = $add_data[3];
$newdata[$i][] = $add_data[4];
$newdata[$i][] = $add_data[5];
$newdata[$i][] = $add_data[6];

//Writing file
$filename = 'stock.csv';
$f = fopen($filename, 'w');

if ($f === false) {
	die('Error opening the file ' . $filename);
}
foreach ($newdata as $row) {
	fputcsv($f, $row);
}
fclose($f);

?>