<?php
/**
* 
* 	@version 	1.0.2  November 10, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.helper');

class GetbibleViewImport extends JViewLegacy
{
	/**
	 * @var bool import success
	 */
	protected $import;
	protected $versions;
	protected $params;

	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		// Initialise variables.
		$this->import	= $this->get('Import');
		$this->versions	= $this->get('Versions');// Get app Params
		$this->params 	= JComponentHelper::getParams('com_getbible');
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			ContentHelper::addSubmenu('import');
			$this->sidebar = JHtmlSidebar::render();
			$this->addToolbar();
		}
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{

		$canDo = JHelperContent::getActions('com_getbible', 'getbible');
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JHtml::stylesheet('com_getbible'.DS.'admin.stylesheet.css', array(), true, false, false);
		
		JToolBarHelper::title(JText::_('COM_GETBIBLE_IMPORT'), 'box-add import');
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_getbible');
		}

		
	}
}
