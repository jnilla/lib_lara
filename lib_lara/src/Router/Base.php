<?php
namespace Jnilla\Lara\Router;

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterBase as JComponentRouterBase;

/**
 * Router base class
 *
 */
class Base extends JComponentRouterBase
{
	/**
	 * Class constructor.
	 *
	 * @param   JApplicationCms  $app   Application-object that the router should use
	 * @param   JMenu            $menu  Menu-object that the router should use
	 *
	 */
	public function __construct($app = null, $menu = null)
	{
		// Get class name of the class extending from us
		$componentNameInPascalCase = static::class;

		$componentNameInPascalCase =  preg_replace("/Router$/", '', $componentNameInPascalCase, 1);
// 		$componentNameInPascalCase = $this->componentNameInPascalCase;
		$componentNameInLowerCase = strtolower($componentNameInPascalCase);
		$componentPath = JPATH_BASE."/components/com_$componentNameInLowerCase";

		\JLoader::registerPrefix($componentNameInPascalCase, $componentPath);

		parent::__construct($app, $menu);
	}

	/**
	 * Build method for URLs
	 * This method is meant to transform the query parameters into a more human
	 * readable form. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 */
	public function build(&$query){
		$segments = array();

		// 1st segment is the view
		if (isset($query['view'])){
			$segments[] = $query['view'];
			unset($query['view']);
		}

		// 2nd segment is the id
		if (isset($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}

		return $segments;
	}

	/**
	 * Parse method for URLs
	 * This method is meant to transform the human readable URL back into
	 * query parameters. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 */
	public function parse(&$segments){
		$vars = array();

		// 1st segment is the view
		$vars['view'] = $segments[0];

		// 2nd segment is the id
		if(isset($segments[1]))
		{
			$vars['id'] = $segments[1];
		}

		return $vars;
	}
}

