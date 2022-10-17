<?php
namespace Jnilla\Lara\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Object\CMSObject as JObject;
use Joomla\CMS\Toolbar\ToolbarHelper as JToolbarHelper;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Filesystem\Folder as JFolder;
use Box\Spout\Common\Exception\EncodingConversionException;

/**
 * Base helper class
 */
class Base{
    
    /**
     * Auto register files from a path
     *
     * @param string $path
     *      Helpers folder path
     * 
     * return void
     */
    public static function autoRegister($path){
    	if(!JFolder::exists($path)){
    		return;
    	}
    	
        $helpers = JFolder::files($path, '.php');
        foreach($helpers as $helper){
            $helper = basename($helper, '.php');
            \JLoader::register($helper, "$path/$helper.php");
        }
    }
    
	/**
	 * Prepares the framework configurations
	 *
	 * @param array $customFrameworkConfigurations
	 *         $customFrameworkConfigurations will be merged 
	 *         with $frameworkConfigurations. Use this option to 
	 *         merge configuration and override configurations with 
	 *         the same name.
	 */
	public static function prepareFrameworkConfigurations($customFrameworkConfigurations = []){
		$frameworkConfigurations = [];
		
		// Load framework configurations
		$frameworkConfigurations = self::loadFrameworkConfigurations();
		
		// Merge $customFrameworkConfigurations into $frameworkConfigurations
		$frameworkConfigurations = array_merge_recursive($customFrameworkConfigurations, $frameworkConfigurations);

		// Add current, singular and plural names
		if(isset($frameworkConfigurations['currentClassName']) && isset($frameworkConfigurations['isList'])){
			// Add current name
			$frameworkConfigurations['currentNameInPascalCase'] = self::getNameFromClassName($frameworkConfigurations['componentNameInPascalCase'], $frameworkConfigurations['currentClassName']);

			if($frameworkConfigurations['isList']){
				// Add singular name
				if(!isset($frameworkConfigurations['singularNameInPascalCase'])){
					$frameworkConfigurations['singularNameInPascalCase'] = self::inflectNameToSingular($frameworkConfigurations['currentNameInPascalCase']);
				}
				// Add plural name
				if(!isset($frameworkConfigurations['pluralNameInPascalCase'])){
					$frameworkConfigurations['pluralNameInPascalCase'] = $frameworkConfigurations['currentNameInPascalCase'];
				}
			}else{
				// Add singular name
				if(!isset($frameworkConfigurations['singularNameInPascalCase'])){
					$frameworkConfigurations['singularNameInPascalCase'] = $frameworkConfigurations['currentNameInPascalCase'];
				}
				// Add plural name
				if(!isset($frameworkConfigurations['pluralNameInPascalCase'])){
					$frameworkConfigurations['pluralNameInPascalCase'] = self::inflectNameToPlural($frameworkConfigurations['currentNameInPascalCase']);
				}
			}
		}

		// Add letter case types variations for the following prefixes
		$prefixes = array(
			'componentName',
			'singularName',
			'pluralName',
			'currentName',
		);
		foreach($prefixes as $prefix){
			if(isset($frameworkConfigurations["{$prefix}InPascalCase"])){
				$variations = self::getLetterCaseTypesVariationsFromPascalCaseText($prefix, $frameworkConfigurations["{$prefix}InPascalCase"]);
				$frameworkConfigurations = array_merge_recursive($variations, $frameworkConfigurations);
			}
		}

		// Add reference to current client helper
		if(JFactory::getApplication()->isClient('site')){
			$frameworkConfigurations['helper'] = "Jnilla\Lara\Helper\Site";
		}else{
			$frameworkConfigurations['helper'] = "Jnilla\Lara\Helper\Admin";
		}

		// Add current class configurations to the configurations root for easy variable extraction
		if(isset($frameworkConfigurations['currentClassName'])){
			if(isset($frameworkConfigurations[$frameworkConfigurations['currentClassName']])){
				$frameworkConfigurations = array_merge_recursive($frameworkConfigurations[$frameworkConfigurations['currentClassName']], $frameworkConfigurations);
			}
		}

		// Sort array for aesthetic reasons
		ksort($frameworkConfigurations);

		return $frameworkConfigurations;
	}

	/**
	 * Load framework configurations
	 *
	 * @return    array    Framework configuration
	 */
	public static function loadFrameworkConfigurations(){
		// Load current framework configurations file (current as in frontend or backend depending on JPATH_COMPONENT value)
		$filePath = JPATH_COMPONENT.'/framework-configurations.php';

		if(file_exists($filePath)){
			require $filePath;
			return $frameworkConfigurations;
		}
	}

	/**
	 * Get letter case types variations from pascal case text
	 *
	 * @param    array     $variationNamePrefix    Prefix used to create the names of the variations
	 * @param    string    $textInPascalCase
	 *
	 * @return    array    Array with case types variations
	 */
	public static function getLetterCaseTypesVariationsFromPascalCaseText($variationNamePrefix, $textInPascalCase){
		$variations = [];
		$variations[$variationNamePrefix."InCamelCase"] = lcfirst($textInPascalCase);
		$variations[$variationNamePrefix."InLowerCase"] = strtolower($textInPascalCase);
		$variations[$variationNamePrefix."InUpperCase"] = strtoupper($textInPascalCase);
		return $variations;
	}

