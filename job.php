<?php 
error_reporting(E_ALL);
ini_set('display_errors', 'On');
// die('blocked');
if (!session_id()) {
    session_start();
}
$data = unserialize(file_get_contents('data'));

require "classes/FB.php";

//1481766625455254 mr 420
//103741982610 jokes and funny picture

// 710171749021198 debidwar police station




$pages = [
			"808033679287136",
			"1541280102762341", //chasmis
			// "623805097783917",
			"1481766625455254", //mr420
			//"103741982610",
			// "1638266876438793",
			// "778202215587089", 
			"192857910771358", //Matha Nosto
			"1621217598164958", //chup harami
			"1692958917386473", // pinik baba
			"892478484197978" //appi
		 ];

$fb = new FB("1536907989950680");

foreach ($pages as $page) 
{
	$a = $fb->getFeed($page, 1);

	$id = $a[0]['id'];

	if(!isset($data[$page]['id']) || $data[$page]['id'] != $id)
	{
		
		$data[$page]['id'] = $id; 
				file_put_contents('data', serialize($data));
				$fb->postFeed($a);
		
	}

}


/*Debidwar times*/

$pages = [
			"710171749021198",
		 ];

$fb = new FB("485526748307245");

foreach ($pages as $page) 
{
	$a = $fb->getFeed($page, 1);

	$id = $a[0]['id'];

	if(!isset($data[$page]['id']) || $data[$page]['id'] != $id)
	{
		$fb->postFeed($a);
		$data[$page]['id'] = $id; 
		file_put_contents('data', serialize($data));
		
	}

}
