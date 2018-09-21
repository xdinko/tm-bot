<?php


function toMap($array){
    $map = array();
    foreach ($array as $row) {
        $map[$row['id']] = $row;
    }
    return $map;
}


//next example will recieve all messages for specific conversation
$service_url = 'http://api.guildwars2.com/v2/raids';
$my_bosses = "https://api.guildwars2.com/v2/account/raids";
function callAPI($method, $url, $data){
  $proxy = 'proxy.eng.it:3128';
  $proxyauth = 'cramato:Cri%2487i%40n';
  $authorization = "Authorization: Bearer 4D60AA9D-3C10-0343-81FB-5E905F6F4B5E842E09EA-AFF4-40CB-9AC8-AA6FB4F0FC75";
  $options = array(
          CURLOPT_RETURNTRANSFER => true,   // return web page
          CURLOPT_HEADER         => false,  // don't return headers
          CURLOPT_FOLLOWLOCATION => true,   // follow redirects
          CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
          CURLOPT_ENCODING       => "",     // handle compressed
          CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
          CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
          CURLOPT_TIMEOUT        => 120,    // time-out on response
      );

   $curl = curl_init();
   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
   }

   // OPTIONS:
   curl_setopt_array($curl, $options);
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
 //  curl_setopt($curl, CURLOPT_PROXY, $proxy);
 //  curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyauth);

   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}

$myJSON = json_decode(callAPI("GET", $service_url, null),true);
$ids = "";
foreach ($myJSON as &$value) {
    $ids = $ids . $value . ",";
}

$bosses = json_decode(callAPI("GET", $service_url . "?ids=" .$ids, null),true);


echo "\n";
$myJSON = json_decode(callAPI("GET", $my_bosses, null),true);
$bossesMap = toMap($bosses);
echo json_encode($bossesMap);
foreach($myJSON as &$value){
    $bossesMap[$value]["done"] = true;
    echo json_encode($bossesMap[$value]);
}


foreach ($bosses as &$value) {
    foreach ($value['wings'] as &$wings) {
        foreach ($wings['events'] as &$event) {
            echo json_encode($event);
        }
    }
}


?>
