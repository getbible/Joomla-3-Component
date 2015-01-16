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

class GetbibleModelVersion extends JModelAdmin
{
	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_GETBIBLE';

	/**
	 * The type alias for this content type (for example, 'com_content.article').
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_getbible.version';
	
	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   type      The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array     Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Version', $prefix = 'GetbibleTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_getbible.edit.version.data', array());
		// set user
		$user = JFactory::getUser();
		/*$user_groups = JUserHelper::getUserGroups($user->id);

		$AppGroups['admin'] = JComponentHelper::getParams('com_getbible')->get('admin');		
		
		$admin_user = (count(array_intersect($AppGroups['admin'], $user_groups))) ? true : false;

		if (!$admin_user){
			$this->setError(JText::_('COM_GETBIBLE_NO_PERMISSION'));
			return false;
		}*/

		if (empty($data)) {
			$data = $this->getItem();
		}
		
		$this->preprocessData('com_getbible.version', $data);

		return $data;
	}
	/**
	 * Method to get a single record.
	 *
	 * @param   integer    The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->book_names);
			$item->book_names = $registry->toArray();
		}

		return $item;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 *
	 * @return  boolean  True on success.
	 * @since   1.6
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();

		if (isset($data['book_names']) && is_array($data['book_names']))
		{
			$registry = new JRegistry;
			$registry->loadArray($data['book_names']);
			$data['book_names'] = (string) $registry;
		}
		if (parent::save($data))
		{
			return true;
		}
		return false;
	}

	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_getbible.version', 'version', array('control' => 'jform', 'load_data' => $loadData));

		return $form;
	}
	
	/**
	 * Method to delete rows.
	 *
	 * @param   array  &$pks  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function delete(&$pks)
	{
		$user  = JFactory::getUser();
		$table = $this->getTable();
		$pks   = (array) $pks;
		
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				// Access checks.
				$allow = $user->authorise('core.delete', 'com_getbible');
				

				if ($allow)
				{
					$version_code_name = $this->getItem($pk)->version;
					
					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
					else
					{
						// Trigger the function to delets all data related to this version.
						$this->deletVersion($version_code_name);
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}
	
	/**
	 * Method to delete all version data.
	 *
	 * @param   string $version_code_name  of version name.
	 *
	 */
	protected function deletVersion($version_code_name)
	{
		$db = JFactory::getDbo();
 		$tables = array('#__getbible_chapters','#__getbible_books','#__getbible_verses');
		foreach ($tables as $table){
			$query = $db->getQuery(true);
			 
			// delete all data of this version.
			$conditions = array(
				$db->quoteName('version') . ' = ' . $db->quote($version_code_name)
			);
			 
			$query->delete($db->quoteName($table));
			$query->where($conditions);
			 
			$db->setQuery($query);
			 
			$result = $db->query();
		}
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	public function publish(&$pks, $value = 1)
	{
		if (parent::publish($pks, $value))
		{
			$pks   = (array) $pks;
			
			// Iterate the items to delete each one.
			foreach ($pks as $i => $pk)
			{
				$this->versionPublish($this->getItem($pk)->version, $value);
			}
			
			return $this->_cpanel();
		}
	}
		
	/**
	 * Method to change the published state of all version data.
	 *
	 * @param   string $version_code_name  of version name.
	 * @param   integer  $value  The value of the published state.
	 *
	 */
	protected function versionPublish($version_code_name, $value = 1)
	{
		$db = JFactory::getDbo();
 		$tables = array('#__getbible_setbooks','#__getbible_chapters','#__getbible_books','#__getbible_verses');
		foreach ($tables as $table){
 
			$query = $db->getQuery(true);
			 
			// Fields to update.
			$fields = array(
				$db->quoteName('published') . ' = ' . $value
			);
			 
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('version') . ' = ' . $db->quote($version_code_name)
			);
			 
			$query->update($db->quoteName($table))->set($fields)->where($conditions);
			 
			$db->setQuery($query);
			 
			$db->execute();
		}
	}
	
	protected function _cpanel()
	{
		// Base this model on the backend version.
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_getbible'.DS.'models'.DS.'cpanel.php';
		$cpanel_model = new GetbibleModelCpanel;
		return $cpanel_model->setCpanel();
	}
}
