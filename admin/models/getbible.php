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

class GetbibleModelGetbible extends JModelList
{
	protected $app_params;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params = JComponentHelper::getParams('com_getbible');
		
	}
	
	public function getItem()
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
			// load request
			$version = $this->getV();
			$request = $this->getP($version);
			if ($request) {
				$answer = $this->getPassage($request,$version);
				return $answer;
			} else {
				return NULL;
			}
		} return NULL;
		
	}
	
	protected function getPassage($req,$version)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		if (!$version){
			$version = 'kjv';
		}
		$found = false;
		foreach($req as $get){
			
			if($get['type'] == 'n'){  // <--- if a book is requeted
				// Create a new query object.
				$query = $db->getQuery(true);
				// Order it by the ordering field.
				$query->select($db->quoteName('book'));
				$query->from($db->quoteName('#__getbible_books'));
				$query->where($db->quoteName('book_nr') . ' = '. $db->quote($get['book_nr']));
				$query->where($db->quoteName('version') . ' = '. $db->quote($version));
				$query->where($db->quoteName('access') . ' = 1');
				$query->where($db->quoteName('published') . ' = 1');
				//echo nl2br(str_replace('#__','api_',$query)); die;
				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				 
				// Load the results
				$results =  $db->loadResult();
				if($results){
					$results = substr($results, 1, -1);
					// load direction
					$direction = $this->getDirection($version);
					// load book name
					$bookName	= $this->getBook($get['book_nr'],$version);
					// load book
					$json 		= '{"type":"book","book_name":"'.$bookName.'","book_nr":'.(int)$get['book_nr'].', "direction":"'.$direction.'", '.$results.'}';
					return $json;
				} return false;
			} elseif ($get['type'] == 'nc'){  // <--- if a chapter is requeted
				// Create a new query object.
				$query = $db->getQuery(true);
				// load query
				$query->select($db->quoteName('chapter'));
				$query->from($db->quoteName('#__getbible_chapters'));
				$query->where($db->quoteName('chapter_nr') . ' = '. $db->quote($get['chapter_nr']));
				$query->where($db->quoteName('book_nr') . ' = '. $db->quote($get['book_nr']));
				$query->where($db->quoteName('version') . ' = '. $db->quote($version));
				$query->where($db->quoteName('access') . ' = 1');
				$query->where($db->quoteName('published') . ' = 1');
				//echo nl2br(str_replace('#__','api_',$query)); die;
				 
				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				
				$results =  $db->loadResult();
				if($results){
					$results = substr($results, 1, -1);
					// load direction
					$direction = $this->getDirection($version);
					// load book name
					$bookName	= $this->getBook($get['book_nr'],$version);
					// load chapter
					$json 		= '{"type":"chapter","book_name":"'.$bookName.'","book_nr":'.(int)$get['book_nr'].',"chapter_nr":'.$get['chapter_nr'].', "direction":"'.$direction.'", '.$results.'}';
					return $json;
				} return false;
				
			} elseif ($get['type'] == 'ncv') {   // <--- if verses is requeted
				$chapter 			= $this->getVerses($get,$version);
				if($chapter){
					// load book name
					$bookName			= $this->getBook($get['book_nr'],$version);
					// load verses
					$returns['book'][] 	= array('book_name'=>$bookName, 'book_nr'=>$get['book_nr'], 'chapter_nr'=>$get['chapter_nr'], 'chapter'=>$chapter);
					$found = true;
				}
			}
		}
		if($found){
			// load direction
			$direction = $this->getDirection($version);
			$returns['direction'] = $direction;
			// set type
			$returns['type'] = 'verse';
			return json_encode($returns);
		} return false;
	}
	// user_id 
	protected function getVerses($req,$ver)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		if ($req['verse_nr'][1]){
			$verses = range($req['verse_nr'][0], $req['verse_nr'][1]);
		} else {
			$verses[] = $req['verse_nr'][0];
		}
		// Create a new query object.
		$query = $db->getQuery(true);
		// Order it by the ordering field.
		$query->select($db->quoteName(array('verse')));
		$query->from($db->quoteName('#__getbible_verses'));
		$query->where($db->quoteName('chapter_nr') . ' = '. $db->quote($req['chapter_nr']));
		$query->where($db->quoteName('book_nr') . ' = '. $db->quote($req['book_nr']));
		$query->where($db->quoteName('version') . ' = '. $db->quote($ver));
		$query->where($db->quoteName('verse_nr') . ' IN ('.implode(',', $verses).')');
		$query->where($db->quoteName('access') . ' = 1');
		$query->where($db->quoteName('published') . ' = 1');
		//echo nl2br(str_replace('#__','api_',$query)); die;
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results
		$verses = $db->loadColumn();
		if($verses){
			$i = $req['verse_nr'][0];
			foreach ($verses as $verse){
				$text = array('verse_nr'=>$i, 'verse'=>$verse);
				$result[$i] = $text;
				$i++;
			}
			return $result;
		} return false;
		
	}
	
	protected function getDirection($version)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		// Order it by the ordering field.
		$query->select($db->quoteName('bidi'));
		$query->from($db->quoteName('#__getbible_versions'));
		$query->where($db->quoteName('version') . ' = '. $db->quote($version));
		//echo nl2br(str_replace('#__','api_',$query)); die;
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		
		return $db->loadResult();
				
	}
	
	protected function getV()
	{
		// Get the input data
		$jinput = JFactory::getApplication()->input;
		
		$version = $jinput->get('v', NULL, 'ALNUM');
		if (!$version){
			$version = $jinput->get('ver', NULL, 'ALNUM');
			if (!$version){
				$version = $jinput->get('version', NULL, 'ALNUM');
				if (!$version){
					$version = $jinput->get('translation', NULL, 'ALNUM');
					if (!$version){
						$version = $jinput->get('lang', NULL, 'ALNUM');
						if (!$version){
							return false;
						}
					}
				}
			}
		}
		return strtolower($version);
	}
	
	protected function getP($version)
	{
		// Get the input data
		$jinput = JFactory::getApplication()->input;
		
		// get passage query string
		$passage = $jinput->get('p', NULL, 'SAFE_HTML');
		if (!$passage){
			$passage = $jinput->get('passage', NULL, 'SAFE_HTML');
			if (!$passage){
				$passage = $jinput->get('text', NULL, 'SAFE_HTML');
				if (!$passage){
					$passage = $jinput->get('scrip', NULL, 'SAFE_HTML');
					if (!$passage){
						$passage = $jinput->get('scripture', NULL, 'SAFE_HTML');
						if (!$passage){
							return false;
						}
					}
				}
			}
		}
		
		// proces query string
		$passage = (string) preg_replace('/[^A-Z0-9:;, \-]/i', '', $passage);
		$passage = ltrim($passage, '.');
		if (strpos($passage,';') !== false) {
			$passage = explode(';',$passage);
		} else {
			$passage = array($passage);
		}
		$request = array();
		$i = 0;
		$iChapter 	= 0;
		$iVerse 	= 0;
		$iBook 		= 0;
		foreach ($passage as $get){
			$ch		= NULL;
			$verses = NULL;
			$value1 = NULL;
			$value2 = NULL;
			$name2 	= NULL;
			$string = (string) preg_replace('/[^A-Z0-9]/i', '', $get);
			$value = str_split($string);
			$value1 = array_shift($value);
			$value2 = array_shift($value);
			if (is_numeric($value1) && $value2 && !is_numeric($value2)){
				$num = substr($get, 1);
				$numbers = (string) preg_replace('/[^0-9,:-]/i', '', $num);
				if (strpos($numbers,':') !== false) {
					list($ch,$vers) = explode(':',$numbers);
					if (strpos($vers,',') !== false) {
						$arrayverses = explode(',',$vers);
						foreach ($arrayverses as $ver){
							if (strpos($ver,'-') !== false) {
								$verses[] 	= explode('-',$ver);
							} else {
								$verses[] 	= array($ver);
							}
						}
					} else {
						if (strpos($vers,'-') !== false) {
							$verses[] 	= explode('-',$vers);
						} else {
							$verses[] 	= array($vers);
						}
					}
				} else {
					$ch = (string) preg_replace('/[^0-9]/i', '', $numbers);
				}
				$name = (string) preg_replace('/[^A-Z]/i', '', $get);
				$name = $value1.$name;
			} else {
				$numbers = (string) preg_replace('/[^0-9,:-]/i', '', $get);
				if (strpos($numbers,':') !== false) {
					list($ch,$vers) = explode(':',$numbers);
					if (strpos($vers,',') !== false) {
						$arrayverses = explode(',',$vers);
						foreach ($arrayverses as $ver){
							if (strpos($ver,'-') !== false) {
								$verses[] 	= explode('-',$ver);
							} else {
								$verses[] 	= array($ver);
							}
							$i++;
						}
					} else {
						if (strpos($vers,'-') !== false) {
							$verses[] 	= explode('-',$vers);
						} else {
							$verses[] 	= array($vers);
						}
					}
				} else {
					$ch = (string) preg_replace('/[^0-9]/i', '', $numbers);
				}
				$name = (string) preg_replace('/[^A-Z]/i', '', $get);
			}
			
			
			if ($name){
				$found = false;
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
									$book_nr = $book['nr']; $book_name = $book['name']; $found = true; break;					
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
			if ($found){
				
				$last = $i - 1;
				if ($book_nr && !$ch) {
					$request[] = array('type' => 'n', 'book_nr' => $book_nr, 'book_name' => $book_name);
					$iBook++;break;
				} elseif ($book_nr && $ch && !$verses) {
					$request[] = array('type' => 'nc', 'book_nr' => $book_nr, 'book_name' => $book_name, 'chapter_nr' => $ch);
					$iChapter++;break;
				} elseif ($ch && !$verses){
					$request[] = array('type' => 'nc', 'book_nr' => $request[$last]["book_nr"], 'book_name' => $request[$last]["book_name"], 'chapter_nr' => $ch);
					$iChapter++;break;
				} elseif (is_array($verses)){
					foreach ($verses as $arrayVerse){
						sort($arrayVerse);
						if ($book_nr && $ch){
							$request[] = array('type' => 'ncv', 'book_nr' => $book_nr, 'book_name' => $book_name, 'chapter_nr' => $ch, 'verse_nr' => $arrayVerse);
							$iVerse++;
							if ($iVerse == 12) {
								break;
							}
						} elseif ($ch){
							$request[] = array('type' => 'ncv', 'book_nr' => $request[$last]["book_nr"], 'book_name' => $request[$last]["book_name"], 'chapter_nr' => $ch, 'verse_nr' => $arrayVers);			
							$iVers++;
							if ($iVers == 12) {
								break;
							}
						} 
					}
				}
			} elseif (!$found){
				if ($name){
					$found2 = false;
					// load all books again but now as KJV
					$books = $this->setBooks($version, true);
					// set query book number and name
					foreach ($books as $book){
						if(!$found2){						
							$name = mb_strtoupper(preg_replace('/\s+/', '', $name), 'UTF-8');
							foreach($book['book_names'] as $key => $value){
								if ($value){
									$value = mb_strtoupper(preg_replace('/\s+/', '', $value), 'UTF-8');
									if ($name == $value){
										$book_nr = $book['nr']; $book_name = $book['name']; $found2 = true; break;					
									} else {
										$found2 = false;
									}
								}
							}
						}
						if ($found2){
							break;
						}
					}
				}
				if ($found2){
				
					$last = $i - 1;
					if ($book_nr && !$ch) {
						$request[] = array('type' => 'n', 'book_nr' => $book_nr, 'book_name' => $book_name);
						$iBook++;break;
					} elseif ($book_nr && $ch && !$verses) {
						$request[] = array('type' => 'nc', 'book_nr' => $book_nr, 'book_name' => $book_name, 'chapter_nr' => $ch);
						$iChapter++;break;
					} elseif ($ch && !$verses){
						$request[] = array('type' => 'nc', 'book_nr' => $request[$last]["book_nr"], 'book_name' => $request[$last]["book_name"], 'chapter_nr' => $ch);
						$iChapter++;break;
					} elseif (is_array($verses)){
						foreach ($verses as $arrayVers){
							sort($arrayVers);
							if ($book_nr && $ch){
								$request[] = array('type' => 'ncv', 'book_nr' => $book_nr, 'book_name' => $book_name, 'chapter_nr' => $ch, 'verse_nr' => $arrayVers);
								$iVers++;
								if ($iVers == 12) {
									break;
								}
							} elseif ($ch){
								$request[] = array('type' => 'ncv', 'book_nr' => $request[$last]["book_nr"], 'book_name' => $request[$last]["book_name"], 'chapter_nr' => $ch, 'verse_nr' => $arrayVers);			
								$iVers++;
								if ($iVers == 12) {
									break;
								}
							} 
						}
					}
				}
			}
			// check point
			if ($iVers == 7 || $iBook == 1 || $iChapter == 1) {
				break;
			}
			$i++;
		}
		return $request;
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
					$books[$book['book_nr']]['name'] 	= $book['book_name'];
				}
			}
		}
		if(!isset($books)){
			 
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
				$books[$book['book_nr']]['book_names'] 	= (array)json_decode($book['book_names']);
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
				$books[$book['book_nr']]['book_names'] 	= (array)json_decode($book['book_names']);
			}
		}
		return $books;		
	}
	
	protected function getBook($book_nr, $version, $default = 'kjv')
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		if ($version){
			// Create a new query object.
			$query = $db->getQuery(true);
			// load query
			$query->select('book_name');
			$query->from($db->quoteName('#__getbible_setbooks'));
			$query->where($db->quoteName('version') . ' = '. $db->quote($version));
			$query->where($db->quoteName('book_nr') . ' = '. (int) $book_nr);
			$query->where($db->quoteName('access') . ' = 1');
			$query->where($db->quoteName('published') . ' = 1');
			 

			//echo nl2br(str_replace('#__','api_',$query)); die;
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			
			$book = $db->loadResult();
		}
		if(!isset($book)){
			 
			// Create a new query object.
			$query = $db->getQuery(true);
			// load query
			$query->select('book_name');
			$query->from($db->quoteName('#__getbible_setbooks'));
			$query->where($db->quoteName('version') . ' = '. $db->quote($default));
			$query->where($db->quoteName('book_nr') . ' = '. (int) $book_nr);
			$query->where($db->quoteName('access') . ' = 1');
			$query->where($db->quoteName('published') . ' = 1');
			 
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			
			$book = $db->loadResult();
		}
		if ($book){
			return $book;
		}
		return false;
	}
	
	
	public function getAvailableVersions()
	{
		// Base this model on the backend version.
		require_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'import.php';
		$versions_model 		= new GetbibleModelImport;
		$availableVersions 		= $versions_model->availableVersions;
		$availableVersionsList 	= $versions_model->availableVersionsList;
		$installedVersions 		= $versions_model->installedVersions;
		
		if($availableVersionsList){
			if ($installedVersions){
				$availableVersionsList = array_diff($availableVersionsList, $installedVersions);
			}
			foreach($availableVersionsList as $version){
				$availableVersions[$version]['not'] = 1;
			}
			return $availableVersions;
		} return false;
	}

}