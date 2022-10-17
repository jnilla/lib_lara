<?php
defined('_JEXEC') or die;

require __DIR__."/../../vendor/autoload.php";

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\MVC\Controller\BaseController as JControllerLegacy;
use Jnilla\Lara\Helper\Base as BaseHelper;

extract(BaseHelper::prepareFrameworkConfigurations());

JFactory::getLanguage()->load('lib_lara', JPATH_LIBRARIES."/lara");

BaseHelper::autoRegister(JPATH_COMPONENT_ADMINISTRATOR."/helpers");

JLoader::register("{$componentNameInPascalCase}Controller", JPATH_COMPONENT.'/controller.php');

$controller = JControllerLegacy::getInstance($componentNameInPascalCase);
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
