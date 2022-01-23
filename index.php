<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://integration.cdek.ru/pvzlist/v1/xml?countryid=1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

ini_set("memory_limit", "1024M");
$response = curl_exec($curl);

curl_close($curl);
$allDataXML = simplexml_load_string($response);
$json = json_encode($allDataXML);
$allData = json_decode($json, true);

$pathDir = $_SERVER['DOCUMENT_ROOT'] . '/results/';

foreach($allData["Pvz"] as $item) {
    $dirName = $pathDir . $item['@attributes']['RegionName'];
    if(!is_dir($dirName)) {
        mkdir("/{$dirName}", 0777, true);
        writeFile($item, $dirName);
    } else {
        writeFile($item, $dirName);
    }
}

function writeFile($item, $dirName) {
    $fileName = $dirName . "/{$item['@attributes']['City']}.json";
    $string = file_get_contents($fileName);
    if(!$string){
        $newItem[$item['@attributes']['Name']] = $item;
        $jsonData = json_encode($newItem, JSON_UNESCAPED_UNICODE);
        $fp = fopen($fileName , 'a');
        fwrite($fp, $jsonData);
        fclose($fp);
    } else {
        $dataArr = json_decode($string, true);
        $dataArr[$item['@attributes']['Name']] = $item;
        $jsonData = json_encode($dataArr, JSON_UNESCAPED_UNICODE);
        file_put_contents($fileName, $jsonData);
    }
}