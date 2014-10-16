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

class GetbibleModelVersions extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'version', 'a.version',
				'name', 'a.name',
				'language', 'a.language',
				'testament', 'a.testament',
				'access', 'a.access',
				'published', 'a.published',
				'created_by', 'a.created_by',
				'created_on', 'a.created_on'
			);
		}

		parent::__construct($config);
	}

	public function getItems()
	{		
		$user = JFactory::getUser();
		/*$user_groups = JUserHelper::getUserGroups($user->id);

		$AppGroups['admin'] = JComponentHelper::getParams('com_getbible')->get('admin');		
		
		$admin_user = (count(array_intersect($AppGroups['admin'], $user_groups))) ? true : false;

		if (!$admin_user){
			$this->setError(JText::_('COM_GETBIBLE_NO_PERMISSION'));
			return false;
		}
		*/
		// check in items
		//$this->checkInNow();
		
		$items = parent::getItems();
		
		if($items){
			foreach ($items as &$item) {
				$item->url = JRoute::_('index.php?option=com_getbible&amp;task=version.edit&amp;id=' .(int) $item->id);
				
				if ($item->created_by == 0){
					$item->created_by = '';
					$item->createduser = '';
					$item->created_on = '';
				} else {
					$item->createduser = JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->created_by);
					$user = JFactory::getUser($item->created_by);
					$item->created_by = $user->name;
					$item->created_on = JHtml::_('date', $item->created_on, JText::_('DATE_FORMAT_LC4'));
				}
				
				if ($item->modified_by == 0){
					$item->modified_by = '';
					$item->modifieduser = '';
					$item->modified_on = '';
				} else {
					$item->modifieduser = JRoute::_('index.php?option=com_users&task=user.edit&id=' . $item->modified_by);
					$user = JFactory::getUser($item->modified_by);
					$item->modified_by = $user->name;
				}
				
			}
		}

		return $items;
	}

	public function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		
		$query = parent::getListQuery();

		$query->select('a.*, u.username');
		$query->from('#__getbible_versions AS a');

		$query->join('LEFT', '#__users AS u ON u.id = a.checked_out');
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}
		
		// Filter by Language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($db->escape($language, true)));
		}
		
		// Filter by Version.
		if ($version = $this->getState('filter.version'))
		{
			$query->where('a.version = ' . $db->quote($db->escape($version, true)));
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}


		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.name LIKE ' . $search . ' OR a.version LIKE ' . $search . ' OR a.language LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		
		if ($orderCol != '') {
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
				
		// echo nl2br(str_replace('#__','api_',$query)); die;
		return $query;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		
		$id = $this->getUserStateFromRequest($this->context . '.filter.id', 'filter_id');
		$this->setState('filter.id', $id);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$name = $app->getUserStateFromRequest($this->context . '.filter.name', 'filter_name');
		$this->setState('filter.name', $name);

		$language = $app->getUserStateFromRequest($this->context . '.filter.language', 'filter_language');
		$this->setState('filter.language', $language);
		
		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$version = $this->getUserStateFromRequest($this->context . '.filter.version', 'filter_version');
		$this->setState('filter.version', $version);

		$testament = $this->getUserStateFromRequest($this->context . '.filter.testament', 'filter_testament');
		$this->setState('filter.testament', $testament);
		
		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created_on = $this->getUserStateFromRequest($this->context . '.filter.created_on', 'filter_created_on');
		$this->setState('filter.created_on', $created_on);

		// List state information.
		parent::populateState('id', 'ASC');
	}
	
	public function getTable($type = 'Version', $prefix = 'GetbibleTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.version');
		$id .= ':' . $this->getState('filter.language');
		$id .= ':' . $this->getState('filter.name');
		$id .= ':' . $this->getState('filter.testament');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.created_on');

		return parent::getStoreId($id);
	}
	
	/**
	 * Build an SQL query to checkin all items left chekced out longer then a day.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time 	= JComponentHelper::getParams('com_getbible')->get('check_in');
		
		if ($time){
			// Get Yesterdays date
			$date =& JFactory::getDate()->modify($time)->toSql();	
	
			// Get a db connection.
			$db = JFactory::getDbo();
			
			$query = $db->getQuery(true);
			 
			// Fields to update.
			$fields = array(
				$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
				$db->quoteName('checked_out') . '=0'
			);
			 
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('checked_out') . '!=0', 
				$db->quoteName('checked_out_time') . '<\''.$date.'\''
			);
			
			// Check table
			$query->update($db->quoteName('#__getbible_versions'))->set($fields)->where($conditions); 
				 
			$db->setQuery($query);
			 
			$result = $db->query();
		}
		
		return true;
	}
}