<?php
/**
* 
* 	@version 	1.0.9  June 24, 2016
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

class GetbibleViewSetbooks extends JViewLegacy
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
			ContentHelper::addSubmenu('setbooks');
		}
		
		// Get data from the model
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state		= $this->get('State');
		//$this->filterForm	= $this->get('FilterForm');
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
		$canDo = JHelperContent::getActions('com_getbible', 'setbook', $this->state->get('filter.id'));
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JHtml::stylesheet('com_getbible/admin.stylesheet.css', array(), true, false, false);
		
		JToolBarHelper::title(JText::_('COM_GETBIBLE_SETBOOKS_TITLE'), 'equalizer setbooks');
		
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('setbook.add');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('setbook.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();

			JToolBarHelper::publishList('setbooks.publish');
			JToolBarHelper::unpublishList('setbooks.unpublish');
	
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('setbooks.archive');
				
			if ($canDo->get('core.admin')) {
				
				JToolBarHelper::checkin('setbooks.checkin');
			}
		}

		if ($canDo->get('core.delete')) {

			if ($this->state->get('filter.published') == '-2') {
				JToolBarHelper::deleteList('', 'setbooks.delete');
			} else {
				JToolBarHelper::trash('setbooks.trash');
			}
		}
		
		JToolBarHelper::divider();
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_getbible');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_getbible&view=setbooks');
		
		// set version selection
		$result = $this->getVersions();
		if($result){
			JHtmlSidebar::addFilter(
			JText::_('COM_GETBIBLE_SELECT_VERSION'),
			'filter_version',
			JHtml::_('select.options', $result, 'value', 'text', $this->state->get('filter.version'))
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
	
	protected function getVersions()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Order it by the ordering field.
		$query->select($db->quoteName(array('version', 'name', 'language')));
		$query->from($db->quoteName('#__getbible_versions'));
		$query->order('language ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// echo nl2br(str_replace('#__','api_',$query)); die; 
		
		$results = $db->loadAssocList();
		
		if ($results){
			$options = array();
			foreach ($results as $version){
				$name = $version['name']. ' ('.$version['language'].')';
				$options[]  = JHtml::_('select.option', $version['version'], $name);
			}
			return $options;
		}
		return false;

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
			'book_name' 	=> JText::_('COM_GETBIBLE_BOOK_NAME'),
			'version' 		=> JText::_('COM_GETBIBLE_VERSION'),
			'book_nr' 		=> JText::_('COM_GETBIBLE_BOOK_NR'),
			'created_by' 	=> JText::_('COM_GETBIBLE_CREATED_BY'),
			'created_on' 	=> JText::_('COM_GETBIBLE_CREATED_ON'),
			'id' 			=> JText::_('JGRID_HEADING_ID')
		);
	}
}