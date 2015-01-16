<?php
/**
* 
* 	@version 	1.0.7  January 16, 2015
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

class GetbibleViewVersions extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $user;
	
	public function display($tpl = null)
	{	
		// check for updates
		GetHelper::update();
		
		if ($this->getLayout() !== 'modal')
		{
			ContentHelper::addSubmenu('versions');
		}
		
		// Get data from the model
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->user 		= JFactory::getUser();
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}
		
		parent::display($tpl);
	}

	public function addToolbar()
	{	
		$canDo = JHelperContent::getActions('com_getbible', 'version', $this->state->get('filter.id'));
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JHtml::stylesheet('com_getbible'.DS.'admin.stylesheet.css', array(), true, false, false);
		
		JToolBarHelper::title(JText::_('COM_GETBIBLE_VERSIONS_TITLE'), 'book versions');
		
		if ($canDo->get('core.create')) {
			// JToolBarHelper::addNew('version.add');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('version.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();

			JToolBarHelper::publishList('versions.publish');
			JToolBarHelper::unpublishList('versions.unpublish');
	
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('versions.archive');
				
			if ($canDo->get('core.admin')) {
				
				JToolBarHelper::checkin('versions.checkin');
			}
		}

		if ($canDo->get('core.delete')) {

			if ($this->state->get('filter.published') == '-2') {
				JToolBarHelper::deleteList('', 'versions.delete');
			} else {
				JToolBarHelper::trash('versions.trash');
			}
		}
		
		JToolBarHelper::divider();
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_getbible');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_getbible&view=versions');
		
		// set language selection
		$result = $this->getLanguages();
		if($result){
			JHtmlSidebar::addFilter(
			JText::_('COM_GETBIBLE_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', $result, 'value', 'text', $this->state->get('filter.language'))
			);
		}

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'published' 	=> JText::_('JSTATUS'),
			'name' 			=> JText::_('COM_GETBIBLE_VERSION_NAME'),
			'testament' 	=> JText::_('COM_GETBIBLE_TESTAMENT'),
			'version' 		=> JText::_('COM_GETBIBLE_VERSION'),
			'language' 		=> JText::_('COM_GETBIBLE_LANGUAGE'),
			'created_by' 	=> JText::_('COM_GETBIBLE_CREATED_BY'),
			'created_on' 	=> JText::_('COM_GETBIBLE_CREATED_ON'),
			'id' 			=> JText::_('JGRID_HEADING_ID')
		);
	}
	
	protected function getLanguages()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Order it by the ordering field.
		$query->select('language');
		$query->from($db->quoteName('#__getbible_versions'));
		$query->order('language ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		
		$results = $db->loadColumn();
		
		if ($results){
			$results = array_unique($results);
			$options = array();
			foreach ($results as $language){
				$options[]  = JHtml::_('select.option', $language, $language);
			}
			return $options;
		}
		return false;
	}
}