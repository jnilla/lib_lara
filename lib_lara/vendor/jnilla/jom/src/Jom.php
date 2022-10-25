<?php
namespace Jnilla\Jom;

defined('_JEXEC') or die();

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Application\WebApplication as JApplicationWeb;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Application\CMSApplication as JApplicationCms;
use Joomla\CMS\Document\Document as JDocument;
use Joomla\CMS\Layout\LayoutHelper as JLayoutHelper;
use Joomla\CMS\Filesystem\Folder as JFolder;
use Joomla\CMS\Filesystem\File as JFile;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Session\Session as JSession;
use Joomla\CMS\Form\Form as JForm;

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
	 * @return JApplicationCms
	 */
	public static function app(){
		return JFactory::getApplication();
	}

	/**
	 * Retuns a global configuration value
	 *
	 * @param string $name
	 *     Configuration name
	 * 
	 * @return mixed
	 *     Configuration value
	 */
	public static function conf($name){
		return JFactory::getConfig()->get($name);
	}

	/**
	 * Retuns a document object
	 *
	 * @return JDocument
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
	public static function req($key = null, $default = null, $filter = 'cmd'){
		// Check if the key is trying to get the value from an array
		if(strpos($key, '[') !== false){
			$key = explode('[', $key);
			$key[1] = explode(']', $key[1])[0];
			$value = self::app()->input->get($key[0], $default, $filter);
			return isset($value[$key[1]]) ? $value[$key[1]] : null;
		}

		return self::app()->input->get($key, $default, $filter);
	}

	/**
	 * Does a 302 redirect to another URL.
	 *
	 * @param string $url 
	 *     The URL to redirect to. Accepts relative and absolute URLs
	 *     If URL is null redirects to current URL
	 *
	 * @return void
	 */
	public static function redirect($url = null){
		if($url === null){
			$url = self::absoluteUrl();
		}

		$url = trim($url);
		$url = ltrim($url, '/');

		preg_match('/^https?:/i', $url, $result);
		if(empty($result)){
			self::app()->redirect(self::baseUrl()."$url");
		}else{
			self::app()->redirect($url);
		}
	}

	/**
	 * Returns the JUri object
	 *
	 * @return JUri
	 */
	public static function uri(){
		return JUri::getInstance();
	}

	/**
	 * Returns the site base URL
	 *
	 * @return string
	 *     The site base URL.
	 */
	public static function baseUrl(){
		return JUri::base();
	}

	/**
	 * Returns the site relative URL
	 *
	 * @return string
	 *     The site base URL.
	 */
	public static function relativeUrl(){
		return JUri::base(true)."/";
	}

	/**
	 * Returns the site absolute URL
	 *
	 * @return string
	 *     The site base URL.
	 */
	public static function absoluteUrl(){
		return JUri::getInstance()->tostring();
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param string $msg
	 *     Message to enqueue.
	 * @param string $type
	 *     Message type [message|notice|warning|error].
	 *
	 * @return void
	 */
	public static function message($msg, $type = 'message'){
		self::app()->enqueueMessage($msg, $type);
	}

	/**
	 * Translates a language string into the current language and prints it to the output
	 *
	 * @param string $string
	 *     The string to translate.
	 *
	 * @return string 
	 *     The translated string
	 */
	public static function translate($string){
		return JText::_($string);
	}

	/**
	 * Truncates a string.
	 *
	 * @param string $string
	 *     string to truncate.
	 * @param integer $length
	 *     Truncation length.
	 *
	 * @return string
	 *     The truncated string
	 */
	public static function truncate($string, $length = 150){
		return JHtml::_('string.truncate', strip_tags($string), $length);
	}
	
	/**
	 * Sends a JSON response and exists the execution
	 *
	 * @param array|Object $data
	 *	  Data to send
	 * @return void
	 */
	public static function jres($data = null, $pretty = false){
		header('Content-Type: application/json');
		if($pretty){
			echo json_encode($data, JSON_PRETTY_PRINT);
		}else{
			echo json_encode($data);
		}
		die;
	}

	/**
	 * Retuns true if the user is visiting the backend
	 *
	 * @return boolean
	 */
	public static function isBackend(){
		return self::app()->getClientId() == 1;
	}

	/**
	 * Retuns true if the user is loading the page from a mobile device
	 *
	 * @return boolean
	 */
	public static function isMobile(){
		return JApplicationWeb::getInstance()->client->mobile;
	}

	/**
	 * Add a CSS file to the document
	 *
	 * @param string $url
	 *     CSS file URL.
	 *
	 * @return void
	 */
	public static function css($url){
		JHtml::stylesheet($url);
	}

	/**
	 * Add an inline CSS declaration to the document
	 *
	 * @param string $declaration
	 *     CSS declaration.
	 *
	 * @return void
	 */
	public static function inlineCss($declaration){
		self::doc()->addStyleDeclaration($declaration);
	}

	/**
	 * Add a JS file to the document.
	 *
	 * @param string $url
	 *     Script file URL.
	 */
	public static function js($url){
		JHtml::script($url);
	}

	/**
	 * Add an inline JS declaration to the document.
	 *
	 * @param string $script
	 *     JS declaration.
	 *
	 * @return array
	 */
	public static function inlineJs($declaration){
		self::doc()->addScriptDeclaration($declaration);
	}
	
	/**
	 * Returns the DB object
	 *
	 * @return JDatabaseDriver
	 */
	public static function db(){
		return JFactory::getDBO();
	}

	/**
	 * Executes a SQL query and returns all rows
	 *
	 * @param string $query
	 * 		Query string
	 * @param array $bindings
	 * 		Query string bindings
	 *
	 * @return array
	 *     array with results
	 */
	public static function query($query, $bindings = []){
		$db = self::db();
		
		// Set Query
		$query = self::prepareQueryBindings($query, $bindings);
		$db->setQuery($query);

		// Execute and return
		return $db->loadAssocList();
	}
	
	/**
	 * Executes a SQL query and returns the first row
	 *
	 * @param string $query
	 * 		Query string
	 * @param array $bindings
	 * 		Query string bindings
	 *
	 * @return array
	 */
	public static function queryOne($query, $bindings = []){
		$db = self::db();

		// Set Query
		$query = self::prepareQueryBindings($query, $bindings);
		$db->setQuery($query);

		// Execute and return
		$rows = $db->loadAssoc();

		return $db->loadAssoc();
	}
	
	/**
	 * Prepare the query bindings
	 *
	 * @param string $query 
	 * 		Query string
	 * @param array $bindings 
	 * 		Query string bindings
	 *
	 * @return array
	 */
	private static function prepareQueryBindings($query, $bindings = []){
		$db = self::db();
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
	 * Executes a SQL query and returns true if at least 1 row exist
	 *
	 * @param string $query
	 * 		Query string
	 * @param array $bindings
	 * 		Query string bindings
	 *
	 * @return array
	 */
	public static function queryExists($query, $bindings = []){
		return !!self::query($query, $bindings);
	}

	/**
	 * Executes a SQL query and returns the rows in chunks to a callback
	 * 
	 * Note: The query should not have LIMIT or OFFSET in it because this 
	 * function will add it automatically
	 *
	 * @param string $query
	 * 		Query string
	 * @param array $bindings
	 * 		Query string bindings
	 * @param integer $size
	 * 		Chunk size
	 * @param callable
	 * 		Callback with chunk rows. Return false to break the chunk loop.
	 *
	 * @return void
	 */
	public static function queryChunk($query, $bindings = [], $size, $callback){
		$offset = 0;
		do {
			$rows = self::query("$query \n LIMIT $size OFFSET $offset", $bindings);
			if(!$rows){
				return;
			}
			if($callback($rows) === false){
				return;
			}
			$offset = $offset+$size;
		} while ($rows);
	}

	/**
	 * Renders a Joomla layout
	 *
	 * @param string $layoutFile   
	 *     Dot separated path to the layout file, relative to base path
	 * @param mixed $displayData  
	 *     Object which properties are used inside the layout file to build displayed output
	 * @param string $basePath
	 *     Base path to use when loading layout files
	 * @param mixed $options
	 *     Optional custom options to load. Registry or array format
	 *
	 * @return string
	 *     Rendered layout
	 */
	public static function layout($layoutFile, $displayData = null, $basePath = '', $options = null){
		return JLayoutHelper::render($layoutFile, $displayData, $basePath, $options);
	}

	/**
	 * Translates a Joomla URL to a SEF URL
	 *
	 * @param string $url
	 *     Joomla URL.
	 *
	 * @return string
	 *     SEF URL
	 */
	public static function route($url){
		return JRoute::_($url, true, $tls = JRoute::TLS_IGNORE, false);
	}

	/**
	 * List folders
	 *
	 * @param string $path
	 *     Folder path
	 * @param boolean $recurse
	 *     List recursively
	 * @param boolean $pathinfo
	 *     Return the pathinfo of each item
	 *
	 * @return array
	 *     List of folders
	 */
	public static function folders($path, $recurse = false, $pathinfo = false){
		$items = JFolder::folders($path, '', $recurse, true);

		if($pathinfo){
			foreach ($items as &$item) {
				$path = $item;
				$item = pathinfo($path);
				$item['path'] = $path;
				unset($item['filename']);
			}
		}

		return $items;
	}

	/**
	 * List files
	 *
	 * @param string $path
	 *     Folder path
	 * @param boolean $recurse
	 *     List recursively
	 * @param boolean $pathinfo
	 *     Return the pathinfo of each item
	 *
	 * @return array
	 *     List of files
	 */
	public static function files($path, $recurse = false, $pathinfo = false){
		$items = JFolder::files($path, '', $recurse, true);

		if($pathinfo){
			foreach ($items as &$item) {
				$path = $item;
				$item = pathinfo($path);
				$item['path'] = $path;
			}
		}

		return $items;
	}

	/**
	 * Creates a zip file
	 * 
	 * Note: Destination file is deleted if exists.
	 *
	 * @param string $source
	 *     Source file or folder path
	 * @param string $destination
	 *     Zip file destination path. 
	 *     If not set the destination path will be calculated.
	 *     If destination is just a filename the destination path will be calculated.
	 *
	 * @return void
	 */
	public static function zip($source, $destination = ''){
		// Check if the php zip extension is loaded
		if (!extension_loaded('zip')) {
			throw new \ErrorException("PHP zip extension is not loaded", 500);
		}

		// Check if source exists
		if(!file_exists($source)){
			throw new \ErrorException("Source path does not exists: $source", 500);
		}

		// Calculate destination path if not set
		if($destination == ''){
			$destination = pathinfo($source);
			$destination = $destination['dirname'].'/'.$destination['filename'].'.zip';
		}

		// Calculate destination path if it is just a name
		if(count(explode('/', $destination)) === 1){
			$name = $destination;
			$destination = pathinfo($source);
			$destination = $destination['dirname'].'/'.$name;
		}

		// Check if destination folder path exists
		if(!file_exists(dirname($destination))){
			throw new \ErrorException("Destination folder path does not exists: ".dirname($destination), 500);
		}

		// Delete destination file if exists
		if(file_exists($destination)) {
			unlink ($destination); 
		}
		
		// Open the zip file
		$zip = new \ZipArchive();
		if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
			throw new \ErrorException("Zip file could not be created: $destination", 500);
		}

		// Add content
		if (is_dir($source) === true){
			$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

			foreach ($files as $file){
				$file = str_replace('\\', '/', $file);

				// Ignore "." and ".." folders
				if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) ){
					continue;
				}

				$file = realpath($file);

				if(is_dir($file) === true){
					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
				}elseif (is_file($file) === true){
					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
				}
			}
		}elseif(is_file($source) === true){
			$zip->addFromString(basename($source), file_get_contents($source));
		}

		// Close the zip file
		$zip->close(); 
	}

	/**
	 * Unzip a zip file
	 * 
	 * @param string $source
	 *     Zip file path
	 * @param string $destination
	 *     Destination folder path.
	 *     If not set the destination path will be calculated.
	 *
	 * @return void
	 */
	public static function unzip($source, $destination = ''){
		// Check if the php zip extension is loaded
		if (!extension_loaded('zip')) {
			throw new \ErrorException("PHP zip extension is not loaded", 500);
		}

		// Check if source exists
		if(!file_exists($source)){
			throw new \ErrorException("Source path does not exists: $source", 500);
		}

		// Calculate destination path if not set
		if($destination == ''){
			$destination = pathinfo($source);
			$destination = $destination['dirname'].'/'.$destination['filename'];
		}

		// Open the zip file
		$zip = new \ZipArchive();
		if (!$zip->open($source)) {
			throw new \ErrorException("Zip file could not be created: $destination", 500);
		}

		// Extract
		$zip->extractTo($destination);

		// Close the zip file
		$zip->close(); 
	}

	/**
	 * Creates a directory path recursively
	 * 
	 * @param string $path
	 *     New directory path
	 * @param integer $permissions
	 *     New directory permissions
	 *
	 * @return void
	 */
	public static function mkdir($path, $permissions = 0777){
		@mkdir($path, 0755, true);
	}

	/**
	 * Renders the form token to against CSRF attacks
	 * 
	 * @return void
	 *     Token field
	 */
	public static function formToken(){
		return JHtml::_('form.token');
	}
	
	/**
	 * Checks the form token to against CSRF attacks
	 * 
	 * @param boolean $terminate
	 *     If true the script will be terminated if the from token is invalid
	 * 
	 * @return boolean
	 *     Returns true if the from token is invalid
	 */
	public static function checkFormToken($terminate = true){
		$isValid= JSession::checkToken();

		if($terminate && !$isValid){
			die(self::translate('JINVALID_TOKEN_NOTICE'));
		}

		return $isValid;
	}

	/**
	 * Renders a form object fieldsets as tabs
	 *
	 * @param JForm $form
	 *     Form object
	 * @param boolean $excludeHiddenFields
	 *     If true hidden fields will be excluded
	 *
	 * @return string
	 *     Renders the form fieldsets as tabs
	 */
	public static function renderFieldsetsAsTabs($form, $excludeHiddenFields = false){
		$fieldsets = $form->getFieldsets();

		foreach ($fieldsets as $fieldset){
			$displaydata['items'][] = [
				'title' => Jom::translate($fieldset->label),
				'content' => Jom::rederFieldset($form, $fieldset->name, $excludeHiddenFields),
			];
		}
		return Jom::layout(
			'stateful_tabs', 
			$displaydata, 
			JPATH_LIBRARIES."/lara/assets/layouts"
		);
	}

	/**
	 * Renders a form object fieldset
	 *
	 * @param JForm $form
	 *     Form object
	 * @param string $fieldsetName
	 *     Fieldset name
	 * @param boolean $excludeHiddenFields
	 *     If true hidden fields will be excluded
	 *
	 * @return void
	 *     Renders the form fieldset to the output
	 */
	public static function rederFieldset($form, $fieldsetName, $excludeHiddenFields = false){
		$fields = $form->getFieldset($fieldsetName);
		ob_start();
		foreach ($fields as $field) {
			if($excludeHiddenFields && ($field->getAttribute("type") === 'hidden')){
				continue;
			}
			echo self::renderFieldOnce($form, $field->getAttribute("name"));
		}
		return ob_get_clean();
	}

	/**
	 * Renders a form field object to the output but only once
	 * 
	 * Note: The function "remembers" the fielods 
	 * to only render them once.
	 * 
	 * @param JForm $form
	 *     Form object
	 * @param string $fieldName
	 *     Form field name
	 *
	 * @return string
	 *     Renders a form field once
	 */
	public static function renderFieldOnce($form, $fieldName){
		$field = $form->getField($fieldName);

		// Skip if field was rendered before
		if($field->getAttribute('isFieldRendered') === 'true'){
			return;
		}

		// Mark field as rendered
		$form->setFieldAttribute($fieldName, 'isFieldRendered', 'true');

		// Render field
		return $field->renderField();
	}

	/**
	 * Renders all the hidden fields
	 * 
	 * @param JForm $form
	 *     Form object
	 *
	 * @return string
	 *     Renders the form hidden fields
	 */
	public static function renderHiddenFields($form){
		$fields = $form->getFieldset();
		foreach ($fields as $field) {
			if($field->getAttribute("type") !== 'hidden'){
				continue;
			}
			self::renderFieldOnce($form, $field->getAttribute("name"));
		}
	}

	/**
	 * Set a form field value
	 * 
	 * @param JForm $form
	 *     Form object
	 * @param string $name
	 *     Field name
	 * @param mixed $value
	 *     Value to set
	 *
	 * @return void
	 */
	public static function setFieldValue($form, $name, $value){
		$form->setValue($name, '', $value);
	}

	/**
	 * get a form field value
	 * 
	 * @param JForm $form
	 *     Form object
	 * @param string $name
	 *     Field name
	 *
	 * @return mixed
	 *     Field value
	 */
	public static function getFieldValue($form, $name){
		return $form->getValue($name);
	}

	/**
	 * Get a form field
	 * 
	 * @param JForm $form
	 *     Form object
	 * @param string $name
	 *     Field name
	 *
	 * @return JformField
	 *     Form field 
	 */
	public static function getField($form, $name){
		return $form->getField($name);
	}

	/**
	 * Add a new option to list fields
	 * 
	 * @param JForm $form
	 *     Form object
	 * @param string $value
	 *     new option value
	 * @param string $text
	 *     New option text
	 *
	 * @return void
	 */
	public static function addFieldOption($form, $name, $value, $text){
		$field = self::getField($form, $name);
		$field->addOption($text, ['value' => $value]);
	}

	/**
	 * Copy a file or folder
	 * 
	 * @param string $source
	 *     Source path
	 * @param string $destination
	 *     Destination path
	 *
	 * @return void
	 */  
	public static function copy($source, $destination){
		self::mkdir(dirname($destination));

		if(is_file($source)){
			JFile::copy($source, $destination);
		}else{
			JFolder::copy($source, $destination, null, true);
		}
	}

	/**
	 * Move or rename a file or folder
	 * 
	 * @param string $source
	 *     Source path
	 * @param string $destination
	 *     Destination path
	 *
	 * @return void
	 */  
	public static function move($source, $destination){
		self::mkdir(dirname($destination));

		if(is_file($source)){
			JFile::move($source, $destination);
		}else{
			JFolder::move($source, $destination);
		}
	}

	/**
	 * Deletes a file or folder
	 * 
	 * @param string $path
	 *     File path
	 *
	 * @return void
	 */  
	public static function delete($path){
		if(is_file($path)){
			JFile::delete($path);
		}else{
			JFolder::delete($path);
		}
	}
}



