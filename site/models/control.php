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

class GetbibleModelControl extends JModelList
{
	/*protected $app_params;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params = JComponentHelper::getParams('com_getbible');
		
	}*/
	
	public function getChapters($book_nr,$version)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.chapter_nr');
		$query->from('#__getbible_chapters AS a');		
		$query->where($db->quoteName('a.version') . ' = '.$db->quote($version));		
		$query->where($db->quoteName('a.book_nr') . ' = '.$book_nr);		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.chapter_nr ASC');
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results
		return $db->loadColumn();
				
	}
	
	public function getBooks($version)
	{
		// get book for this translation
		$savedBooks = $this->getSavedBooks($version);

		if(is_array($savedBooks) && count($savedBooks)){
			foreach($savedBooks as $book_nr){
				// load the book details for this translation or that of KJV if none is found
				$books[] 	= $this->getSetBook($version,$book_nr);
			}
			return $books;
		}
		return false;
	}
	
	public function getSavedBooks($version)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.book_nr');
		$query->from('#__getbible_books AS a');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.book_nr ASC');
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// echo nl2br(str_replace('#__','api_',$query)); die;
		
		return $db->loadColumn();
				
	}
	
	public function getSetBook($version,$book_nr)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.book_name, a.book_nr, a.book_names AS ref');
		$query->from('#__getbible_setbooks AS a');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->where($db->quoteName('a.book_nr') . ' = ' . $book_nr);
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// echo nl2br(str_replace('#__','api_',$query)); die;
		$db->execute();
		$num_rows = $db->getNumRows();
		
		if($num_rows){
			// Load the results
			$result 		= $db->loadObject();
			$result->ref 	= json_decode($result->ref)->name2;
			// return result
			return $result;
		} else {
			// fall back on default
			return $this->getSetBook('kjv',$book_nr);
		}
				
	}
}