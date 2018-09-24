<?php


require('./vendor/ktamas77/firebase-php/src/firebaseLib.php');

const DEFAULT_URL = 'https://gw2rbot.firebaseio.com/';
const DEFAULT_TOKEN = 'n5p3CRjh2GCLIijiVrDhYcDidLOlUrukaLUqsiXQ';
const DEFAULT_PATH = '';

$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

$content = file_get_contents("php://input");
$update = json_decode($content, true);
if(!$update)
{
  exit;
}
$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "" ;
$text = trim($text);
$text = strtolower($text);
error_log($message['entities'][0]['type'] . $text );

// --- reading the stored string ---
$name = $firebase->get(DEFAULT_PATH . '/active/', array("orderBy" => "\"status\"", "equalTo" => "\"" . $username . "_active\""));

$key = key((array)$name);
error_log($name);


function toMap(&$array){
    $map = array();
   foreach ($array as &$value) {
       foreach ($value['wings'] as &$wings) {
           foreach ($wings['events'] as &$event) {
               $map[$event["id"]] = &$event;
           }
       }
   }
    return $map;
}

function toText($from) {
    return ucwords(str_replace("_"," ",$from));
}


//next example will recieve all messages for specific conversation
$service_url = 'http://api.guildwars2.com/v2/raids';
$my_bosses = "https://api.guildwars2.com/v2/account/raids";
function callAPI($method, $url, $data, $key){
  $proxy = 'proxy.eng.it:3128';
  $proxyauth = 'cramato:Cri%2487i%40n';
  $authorization = "Authorization: Bearer " . $key;
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

$myJSON = json_decode(callAPI("GET", $service_url, null, $key),true);
$ids = "";
foreach ($myJSON as &$value) {
    $ids = $ids . $value . ",";
}

$bosses = json_decode(callAPI("GET", $service_url . "?ids=" .$ids, null, $key),true);


$myJSON = json_decode(callAPI("GET", $my_bosses, null, $key),true);
$bossesMap = toMap($bosses);


foreach($myJSON as &$value){
    $bossesMap[$value]["done"] = true;
}

$resp = "";
foreach ($bosses as &$value) {
    foreach ($value['wings'] as &$wings) {
    $complete = true;
    $status = "";
        foreach ($wings['events'] as &$event) {
            $event["descr"] = toText($event["id"]);
            if($event["done"]){
                $status = $status . "\xE2\x9C\x85 ";
            }else{
                $status = $status . "\xE2\x9D\x8C ";
                $complete = false;
            }
            $status = $status . "" . toText($event["descr"]) . "\n";
        }
         if($complete){
             $resp = $resp . "\xE2\x9C\x85 ";
          }else{
             $resp = $resp . "\xE2\x9D\x8C ";
          }
    $resp = $resp . "*" . toText($wings["id"]) . "*\n";
        $resp = $resp . $status . "\n";
    }
}

	header("Content-Type: application/json");
	$parameters = array('chat_id' => $chatId, "text" => $resp, "parse_mode" => "Markdown");
	$parameters["method"] = "sendMessage";
	echo json_encode($parameters);


?>
