<?php
function dd($c)
{
	echo "<pre>";
	print_r($c);
	die();
}
error_reporting(E_ALL);
ini_set('display_errors', 'On');
if (!session_id()) {
    session_start();
}


require "pages/OnnoKhobor.php";
$fb = new OnnoKhobor();

$post = $fb->getFeed("752597858092152", 1)[0];

// $fb->setDefaultAccessToken("EAAYA1dm8VGABALXcZAkG2Gw8hWnymDJQuP2xlZA2v1ucY9ygtbS6oG0F4xHz3TQMwEpxsHzmzZAIeqpKNKRJiby6F2YorVTsZCbFvApdP7nPGNycq0w56apA3g950SKc7sUjjeZAWT8vXUZAIkmhvZCdfhMzrAfdp0ZD");
//$fb->postFeed($a);
	$c = $fb->get($post['id'].'/comments')->getGraphEdge()->asArray();

	// die();
	$i = 0;

	// foreach ($c as $com) {
	// 	// if($i == 3){ die(); }
	// 	$c = $fb->post('/'.$com['id'].'/likes');
	// 	$c = $fb->post('/'.$com['id'].'/comments', ['message' => 'আমাদের পেইজ থেকে একটু ঘুরে আসুন প্লিজ। ভাল লাগলে লাইক দিবেন।']);
	// 	$c_id = $com['id'];
	// 	$fb->post($c_id.'/likes'); 
	// 	$i ++;
	// }

	dd($c);


/*
khaia chaira de 1096731183740130
golpota tumar amar 1085393431545207
chashmish 1541280102762341
*/


