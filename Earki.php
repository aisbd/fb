<?php 
function dd($dd)
{
	echo "<pre>";
	print_r($dd);
	die();
}
/**
* 
*/
class Earki
{
	const URL = "http://www.earki.com/";

	public function getLatestPost()
	{
		$xpath = $this->getXpath($this->getDom(self::URL));
		$column = $this->getElementsByClass('contents_group_2', $xpath)->item(0);
		$post = $this->getElementsByClass('each', $this->getXpath($column))->item(0);
		$a = $post->getElementsByTagName('a')->item(0);
		$url = self::URL.$a->getAttribute('href');
		return $this->fetchPost($url);
	}

	public function fetchPost($url)
	{
		$text = "";
		$imgs = [];
		$dom = $this->getDom($url);
		$title  = $dom->getElementsByTagName('title')->item(0)->nodeValue;
		$nodes = $dom->getElementsByTagName('article')->item(0)->getElementsByTagName('p');
		foreach ($nodes as $node) {
			$text .= "\n";
			$text .= trim($node->nodeValue); 
			$images = $node->getElementsByTagName('img');
			if($images->length != 0){
				foreach ($images as $image) {
					$imgs[] = $image->getAttribute('src');
				}
			}
		}
		$data = new StdClass();
		$data->images = $imgs;
		$data->text = $text;
		$data->title = trim($title);
		return $data;
	}

	public function getElementsByClass($class, $xpath = false)
	{
		if(!$xpath)
		{
			$xpath = $this->xpath;
		}
		return $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]");
	}

	public function getXpath($dom)
	{
		if(!$dom instanceof DOMDocument)
		{
			$doc = new DOMDocument();
			$doc->loadHtml($this->nodeToHtml($dom));
			$dom = $doc;
		}
		return $this->xpath = new DOMXpath($dom);		
	}

	public function getDom($url)
	{
		$content = file_get_contents($url);
		$doc = new DOMDocument();
		@$doc->loadHtml($content);	
		return $doc;	
	}
	public function nodeToHtml($element)
	{
	    $innerHTML = ""; 
	    $children  = $element->childNodes;

	    foreach ($children as $child) 
	    { 
	        $innerHTML .= $element->ownerDocument->saveHTML($child);
	    }
		return mb_convert_encoding($innerHTML, 'HTML-ENTITIES', 'UTF-8');
	}
	
}
$e = new Earki();
$post = $e->getLatestPost();

// die('blocked');
if (!session_id()) {
    session_start();
}
$data = unserialize(file_get_contents('data'));

require "classes/FB.php";

$fb = new FB("1536907989950680");
$pages = ['earki'];
foreach ($pages as $page) 
{
	$id = $post->title;
	if(!isset($data[$page]['id']) || $data[$page]['id'] != $id)
	{

		$msg = "***".$post->title."***\n".$post->text;
		$data[$page]['id'] = $id; 
		file_put_contents('data', serialize($data));

					if(count($post->images) <= 1)
					{
						if(count($post->images) == 0)
						{
							$fb->postStatus($msg);
						}else{
							$fb->uploadImage($post->images[0], $msg);
						}
						//single image
					}else{
						//multiple image
						$fb->uploadMultipleImage($post->images, $msg);
					}

		
	}

}


/*Debidwar times*/

// $pages = [
// 			"710171749021198",
// 		 ];

// $fb = new FB("485526748307245");

// foreach ($pages as $page) 
// {
// 	$a = $fb->getFeed($page, 1);

// 	$id = $a[0]['id'];

// 	if(!isset($data[$page]['id']) || $data[$page]['id'] != $id)
// 	{
// 		$fb->postFeed($a);
// 		$data[$page]['id'] = $id; 
// 		file_put_contents('data', serialize($data));
		
// 	}

// }

