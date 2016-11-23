<?php


$statuses=["toot","toot!","TOOT!","TOOT","Toot!","Toot"];
$MY_STATUS = $statuses[array_rand($statuses)];

if(!isset($MY_CLIENT_ID) || !isset($MY_CLIENT_SECRET)){
	echo "<h1>Step 1: get client id + secret from /api/v1/apps</h1>";
	$url = 'https://mastodon.social/api/v1/apps';
	$data = array(
		'client_name' => 'MyFirstMastodonBot',
		'redirect_uris' => 'urn:ietf:wg:oauth:2.0:oob',
		'scopes' => 'read write follow',
		'response_type' => 'code'
	);

	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data)
	    )
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { echo "<p>ERROR</p>"; }

	echo "<p>";
	var_dump($result);
	echo "</p>";
	$result=json_decode($result, true);
	$MY_CLIENT_ID = $result["client_id"];
	$MY_CLIENT_SECRET = $result["client_secret"];
}

if(!isset($MY_ACCESS_TOKEN)){
	echo "<h1>Step 2: provide client id + secret + username + password to oath to get token with write access</h1>";
	$url = "https://mastodon.social/oauth/token";
	$data = array(
		'client_id' => $MY_CLIENT_ID,
		'client_secret' => $MY_CLIENT_SECRET,
		'grant_type' => "password",
		'username' => $MY_USERNAME,
		'password' => $MY_PASSWORD,
		'scope' => 'write',
		'response_type' => 'code'
	);

	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data)
	    )
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { echo "<p>ERROR</p>"; }

	echo "<p>";
	var_dump($result);
	echo "</p>";
	$result=json_decode($result, true);
	$MY_ACCESS_TOKEN = $result["access_token"];
}




echo "<h1>Step 3: use the token to write a status</h1>";
$url = "https://mastodon.social/api/v1/statuses";
$data = array(
	'status' => $MY_STATUS,
	'response_type' => 'code'
);

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer ".$MY_ACCESS_TOKEN."\r\nContent-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { echo "<p>ERROR</p>"; }

echo "<p>";
var_dump($result);
echo "</p>";
$result=json_decode($result, true);







/*echo "<h1>Step 3: use the token to do stuff!</h1>";
$url = "https://mastodon.social/api/v1/timelines/home";
$data = array(
	'response_type' => 'code'
);

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer ".$MY_ACCESS_TOKEN."\r\nContent-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'GET',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { echo "<p>ERROR</p>"; }

echo "<p>";
var_dump($result);
echo "</p>";
$result=json_decode($result, true);*/


?>
