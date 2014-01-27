<?php
/**
 * Gempon PHP library
 *
 * PHP Version 5
 *
 * @author		Agate Studio
 * @copyright	2012 Agate Studio
 * @version		0.2c
 * @website		www.agatestudio.com
 *
 */

class Gempon {

	// API KEY is required to make api call to gempon server
	private $api_key = NULL;
	
	// API SECRET is required to validate signed request
	private $api_secret = NULL;

	// BASE API URL 
	private $base_api_url = 'http://sandbox.gempon.net/index.php/rest'; // just make sure no slash at the end of the url string
	function __construct($api_key, $api_secret)
	{
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
	}
	
	// Validate given signed request is valid or not
	function validate($signed_request, $hash, $user_id)
	{
		$expected = hash_hmac('sha256', $user_id.$hash, $this->api_secret, FALSE);
		if($signed_request === $expected)
		{	
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	// Get all param necessary
	function get_param()
	{
		$param['signed_request'] = NULL;
		$param['hash'] = NULL;
		$param['user_id'] = NULL;
		$param['facebook_id'] = NULL;
		
		if(isset($_GET['signed_request']))
		{
			$param['signed_request'] = $_GET['signed_request'];
		}
		
		if(isset($_GET['hash']))
		{
			$param['hash'] = $_GET['hash'];
		}
		
		if(isset($_GET['user_id']))
		{
			$param['user_id'] = $_GET['user_id'];
		}
		
		if(isset($_GET['user_name']))
		{
			$param['user_name'] = $_GET['user_name'];
		}
		if(isset($_GET['facebook_id']))
		{
			$param['facebook_id'] = $_GET['facebook_id'];
		}
		
		if($this->validate($param['signed_request'],$param['hash'],$param['user_id'])){
			return $param;	
		}else{
			return FALSE;
		}
	}
	function get_user_info($user_name,$format = 'json')
    {
			$get_user_info_url = $this->base_api_url.'/user/id/'.$user_name.'/format/'.$format;
			$result_curl = $this->native_curl_get($get_user_info_url);
			return $result_curl;
    }
	function get_user_friends($user_name,$format = 'json')
    {
			$get_user_friends_url = $this->base_api_url.'/friends/id/'.$user_name.'/format/'.$format;
			$result_curl = $this->native_curl_get($get_user_friends_url);
			return $result_curl;
    }
    function get_random_user($format = 'json')
    {
			$get_user_friends_url = $this->base_api_url.'/random_user/format/'.$format;
			$result_curl = $this->native_curl_get($get_user_friends_url);
			return $result_curl;
    }
    function get_user_current_maja($user_name,$format = 'json')
    {
			$get_maja_info_url = $this->base_api_url.'/user_current_maja/id/'.$user_name.'/format/'.$format;
			$result_curl = $this->native_curl_get($get_maja_info_url,array());
			return $result_curl;
    }
    function payment_maja($user_name,$amount,$description,$format){
    	$get_payment_url = $this->base_api_url.'/payment/format/'.$format;
			$hash = md5(rand(0,50000000));
			$signed_request = hash_hmac('sha256', $user_name.$amount.$description.$hash, $this->api_secret, FALSE);

    	$array_post = array(
    		'api_key'=>$this->api_key,
    		'id'=>$user_name,
    		'amount'=>$amount,
    		'description'=>$description,
    		'format'=>$format,
    		'hash'=>$hash,
    		'signed_request'=>$signed_request
	    );
			$result_curl = $this->native_curl_post($get_payment_url,$array_post);
			return $result_curl;
    }
    function share_twitter($text,$format = 'json'){
    	$text = rawurlencode($text);
    	$get_share_url = $this->base_api_url.'/share_twitter/text/'.$text.'/format/'.$format;
    	$result_curl = $this->native_curl_get($get_share_url);
    	return $result_curl;
    }

    function share_facebook($text,$format = 'json'){
    	$text = rawurlencode($text);
    	$get_share_url = $this->base_api_url.'/share_facebook/text/'.$text.'/format/'.$format;
    	$result_curl = $this->native_curl_get($get_share_url);
    	return $result_curl;
    }

    function send_notification($username,$message,$link = '',$format = 'json'){
    	$notif_url = $this->base_api_url.'/notification/format/'.$format;
	    $array_post = array(
	    		'api_key'=>$this->api_key,
	    		'userid'=>$username,
	    		'message'=>$message,
	    		'link'=>$link,
		    );
			$result_curl = $this->native_curl_post($notif_url,$array_post);
			return $result_curl;
    }

    function native_curl_get($url,$array_post=array()){
    	$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $url);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
			if(!empty($array_post)){
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $array_post);
			}
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER,array("Expect:"));
			$buffer = curl_exec($curl_handle);
			$info = curl_getinfo($curl_handle);
			curl_close($curl_handle);
			if($info['http_code'] == 200){
				return $buffer;
			}else {
				return false;
			}
    }
    function native_curl_post($url,$array_post){
    	$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $url);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl_handle, CURLOPT_POST, TRUE);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $array_post);
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER,array("Expect:"));
			// Optional, delete this line if your API is open or using another method of authentification
			//curl_setopt($curl_handle, CURLOPT_USERPWD, $username . ':' . $password);

			$buffer = curl_exec($curl_handle);
			$info = curl_getinfo($curl_handle);
			if($info['http_code'] == 200){
				return $buffer;
			}else {
				return false;
			}
			curl_close($curl_handle);
			return $buffer;
    }

}
/* End of file gempon.php */