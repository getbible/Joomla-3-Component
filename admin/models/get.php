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

class GetbibleModelGet extends JModelList
{
	protected $app_params;
	protected $request_query;
	protected $request_type;
	protected $request_version;
	
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
			$search  = $this->searchFor();
			if ($request) {
				$answer = $this->getPassage($request,$version);
				return $answer;
			} elseif ($search){
				$type 	= $this->searchType();
				$answer = $this->getSearch($search, $type, $version);
				return $answer;
			} else {
				return NULL;
			}
		} return NULL;
		
	}
	
	public function getRequest()
	{
		// start setup of Request Object
		$request 			= new StdClass;
		$request->query 	= $this->request_query;
		$request->type 		= $this->request_type;
		$request->version 	= $this->request_version;
		
		return $request;
	}
	protected function getSearch($search, $type, $version)
	{
		$how = $this->searchCriteria();
		// Get a db connection.
		$db = JFactory::getDbo();
		// set default version
		if (!$version){
			$version = 'kjv';
		}
		// case sensitive swith
		if($how['case'] == 2){ 
			$case = 'BINARY';
		} else {
			$case = ' ';
		}
		
		// Create a new query object.
		$query = $db->getQuery(true);
			
		// Set Query.
		$query->select($db->quoteName(array('a.verse','a.verse_nr','a.chapter_nr','a.book_nr')));
		$query->from('#__getbible_verses AS a');	
		$query->where($db->quoteName('a.version') . ' = '. $db->quote($version));
		
		if($how['search'] == 2){ // 2 = ANY WORDS
			if($how['match'] == 2){
				// 2 = partial match
				if (strpos($search,' ') !== false) {
					$words = explode(' ', $search);
					$dbSearch = '('.$case.' a.verse LIKE ';
					$i = 0;
					foreach ($words as $word){
						if (!$i){
							$dbSearch .= $db->quote('%' . $db->escape($word, true) . '%');
						} else {
							$dbSearch .= ' OR '.$case.' a.verse LIKE '.$db->quote('%' . $db->escape($word, true) . '%');
						}
						$i++;
					}
					$dbSearch .= ')';
					$query->where($dbSearch);
				} else {
					$dbSearch = $db->quote('%' . $db->escape($search, true) . '%');	
					$query->where('( '.$case.' a.verse LIKE ' . $dbSearch . ')');
				}		
			} elseif($how['match'] == 1) {
				// 1 = exact match
				if (strpos($search,' ') !== false) {
					$words = explode(' ', $search);
					$dbSearch = '( ' . $case . ' a.verse  REGEXP ';
					$i = 0;
					foreach ($words as $word){
						if (!$i){
							$dbSearch .=  $db->quote('[[:<:]]' . $db->escape($word, true). '[[:>:]]');
						} else {
							$dbSearch .= ' OR '.$case.' a.verse REGEXP '. $db->quote('[[:<:]]' . $db->escape($word, true). '[[:>:]]');
						}
						$i++;
					}
					$dbSearch .= ')';
					$query->where($dbSearch);
				} else {
					$dbSearch_1 = $case . ' a.verse LIKE ' . $db->quote('% ' . $db->escape($search, true) . ' %');
					$dbSearch_2 = 'OR '. $case . ' a.verse LIKE ' . $db->quote($db->escape($search, true) . ' %');
					$dbSearch_3 = 'OR '. $case . ' a.verse LIKE ' . $db->quote('% ' . $db->escape($search, true));
					$query->where('( '. $dbSearch_1 . $dbSearch_2 . $dbSearch_3 . ')');	
				}			
			}
		} elseif ($how['search'] == 3){ // 3 = EXACT PHRASE
			if($how['match'] == 2){
				// 2 = partial match
				$dbSearch = $db->quote('%' . $db->escape($search, true) . '%');	
				$query->where('( '.$case.' a.verse LIKE ' . $dbSearch . ')');		
			} elseif($how['match'] == 1) {
				// exact match
				$dbSearch =  $case . ' a.verse  REGEXP ' . $db->quote('[[:<:]]' . $db->escape($search, true). '[[:>:]]');
				$query->where('( '. $dbSearch . ')');			
			}
		} elseif ($how['search'] == 1){ // 1 = ALL WORDS
			if($how['match'] == 2){
				// 2 = partial match
				if (strpos($search,' ') !== false) {
					$words = explode(' ', $search);
					foreach ($words as $word){
						$dbSearch = $db->quote('%' . $db->escape($word, true) . '%');	
						$query->where('( '.$case.' a.verse LIKE ' . $dbSearch . ')');
					}
				} else {
					$dbSearch = $db->quote('%' . $db->escape($search, true) . '%');	
					$query->where('( '.$case.' a.verse LIKE ' . $dbSearch . ')');
				}		
			} elseif($how['match'] == 1) {
				// 1 = exact match
				if (strpos($search,' ') !== false) {
					$words = explode(' ', $search);
					foreach ($words as $word){
						$dbSearch	=  $case.' a.verse REGEXP '. $db->quote('[[:<:]]' . $db->escape($word, true). '[[:>:]]');
						$query->where('( '. $dbSearch . ')');
					}
				} else {
					$dbSearch	=  $case.' a.verse REGEXP '. $db->quote('[[:<:]]' . $db->escape($search, true). '[[:>:]]');
					$query->where('( '. $dbSearch . ')');
				}			
			}
		}
		// set default type
		if (!$type){
			$type = 'all';
		}
		// load as per type option	
		if($type == 'all'){  
			// if search the whole bible
		} elseif($type == 'ot'){  
			// if search the old Testament
			$books = range(1, 39);
			$query->where($db->quoteName('a.book_nr') . ' IN ('.implode(',', $books).')');
			
		} elseif($type == 'nt'){  
			//if search the new Testament
			$books = range(40, 66);
			$query->where($db->quoteName('a.book_nr') . ' IN ('.implode(',', $books).')');
			
		} else {
			// if search only a book
			$found = false;
			// load all books
			$books = $this->setBooks($version);
			// set query book number and name
			foreach ($books as $book){
				if(!$found){						
					$name = mb_strtoupper(preg_replace('/\s+/', '', $type), 'UTF-8');
					foreach($book['book_names'] as $key => $value){
						if ($value){
							$value = mb_strtoupper(preg_replace('/\s+/', '', $value), 'UTF-8');
							if ($name == $value){
								$book_nr = (int)$book['nr']; $found = true; break;					
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
			
			if($found){
				$query->where($db->quoteName('a.book_nr') . ' = ' . $book_nr);
			}
		}
		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order($db->quoteName('a.book_nr') . ' ASC');
		
		// echo nl2br(str_replace('#__','api_',$query)); die;
		$db->setQuery($query);
		// Load the results
		$verses = $db->loadAssocList();
		$counter = 0;
		if($verses){
			foreach($verses as $verse){
				$key 		= $verse['book_nr'].'_'.$verse['chapter_nr'];
				// group verses
				$chapters[$key][$verse['verse_nr']] = array('verse_nr'=>$verse['verse_nr'], 'verse'=>$verse['verse']);
				$counter++;
				
			}
			foreach($chapters as $key => $chapter){
				list($book_nr,$chapter_nr,) = explode('_',$key);
				// get book name
				$book_name 	= $this->getBook($book_nr,$version);
				$book_ref	= $this->getBookRef($book_nr,$version);
				// load in to result set
				$returns['book'][] = array('version' => $version, 'book_name'=>$book_name, 'book_ref'=>$book_ref, 'book_nr'=>$book_nr, 'chapter_nr'=>$chapter_nr, 'chapter'=>$chapter);
				
			}
			// load direction
			$direction = $this->getDirection($version);
			$returns['direction'] 	= $direction;
			// set the number of results
			$returns['counter'] 	= $counter;
			// set type
			$returns['type'] 		= 'search';
			return json_encode($returns);
		} return false;
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
					$json 		= '{"type":"book","version":"'.$version.'","book_name":"'.$bookName.'","book_nr":'.(int)$get['book_nr'].', "direction":"'.$direction.'", '.$results.'}';
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
					$json 		= '{"type":"chapter","version":"'.$version.'","book_name":"'.$bookName.'","book_nr":'.(int)$get['book_nr'].',"chapter_nr":'.$get['chapter_nr'].', "direction":"'.$direction.'", '.$results.'}';
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
			$returns['version'] = $version;
			return json_encode($returns);
		} return false;
	}
	
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
		$query->order('verse_nr ASC');
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
		// echo nl2br(str_replace('#__','api_',$query)); die;
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
		$version = strtolower($version);
		// set request log values
		$this->request_version = $version;
		
		return $version;
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
		
		// set request log values
		$this->request_query = $passage;
		$this->request_type = 'passage';
		
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
	
	protected function searchFor()
	{
		// Get the input data
		$jinput = JFactory::getApplication()->input;
		
		$search = $jinput->get('s', NULL, 'SAFE_HTML');
		if (!$search){
			$search = $jinput->get('for', NULL, 'SAFE_HTML');
			if (!$search){
				$search = $jinput->get('search', NULL, 'SAFE_HTML');
				if (!$search){
					$search = $jinput->get('lookup', NULL, 'SAFE_HTML');
					if (!$search){
						$search = $jinput->get('find', NULL, 'SAFE_HTML');
						if (!$search){
							return false;
						}
					}
				}
			}
		}
		// set request log values
		$this->request_query = $search;
		$this->request_type = 'search';		
		// return search value
		return $search;
	}
	
	protected function searchType()
	{
		// Get the input data
		$jinput = JFactory::getApplication()->input;
		
		$type = $jinput->get('t', NULL, 'ALNUM');
		if (!$type){
			$type = $jinput->get('in', NULL, 'ALNUM');
			if (!$type){
				$type = $jinput->get('type', NULL, 'ALNUM');
				if (!$type){
					return false;
				}
			}
		}
		return strtolower($type);
	}
	
	protected function searchCriteria()
	{
		// Get the input data
		$jinput = JFactory::getApplication()->input;
		
		$criteria = $jinput->get('crit', NULL, 'CMD');
		if (!$criteria){
			$criteria = $jinput->get('criteria', NULL, 'CMD');
			if (!$criteria){
				$criteria = $jinput->get('way', NULL, 'CMD');
			}
		}
		$criteria = (string) preg_replace('/[^0-9_]/i', '', $criteria);
		// set criteria
		if (strpos($criteria,'_') !== false) {
			list($search,$match,$case)  =  explode('_', $criteria);
		}
		// set the way search is to be made 1 = ALL WORDS, 2 = ANY WORDS, 3 = EXACT PHRASE
		if($search){
			$crit['search'] = (int)$search;
		} elseif ((int)$criteria <= 3){
			$crit['search'] = (int)$criteria;
		} else {
			$crit['search'] = (int)1;
		}
		// set the way match is made 1 = EXACT MATCH, 2 = PARTIAL MATCH
		if((int)$match <= 2){
			$crit['match'] = (int)$match;
		} else {
			$crit['match'] = (int)1;
		}
		// set the case sentisitivity is handled 1 = CASE INSENSITIVE, 2 = CASE SENSITIVE	 
		if((int)$case <= 2){
			$crit['case'] = (int)$case;
		} else {
			$crit['case'] = (int)1;
		}
		
		return $crit;
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
	
	protected function getBookRef($book_nr,$version,$tryAgain = false)
	{
		
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.book_names');
		$query->from('#__getbible_setbooks AS a');		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		if($tryAgain){
			$query->where($db->quoteName('a.version') . ' = ' . $db->quote($tryAgain));
		} else {
			$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));
		}
		$query->where($db->quoteName('a.book_nr') . ' = ' . $book_nr);
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		 if($num_rows){
			// Load the results
			$result 			= $db->loadObject();
			return json_decode($result->book_names)->name2;
		} else {
			// fall back on default
			return $this->getBookRef($book_nr,$version,'kjv');
		}
	}
	
	public function getAvailableVersions()
	{
		// Base this model on the backend version.
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_getbible'.DS.'models'.DS.'import.php';
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
