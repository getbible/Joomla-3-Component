<?php
/**
* 
* 	@version 	1.0.2  November 10, 2014
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
		$jinput 	= JFactory::getApplication()->input;
		$search_app = $jinput->post->get('search_app', 0, 'INT');
		$load_app 	= $jinput->post->get('load_app', 0, 'INT');
		if($search_app === 1){
			// Load the results
			$result 			= new stdClass();
			$result->search 	= $search_app;
			$result->searchFor 	= $jinput->post->get('search', 'repent', 'SAFE_HTML');
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
		} elseif($load_app === 1){
			// Load default passage
			$defaultPassage 			= $this->app_params->get('defaultStartBook'). ' '. $this->app_params->get('defaultStartChapter');
			// get values from post
			$passage 				= $jinput->post->get('passage', $defaultPassage, 'SAFE_HTML');
			$defaultVersion 		= $jinput->post->get('search_version', 'kjv', 'ALNUM');
			// get the defaults from request
			if($passage == $defaultPassage){
				$loadDefaults 			= new stdClass();
				$loadDefaults->Book 	= $this->app_params->get('defaultStartBook');
				$loadDefaults->Chapter 	= $this->app_params->get('defaultStartChapter');
			} else {
				$loadDefaults			= $this->getLoadDefaults($passage);
			}
			// load defaults
			$defaults 				= $this->bookDefaults($loadDefaults->Book,$defaultVersion);
			
			if(is_object($defaults)){
				// set defaults to loading request
				$defaults->chapter 		= $loadDefaults->Chapter;
				$defaults->lastchapter	= $defaults->chapter - 1;
				$defaults->load 		= $load_app;
			} else {
				// if no defaults found load global default
				$defaultVersion 		= $this->app_params->get('defaultStartVersion');
				$defaultStartBook 		= $this->app_params->get('defaultStartBook');
				$defaults 				= $this->bookDefaults($defaultStartBook,$defaultVersion);
			}
			return $defaults;			
		} else {
			$defaultVersion 		= $this->app_params->get('defaultStartVersion');
			$defaultStartBook 		= $this->app_params->get('defaultStartBook');
			$defaults 				= $this->bookDefaults($defaultStartBook,$defaultVersion);
					
			return $defaults;
		}
		
	}
	
	protected function getLoadDefaults($passage)
	{
		// proces query string
		$passage = (string) preg_replace('/[^A-Z0-9:;, \-]/i', '', $passage);
		$passage = ltrim($passage, '.');
		// strip all other passages we only return one
		if (strpos($passage,';') !== false) {
			$passage = explode(';',$passage);
		} else {
			$passage = array($passage);
		}		
		$ch		= NULL;
		$value	= NULL;
		$value1 = NULL;
		$value2 = NULL;
		$name	= NULL;
		$name2 	= NULL;
		$string = (string) preg_replace('/[^A-Z0-9]/i', '', $passage[0]);
		$value = str_split($string);
		$value1 = array_shift($value);
		$value2 = array_shift($value);
		if (is_numeric($value1) && $value2 && !is_numeric($value2)){
			$num = substr($passage[0], 1);
			$numbers = (string) preg_replace('/[^0-9,:-]/i', '', $num);
			if (strpos($numbers,':') !== false) {
				list($ch,$vers) = explode(':',$numbers);
			} else {
				$ch = (string) preg_replace('/[^0-9]/i', '', $numbers);
			}
			$name = (string) preg_replace('/[^A-Z]/i', '', $passage[0]);
			$name = $value1.$name;
		} else {
			$numbers = (string) preg_replace('/[^0-9,:-]/i', '', $passage[0]);
			if (strpos($numbers,':') !== false) {
				list($ch,$vers) = explode(':',$numbers);
			} else {
				$ch = (string) preg_replace('/[^0-9]/i', '', $numbers);
			}
			$name = (string) preg_replace('/[^A-Z]/i', '', $passage[0]);
		}
		if(strlen($name) == 0){
			$name = $this->app_params->get('defaultStartBook');
		}
		if(1 > $ch){
			$ch = 1;
		}
		$request 			= new stdClass();
		$request->Book 		= $name;
		$request->Chapter 	= $ch;
		
		return $request;
	}
	
	protected function bookDefaults($book,$version)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.book_nr ,a.book_names');
		$query->from('#__getbible_setbooks AS a');		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));
		$query->where($db->quoteName('a.book_name') . ' = ' . $db->quote($book));
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows()){
			// Load the results
			$result 				= $db->loadObject();
			$result->book_ref 		= json_decode($result->book_names)->name2;
			$result->chapter 		= $this->app_params->get('defaultStartChapter');
			$result->lastchapter 	= $this->app_params->get('defaultStartChapter') - 1;
			$result->version 		= $version;
			// remove books from result set
			unset($result->book_names);
			return $result;
		} else {
			return $this->bookSearch($book,$version);
		}
	}
	
	protected function bookSearch($name,$version)
	{
		$found = false;
		if ($name){
			// load all books
			$books = $this->setBooks($version);
			// set query book number and name
			foreach ($books as $book){
				if(!$found){						
					$name = mb_strtoupper(preg_replace('/\s+/', '', $name), 'UTF-8');
					foreach($book['book_names'] as $key => $value){
						if ($value){
							$value = mb_strtoupper(preg_replace('/\s+/', '', $value), 'UTF-8');
							if ($name == $value){
								$book_ref = $book['book_names']['name2'];
								$book_nr = $book['nr'];
								$found = true; 
								break;					
							} else {
								$found = false;
							}
						}
					}
				}
				if ($found){
					break;	
				}
			}
		}
		if (!$found){
			if ($name){
				// load all books again but now as KJV
				$books = $this->setBooks($version, true);
				// set query book number and name
				foreach ($books as $book){
					if(!$found){						
						$name = mb_strtoupper(preg_replace('/\s+/', '', $name), 'UTF-8');
						foreach($book['book_names'] as $key => $value){
							if ($value){
								$value = mb_strtoupper(preg_replace('/\s+/', '', $value), 'UTF-8');
								if ($name == $value){
									$book_ref = $book['book_names']['name2'];
									$book_nr = $book['nr']; 
									$found = true; 
									break;					
								} else {
									$found = false;
								}
							}
						}
					}
					if ($found){
						break;
					}
				}
			}
		}
		if(!$found){
			JFactory::getApplication()->enqueueMessage(JText::_('Check the spelling of the book that it correspondence with the getBible book names for the '.$version.' version'), 'error'); 
			return false;
		} else {
			// Load the results
			$result 				= new stdClass();
			$result->book_nr		= $book_nr;
			$result->book_ref 		= $book_ref;
			$result->chapter 		= $this->app_params->get('defaultStartChapter');
			$result->lastchapter 	= $this->app_params->get('defaultStartChapter') - 1;
			$result->version 		= $version;
			
			return $result;
		}
	}
	
	protected function setBooks($version = NULL, $retry = false, $default = 'kjv')
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		if ($version){
			// Create a new query object.
			$query = $db->getQuery(true);
			// Order it by the ordering field.
			$query->select($db->quoteName(array('book_names', 'book_nr', 'book_name')));
			$query->from($db->quoteName('#__getbible_setbooks'));
			$query->where($db->quoteName('version') . ' = '. $db->quote($version));
			$query->where($db->quoteName('access') . ' = 1');
			$query->where($db->quoteName('published') . ' = 1');
			$query->order('book_nr ASC');
			 
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			 
			// Load the results
			$results = $db->loadAssocList();
			
			if($results){
				foreach ($results as $book){
					$books[$book['book_nr']]['nr'] 			= $book['book_nr'];
					$books[$book['book_nr']]['book_names'] 	= (array)json_decode($book['book_names']);
					// if retry do't change name
					$books[$book['book_nr']]['name'] 		= $book['book_name'];
				}
			}
		}
		if(!is_array($books)){
			 
			// Create a new query object.
			$query = $db->getQuery(true);
			// Order it by the ordering field.
			$query->select($db->quoteName(array('book_names', 'book_nr', 'book_name')));
			$query->from($db->quoteName('#__getbible_setbooks'));
			$query->where($db->quoteName('version') . ' = '. $db->quote($default));
			$query->where($db->quoteName('access') . ' = 1');
			$query->where($db->quoteName('published') . ' = 1');
			$query->order('book_nr ASC');
			 
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			 
			// Load the results
			$results = $db->loadAssocList();
			foreach ($results as $book){
				$books[$book['book_nr']]['nr'] 			= $book['book_nr'];
				$books[$book['book_nr']]['book_names'] 	= json_decode($book['book_names'],true);
				$books[$book['book_nr']]['name'] 		= $book['book_name'];
			}
		}
		if($retry){
			 
			// Create a new query object.
			$query = $db->getQuery(true);
			// Order it by the ordering field.
			$query->select($db->quoteName(array('book_names', 'book_nr', 'book_name')));
			$query->from($db->quoteName('#__getbible_setbooks'));
			$query->where($db->quoteName('version') . ' = '. $db->quote($default));
			$query->where($db->quoteName('access') . ' = 1');
			$query->where($db->quoteName('published') . ' = 1');
			$query->order('book_nr ASC');
			 
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			 
			// Load the results
			$results = $db->loadAssocList();
			foreach ($results as $book){
				$books[$book['book_nr']]['nr'] 			= $book['book_nr'];
				$books[$book['book_nr']]['book_names'] 	= json_decode($book['book_names'],true);
			}
		}
		return $books;		
	}
}