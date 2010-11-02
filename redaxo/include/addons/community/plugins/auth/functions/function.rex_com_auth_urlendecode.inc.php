<?php

function rex_com_auth_urlencode($url)
{
	return base64_encode($url);
}

function rex_com_auth_urldecode($url)
{
	return base64_decode($url);
}

?>