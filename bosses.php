<?php
  // create curl resource


//next example will recieve all messages for specific conversation
$service_url = 'http://api.guildwars2.com/v2/raids';
function callAPI($method, $url, $data){
  $proxy = 'proxy.eng.it:3128';
  $proxyauth = 'cramato:Cri%2487i%40n';

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
 //  curl_setopt($curl, CURLOPT_PROXY, $proxy);
 //  curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyauth);

   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}

$myJSON = json_decode(callAPI("GET", $service_url, null), true)[0];
echo $myJSON;
?>
