<?php
/**
* 
* 	@version 	1.0.3  November 25, 2014
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
		
		// Include the JLog class.
		jimport('joomla.log.log');
		
		// get ip for log
		$ip = $this->getUserIP();
		
		// Add the logger.
		JLog::addLogger(
			 // Pass an array of configuration options
			array(
					// Set the name of the log file
					'text_file' => 'getbible_saves.php',
					// (optional) you can change the directory
					//'text_file_path' => 'logs'
			 ),
			 JLog::NOTICE
		);
		
		// start logging...
		JLog::add('id->['.$this->id.'] name->['.$this->book_name.'] saved by ' . $user->name.'->['. $user->id.'] ip->['.$ip.']', JLog::NOTICE, 'Setbook');
		
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
	
	protected function getUserIP()
	{
		$ip = "";
		
		if (isset($_SERVER)) {
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			} else {
				$ip = $_SERVER["REMOTE_ADDR"];
			}
		} else {
			if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
				$ip = getenv( 'HTTP_X_FORWARDED_FOR' );
			} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
				$ip = getenv( 'HTTP_CLIENT_IP' );
			} else {
				$ip = getenv( 'REMOTE_ADDR' );
			}
		}
		return $ip;
    }
}
