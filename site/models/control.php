<?php
/**
* 
* 	@version 	1.0.6  January 06, 2015
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
	protected $app_params;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params = JComponentHelper::getParams('com_getbible');
		
	}
	
	public function getChapters($book_nr,$version)
	{
		if($this->app_params->get('jsonAPIaccess') && $this->app_params->get('jsonQueryOptions') == 1){

			// Get the input data
			$jinput 	= JFactory::getApplication()->input;
			$URLkey 	= $jinput->get('key', NULL, 'ALNUM');
			$APIkey 	= $this->app_params->get('jsonAPIkey');
			$appKey 	= $jinput->get('appKey', NULL, 'ALNUM');
			$token 		= JSession::getFormToken();
	
		} else {
			$URLkey = 'free';
			$APIkey = 'free';
		}
		if ($URLkey == $APIkey || $appKey == $token){
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
		return false;
				
	}
	
	public function getBooks($version)
	{
		if($this->app_params->get('jsonAPIaccess') && $this->app_params->get('jsonQueryOptions') == 1){

			// Get the input data
			$jinput 	= JFactory::getApplication()->input;
			$URLkey 	= $jinput->get('key', NULL, 'ALNUM');
			$APIkey 	= $this->app_params->get('jsonAPIkey');
			$appKey 	= $jinput->get('appKey', NULL, 'ALNUM');
			$token 		= JSession::getFormToken();
	
		} else {
			$URLkey = 'free';
			$APIkey = 'free';
		}
		if ($URLkey == $APIkey || $appKey == $token){
			// get book for this translation
			$savedBooks = GetHelper::getSavedBooks($version);
	
			if(is_array($savedBooks) && count($savedBooks)){
				foreach($savedBooks as $book_nr){
					// load the book details for this translation or that of KJV if none is found
					$books[] = GetHelper::getSetBook($version,$book_nr,true);
				}
				return $books;
			}
		}
		return false;
	}
	
	public function setHighlight($highlight,$publish,$jsonKey,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// get current date
				$date 		= date('Y-m-d H:i:s');
				$aHighlight 	= (string) preg_replace('/[^A-Z0-9_]/i', '', $highlight);
				list($mark, $color) = explode('__',$aHighlight);
				return $this->setHighlight_db($mark, $color, $publish, $user->id, $date);
			}
		}
		return false;
	}
	
	public function setHighlights($highlight,$action,$publish,$jsonKey,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				if($action == 2){
					$this->clearHighlights($jsonKey,$tu);
				}
				// get current date
				$date 		= date('Y-m-d H:i:s');
				$highlights 	= json_decode(base64_decode($highlight),true);
				if(is_array($highlights)){
					foreach($highlights as $mark => $color){
						$this->setHighlight_db($mark, $color, $publish, $user->id, $date);
					}
					return true;
				}
			}
		}
		return false;
	}
	
	public function getHighlights($jsonKey,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// Get a db connection.
				$db = JFactory::getDbo();
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('books_nr','chapter_nr','verse_nr','color')));
				$query->from($db->quoteName('#__getbible_highlights'));
				$query->where($db->quoteName('user') . ' = '. $db->quote($user->id));
				$query->where($db->quoteName('published') . ' = 1');
				$db->setQuery($query);
				$db->execute();
				if($db->getNumRows()){
					$rows = $db->loadObjectList();
					foreach($rows as $row){
						$buket[$row->books_nr.'_'.$row->chapter_nr.'_'.$row->verse_nr] = $row->color;
					}
					return base64_encode(json_encode($buket));
				}
			}
		}
		return false;
	}
	
	public function clearHighlights($jsonKey,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// Get a db connection.
				$db = JFactory::getDbo();
				// Create a new query object.
				$query = $db->getQuery(true);
				$conditions = array(
					$db->quoteName('user') . ' = '. $db->quote($user->id)
				);
				$query->delete($db->quoteName('#__getbible_highlights'));
				$query->where($conditions);
				$db->setQuery($query);
				return $db->execute();
			}
		}
		return false;		
	}
	
	protected function setHighlight_db($mark, $color, $publish, $user, $date)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$mark 	= (string) preg_replace('/[^A-Z0-9_]/i', '', $mark);
		$color 	= (string) preg_replace('/[^A-Z]/i', '', $color);
		list($books_nr,$chapter_nr,$verse_nr) = explode('_',$mark);
		$query->select('id');
		$query->from($db->quoteName('#__getbible_highlights'));
		$query->where($db->quoteName('books_nr') . 		' = '. $db->quote($books_nr));
		$query->where($db->quoteName('chapter_nr') . 	' = '. $db->quote($chapter_nr));
		$query->where($db->quoteName('verse_nr') . 		' = '. $db->quote($verse_nr));
		$query->where($db->quoteName('user') . 			' = '. $db->quote($user));		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows()){
			$id = $db->loadResult();
			// update value
			$query = $db->getQuery(true);
			// Fields to update.
			$fields = array(
				$db->quoteName('color') . ' = ' . $db->quote($color),
				$db->quoteName('published') . ' = ' . $publish,
				$db->quoteName('modified_on') . ' = ' . $db->quote($date)
			);
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('id') . ' = ' . $id
			);
			$query->update($db->quoteName('#__getbible_highlights'))->set($fields)->where($conditions);
			$db->setQuery($query);
			return $db->execute();
		} else {
			// add new value
			$query = $db->getQuery(true);
			// Insert columns.
			$columns = array('user', 'books_nr', 'chapter_nr', 'verse_nr', 'color', 'published', 'created_on');
			// Insert values.
			$values = array($db->quote($user), $db->quote($books_nr), $db->quote($chapter_nr), $db->quote($verse_nr), $db->quote($color), $db->quote($publish), $db->quote($date));
			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__getbible_highlights'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			return $db->execute();
		}
	}
	
	public function setNote($note,$jsonKey,$verse,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// get current date
				$date 		= date('Y-m-d H:i:s');
				return $this->setNote_db($note, $verse, $user->id, $date);
			}
		}
		return false;
	}
	
	protected function setNote_db($note, $verse, $user, $date)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		list($books_nr,$chapter_nr,$verse_nr) = explode('_',$verse);
		$query->select('id');
		$query->from($db->quoteName('#__getbible_notes'));
		$query->where($db->quoteName('books_nr') . 		' = '. $db->quote($books_nr));
		$query->where($db->quoteName('chapter_nr') . 	' = '. $db->quote($chapter_nr));
		$query->where($db->quoteName('verse_nr') . 		' = '. $db->quote($verse_nr));
		$query->where($db->quoteName('user') . 			' = '. $db->quote($user));		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows()){
			$id = $db->loadResult();
			// update value
			$query = $db->getQuery(true);
			// Fields to update.
			$fields = array(
				$db->quoteName('note') . ' = ' . $db->quote($note),
				$db->quoteName('access') . ' = 1',
				$db->quoteName('modified_on') . ' = ' . $db->quote($date)
			);
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('id') . ' = ' . $id
			);
			$query->update($db->quoteName('#__getbible_notes'))->set($fields)->where($conditions);
			$db->setQuery($query);
			if($db->execute()){
				return array($verse => $note);
			}
			return false;
		} else {
			// add new value
			$query = $db->getQuery(true);
			// Insert columns.
			$columns = array('user', 'books_nr', 'chapter_nr', 'verse_nr', 'note', 'access', 'created_on');
			// Insert values.
			$values = array($db->quote($user), $db->quote($books_nr), $db->quote($chapter_nr), $db->quote($verse_nr), $db->quote($note), 1, $db->quote($date));
			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__getbible_notes'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			if($db->execute()){
				return array($verse => $note);
			}
			return false;
		}
	}
	
	public function getNotes($jsonKey,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// Get a db connection.
				$db = JFactory::getDbo();
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('books_nr','chapter_nr','verse_nr','note')));
				$query->from($db->quoteName('#__getbible_notes'));
				$query->where($db->quoteName('user') . ' = '. $db->quote($user->id));
				$query->where($db->quoteName('access') . ' = 1');
				$db->setQuery($query);
				$db->execute();
				if($db->getNumRows()){
					$rows = $db->loadObjectList();
					foreach($rows as $row){
						$notes[$row->books_nr.'_'.$row->chapter_nr.'_'.$row->verse_nr] = $row->note;
					}
					return $notes;
				}
			}
		}
		return false;
	}
	
	public function setTaged($action,$tag,$jsonKey,$verse,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// get current date
				$date 		= date('Y-m-d H:i:s');
				return $this->setTaged_db($action,$tag,$verse,$user->id,$date);
			}
		}
		return false;
	}
	
	protected function setTaged_db($action, $name, $verse, $user, $date)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// set the verse to its actual values
		list($books_nr,$chapter_nr,$verse_nr) = explode('_',$verse);
		$tag = $this->getTagId($user,$name);
		if($tag){
			if($action == 1){
				// first check if it already is set
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from($db->quoteName('#__getbible_taged'));
				$query->where($db->quoteName('books_nr') . 		' = '. $db->quote($books_nr));
				$query->where($db->quoteName('chapter_nr') . 	' = '. $db->quote($chapter_nr));
				$query->where($db->quoteName('verse_nr') . 		' = '. $db->quote($verse_nr));
				$query->where($db->quoteName('tag') . 			' = '. $db->quote($tag));
				$query->where($db->quoteName('user') . 			' = '. $db->quote($user));		 
				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				$db->execute();
				if($db->getNumRows()){
					return array($verse => $name);
				} else {
					// add tag
					$query = $db->getQuery(true);
					// Insert columns.
					$columns = array('user', 'books_nr', 'chapter_nr', 'verse_nr', 'tag', 'created_on');
					// Insert values.
					$values = array($db->quote($user), $db->quote($books_nr), $db->quote($chapter_nr), $db->quote($verse_nr), $db->quote($tag), $db->quote($date));
					// Prepare the insert query.
					$query
						->insert($db->quoteName('#__getbible_taged'))
						->columns($db->quoteName($columns))
						->values(implode(',', $values));
					$db->setQuery($query);
					if($db->execute()){
						return array($verse => $name);
					}
				}
				return false;
			} elseif($action == 0){
				// remove the tag
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$conditions = array(
					$db->quoteName('user') . 		' = '.$db->quote($user), 
					$db->quoteName('books_nr') . 	' = '.$db->quote($books_nr), 
					$db->quoteName('chapter_nr') . 	' = '.$db->quote($chapter_nr), 
					$db->quoteName('verse_nr') . 	' = '.$db->quote($verse_nr), 
					$db->quoteName('tag') . 		' = '.$db->quote($tag)
				);
				 
				$query->delete($db->quoteName('#__getbible_taged'));
				$query->where($conditions);
				$db->setQuery($query);
				return  $db->execute();
			}
		}
		return false;
	}
		
	public function getTaged($jsonKey,$verse,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// set the verse to its actual values
				list($books_nr,$chapter_nr) = explode('_',$verse);
				// Get a db connection.
				$db = JFactory::getDbo();
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('a.verse_nr','a.tag')));
				$query->select($db->quoteName('b.name'));
				$query->from($db->quoteName('#__getbible_taged', 'a'));
				$query->join('LEFT', $db->quoteName('#__getbible_tags', 'b') . ' ON (' . $db->quoteName('a.tag') . ' = ' . $db->quoteName('b.id') . ')');
				$query->where($db->quoteName('a.user') . ' = '. $db->quote($user->id));
				$query->where($db->quoteName('a.books_nr') . ' = '. $db->quote($books_nr));
				$query->where($db->quoteName('a.chapter_nr') . ' = '. $db->quote($chapter_nr));
				// echo nl2br(str_replace('#__','api_',$query)); die;
				$db->setQuery($query);
				$db->execute();
				if($db->getNumRows()){
					$rows = $db->loadObjectList();
					foreach($rows as $row){
						$taged[$row->verse_nr][$row->tag] = $row->name;
					}
					return $taged;
				}
			}
		}
		return false;
	}
	
	protected function getTagId($user,$name,$create = true)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__getbible_tags'));
		$query->where($db->quoteName('user') . ' = '. $db->quote($user));
		$query->where($db->quoteName('name') . ' = '. $db->quote($name));
		$query->where($db->quoteName('published') . ' = 1');
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows()){
			return $db->loadResult();
		} elseif($create) {
			// get current date
			$date = date('Y-m-d H:i:s');
			// check if the tag aready exist
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__getbible_tags'));
			$query->where($db->quoteName('user') . ' = '. $db->quote($user));
			$query->where($db->quoteName('name') . ' = '. $db->quote($name));
			$db->setQuery($query);
			$db->execute();
			if($db->getNumRows()){
				$id		= $db->loadResult();
				// update value
				$query = $db->getQuery(true);
				// Fields to update.
				$fields = array(
					$db->quoteName('published') . ' = 1',
					$db->quoteName('modified_on') . ' = '. $db->quote($date)
				);
				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('id') . ' = ' . $id
				);
				$query->update($db->quoteName('#__getbible_tags'))->set($fields)->where($conditions);
				$db->setQuery($query);
				if($db->execute()){
					return $id;
				}
			} else {
				// add new value
				$query = $db->getQuery(true);
				// Insert columns.
				$columns = array('user', 'name', 'access', 'published', 'created_on');
				// Insert values.
				$values = array($db->quote($user), $db->quote($name), $db->quote(1), $db->quote(1), $db->quote($date));
				// Prepare the insert query.
				$query
					->insert($db->quoteName('#__getbible_tags'))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));
				// Set the query using our newly populated query object and execute it.
				$db->setQuery($query);
				if($db->execute()){
					return $this->getTagId($user,$name);
				}
			}
		} else {
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__getbible_tags'));
			$query->where($db->quoteName('user') . ' = '. $db->quote($user));
			$query->where($db->quoteName('name') . ' = '. $db->quote($name));
			$db->setQuery($query);
			$db->execute();
			if($db->getNumRows()){
				return $db->loadResult();
			}
		}
		return false;
	}
	
	public function setTags($name,$note,$access,$published,$jsonKey,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// get current date
				$date 		= date('Y-m-d H:i:s');
				return $this->setTags_db($name,$note,$access,$published,$user->id,$date);
			}
		}
		return false;
	}
	
	protected function setTags_db($name, $note, $access, $published, $user, $date)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// check if tag exist
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__getbible_tags'));
		$query->where($db->quoteName('name') . ' = '. $db->quote($name));
		$query->where($db->quoteName('user') . ' = '. $db->quote($user));		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows()){
			$id = $db->loadResult();
			// update value
			$query = $db->getQuery(true);
			// Fields to update.
			$fields = array(
				$db->quoteName('note') . ' = ' . $db->quote($note),
				$db->quoteName('access') . ' = ' . $db->quote($access),
				$db->quoteName('published') . ' = ' . $db->quote($published),
				$db->quoteName('modified_on') . ' = ' . $db->quote($date)
			);
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('id') . ' = ' . $id
			);
			$query->update($db->quoteName('#__getbible_tags'))->set($fields)->where($conditions);
			$db->setQuery($query);
			if($db->execute()){
				return true;
			}
			return false;
		} else {
			// add new value
			$query = $db->getQuery(true);
			// Insert columns.
			$columns = array('user', 'name', 'note', 'access', 'published', 'created_on');
			// Insert values.
			$values = array($db->quote($user), $db->quote($name), $db->quote($note), $db->quote($access), $db->quote($published), $db->quote($date));
			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__getbible_tags'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			if($db->execute()){
				return true;
			}
			return false;
		}
	}
		
	public function getTags($jsonKey,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				$tags = array();
				// Get a db connection.
				$db = JFactory::getDbo();
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('id','name','note','access','published')));
				$query->from($db->quoteName('#__getbible_tags'));
				$query->where($db->quoteName('user') . ' = '. $db->quote($user->id));
				$query->where($db->quoteName('published') . ' = 1');
				$db->setQuery($query);
				$db->execute();
				if($db->getNumRows()){
					$rows = $db->loadObjectList();
					foreach($rows as $row){
						$tags[] = $row->name;
					}
					return $tags;
				}
			}
		}
		return false;
	}
	
	public function getTagverse($jsonKey,$version,$name,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				$tag = $this->getTagId($user->id,$name,false);
				if($tag){
					// Get a db connection.
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select($db->quoteName(array('books_nr','chapter_nr','verse_nr')));
					$query->from($db->quoteName('#__getbible_taged'));
					$query->where($db->quoteName('tag') .	' = '. $db->quote($tag));
					$query->where($db->quoteName('user') .	' = '. $db->quote($user->id));
					$query->order($db->quoteName('books_nr') . ' ASC');	 
					// Reset the query using our newly populated query object.
					$db->setQuery($query);
					$db->execute();
					if($db->getNumRows()){
						$verses 	= $db->loadObjectList();
						$verseList	= array();
						$book_nrs	= array();
						foreach($verses as $verse){
							if(!array_key_exists($verse->books_nr, $book_nrs)){
								$book_nrs[$verse->books_nr] = $this->getBookRef($verse->books_nr,$version);
							}
							$verseList[$book_nrs[$verse->books_nr]][$verse->chapter_nr][$verse->verse_nr] = $verse->verse_nr;
						}
						unset($verses);
						unset($book_nrs);
						
						// now build return query
						$query = 'p=';
						$counter = 0;
						foreach($verseList as $book => $chapters){
							if($counter){
								$query .= ';'.$book;
							} else {
								$query .= $book;
							}
							$chaCounter = 0;
							ksort($chapters, SORT_NUMERIC);
							foreach($chapters as $chapter => $verses){
								if($chaCounter){
									$query .= ';'.$chapter.':';
								} else {
									$query .= $chapter.':';
								}
								$verCounter = 0;
								asort($verses, SORT_NUMERIC);
								$verses = $this->joinConsecutiveVerses($verses);
								foreach($verses as $verse){
									if($verCounter){
										$query .=  ','.$verse;										
									} else {
										$query .=  $verse;
									}									
									$verCounter++;
								}
								$chaCounter++;
							}
							$counter++;
						}
						unset($verseList);
						// return query string
						return $query.'&v='.$version;
					}
				}
			}
		}
		return false;
	}
	
	protected function joinConsecutiveVerses($array) {
	   $ret  = array();
	   $temp = array();
	   foreach($array as $val) {
		  if(next($array) == ($val + 1))
			 $temp[] = $val;
		  else
			 if(count($temp) > 0) {
				$temp[] = $val;
				$ret[]  = $temp[0].'-'.end($temp);
				$temp   = array();
			 }
			 else
				$ret[] = $val;
	   }
	   return $ret;
	}

	protected function getBookRef($nr,$version)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('a.book_names');
		$query->from('#__getbible_setbooks AS a');		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));
		$query->where($db->quoteName('a.book_nr') . ' = ' . $db->quote($nr));
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows()){
			// return ref
			return json_decode($db->loadResult())->name2;
		} else {
			return $this->getBookRef($nr,'kjv');
		}
	}
	
	public function sendEmail($jsonKey,$tu,$version,$mailaddress,$html,$title,$type)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				// load the mailer
				$mailer =& JFactory::getMailer();
				$mailaddress = base64_decode($mailaddress);
				if($mailer->addRecipient($mailaddress)->IsError()){
					return array('status' => 'danger', 'message' => 'Error! Check email address!');
				} else {
					if($user->email != $mailaddress){
						// set user as reply to
						$mailer->addReplyTo($user->email, $user->name);
					}
					// load website default email
					$config = JFactory::getConfig();
					$sender = array( 
						$config->get('mailfrom'),
						$config->get('fromname') );
					$mailer->setSender($sender);
					$title = urldecode($title);
					if($type == 1){ // <-- email of a tag
						// set the subject
						$mailer->setSubject($user->name.'\'s Tag: '.$title);
						// setup the header 
						$header = '<h2>'.$title.'</h2>';
					} elseif($type == 2) { // <-- email of a search
						// set the subject
						$mailer->setSubject($user->name.' Searched: '.$title);
						// setup the header 
						$header = '<h2>'.$title.'</h2>';						
					}
					// fix the html
					$html = base64_decode($html);
					$html = urldecode($html);
					$html = $this->stripNotWanted($html);
					// build the link back to the getBible page
					$appMenuItemid	= $this->getMenuItemId('app');
					$url = rtrim(JURI::root(),"/");
					if($appMenuItemid){
						$getbibleLink 	= $url.$this->getRouteUrl('index.php?Itemid='.$appMenuItemid);
					} else {
						$getbibleLink 	= $url.$this->getRouteUrl('index.php?option=com_getbible&view=app');
					}
					$version = $this->getVersionName($version);
					if($version){
						$versionNote = 'Scripture taken from <b>'.$version.'</b> | ';
					} else {
						$versionNote = '';
					}
					// setup footer
					$footer = '<br /><center><small>'.$versionNote.'Read the Holy Scripture on <a href="'.$getbibleLink.'" target="_blank">'.$config->get('sitename').'</a></small></center>';
					// set the body
					$body = $header.$html.$footer;
					$mailer->isHTML(true);
					$mailer->setBody($body);
					// send the mail
					$send = $mailer->Send();
					if ( $send !== true ) {
						return array('status' => 'danger', 'message' => 'Error sending email: ' . $send->__toString());
					} else {
						return array('status' => 'success', 'message' => 'Email was send successfully!');
					}				
				}
			}
		}
		return array('status' => 'danger', 'message' => 'Error! No Access!');
	}
	
	protected function getMenuItemId($view)
	{
        $menu 		= JFactory::getApplication()->getMenu();
		$component 	= JComponentHelper::getComponent('com_getbible');
        //get only com_getbible menu items
        $items  	= $menu->getItems('component_id', $component->id);
        foreach ($items as $item) {
            if (isset($item->query['view']) && $item->query['view'] === $view) {
				return $item->id;
			}
        }
        return false;
    }
	
	protected function getRouteUrl($route)
	{
		// Get the global site router.
		$config = &JFactory::getConfig();
		$router = JRouter::getInstance('site');
		$router->setMode( $config->get('sef', 1) );
		$uri    = &$router->build($route);
		$path   = $uri->toString(array('path', 'query', 'fragment'));
		return $path;
	}
	
	protected function stripNotWanted($string)
	{
		// Unsafe HTML tags that members may abuse
		$remove = array(
						'/onload="(.*?)"/is',
						'/onunload="(.*?)"/is',
						'/onclick="(.*?)"/is',
						'/data-uk-tooltip="(.*?)"/is',
						'/data-cached-title="(.*?)"/is',
						'/title="(.*?)"/is',
						'/title="(.*?)"/is',
						'/id="(.*?)"/is');
		// do a normal strip of unwanted tags
		$string = strip_tags($string, '<p><span><br>');
		// Remove these tags and all parameters within them
		$string = preg_replace($remove, "", $string);
		return $this->stripCss($string);
	}
	
	protected function stripCss($string)
	{
		//reset
		$css = array();
		$inline = array();
		// highlight styles
		$marks = range('a','z');
		foreach($marks as $mark){
			// small
			$css[] = 'class="verse highlight_'.$mark.' verse_small"';
			$inline[] = 'style="color: '.$this->app_params->get('mark_'.$mark.'_textcolor').'; border-bottom: 1px '.$this->app_params->get('mark_'.$mark.'_linetype').' '.$this->app_params->get('mark_'.$mark.'_linecolor').'; background-color: '.$this->app_params->get('mark_'.$mark.'_background').'; font-size: '.$this->app_params->get('font_small').'px; line-height: 1.5;"';
			// medium
			$css[] = 'class="verse highlight_'.$mark.' verse_medium"';
			$inline[] = 'style="color: '.$this->app_params->get('mark_'.$mark.'_textcolor').'; border-bottom: 1px '.$this->app_params->get('mark_'.$mark.'_linetype').' '.$this->app_params->get('mark_'.$mark.'_linecolor').'; background-color: '.$this->app_params->get('mark_'.$mark.'_background').'; font-size: '.$this->app_params->get('font_medium').'px; line-height: 1.5;"';
			// large
			$css[] = 'class="verse highlight_'.$mark.' verse_large"';
			$inline[] = 'style="color: '.$this->app_params->get('mark_'.$mark.'_textcolor').'; border-bottom: 1px '.$this->app_params->get('mark_'.$mark.'_linetype').' '.$this->app_params->get('mark_'.$mark.'_linecolor').'; background-color: '.$this->app_params->get('mark_'.$mark.'_background').'; font-size: '.$this->app_params->get('font_large').'px; line-height: 1.5;"';
		}
		// other styles
		$css[] = 'class="verse verse_small"';
		$inline[] = 'style="font-size: '.$this->app_params->get('font_small').'px; line-height: 1.5;"';
		$css[] = 'class="verse verse_medium"';
		$inline[] = 'style="font-size: '.$this->app_params->get('font_medium').'px; line-height: 1.5;"';
		$css[] = 'class="verse verse_large"';
		$inline[] = 'style="font-size: '.$this->app_params->get('font_large').'px; line-height: 1.5;"';
		$css[] = 'class="verse_nr ltr nr_small"';
		$inline[] = 'style="font-size: '.($this->app_params->get('font_small') - 3).'px; line-height: 1.5;"';
		$css[] = 'class="verse_nr ltr nr_medium"';
		$inline[] = 'style="font-size: '.($this->app_params->get('font_medium') - 4).'px; line-height: 1.5;"';
		$css[] = 'class="verse_nr ltr nr_large"';
		$inline[] = 'style="font-size: '.($this->app_params->get('font_large') - 5).'px; line-height: 1.5;"';
		$css[] = 'class="verse_nr uk-text-muted ltr"';
		$inline[] = 'style="color:#92969a; direction: ltr; text-align: left; unicode-bidi: bidi-override;"';
		$css[] = 'class="rtl"';
		$inline[] = 'style="direction: rtl; text-align: right; unicode-bidi: bidi-override;"';
		$css[] = 'class="ltr"';
		$inline[] = 'style="direction: ltr; text-align: left; unicode-bidi: bidi-override;"';
		$css[] = 'class="uk-text-center uk-text-bold"';
		$inline[] = 'style="font-size: '.($this->app_params->get('font_large') - 5).'px; line-height: 1.5; font-weight: bold;"';
		$css[] = 'class="highlight"';
		$inline[] = 'style="color: #52a9ca; font-weight: bold;"';
	
		// Remove these css and all its classes and replace with styles
		return str_replace($css, $inline, $string);
	}
	
	protected function getVersionName($version)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('a.name');
		$query->from('#__getbible_versions AS a');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows()){
			 return $db->loadResult();
		}
		return false;
	}
	
	public function getStatusTags($jsonKey,$tu)
	{
		$user = JFactory::getUser();
		if($user->id != 0 && $user->id == (int) base64_decode($tu)){
			// Check Token!
			$token = JSession::getFormToken();
			if ($jsonKey == $token){
				return $this->getStatusTags_db($user->id);
			}
		}
		return array('status' => 'danger', 'message' => 'Error! No Access!');
	}
	
	protected function getStatusTags_db($user)
	{
		// first get tag defaults
		if(strlen($this->app_params->get('tags_defaults')) > 0){
			$tags_string = $this->app_params->get('tags_defaults');
			if (strpos($tags_string,',') !== false) {
					$tags_defaults = explode(',', $tags_string);
			} else {
				$tags_defaults = array($tags_string);
			}
		} else {
			$tags_defaults = array();
		}
		// set global defaults
		$usedTags		= array();
		$unusedTags		= array();
		$usedTagsAlpha	= false;
		$defaultTags	= $tags_defaults;
		// Get a db connection.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id','name')));
		$query->from($db->quoteName('#__getbible_tags'));
		$query->where($db->quoteName('user') . ' = '. $db->quote($user));
		$query->where($db->quoteName('published') . ' = 1');
		$query->order($db->quoteName('name') . ' ASC');
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows()){
			$allTags = $db->loadAssocList('id');
			// get tagged tags
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('b.id','b.name')));
			$query->from($db->quoteName('#__getbible_taged', 'a'));
			$query->join('LEFT', $db->quoteName('#__getbible_tags', 'b') . ' ON (' . $db->quoteName('a.tag') . ' = ' . $db->quoteName('b.id').')');
			$query->where($db->quoteName('a.user') . ' = '. $db->quote($user));
			$query->order($db->quoteName('b.name') . ' ASC');
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$db->execute();
			if($db->getNumRows()){
				$tagedTags = $db->loadAssocList();
				foreach($tagedTags as $taged){
					$usedTags[$taged['id']] = $taged['name'];
					if(array_key_exists($taged['id'], $allTags)){
						unset($allTags[$taged['id']]);
					}
					if(count($defaultTags)){
						$foundDefault = array_search($taged['name'], $defaultTags);
						if($foundDefault){
							unset($defaultTags[$foundDefault]);
						}
					}
				}
				foreach($allTags as $all){
					$unusedTags[$all['id']] = $all['name'];
					if(count($defaultTags)){
						$foundDefault = array_search($all['name'], $defaultTags);
						if($foundDefault){
							unset($defaultTags[$foundDefault]);
						}
					}
				}
			} else {
				foreach($allTags as $all){
					$unusedTags[$all['id']] = $all['name'];
					$foundDefault = array_search($all['name'], $defaultTags);
					if($foundDefault){
						unset($defaultTags[$foundDefault]);
					}
				}
			}			
		}
		function alphaSort($a, $b)
		{
			$c = strtoupper($a[0]);
			$d = strtoupper($b[0]);
			return strcmp($c,$d);
		}
		// sort taks
		if(count($usedTags)){
			usort($usedTags, "alphaSort");
		}
		if(count($unusedTags)){
			usort($unusedTags, "alphaSort");
		}
		if(count($defaultTags)){
			usort($defaultTags, "alphaSort");
		}
		return array('used' => $usedTags, 'unused' => $unusedTags, 'default' => $defaultTags); 
	}
	
	public function getAppDefaults($search_app = 0, $check_search = 'repent', $defaultVersion = 'kjv', $URLkey, $appKey, $crit = '1_1_1', $searchType = 'all')
	{
		if($this->app_params->get('jsonAPIaccess') && $this->app_params->get('jsonQueryOptions') == 1){
			// Get the global data
			$APIkey = $this->app_params->get('jsonAPIkey');
			$token 	= JSession::getFormToken();
		} else {
			$URLkey = 'free';
			$APIkey = 'free';
		}
		if ($URLkey == $APIkey || $appKey == $token){
			if($search_app === 1){
				$load 			= (preg_match('~[0-9]~', $check_search) > 0);
				// if number is found in search request load passage
				if($load){
					// load search to get passage
					$passage 		= $check_search;
					// Load default passage
					$defaultPassage	= $this->app_params->get('defaultStartBook'). ' '. $this->app_params->get('defaultStartChapter');
					// get the defaults from request
					if($passage == $defaultPassage){
						$loadDefaults 			= new stdClass();
						$loadDefaults->Book 	= $this->app_params->get('defaultStartBook');
						$loadDefaults->Chapter 	= $this->app_params->get('defaultStartChapter');
					} else {
						$loadDefaults			= $this->getLoadDefaults($passage);
					}
					// load defaults
					$defaults 					= $this->bookDefaults($loadDefaults->Book,$defaultVersion);
					
					if(is_object($defaults)){
						// set defaults to loading request
						$defaults->chapter 		= $loadDefaults->Chapter;
						$defaults->vers 		= $loadDefaults->Vers;
						$defaults->lastchapter	= $defaults->chapter - 1;
						$defaults->load 		= 1;
						return $defaults;
					}
				}
				
				// Load the results
				$result 			= new stdClass();
				$result->search 	= 1;
				$result->searchFor 	= $check_search;
				$result->version 	= $defaultVersion;
				// ensure the criteria is set correctly
				$result->crit 		= (string) preg_replace('/[^0-9_]/i', '', $crit);
				// check to see if its a book name
				$result->type 		= (string) $searchType;
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
		return false;
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
		$request->Vers	 	= $vers;
		
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
			$savedBooks = GetHelper::getSavedBooks($version);						
			$name 		= mb_strtoupper(preg_replace('/[^A-Z0-9]/i', '', $name), 'UTF-8');
			// set query book number and name
			foreach ($savedBooks as $book_nr){
				$book = GetHelper::getSetBook($version,$book_nr,false);
				if(!$found){
					foreach($book->ref as $key => $value){
						if ($value){
							$value = mb_strtoupper(preg_replace('/[^A-Z0-9]/i', '', $value), 'UTF-8');
							if ($name == $value || $book->book_name == $name){
								$book_ref 	= (string) preg_replace('/\s+/', '', $book->ref->name2);
								$book_nr 	= $book->book_nr;
								$found 		= true; 
								break;					
							} else {
								$found 		= false;
							}
						}
					}
				}
				if ($found){
					break;	
				}
			}
		}
		if(!$found){ 
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
}
