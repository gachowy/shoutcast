<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Shoutcast {

	protected static $config;
	static $array = '';
	static $xml;
	static $value;
	static $index;


	
	public static function config($arg = '') 
	{
		if (!self::$config) {
			self::$config = Kohana::config('shoutcast')->as_array();
		}
		return (!empty($arg)? Arr::get(self::$config, $arg) : self::$config);	
	}

	public static function connect()
	{

		$fp = fsockopen(self::$config['host'], self::$config['port'], $errno, $errstr, 10);  
		if($fp) 
		{
			fputs($fp, "GET /admin.cgi?pass=".self::$config['password']."&mode=viewxml HTTP/1.0\r\n");
			fputs($fp, "User-Agent: Mozilla\r\n\r\n"); 
			
                    	while(!feof($fp)) { 
       				$xml = trim(substr(fread($fp, 1024 * 6), 42)); 
				$xmlparser = xml_parser_create(); 
            			if (!xml_parse_into_struct($xmlparser, $xml, self::$value, self::$index)) { 
               				return "Unparsable XML";  
            			} 
       			 	xml_parser_free($xmlparser);
			}
				
            				
			
		} else {
			return 'false';
		}
		fclose($fp); 

	}
	public static function get($key=NULL)
	{
		$result = '';
		if(is_array($key))
		{
			foreach($key as $item)
			{
				
				$result[$item] = self::$value[self::$index["$item"][0]]["value"];
			}
		
		} else {
			$result = self::$value[self::$index[$key][0]]["value"];
		}
		return $result;		


	}
}

	
