<?php

require('./vendor/ktamas77/firebase-php/src/firebaseLib.php');

const DEFAULT_URL = 'https://gw2rbot.firebaseio.com/';
const DEFAULT_TOKEN = 'n5p3CRjh2GCLIijiVrDhYcDidLOlUrukaLUqsiXQ';
const DEFAULT_PATH = '/';

$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

// --- storing an array ---
$test = array(
    "foo" => "bar",
    "i_love" => "lamp",
    "id" => 42
);
$dateTime = new DateTime();
$firebase->set(DEFAULT_PATH . '/' . $dateTime->format('c'), $test);

// --- storing a string ---
$firebase->set(DEFAULT_PATH . '/name/contact001', "John Doe");

// --- reading the stored string ---
$name = $firebase->get(DEFAULT_PATH . '/name/contact001');
echo $name;
?>