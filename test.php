<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);
$text = 

$lines = file('https://tabby-merda.herokuapp.com/frasi.txt');


$text = $lines[0];

echo $text;