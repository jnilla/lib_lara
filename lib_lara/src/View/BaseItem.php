<?php
namespace Jnilla\Lara\View;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as JViewLegacy;
use Exception;
use Jnilla\Lara\Helper\Base as BaseHelper;
use Jnilla\Jom\Jom as Jom;

/**
 * Base item view class
 */
class BaseItem extends JViewLegacy{

	public $frameworkConfigurations = array();

	protected $state;

	protected $item;

	protected $form;

	protected $isNew;

	/**
	 * Constructor
	 */
	public function __construct($config = array()){
		// Set if this class is for a list
		$this->frameworkConfigurations['isList'] = false;
		
		// Get class name of current instantiated object
		$this->frameworkConfigurations['currentClassName'] = get_class($this);

		// Initialize framework configurations
		$this->frameworkConfigurations = BaseHelper::prepareFrameworkConfigurations($this->frameworkConfigurations);

		parent::__construct($config);
	}

	/**
	 * Display the view
	 */
	public function display($tpl = null){
		// Extract framework configurations
		extract($this->frameworkConfigurations);

		$model = $this->getModel();

		$this->state = $model->getState();
		$this->item = $model->getItem();
		$this->form = $model->getForm();
		$this->isNew = Jom::frGetFieldValue($this->form, 'id') === null;

		// Check for errors
		if(count($errors = $this->get('Errors'))){
			throw new Exception(implode("\n", $errors));
		}

		// Store list of form fields from framework configurations
		$formFields = $helper::listFormFieldsNames($this->form);
		$this->frameworkConfigurations["formFields"] = array();
		$this->frameworkConfigurations["formFields"] = $helper::arrayMergeAndRemoveDuplicates($this->frameworkConfigurations["formFields"], $formFields);

		parent::display($tpl);
	}
}




