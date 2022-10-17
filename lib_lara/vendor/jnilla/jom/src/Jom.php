<?php
defined('_JEXEC') or die();

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Application\WebApplication as JApplicationWeb;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Filesystem\File as JFile;
use Joomla\CMS\Filesystem\Folder as JFolder;

/**
 * Jom is a Joomla API facade
 */
class Jom
{
	/**
	 * Dumps the given variables and ends the execution of the script
	 *
	 * @param mixed $args
	 * 		Variables to dump
	 *
	 * @return array
	 */
	public static function dd(...$args){
		$trace = debug_backtrace();
		$trace = __FUNCTION__.":".$trace[0]['file'].":".$trace[0]['line'];
		
		foreach($args as $arg){
			echo "<pre style=\"border: 2px solid #000; padding: 5px; background: lightyellow;\">";
			echo "<small style=\"color: blue; font-weight: bold;\">$trace</small><br /><br />";
			echo var_dump($arg);
			echo "</pre>";
		}
		
		die;
	}
	
	/**
	 * Dumps the given variables
	 *
	 * @param mixed $args
	 * 		Variables to dump
	 *
	 * @return array
	 */
	public static function dump(...$args){
		$trace = debug_backtrace();
		$trace = __FUNCTION__.":".$trace[0]['file'].":".$trace[0]['line'];
		
		foreach($args as $arg){
			echo "<pre style=\"border: 2px solid #000; padding: 5px; background: lightyellow;\">";
			echo "<small style=\"color: blue; font-weight: bold;\">$trace</small><br /><br />";
			echo var_dump($arg);
			echo "</pre>";
		}
	}

	/**
	 * Retuns an application object
	 *
	 * @param mixed $args
	 * 		Variables to dump
	 *
	 * @return array
	 */
	public static function app(){
		return JFactory::getApplication();
	}

	/**
	 * Retuns a document object
	 *
	 * @param mixed $args
	 * 		Variables to dump
	 *
	 * @return array
	 */
	public static function doc(){
		return JFactory::getDocument();
	}

	/**
	 * Get data from the request
	 *
	 * @param string|null $key
	 *	  Variable key
	 * @param string|array|null $default
	 *	  Default value
	 * @param string $filter
	 *	  Filter
	 * @return string|array|null
	 * 		Variable value
	 */
	public static function req($key = null, $default = null, $filter = 'STRING'){
		return self::app()->input->get($key, $default, $filter);
	}

	/**
	 * Does a 302 redirect to another URL.
	 *
	 * @param   string   $url 
	 *     The URL to redirect to. Accepts relative and absolute URLs
	 *     If URL is null redirects to current URL
	 *
	 * @return  void.
	 *
	 */
	public static function redirect($url = null){
		if($url === null){
			$url = self::absoluteUrl();
		}

		$url = trim($url);
		$url = ltrim($url, '/');
		$app = self::app();

		preg_match('/^https?:/i', $url, $result);
		if(empty($result)){
			$app->redirect(self::baseUrl()."/$url");
		}else{
			$app->redirect($url);
		}
	}

	/**
	 * Returns the site base URL
	 *
	 * @return string The site base URL.
	 *
	 */
	public static function baseUrl(){
		return JUri::base();
	}

	/**
	 * Returns the site relative URL
	 *
	 * @return string The site base URL.
	 *
	 */
	public static function relUrl(){
		return JUri::base(true)."/";
	}

