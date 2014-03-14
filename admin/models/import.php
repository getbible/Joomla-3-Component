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

//Import filesystem libraries. Perhaps not necessary, but does not hurt
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.application.component.helper');

class GetbibleModelImport extends JModelLegacy
{
	protected 	$user;
	protected 	$dateSql;
	protected 	$book_counter;
	protected 	$app_params;
	
	public 		$availableVersions;
	public 		$availableVersionsList;
	public 		$installedVersions;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params = JComponentHelper::getParams('com_getbible');
		
		// get user data
		$this->user = JFactory::getUser();
		// get todays date
		$this->dateSql = JFactory::getDate()->toSql();
		
		// load available verstions
		$this->getVersionAvailable();
		// get installed versions
		$this->getInstalledVersions();
		
	}
	
	protected function populateState() 
	{		
		parent::populateState();
		
		
		// Get the input data
		$jinput = JFactory::getApplication()->input;
		
		$source = $jinput->post->get('translation', NULL, 'STRING');
		$translation = (string) preg_replace('/[^A-Z0-9_\)\(-]/i', '', $source);
		
		// Set to state
		$this->setState('translation', $translation);
	}
	
	public function getVersions()
	{
		// get instilation opstion set in params
		$installOptions = $this->app_params->get('installOptions');
		// reload version list for app
		$this->_cpanel();
		
		$available = $this->availableVersionsList;
		$alreadyin = $this->installedVersions;
		if($available){
			if ($alreadyin){
				$result = array_diff($available, $alreadyin);
			} else {
				$result = $available;
			}
			if($result){
				$setVersions = array();
				$setVersions[] = JHtml::_('select.option', '', JText::_('COM_GETBIBLE_PLEASE_SELECT'));
				foreach ($this->availableVersions as $key => $values){
					if(in_array($key, $result)){
					$name = $values["versionName"]. ' ('.$values["versionLang"].')';
					$setVersions[]  = JHtml::_('select.option', $values["fileName"], $name);
					}
				}
				return $setVersions;
			}
			JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_ALL_BIBLES_ALREADY_INSTALLED'), 'success');
			return false;
		}
		JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_GETBIBLE_OFFLINE'), 'error'); return false;
	}
	
	public function getImport()
	{
		if ($this->getState('translation')){
			// set version
			$versionFileName = $this->getState('translation');
			$versionfix = str_replace("___", "'", $versionFileName);
			list($versionLang,$versionName,$versionCode,$bidi) = explode('__', $versionfix);
			$versionName = str_replace("_", " ", $versionName);
			$version = $versionCode;
			//check input
			if ($this->checkTranslation($version) && $this->checkFileName($versionFileName)){

				// get the file
				$filename 			= 'http://getbible.net/scriptureinstall/'.$versionFileName.'.txt';
				
				// parse data to array of srings
				$lines = file($filename, FILE_IGNORE_NEW_LINES);
				// split strings into arrays
				foreach ($lines as $line){
					//$line = str_replace ("||", "##",$line );
					//$line = str_replace ("|", "##",$line );
					$verses[] = explode("||",$line );
				}
				
				// start up database
				$db = JFactory::getDbo();
				// load all books
				$books = $this->setBooks($version);
				
				$i = 1;
				// build query to save
				foreach ($verses as $verse){
					$found = false;
					// rename books
					foreach ($books as $book){
						$verse[0] = strtoupper(preg_replace('/\s+/', '', $verse[0]));
						if ($book['nr'] <= 39) {
							$search_value = sprintf("%02s", $book['nr']).'O';
						} else {
							$search_value = $book['nr'].'N';
						}
						if ($verse[0] == $search_value){
							$verse[0] 	= $book['nr'];
							$book_nr 	= $book['nr'];
							$book_name 	= $book['name'];
							$found 		= true;
							break;					
						}
					}
					if(!$found){
						foreach ($books as $book){
							$verse[0] = strtoupper(preg_replace('/\s+/', '', $verse[0]));
							foreach($book['book_names'] as $key => $value){
								if ($value){
									$value = strtoupper(preg_replace('/\s+/', '', $value));
									if ($verse[0] == $value){
										$verse[0] 	= $book['nr'];
										$book_nr 	= $book['nr'];
										$book_name 	= $book['name'];
										$found 		= true;
										break;					
									}
								}
							}
						}
					}
					if(!$found){
						// load all books again as KJV
						$books = $this->setBooks($version, true);
						foreach ($books as $book){
							foreach($book['book_names'] as $key => $value){
								if ($value){
									$value = strtoupper(preg_replace('/\s+/', '', $value));
									if ($verse[0] == $value){
										$verse[0] 	= $book['nr'];
										$book_nr 	= $book['nr'];
										$found 		= true;
										break;					
									}
								}
							}
						}
					}
					// set data
					if($verse[3]){
						$Bible[$verse[0]][$verse[1]][$verse[2]] = $verse[3];
								
						// Create a new query object for this verse
						$query = $db->getQuery(true);
						// Insert columns
						$columns = array( 'version', 'book_nr', 'chapter_nr', 'verse_nr', 'verse', 'access', 'published', 'created_by', 'created_on');
						// Insert values.
						$values	= array( 
										$db->quote($version), 
										$db->quote($book_nr), 
										$db->quote($verse[1]), 
										$db->quote($verse[2]), 
										$db->quote($verse[3]), 
										1,
										1,
										$this->user->id, 
										$db->quote($this->dateSql)
										);
						// Prepare the insert query.
						$query
							->insert($db->quoteName('#__getbible_verses'))
							->columns($db->quoteName($columns))
							->values(implode(',', $values));
						 
						// Set the query using our newly populated query object and execute it.
						$db->setQuery($query);
						$db->query();
					}					
				}
				// save complete books & chapters
				foreach ($books as $book){
					
					$this->saveChapter($version, $book["nr"], $Bible[$book["nr"]]);
					$this->saveBooks($version, $book["nr"], $Bible[$book["nr"]]);
					
				}
				
				// Set version details
				if(is_array($this->book_counter)){
					if(in_array(1,$this->book_counter) && in_array(66,$this->book_counter)){
						$testament = 'OT&NT';
					} elseif(in_array(1,$this->book_counter) && !in_array(66,$this->book_counter)){
						$testament = 'OT';
					} elseif(!in_array(1,$this->book_counter) && in_array(66,$this->book_counter)){
						$testament = 'NT';
					} else {
						$testament = 'NOT';
					}
					$book_counter = json_encode($this->book_counter);
				} else {
					$book_counter 	= 'error';
					$testament	 	= 'error';
				}
				
				// Create a new query object for this version
				$query = $db->getQuery(true);
				// Insert columns
				$columns = array( 'name', 'bidi', 'language', 'books_nr', 'testament', 'version', 'link', 'installed', 'access', 'published', 'created_by', 'created_on');
				// Insert values.
				$values = array( 
								$db->quote($versionName),
								$db->quote($bidi),
								$db->quote($versionLang),
								$db->quote($book_counter),
								$db->quote($testament),								
								$db->quote($version), 
								$db->quote($filename),
								1,
								1,
								1,
								$this->user->id,
								$db->quote($this->dateSql)
								);
				// Prepare the insert query.
				$query
					->insert($db->quoteName('#__getbible_versions'))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));
				 
				// Set the query using our newly populated query object and execute it.
				$db->setQuery($query);
				$db->query();
				
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_GETBIBLE_MESSAGE_BIBLE_INSTALLED_SUCCESSFULLY', $versionName));
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	protected function checkTranslation($version)
	{
		// get instilation opstion set in params
		$installOptions = $this->app_params->get('installOptions');
		
		$available = $this->availableVersionsList;
		$alreadyin = $this->installedVersions;
		if ($available){
			if(in_array($version, $available)){
				if ($alreadyin){
					if(in_array($version, $alreadyin)){
						JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_VERSION_ALREADY_INSTALLED'), 'error'); return false;
					}return true;
				}return true;
			}
			JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_VERSION_NOT_FOUND_ON_GETBIBLE'), 'error'); return false;
		}
		JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_GETBIBLE_OFFLINE'), 'error'); return false;
	}
	
	protected function checkFileName($versionFileName)
	{
		$available = $this->availableVersions;
		$found = false;
		if ($available){
			foreach($available as $file){
				if (in_array($versionFileName, $file)){
					$found = true;
					break;
				} else {
					$found = false;
				}
			}
			if(!$found){
				JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_VERSION_NOT_FOUND_ON_GETBIBLE'), 'error'); return false;
			} else {
				return $found;
			}
		}JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_GETBIBLE_OFFLINE'), 'error'); return false;
	}
	
	protected function saveChapter($version, $book_nr, $chapters)
	{
		if ( $chapters ){
			// start up database
			$db = JFactory::getDbo();
			// Create a new query object for this verstion
			$query = $db->getQuery(true);
			// set chapter number
			$chapter_nr = 1;
			$values = '';
			// set the data
			foreach ($chapters as $chapter)
			{
				$setup = NULL;
				$text = NULL;
				$ver = 1;
				foreach($chapter as $verse)
				{
					$text[$ver] = array( 'verse_nr' => $ver,'verse' => $verse);
					$ver++; 
				}
				$setup = array('chapter'=>$text);
				$scripture = json_encode($setup);
				if($chapter_nr != 1){
					$values .= ', ';
				}
				// Insert values.
				$values .= '('.$db->quote($version).', '.$db->quote((int)$book_nr).', '.$db->quote((int)$chapter_nr).', '.$db->quote($scripture).', 1, 1, '.$this->user->id.', '.$db->quote($this->dateSql).')';
				$chapter_nr++;
			}
			// Insert columns.
			$columns = '('
							.$db->quoteName('version').', '
							.$db->quoteName('book_nr').', '
							.$db->quoteName('chapter_nr').', '
							.$db->quoteName('chapter').', '
							.$db->quoteName('access').', '
							.$db->quoteName('published').', '
							.$db->quoteName('created_by').', '
							.$db->quoteName('created_on').
						')';
			
			// Prepare the insert query.
			$query = "INSERT INTO ".$db->quoteName('#__getbible_chapters')." ";


			$query .= $columns."  VALUES  ";
			$query .= $values;
			// echo nl2br(str_replace('#__','giz_',$query)); die;
			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			$db->query();
		}
		
		return true;
	}
	
	protected function saveBooks($version, $book_nr, $book)
	{
		if ( $book ){
			//set book number
			$this->book_counter[] = $book_nr;
			// start up database
			$db = JFactory::getDbo();
			// Create a new query object for this verstion
			$query = $db->getQuery(true);
			// set chapter number
			$chapter_nr = 1;
			$values = '';
			// set the data
			foreach ($book as $chapter)
			{
				$setup = NULL;
				$text = NULL;
				$ver = 1;
				foreach($chapter as $verse)
				{
					$text[$ver] = array( 'verse_nr' => $ver,'verse' => $verse);
					$ver++; 
				}
				$setupChapter[$chapter_nr] = array('chapter_nr'=>$chapter_nr,'chapter'=>$text);
				$chapter_nr++;
			}
			$setup = array('book'=>$setupChapter);
			$saveBook = json_encode($setup);
			// Create a new query object for this verstion
			$query = $db->getQuery(true);
			// Insert columns.
			$columns = array('version', 'book_nr', 'book', 'access', 'published', 'created_by', 'created_on');
			 
			// Insert values.
			$values = array($db->quote($version), $db->quote($book_nr), $db->quote($saveBook), 1, 1, $this->user->id, $db->quote($this->dateSql));
			 

			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__getbible_books'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			//echo nl2br(str_replace('#__','giz_',$query)); die;
			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			$db->query();
		}
		
		return true;
	}
	
	protected function getInstalledVersions()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 

		// Create a new query object.
		$query = $db->getQuery(true);
		// Order it by the ordering field.
		$query->select($db->quoteName('version'));
		$query->from($db->quoteName('#__getbible_versions'));
		$query->order('version ASC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results
		$results = $db->loadColumn();
		
		if ($results){
			$this->installedVersions = $results;
			return true;
		}
		return false;

	}
	
	/*protected function getVersionAvailable()
	{
		$path = JPATH_SITE.'/scriptureinstall';
		$recurse = false;
		$fullpath = false;
		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html', '.htaccess');
		if (JFolder::exists($path)){
			$files = JFolder::files($path, $filter = '.', $recurse, $fullpath , $exclude);
			foreach ($files as $file){
				$found[] = JFile::stripExt($file);
			}
			echo '<pre>';
			var_dump($found);exit;
			return $found;
		}
		return false;

	}*/
	
	protected function getVersionAvailable()
	{
		
		// check the available versions on getBible
		$xml 				= 'http://www.getbible.net/scriptureinstall/xml/version.xml.php/versions.xml';
		if(@fopen($xml, 'r')){
				if (($response_xml_data = file_get_contents($xml))===false){
				$this->availableVersions 		= false;
				$this->availableVersionsList 	= false;
				return false;
			} else {
			   libxml_use_internal_errors(true);
			   $data = simplexml_load_string($response_xml_data);
			   if (!$data) {
					$this->availableVersions 		= false;
					$this->availableVersionsList 	= false;
					return false;
			   } else {
					$data 	= json_encode($data);
					$data 	= json_decode($data,TRUE);
					foreach ($data['name'] as $version){
						
						$versionfix = str_replace("___", "'", $version);
						list($versionLang,$versionName,$versionCode) = explode('__', $versionfix);
						$versionName = str_replace("_", " ", $versionName);
						$versions[$versionCode]['fileName'] = $version;
						$versions[$versionCode]['versionName'] = $versionName;
						$versions[$versionCode]['versionLang'] = $versionLang;
						$versions[$versionCode]['versionCode'] = $versionCode;
						$version_list[] = $versionCode;				
					}
					$this->availableVersions 		= $versions;
					$this->availableVersionsList 	= $version_list;
					return true;
			   }
			}
		} else {
			$this->availableVersions 		= false;
			$this->availableVersionsList 	= false;
			return false;
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
				$books[$book['book_nr']]['name'] 	= $book['book_name'];
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
				if(!$books[$book['book_nr']]['nr']){
					$books[$book['book_nr']]['nr']		= $book['book_nr'];
				}
				
				$books[$book['book_nr']]['book_names'] 	= (array)json_decode($book['book_names']);
				
				if(!$books[$book['book_nr']]['name']){
					$books[$book['book_nr']]['name'] 	= $book['book_name'];
				}
			}
		}
		return $books;		
	}	
	
	protected function _cpanel()
	{
		// Base this model on the backend version.
		require_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'cpanel.php';
		$cpanel_model = new GetbibleModelCpanel;
		return $cpanel_model->setCpanel();
	}
}