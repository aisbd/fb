<?php 
require "Fb/autoload.php";


/**
* 
*/
 class FB extends Facebook\Facebook
{
	private $app_id, $app_secret;
	protected $user_id;

	/**
	 * Facebook App ID
	 */
	const APP_ID = "1689768341296224";

	/**
	 * Facebook App Secret
	 */
	const APP_SECRET = "680bdedb0c0cf76599d8560f22923a7e";

	/**
	 * Facebook App URL
	 */
	const APP_URL = "http://www.echobd.net/fb";

	public function __construct($profile_id = false)
	{
		parent::__construct([
						'app_id' 				=> static::APP_ID,
					  	'app_secret' 			=> static::APP_SECRET,
  						]);

		//generate token or login user/ Set default app loging .
		$this->app_id = static::APP_ID;
		$this->app_secret = static::APP_SECRET;
		$this->app_url = static::APP_URL;
		$this->login();

		//set the user_id
		if($profile_id != false)
		{
			$this->setProfileId($profile_id);
		}

		//set page, group access token
		
			$this->setToken();
	}

	/**
	 * Set target profile id to post or get data.
	 */
	public function setProfileId($id)
	{
		if($id != false)
		{
			$this->profile_id = $id;
		}else if(!isset($this->profile_id))
		{
			$this->profile_id = 'me';
		}
	}