	/**
	 * Returns the site absolute URL
	 *
	 * @return string The site base URL.
	 *
	 */
	public static function absUrl(){
		return JUri::getInstance()->toString();
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param   string  $msg   The message to enqueue.
	 * @param   string  $type  The message type. Default is message.
	 *
	 * @return  void
	 */
	public static function message($msg, $type = 'message'){
		self::app()->enqueueMessage($msg, $type);
	}

	/**
	 * Translates a language string into the current language.
	 *
	 * @param   string   $string The string to translate.
	 *
	 * @return  string  The translated string
	 */
	public static function trans($string){
		return JText::_($string);
	}

	/**
	 * Truncates a string.
	 *
	 * @param   string   $string The string to truncate.
	 * @param   integer  $maxLength String max length.
	 *
	 * @return  string  The truncated string
	 */
	public static function truncate($string, $maxLength = 150){
		return JHtml::_('string.truncate', strip_tags($string), $maxLength);
	}
	
	/**
	 * Sends a JSON response and ends the execution of the script
	 *
	 * @param mixed $data
	 *	  The data to send
	 * @return void
	 */
	public static function jsonRes($data = null, $pretty = false){
		header('Content-Type: application/json');
		if($pretty){
			echo json_encode($data, JSON_PRETTY_PRINT);
		}else{
			echo json_encode($data);
		}
		die;
	}

	/**
	 * Retuns true if the current application is the backend
	 *
	 * @return boolean
	 */
	public static function isBackend(){
		return self::app()->getClientId() == 1;
	}

	/**
	 * Retuns true if the current client is a mobile device
	 *
	 * @return boolean
	 */
	public static function isMobile(){
		return JApplicationWeb::getInstance()->client->mobile;
	}

	/**
	 * Add a stylesheet to the document.
	 *
	 * @param string $url
	 *     Stylesheet file URL.
	 *
	 * @return array
	 */
	public static function css($url){
		JHtml::stylesheet($url);
	}

	/**
	 * Add an inline style declaration to the document.
	 *
	 * @param string $style
	 *     Style declaration to add.
	 *
	 * @return array
	 */
	public static function inlineCss($style){
		self::doc()->addStyleDeclaration($style);
	}

	/**
	 * Add a script to the document.
	 *
	 * @param string $url
	 *     Script file URL.
	 *
	 * @return array
	 */
	public static function js($url){
		JHtml::script($url);
	}

	/**
	 * Add an inline style declaration to the document.
	 *
	 * @param string $script
	 *     Script declaration to add.
	 *
	 * @return array
	 */
	public static function inlineJs($script){
		self::doc()->addScriptDeclaration($script);
	}
	
	
	
	
	
	
	
	
	/**
	 * Select database method
	 *
	 * @param String $query
	 * 		Query string
	 * @param Array $bindings
	 * 		Query string bindings
	 *
	 * @return Array
	 */
	public static function select($query, $bindings = []){
		$db = JFactory::getDBO();
		
		// Set Query
		$query = self::prepareQueryBindings($query, $bindings);
		$db->setQuery($query);

		// Execute and return
		return $db->loadAssocList();
	}
	
	/**
	 * Select database method
	 *
	 * @param String $query
	 * 		Query string
	 * @param Array $bindings
	 * 		Query string bindings
	 *
	 * @return Array
	 */
	public static function selectOne($query, $bindings = []){
		$db = JFactory::getDBO();

		// Set Query
		$query = self::prepareQueryBindings($query, $bindings);
		$db->setQuery($query);

		// Execute and return
		return $db->loadAssoc();
	}
	
	/**
	 * Prepare the query bindings
	 *
	 * @param String $query 
	 * 		Query string
	 * @param Array $bindings 
	 * 		Query string bindings
	 *
	 * @return Array
	 */
	public static function prepareQueryBindings($query, $bindings = []){
		$db = JFactory::getDBO();
		$time = microtime(true);
		
		if(!empty($bindings)){
			// Note: We can't replace the query params with the preg_replace() 
			// because this function evaluates escaped characters passed 
			// in the $replacement argument which causes undesired results.
			// The function str_replace() does not evaluates escaped 
			// characters passed in the $replacement argument.
			
			// Prepare placeholders
			foreach($bindings as $bindingKey => $bindingValue){
				$placeholder = "@@-placeholder-$bindingKey-$time-@@";
				$bindingKey = preg_quote($bindingKey, '/');
				$query = preg_replace('/\:'.$bindingKey.'\b/', $placeholder, $query);
			}

			// Replace placeholders
			foreach($bindings as $bindingKey => $bindingValue){
				$placeholder = "@@-placeholder-$bindingKey-$time-@@";
				$bindingValue = $db->escape($bindingValue);
				$query= str_replace($placeholder, $bindingValue, $query);
			}
		}

		return "$query;";
	}
	
	/**
	 * Update database method
	 *
	 * @param String $query
	 * 		Query string
	 * @param Array $bindings
	 * 		Query string bindings
	 *
	 * @return Integer
	 * 		Number of effected rows
	 */
	public static function update($query, $bindings = []){
		$db = JFactory::getDBO();
		
		// Set Query
		$query = self::prepareQueryBindings($query, $bindings);
		$db->setQuery($query);

		// Execute
		$db->execute();
		
		// Return
		return $db->getAffectedRows();
	}
	
	/**
	 * Determine if any rows exist
	 *
	 * @param String $query
	 * 		Query string
	 * @param Array $bindings
	 * 		Query string bindings
	 *
	 * @return Array
	 */
	public static function exists($query, $bindings = []){
		$db = JFactory::getDBO();
		
		// Set Query
		$query = self::prepareQueryBindings($query, $bindings);
		$db->setQuery($query);
		
		// Execute and return
		return $db->loadAssocList();
	}

	/**
	 * Insert database method
	 *
	 * @param String $query
	 * 		Query string
	 * @param Array $bindings
	 * 		Query string bindings
	 *
	 * @return Boolean
	 * 		True on success
	 */
	public static function insert($query, $bindings = []){
		$db = JFactory::getDBO();
		
		// Set Query
		$query = self::prepareQueryBindings($query, $bindings);
		$db->setQuery($query);

		// Execute and return
		return ($db->execute() !== false) ? true : false;
	}

	/**
	 * Delete database method
	 *
	 * @param String $query
	 * 		Query string
	 * @param Array $bindings
	 * 		Query string bindings
	 *
	 * @return Integer
	 * 		Number of effected rows
	 */
	public static function delete($query, $bindings = []){
		$db = JFactory::getDBO();
		
		// Set Query
		$query = self::prepareQueryBindings($query, $bindings);
		$db->setQuery($query);

		// Execute
		$db->execute();

		// Return
		return $db->getAffectedRows();
	}
	
}



