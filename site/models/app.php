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

jimport('joomla.application.component.helper');

class GetbibleModelApp extends JModelList
{
	protected $app_params;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params = JComponentHelper::getParams('com_getbible');
		
	}
	
	public function getCpanel()
	{	
		if ($this->app_params->get('jsonQueryOptions') == 1){
			
			$path 		= JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE){
				return false;
			}
			
			return json_decode($cpanel);
			
		} elseif ($this->app_params->get('jsonQueryOptions') == 2) {
			
			$path 		= 'https://getbible.net/media/com_getbible/json/cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE){
				return false;
			}
			
			return json_decode($cpanel);
			
		} else {
			
			$path 		= 'http://getbible.net/media/com_getbible/json/cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE){
				return false;
			}
			
			return json_decode($cpanel);
			
		}

	}
	
	public function getBookdefaults()
	{
		$defaultVersion 		= $this->app_params->get('defaultStartVersion');
		$defaultStartBook 		= $this->app_params->get('defaultStartBook');	
		$defaults 				= $this->bookDefaults($defaultStartBook,$defaultVersion);
				
		return $defaults;
		
	}
	
	protected function bookDefaults($defaultStartBook,$defaultVersion,$tryAgain = false)
	{
		
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.book_nr ,a.book_names');
		$query->from('#__getbible_setbooks AS a');		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		if($tryAgain){
			$query->where($db->quoteName('a.version') . ' = ' . $db->quote($tryAgain));
			$query->where($db->quoteName('a.book_name') . ' = ' . $db->quote($defaultStartBook));
		} else {
			$query->where($db->quoteName('a.version') . ' = ' . $db->quote($defaultVersion));
			$query->where($db->quoteName('a.book_name') . ' = ' . $db->quote($defaultStartBook));
		}
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		 if($num_rows){
			// Load the results
			$result 			= $db->loadObject();
			$result->book_ref 	= json_decode($result->book_names)->name2;
			$result->chapter 	= $this->app_params->get('defaultStartChapter');
			$result->version 	= $defaultVersion;
			// remove books from result set
			unset($result->book_names);
			return $result;
		} else {
			// fall back on default
			return $this->bookDefaults($defaultStartBook,$defaultVersion,'kjv');
		}
	}
}