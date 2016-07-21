<?php
/**
*
* 	@version 	1.0.9  June 24, 2016
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
	protected	$local 		= false;
	protected	$curlError	= false;

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
		
		// we need a loger execution time
		if (ini_set('max_execution_time', 300) === false)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_INCREASE_EXECUTION_TIME'), 'error'); return false;
		}

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
		// reload version list for app

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
		if($this->curlError){
			JFactory::getApplication()->enqueueMessage(JText::_($this->curlError), 'error'); return false;
		} else {
			if($this->local){
				JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_LOCAL_OFFLINE'), 'error'); return false;
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_GETBIBLE_OFFLINE'), 'error'); return false;
			}
		}
	}

	public function rutime($ru, $rus, $index) {
		return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
		 -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
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

				// get instilation opstion set in params
				$installOptions = $this->app_params->get('installOptions');

				if($installOptions){
					// get the file
					$filename	= 'https://getbible.net/scriptureinstall/'.$versionFileName.'.txt';
				} else {
					// get localInstallFolder set in params
					$filename = JPATH_ROOT.'/'.rtrim(ltrim($this->app_params->get('localInstallFolder'),'/'),'/').'/'.$versionFileName.'.txt';
				}

				// load the file
				$file = new SplFileObject($filename);
				// start up database
				$db = JFactory::getDbo();
				// load all books
				$books = $this->setBooks($version);

				$i = 1;
				// build query to save
				if (is_object($file)) {
					while (! $file->eof()) {
						$verse = explode("||",$file->fgets());
						$found = false;
						// rename books
						foreach ($books as $book){
							$verse[0] = strtoupper(preg_replace('/\s+/', '', $verse[0]));
							if ($book['nr'] <= 39) {
								if (strpos($verse[0],'O') !== false) {
									$search_value = sprintf("%02s", $book['nr']).'O';
								} else {
									$search_value = sprintf("%02s", $book['nr']);
								}
							} else {
								if (strpos($verse[0],'N') !== false) {
									$search_value = $book['nr'].'N';
								} else {
									$search_value = $book['nr'];
								}
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
						if(isset($verse[3]) && $verse[3]){
							$Bible[$verse[0]][$verse[1]][$verse[2]] = $verse[3];

							// Create a new query object for this verse.
							$versObject = new stdClass();
							$versObject->version = $version;
							$versObject->book_nr = $book_nr;
							$versObject->chapter_nr = $verse[1];
							$versObject->verse_nr = $verse[2];
							$versObject->verse = $verse[3];
							$versObject->access = 1;
							$versObject->published = 1;
							$versObject->created_by = $this->user->id;
							$versObject->created_on = $this->dateSql;
							// Insert the object into the verses table.
							$result = JFactory::getDbo()->insertObject('#__getbible_verses', $versObject);
						}
					}
				}
				// clear from memory
				unset($file);
				// save complete books & chapters
				foreach ($books as $book)
				{
					if (isset($book["nr"]) && isset($Bible[$book["nr"]]))
					{
						$this->saveChapter($version, $book["nr"], $Bible[$book["nr"]]);
						$this->saveBooks($version, $book["nr"], $Bible[$book["nr"]]);
					}
				}
				// clear from memory
				unset($books);

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

				// Create a new query object for this Version.
				$versionObject = new stdClass();
				$versionObject->name = $versionName;
				$versionObject->bidi = $bidi;
				$versionObject->language = $versionLang;
				$versionObject->books_nr = $book_counter;
				$versionObject->testament = $testament;
				$versionObject->version = $version;
				$versionObject->link = $filename;
				$versionObject->installed = 1;
				$versionObject->access = 1;
				$versionObject->published = 1;
				$versionObject->created_by = $this->user->id;
				$versionObject->created_on = $this->dateSql;
				// Insert the object into the versions table.
				$result = JFactory::getDbo()->insertObject('#__getbible_versions', $versionObject);

				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_GETBIBLE_MESSAGE_BIBLE_INSTALLED_SUCCESSFULLY', $versionName));
				// reset the local version list.
				$this->_cpanel();
				// if first Bible is installed change the application to load localy with that Bible as the default
				$this->setLocal();
				// clean cache to insure the dropdown removes this installed version.
				$this->cleanCache('_system');
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
		if($this->curlError){
			JFactory::getApplication()->enqueueMessage(JText::_($this->curlError), 'error'); return false;
		} else {
			if($this->local){
				JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_LOCAL_OFFLINE'), 'error'); return false;
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_GETBIBLE_OFFLINE'), 'error'); return false;
			}
		}
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
		}
		if($this->curlError){
			JFactory::getApplication()->enqueueMessage(JText::_($this->curlError), 'error'); return false;
		} else {
			if($this->local){
				JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_LOCAL_OFFLINE'), 'error'); return false;
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_GETBIBLE_MESSAGE_GETBIBLE_OFFLINE'), 'error'); return false;
			}
		}
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
				// Insert values.
				$values[]  = $db->quote($version).', '.(int)$book_nr.', '.(int)$chapter_nr.', '.$db->quote($scripture).', 1, 1, '.$this->user->id.', '.$db->quote($this->dateSql);
				$chapter_nr++;
			}
			// clear from memory
			unset($chapters);
			// Insert columns.
			$columns = array(
				'version',
				'book_nr',
				'chapter_nr',
				'chapter',
				'access',
				'published',
				'created_by',
				'created_on'
				);

			// Prepare the insert query.
			$query->insert($db->quoteName('#__getbible_chapters'));
			$query->columns($db->quoteName($columns));

			$query->values($values);

			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			$db->query();
		}

		return true;
	}

	protected function saveBooks($version, $book_nr, $book)
	{
		if (is_array($book) && count($book)){
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
			// clear from memory
			unset($book);
			$setup = array('book'=>$setupChapter);
			$saveBook = json_encode($setup);
			// Create a new query object for this verstion
			$query = $db->getQuery(true);
			// Insert columns.
			$columns = array('version', 'book_nr', 'book', 'access', 'published', 'created_by', 'created_on');

			// Insert values.
			$values = array($db->quote($version), (int) $book_nr, $db->quote($saveBook), 1, 1, $this->user->id, $db->quote($this->dateSql));


			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__getbible_books'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			//echo nl2br(str_replace('#__','api_',$query)); die;
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

	protected function setLocalXML()
	{
		jimport( 'joomla.filesystem.folder' );
		// get localInstallFolder set in params
		$path = rtrim(ltrim($this->app_params->get('localInstallFolder'),'/'),'/');
		// creat folder
		JFolder::create(JPATH_ROOT.'/'.$path.'/xml');
		// set the file name
		$filepath = JPATH_ROOT.'/'.$path.'/xml/version.xml.php';
		// set folder path
		$folderpath = JPATH_ROOT.'/'.$path;

		$fh = fopen($filepath, "w");
		if (!is_resource($fh)) {
			return false;
		}
		$data = $this->setPHPforXML($folderpath);
		if(!fwrite($fh, $data)){
			// close file.
			fclose($fh);
			return false;
		}
		// close file.
		fclose($fh);

		// return local file path
		return JURI::root().$path.'/xml/version.xml.php/versions.xml';
	}

	protected function setPHPforXML($path)
	{
		return "<?php

foreach (glob(\"".$path."/*.txt\") as \$filename) {
    \$available[] = str_replace('.txt', '', basename(\$filename));
    // do something with \$filename
}

\$xml = new SimpleXMLElement('<versions/>');

foreach (\$available as \$version) {
   \$xml->addChild('name', \$version);
   }

header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
print(\$xml->asXML());
?>";
	}

	protected function getVersionAvailable()
	{
		// get instilation opstion set in params
		$installOptions = $this->app_params->get('installOptions');

		if(!$installOptions){
			$xml			= $this->setLocalXML();
			$this->local 	= true;
		} else {
			// check the available versions on getBible
			$xml	= 'https://getbible.net/scriptureinstall/xml/version.xml.php/versions.xml';
		}
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
					foreach ($data->name as $version){

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
		} elseif(function_exists('curl_init')){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL,$xml);
			$response_xml_data = curl_exec($ch);
			if(curl_error($ch)){
				$this->curlError = curl_error($ch);
			}
			curl_close($ch);

			$data = simplexml_load_string($response_xml_data);
			// echo'<pre>';var_dump($data);exit;
			if (!$data) {
				$this->availableVersions 		= false;
				$this->availableVersionsList 	= false;
				return false;
			} else {
				foreach ($data->name as $version){

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
		require_once JPATH_ADMINISTRATOR.'/components/com_getbible/models/cpanel.php';
		$cpanel_model = new GetbibleModelCpanel;
		return $cpanel_model->setCpanel();
	}

	protected function setLocal()
	{
		$this->getInstalledVersions();
		$versions 	= $this->installedVersions;
		$number 	= count($versions);
		// only change to local API on install of first Bible
		if ($number == 1){
			// get default Book Name
			$defaultStartBook = $this->app_params->get('defaultStartBook');
			// make sure it is set to the correct name avaliable in this new default version
			// first get book number
			$book_nr = $this->getLocalBookNR($defaultStartBook, $versions[0]);
			// second check if this version has this book and return the book number it has
			$book_nr = $this->checkVersionBookNR($book_nr, $versions[0]);
			// third set the book name
			$defaultStartBook = $this->getLocalDefaultBook($defaultStartBook, $versions[0], $book_nr);
			// Update Global Settings
			$params = JComponentHelper::getParams('com_getbible');
			$params->set('defaultStartVersion', $versions[0]);
			$params->set('defaultStartBook', $defaultStartBook);
			$params->set('jsonQueryOptions', 1);

			// Get a new database query instance
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Build the query
			$query->update('#__extensions AS a');
			$query->set('a.params = ' . $db->quote((string)$params));
			$query->where('a.element = "com_getbible"');

			// Execute the query
			// echo nl2br(str_replace('#__','api_',$query)); die;
			$db->setQuery($query);
			$db->query();

			return true;
		}
		return false;
	}

	protected function getLocalBookNR($defaultStartBook,$version,$retry = false)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('a.book_nr');
		$query->from('#__getbible_setbooks AS a');
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));
		$query->where($db->quoteName('a.book_name') . ' = ' . $db->quote($defaultStartBook));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		 if($num_rows){
			// Load the results
			return $db->loadResult();
		} else {
			// fall back on default
			if($retry){
				return 43;
			}
			return $this->getLocalBookNR($defaultStartBook,'kjv',true);
		}
	}

	protected function checkVersionBookNR($book_nr, $defaultVersion)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('a.book_nr');
		$query->from('#__getbible_books AS a');
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($defaultVersion));
		$query->where($db->quoteName('a.book_nr') . ' = ' . $book_nr);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		 if($num_rows){
			// Load the results
			return $book_nr;
		} else {
			// Run the default
			// Create a new query object.
			$query = $db->getQuery(true);

			$query->select('a.book_nr');
			$query->from('#__getbible_books AS a');
			$query->where($db->quoteName('a.access') . ' = 1');
			$query->where($db->quoteName('a.published') . ' = 1');
			$query->where($db->quoteName('a.version') . ' = ' . $db->quote($defaultVersion));

			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			// Load the results
			$results = $db->loadColumn();
			// set book array
			$results = array_unique($results);
			// set book for Old Testament (Psalms) or New Testament (John)
			if (in_array(43,$results)){
				return 43;
			} elseif(in_array(19,$results)) {
				return 19;
			}
			return false;
		}
	}

	protected function getLocalDefaultBook($defaultStartBook,$defaultVersion,$book_nr,$tryAgain = false)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('a.book_name');
		$query->from('#__getbible_setbooks AS a');
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		if($tryAgain){
			$query->where($db->quoteName('a.version') . ' = ' . $db->quote($tryAgain));
			$query->where($db->quoteName('a.book_nr') . ' = ' . $book_nr);
		} else {
			$query->where($db->quoteName('a.version') . ' = ' . $db->quote($defaultVersion));
			$query->where($db->quoteName('a.book_nr') . ' = ' . $book_nr);
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		 if($num_rows){
			// Load the results
			return $db->loadResult();
		} else {
			// fall back on default
			return $this->getLocalDefaultBook($defaultStartBook,$defaultVersion,$book_nr,'kjv');
		}
	}
}