	/**
	 * Dwonload file and genereate local path from  url
	 * @param string $url
	 * @return local path 
	 */
	protected function download($url)
	{
		ini_set('max_execution_time', 999);
		$file_name = $this->generateFileName();
		$extension = $this->getFilExtension($url);
		$dir = __DIR__.'/../tmp_file/';
		$path = $dir .$file_name.'.'.$extension;
		$file = fopen($path, 'w+');
		$ch = curl_init(str_replace(" ",  "%20", $url));
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_FILE, $file);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		curl_close($ch);
		return $path;
	}

	/**
	 * Get file extension from name/string
	 * @param string name or url
	 * @return file extention
	 */
	protected function getFilExtension($fname)
	{
		$fname = explode('.', $fname);
		return end($fname);
	}
 	
 	/**
 	 * Gennerate new file name
 	 */
 	protected function generateFileName()
 	{
 		return date('YmdHis').'_'.uniqid();
 	}


	private function login()
	{

		// $data = unserialize(file_get_contents('token'));
		// $datetime1 = date_create($data['created_at']);
		// $datetime2 = date_create(date('Y-m-d'));
		// $interval = date_diff($datetime1, $datetime2);
		// $token = $data['token'];
		// if($interval->format('%a') >= 50)
		// {
		// 	$url = "https://graph.facebook.com/oauth/access_token?client_id=$this->app_id&client_secret=$this->app_secret&grant_type=fb_exchange_token&fb_exchange_token=$token";
		// 	$token = json_decode($this->file_get_contents($url))->access_token;
		// 	$data['token'] = $token;
		// 	$data['created_at'] = date('Y-m-d');
		// 	file_put_contents('token', serialize($data));
		// }
		// $this->setDefaultAccessToken($token);
		$this->setDefaultAccessToken("EAAYA1dm8VGABALXcZAkG2Gw8hWnymDJQuP2xlZA2v1ucY9ygtbS6oG0F4xHz3TQMwEpxsHzmzZAIeqpKNKRJiby6F2YorVTsZCbFvApdP7nPGNycq0w56apA3g950SKc7sUjjeZAWT8vXUZAIkmhvZCdfhMzrAfdp0ZD");
		
/*


			if(isset($_SESSION['facebook_access_token']))
			{
				$this->setDefaultAccessToken($_SESSION['facebook_access_token']);
				$_SESSION['facebook_access_token'];
			}else if(isset($_GET['token']))
			{
				$this->setDefaultAccessToken($_GET['token']);
			}else if(isset($_GET['code']))
			{	$redirect_uri = urlencode("http://www.echobd.net/fb");
				$url = "https://graph.facebook.com/oauth/access_token?client_id=$this->app_id&client_secret=$this->app_secret&redirect_uri=$redirect_uri&code=".$_GET['code'];
				$token = explode('=', $this->file_get_contents($url))[1];
				$_SESSION['facebook_access_token'] = $token;
				$this->setDefaultAccessToken($token);
				header("Location: $this->app_url");
   			}else {
				$helper = $this->getRedirectLoginHelper();
				$url = $helper->getLoginUrl("http://www.echobd.net/fb");
				header("Location: $url");
				exit;
			}
		*/
	}

	protected function file_get_contents($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	protected function getToken()
	{
		$request = $this->request("GET", "/$this->profile_id?fields=access_token");
		$response = $this->getClient()->sendRequest($request);
		return $response->getGraphNode()->asArray()['access_token'];		
	}

	protected function setToken()
	{
		$this->setDefaultAccessToken($this->getToken());
	}

	/**
	 * Post image on page
	 * @param string  $img, $msg
	 * @return post id
	 */
	public function postImage($img, $msg)
	{
		if(is_array($img))
		{
			return $this->uploadMultipleImage($img, $msg);
			
		}else{
			return $this->uploadImage($img, $msg);
		}
		
	}

	/**
	 * upload and post video
	 * @param string $url, string Description
	 * @return string post/video id
	 */
	public function postVideo($video_url, $description)
	{
		$args = [
			'file_url'		=> $video_url,
			'description'	=> $description
		];

		$response = $this->post("/$this->profile_id/videos", $args);
		return $response->getGraphNode()['id'];
	}

	/**
	 * Post status
	 * @param status text $text
	 * @return status/post  id
	 */
	public function postStatus($msg)
	{
		$response = $this->post("/$this->profile_id/feed", ['message' => $msg]);
		return $response->getGraphNode()['id'];
	}

	/**
	 * Upload multiple images
	 * @param array $images, string $msg
	 * @return string post ID
	 */
	public function uploadMultipleImage($images = [], $msg)
	{
		$args = [];
		$i = 0;
		foreach ($images as $image) 
		{
			$args["attached_media[$i]"] = '{"media_fbid":"'.$this->uploadImage($image, $msg, false).'"}';
			$i ++;
		}
		$args['message'] = $msg;
		$response = $this->post("/$this->profile_id/feed", $args);
		return $response->getGraphNode()['id'];
	}

	/**
	 * upload images
	 * @param string $img, strin $msg/caption 
	 */
	public function uploadImage($img, $msg, $published = true)
	{
		if(filter_var($img, FILTER_VALIDATE_URL))
		{
			$img_key = 'url';
			$img_value = $img;
		}else{
			$img_key = 'source';
			$img_value = $this->download($this->fileToUpload($img));
		}
		$args = ['message' => $msg, $img_key => $img_value];

		/* check published*/
		if(!$published)
		{
			$args['published'] = 'false';
		}
		$response = $this->post("/$this->profile_id/photos", $args);
		return $response->getGraphNode()['id'];
	}

	/**
	 * Post link on profile
	 */
	public function postLink($link, $msg)
	{
		$args = ['link' => $link, 'message' => $msg];
		$response = $this->post("/$this->profile_id/feed", $args);
		return $response->getGraphNode()->asArray()['id'];
	}

	/**
	 * Get feed
	 */
	public function getFeed($page_id, $limit = 10) 
	{
		$response = $this->get("/$page_id/posts?limit=$limit&fields=source,link,status_type,message")->getGraphEdge()->asArray();
		foreach ($response as $key => $value) {
			if(isset($value['status_type']) && $value['status_type'] == 'added_photos')
			{
				$response[$key]['attachments'] = $this->getAttachments($value['id']);
			}
		} 
		return $response;
	}

	/**
	 * Get feed details
	 */
	public function getAttachments($id)
	{
		$data = [];
		$attachments = $this->get("/$id/attachments")->getGraphEdge()->asArray();
		if(isset($attachments[0]['subattachments']))
		{
			foreach ($attachments[0]['subattachments'] as $attachment) 
			{  				
					$files = ['src' => $attachment['media']['image']['src']];
					$data[] = $files;
			}
		}else{
					$data[] = ['src' => $attachments[0]['media']['image']['src']];
		}

		return $data;
	}

	/**
	 * Post feed
	 * @param array Getfeed array, 
	 */
	public function postFeed($args =[])
	{
	//	$args = array_reverse($args, true);
		foreach ($args as $key => $value) {
			//Set common variable values first
			if(isset($value['message']))
			{
				$msg =$value['message'];
			}else if(isset($value['description']))
			{
				$msg = $value['description'];
			}else{
				$msg = "";
			}
			// filter the txt;
			$msg = $this->textFilter($msg);

			if($msg == false){return false;}

			switch ($value['status_type']) {
				case 'added_photos':
					$images = [];
					foreach ($value['attachments'] as $attachment) {
						$images[] = $attachment['src'];	
					}
					if(count($images) <= 1)
					{
						//single image
						$this->uploadImage($images[0], $msg);
					}else{
						//multiple image
						$this->uploadMultipleImage($images, $msg);
					}
					break;

				case 'added_video':
					$this->postVideo($value['source'], $msg);
					break;

				case 'mobile_status_update':
					if($msg != "")
					{
						$this->postStatus($msg);
					}
					break;
			}
		}
	}

	/**
	 * Text filter
	 * @param string $text
	 * @return string $fitered text;
	 */
	protected function textFilter($text)
	{
		$filterF = [
			"#তানু_মনি", "#টুকুন", "#কুইন",  "#রোমান্টিক_বাঘিনী",  "#অভ্র", "#সুপান্থ", "#রোমান্টিক_বাঘিনী", "#ভিনগ্রহী",
			 "#টিকটিকি", "#তাকওয়া", 
		];
		$filterM = [
			"#তানু_মনি", "#লালু", "#টুকুন", "#মুন্না_ভাই", "#Mr_raZ", "#ভদ্র_হারামী", "#কুইন", "#বাণীতে", "#কেরাশ_খোর", "#ডিজিটালকামলা", "#রোমান্টিক_বাঘিনী", "#সাদা", "#আজিব_মুগ্ধ", "#Tarim_Ahmed_Mugdho", "#পিচ্চি_হারামী", "#মিস_মাশকারা", "(স্কিজফ্রেনিয়া)", "#অভ্র", "#সুপান্থ", "#রোমান্টিক_বাঘিনী", "#ভিনগ্রহী",
			"#MrChieez", "#dsb_saheb", "#পিলু", "#টিকটিকি", "#তাকওয়া", "#HA4DCORE3", "নিলয় রহমান", "বদ্দা"
		];

		$ban = [
		"চুপ হারামী", 
		];
		if($this->contain($text, $ban))
		{
			return false;
		}
		$text = str_replace($filterF, "#Ridhi", $text);
		$text = str_replace($filterM, "#Ruhan", $text);
		return $text;
	}


	public function contain( $str ,$args)
	{
		    foreach($args as $a) {
       			 if (stripos($str,$a) !== false) return true;
  			  }
   			 return false;
	}

}