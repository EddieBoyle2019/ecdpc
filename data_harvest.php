<?php

//current date
$date = date("dmy");

$ecdpc_url = "http://opendata.ecdc.europa.eu/covid19/casedistribution/json";

$data_filename = "/var/www/html/D3/Data/ecdpc_data_". $date . ".json";

error_reporting(-1);
ini_set('display_errors', 'On');

$json = file_get_contents($ecdpc_url);

$data = var_export($json, true);

//remove quotes
$data = substr($data, 1, -1);

file_put_contents($data_filename, $data);

?