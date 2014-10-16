<?php
/**
* 
* 	@version 	1.0.1  August 16, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.helper');

class GetbibleViewGetbible extends JViewLegacy
{
	/**
	 * @var bool import success
	 */
	protected $params;
	protected $versions;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->versions	= $this->get('AvailableVersions');
		// Get app Params
		$this->params 	= JComponentHelper::getParams('com_getbible');
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			ContentHelper::addSubmenu('getbible');
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
	public function addToolbar()
	{	
		$canDo = JHelperContent::getActions('com_getbible', 'getbible');
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JHtml::stylesheet('com_getbible'.DIRECTORY_SEPARATOR.'admin.stylesheet.css', array(), true, false, false);
		
		JToolBarHelper::title(JText::_('COM_GETBIBLE'), 'book getbible');
		// JToolBarHelper::custom('setupCpanel', 'cog', '', JText::_('COM_GETBIBLE_SETUP_CPANEL'), false);
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_getbible');
		}
	}
}