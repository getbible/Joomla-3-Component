<?php
/**
* 
* 	@version 	1.0.0 Feb 02, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class GetbibleViewSetbook extends JViewLegacy
{
	protected $item;
	protected $form;
	protected $state;
	protected $xml;
	
	public function display($tpl = null)
	{	
		/*$manifestUrl = JPATH_ADMINISTRATOR."/components/com_getbible/manifest.xml";
		$this->xml = simplexml_load_file($manifestUrl);*/
		
		// Get data from the model
		$this->item 	= $this->get('Item');
		$this->form 	= $this->get('Form');
		$this->state	= $this->get('State');
		$this->canDo 	= JHelperContent::getActions('com_getbible', 'setbook', $this->item->id);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->_prepareDocument();

		parent::display($tpl);
	}

	public function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		// Built the actions for new and existing records.
		$canDo = $this->canDo;
		JToolbarHelper::title(JText::_('COM_GETBIBLE_' . ($checkedOut ? 'VIEW_SETBOOK' : ($isNew ? 'ADD_SETBOOK' : 'EDIT_SETBOOK'))), 'pencil-2 setbook-add');

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories('com_getbible', 'core.create')) > 0))
		{
			JToolbarHelper::apply('setbook.apply');
			JToolbarHelper::save('setbook.save');
			JToolbarHelper::save2new('setbook.save2new');
			JToolbarHelper::cancel('setbook.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId))
				{
					JToolbarHelper::apply('setbook.apply');
					JToolbarHelper::save('setbook.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create'))
					{
						JToolbarHelper::save2new('setbook.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2copy('setbook.save2copy');
			}

			/*if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit'))
			{
				JToolbarHelper::versions('com_getbible.setbook', $this->item->id);
			}*/

			JToolbarHelper::cancel('setbook.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		/*// Add Theme to Page
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'theme.php' );
		// The CSS for Theme
		if ($vdmTheme == 1){
			$this->document->addStyleSheet(JURI::base() . '../media/com_getbible/css/theme.css');
		}
		// The  JS
		$this->document->addScript(JURI::base() . '../media/com_getbible/js/jquery-1.10.2.min.js');
		$this->document->addScriptDeclaration($theme);  

		JHTML::_('behavior.tooltip');*/          
	}
}