<?php
header("Access-Control-Allow-Origin: *");

$filename = './data.csv';
$data = [];

$f = fopen($filename, 'r');

if ($f === false) {
	die('Cannot open the file ' . $filename);
}

while (($row = fgetcsv($f)) !== false) {
	$data[] = $row;
}

fclose($f);

$res['users'] = [];
foreach($data as $key => $val) {
	if($key != 0) {
		$arr = array(
			'id' => $val[0],
			'name' => $val[1],
			'state' => $val[2],
			'zip' => $val[3],
			'amount' => $val[4],
			'qty' => $val[5],
			'item' => $val[6]
		);

		$res['users'][] = $arr;
	}
}

echo json_encode($res);
?>