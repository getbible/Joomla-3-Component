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

class GetbibleModelSetbook extends JModelAdmin
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
	public $typeAlias = 'com_getbible.setbook';
	
	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   type      The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array     Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Setbook', $prefix = 'GetbibleTable', $config = array())
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
		$data = $app->getUserState('com_getbible.edit.setbook.data', array());
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
		
		$this->preprocessData('com_getbible.setbook', $data);

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
		
		// Set the Book Names to json
		if (isset($data['book_names']) && is_array($data['book_names']))
		{
			$registry = new JRegistry;
			$registry->loadArray($data['book_names']);
			$data['book_names'] = (string) $registry;
		}
		
		// Alter the title for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
			$data['title'] = $title;
			$data['alias'] = $alias;
			$data['state'] = 0;
		}
		
		if (parent::save($data))
		{
			return true;
		}
		return false;
	}

	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_getbible.setbook', 'setbook', array('control' => 'jform', 'load_data' => $loadData));

		return $form;
	}
}
