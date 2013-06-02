<?php 
/*
 *  @autor: JSM
 *  @created 02.06.2013
 *  @version 1.0.0
 *  
 */
  	
class rex_security {	

	protected static $expire = '360'; 	
	protected static $token_name = 'csrf_token';	
	protected static $cookie_name = 'csrf_token';	
	protected static $csrfHash	= '';
	protected static $_config = array('EXPIRE', 'TOKEN_NAME', 'COOKIE_NAME');	
		
	
	public function __construct()
	{
		global $REX;
		
		if ($REX['CSRF']['status'] === TRUE)
		{		
			foreach(self::$_config as $key)
			{
				$prefix = "";
				$strtolower = strtolower($key);
				
				if(in_array($key, array('TOKEN_NAME', 'COOKIE_NAME')))				
					$prefix = (!empty($REX['TABLE_PREFIX']))? $REX['TABLE_PREFIX'] : ""; 				
				
				if(!empty($REX['CSRF'][$key]) || $REX['CSRF'][$key] != '')
				{					
					self::${$strtolower} = (self::${$strtolower} == $REX['CSRF'][$key])? $prefix.self::${$strtolower} : $prefix.$REX['CSRF'][$key];
				}else
				{
					self::${$strtolower} = $prefix.self::${$strtolower};
				}
				
			}
				
			self::setRexCsrfHash();
		}
	}
	
// --------------------------------------------------------------------	

	public static function csrfVerify()
	{
		global $REX, $I18N;
		
			if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
			{
				return self::setRexCookie();
			}
					
			if ( !isset($_POST[self::$token_name], $_COOKIE[self::$cookie_name]))
			{
				header('Location: /redaxo/index.php?rex_logout=1&csrf_message='.$I18N->msg('csrf_post'));
				exit;
			}
			
			if ($_POST[self::$token_name] != $_COOKIE[self::$cookie_name])
			{
				header('Location: /redaxo/index.php?rex_logout=1&csrf_message='.$I18N->msg('csrf_not_correct'));
				exit;
			}
	
			unset($_POST[self::$token_name]);		
			unset($_COOKIE[self::$cookie_name]);
			self::setRexCsrfHash();
			
			return self::setRexCookie();
		
	}
	
// --------------------------------------------------------------------

	protected static function setRexCookie()
	{
		global $REX, $I18N;
		
		$expire = time() + self::$expire;
		$domain = parse_url($REX['SERVER'], PHP_URL_HOST);

		$result = setcookie(self::$cookie_name, self::$csrfHash, $expire, "", $domain, 0);
		
		if(empty($result))
			self::csrfError($I18N->msg('csrf_cookie_error'));
		else 
			return rex_info($I18N->msg('csrf_cookie'));
	}

// --------------------------------------------------------------------

	protected static function setRexCsrfHash()
	{
		if (self::$csrfHash == '')
		{
			if (isset($_COOKIE[self::$cookie_name]) && preg_match('#^[0-9a-f]{32}$#iS', $_COOKIE[self::$cookie_name]) === 1 && empty($_POST[self::$token_name]))
			{
				return self::$csrfHash = $_COOKIE[self::$cookie_name];
			}
			

			return self::$csrfHash = md5(uniqid(rand(), TRUE));
		}

		return self::$csrfHash;
	}
// --------------------------------------------------------------------
	
	public static function csrfError($error)
	{
		echo rex_warning($error);		
	}

	// --------------------------------------------------------------------

	public static function getCsrfHash()
	{
		return self::$csrfHash;
	}
	public static function getHiddenInput()
	{		
		$input = '<input type="hidden" name="'.self::$token_name.'" value="'.self::$csrfHash.'" />';
		return $input;		
	}
	
	
} 
/*
 *  FUNCTIONS
 * 
 */
	function rex_paramsCrsf($params)
	{
		global $inputCRSF;
		$input = $inputCRSF;
		
		$output	=	$params['subject'];		
		$output	=	preg_replace('/<form(\s[^>]*)?>/i', '<form\\1>' . $input, $output);
		
					
		return $output;
	}
	
?>
