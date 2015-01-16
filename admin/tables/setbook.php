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

class GetbibleTableSetbook extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__getbible_setbooks', 'id', $db);
	}
	
	/*public function bind($array, $ignore = '') 
	{
		
		// set array of book_names
		if (!isset($array['book_names'])){
			$i = 1;
			foreach ($array as $key => $value){
				if ($key == 'name'.$i && $value){
					$book_names[$key] = $value;
					$i++;
				}
			}
			if (isset($book_names) && is_array($book_names)) {
				// Convert the params field to a string.
				$names = new JRegistry;
				$names->loadArray($book_names);
				$array['book_names'] = (string)$names;
			} else {
				$array['book_names'] = '';
			}
		}
		
		return parent::bind($array, $ignore);
	}*/

	public function check()
	{
		// get current user
		$user = JFactory::getUser();
		if ($this->id){
			// set modified data
			$this->modified_by = $user->id;
			$this->modified_on = JFactory::getDate()->toSql();
		} elseif (!$this->id && !$this->created_by) {
			// set creation data
			$this->created_by = $user->id;
			$this->created_on = JFactory::getDate()->toSql();
		}

		return true;
	}
}