	/**
	 * Get the name from the class name
	 *
	 * @param     string    $componentNameInPascalCase    Component name in pascal case
	 * @param     string    $currentClassNameInPascalCase    Class Name in pascal case
	 *
	 * @return    string    Name in pascal case
	 */
	public static function getNameFromClassName($componentNameInPascalCase, $currentClassNameInPascalCase){
		$result1 = preg_replace("/^{$componentNameInPascalCase}(?:Model|View|Controller|Table)/i", '', $currentClassNameInPascalCase);
		// $result2 = preg_replace("/List$/i", '', $result1);

		// if($result2 === '') $result2 = $result1;

		// return $result2;
		return $result1;
	}

	/**
	 * Inflect a plural name to the singular version
	 *
	 * @param     string    $name    Plural Name
	 *
	 * @return    string    Singular name
	 */
	public static function inflectNameToSingular($pluralName){
		// TODO: This is a simple hack. Replace with word inflector
		return preg_replace("/s$/i", '', $pluralName);
	}

	/**
	 * Inflect a singular name to the plural version
	 *
	 * @param     string    $singularName    Singular Name
	 *
	 * @return    string    Plural name
	 */
	public static function inflectNameToPlural($singularName){
		// TODO: This is a simple hack. Replace with word inflector
		return $singularName.'s';
	}

	/**
	 * Generate context string
	 *
	 * @param     array    $parts    Optional parts to build the context string
	 *
	 * @return    array    Context string
	 */
	public static function generateContextString($parts = array())
	{
		$app = JFactory::getApplication();
		$names = array(
			'app',
			'option',
			'model',
			'view',
			'layout',
			'tmpl',
			'lang',
			'forcedlang',
			'menuitem',
		);

		// app: Joomla application name (site, administrator)
		if(!isset($parts['app']))
		{
			$parts['app'] = $app->getName();
		}

		// option: If not set guess the component name from the request
		if(!isset($parts['option']))
		{
			$parts['option'] = $app->input->get('option', '', 'cmd');
		}

		// model
		if(isset($parts['model']))
		{
			$parts['model'] = $app->input->get('view', '', 'cmd');
		}

		// view
		if(!isset($parts['view']))
		{
			$parts['view'] = $app->input->get('view', '', 'cmd');
		}

		// layout: Usefull for modal windows.
		if(!isset($parts['layout']))
		{
			$value = $app->input->get('layout', null, 'cmd');
			if(isset($value))
			{
				$parts['layout'] = $value;
			}
		}

		// tmpl: Usefull for modal windows
		if(!isset($parts['tmpl']))
		{
			$value = $app->input->get('tmpl', null, 'cmd');
			if(isset($value))
			{
				$parts['tmpl'] = $value;
			}
		}

		// menuitem
		if(!isset($parts['menuitem']))
		{
			if($parts['app'] === 'site')
			{
				$menuItem = $app->getMenu()->getActive();
				if(empty($menuItem)) $menuItem = $app->getMenu()->getDefault();
				$parts['menuitem'] = $menuItem->id;
			}
		}

		// lang: Current language
		if(!isset($parts['lang']))
		{
			$parts['lang'] = $app->getLanguage()->getTag();
		}

		// forcedlang: Usefull when languages are forced
		if(!isset($parts['forcedlang']))
		{
			$value = $app->input->get('forcedLanguage', null, 'cmd');
			if(isset($value))
			{
				$parts['forcedlang'] = $app->getLanguage()->getTag();
			}
		}

		// Generate the context string
		$context = array();
		foreach($names as $name)
		{
			if(isset($parts[$name]))
			{
				$context[] = $name.':'.$parts[$name];
			}
		}

		return implode('.', $context);
	}



	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   JView  $view  Views object.
	 *
	 * @return  void
	 *
	 */
	public static function getActions(&$view){
		extract($view->frameworkConfigurations);

		$user = JFactory::getUser();
		$result = new JObject();
		$xml = JFactory::getXml(JPATH_ADMINISTRATOR."/components/com_$componentNameInLowerCase/access.xml", true);
		$actions = $xml->section->action;

		foreach($actions as $action){
			$action = (string)$action['name'];
			$result->set($action, $user->authorise($action, $componentNameInLowerCase));
		}

		return $result;
	}

	/**
	 * Merge two arrays and remove duplicates
	 *
	 * @param    array  $array1  An array
	 * @param    array  $array2  An array
	 *
	 * @return   Array    Resulting array
	 *
	 */
	public static function arrayMergeAndRemoveDuplicates($array1, $array2){
		if(!isset($array1) || !is_array($array1)) $array1 = array();
		if(!isset($array2) || !is_array($array2)) $array2 = array();

		return array_unique(array_merge($array1, $array2));
	}

	/**
	 * Creates a list of form field names
	 *
	 * @param   JForm   $form       Form object
	 * @param   string  $groupName  (optional) Form group name. If not set return all fields
	 * @param   array   $exclude    (Optional) Array of names to exclude
	 *
	 * @return    Array    Array of form fields
	 *
	 */
	public static function listFormFieldsNames($form, $groupName = "", $exclude = array()){
		$array = array();
		$fields = $form->getGroup($groupName);

		foreach($fields as $field){
			$name = $field->getAttribute('name');

			if(in_array($name, $exclude)){
				continue;
			}

			$array[] = $field->getAttribute('name');
		}

		return $array;
	}

	/*
	 * Gets the current relative URI path
	 *
	 * return    string    Current relative URI path
	 */
	static function getRelativeUriPath(){
		$baseUri = JUri::base();
		$baseUri = preg_quote($baseUri, '/');
		$currentUri = JUri::current();

		return preg_replace("/^$baseUri/", '', $currentUri, 1);
	}
}


