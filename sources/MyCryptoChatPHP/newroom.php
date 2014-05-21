<?php
require 'inc/constants.php';
require 'inc/init.php';
require 'inc/functions.php';
require 'inc/classes.php';
require 'inc/dbmanager.php';

if(!isset($_POST['nbMinutesToLive']) || $_POST['nbMinutesToLive'] < NB_MINUTES_TO_LIVE_MIN) {
    $nbMinutesToLive = NB_MINUTES_TO_LIVE_MIN;
} else if ($_POST['nbMinutesToLive'] > NB_MINUTES_TO_LIVE_MAX) {
    $nbMinutesToLive = NB_MINUTES_TO_LIVE_MAX;
} else {
    $nbMinutesToLive = $_POST['nbMinutesToLive'];
}

$time = $_SERVER['REQUEST_TIME'];

$selfDestroys = isset($_POST['selfDestroys']) && $_POST['selfDestroys'] == 'true';

$userHash = getHashForIp();

// we generate a random key
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$key = '';
for ($i = 0; $i < 20; $i++) {
    $key .= $characters[rand(0, strlen($characters) - 1)];
}
// we add the seed, hash the whole key and only take the 20 first characters
$key = substr(md5($key . SEED), 0, 20);

// we create the chat room object
$chatRoom = new ChatRoom;
$chatRoom->id = $key;
$chatRoom->dateCreation = $time;
$chatRoom->dateLastNewMessage = $time;
$chatRoom->noMoreThanOneVisitor = $selfDestroys;
$chatRoom->userId = $userHash;
$chatRoom->dateEnd = $time + ($nbMinutesToLive * 60);

$chatUser = array();
$chatUser['id'] = $userHash;
$chatUser['dateLastSeen'] = $time;

array_push($chatRoom->users, $chatUser);

$dbManager = new DbManager();
// we delete old chatrooms
$dbManager->CleanChatrooms($time);
// we save the chat room in sqlite
$dbManager->CreateChatroom($chatRoom);

header('Location: chatroom.php?id=' . $key);
?>