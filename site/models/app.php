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

class GetbibleModelApp extends JModelList
{
	protected $app_params;
	
	protected $bookNameCounter = 0;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params = JComponentHelper::getParams('com_getbible');
		
	}
	
	public function getCpanel()
	{	
		if ($this->app_params->get('jsonQueryOptions') == 1){
			
			$path 		= JPATH_SITE.DS.'media'.DS.'com_getbible'.DS.'json'.DS.'cpanel.json';
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
	
	public function getAppDefaults()
	{
		// check if search form is used
		$jinput = JFactory::getApplication()->input;
		$search_app = $jinput->post->get('search_app', false, 'BOOL');
		
		if($search_app){
			// Load the results
			$result 			= new stdClass();
			$result->app 		= $search_app;
			$result->search 	= $jinput->post->get('search', 'repent', 'SAFE_HTML');
			$result->version 	= $jinput->post->get('search_version', 'kjv', 'ALNUM');
			// ensure the criteria is set correctly
			$crit 				= $jinput->post->get('search_crit', '1_1_1', 'CMD');
			$result->crit 		= (string) preg_replace('/[^0-9_]/i', '', $crit);
			// check to see if its a book name
			$result->type 		= $jinput->post->get('search_type', 'all', 'ALNUM');
			if($result->type == 'ot' || $result->type == 'nt' || $result->type == 'all'){
				$result->book_ref = NULL;
			} else {
				$result->book_ref = $result->type;
			}
			
			return $result;
		} else {
			$defaultVersion 		= $this->app_params->get('defaultStartVersion');
			$defaultStartBook 		= $this->app_params->get('defaultStartBook');
			$defaults 				= $this->bookDefaults($defaultStartBook,$defaultVersion);
					
			return $defaults;
		}
		
	}
	
	protected function bookDefaults($defaultStartBook,$defaultVersion,$tryAgain = false)
	{
		$this->bookNameCounter++;
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
			if($this->bookNameCounter > 2){
				JFactory::getApplication()->enqueueMessage(JText::_('Check the spelling of the (Default Starting Book) that it correspondence with the (Default App Version) in the getBible global settings'), 'error'); return false;
			} else {
				// fall back on default
				return $this->bookDefaults($defaultStartBook,$defaultVersion,'kjv');
			}
		}
	}
}