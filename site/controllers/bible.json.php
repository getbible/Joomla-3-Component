<?php
/**
* 
* 	@version 	1.0.4  December 06, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

// import Joomla controller library
jimport('joomla.application.component.controller');

class GetbibleControllerBible extends JControllerLegacy
{
	public function __construct($config)
	{
		parent::__construct($config);
		
		$this->registerTask('books', 'bible');
		$this->registerTask('chapter', 'bible');
		$this->registerTask('defaults', 'bible');
		$this->registerTask('setbookmark', 'bible');
		$this->registerTask('setbookmarks', 'bible');
		$this->registerTask('getbookmarks', 'bible');
		$this->registerTask('clearbookmarks', 'bible');
	}
	
	public function bible()
	{
		$jinput 	= JFactory::getApplication()->input;
		$task 		= $this->getTask();
		if ($task == 'books'){
			try
			{
				$version = $jinput->get('v', NULL, 'ALNUM');
				
				if($version){
					$result = $this->getModel('control')->getBooks($jinput->get('v', NULL, 'ALNUM'));
				} else {
					$result = false;
				}
				echo $_GET['callback']."(".json_encode($result).");";
			}
				catch(Exception $e)
			{
			  	echo $_GET['callback']."(".json_encode($e).");";
			}
		} elseif ($task == 'chapter'){
			try
			{				 
				$result = $this->getModel('control')->getChapters(	$jinput->get('nr', NULL, 'INT'),
																	$jinput->get('v', NULL, 'ALNUM') );
				
				echo $_GET['callback']."(".json_encode($result).");";
			}
				catch(Exception $e)
			{
			  	echo $_GET['callback']."(".json_encode($e).");";
			}
		} elseif ($task == 'defaults'){
			try
			{				 
				$result = $this->getModel('control')->getAppDefaults(	$jinput->get('search_app', null, 'INT'), 
																		$jinput->get('search', null, 'SAFE_HTML'), 
																		$jinput->get('search_version', null, 'ALNUM'), 
																		$jinput->get('search_crit', null, 'CMD'), 
																		$jinput->get('search_type', null, 'ALNUM') );
				echo $_GET['callback']."(".json_encode($result).");";
			}
				catch(Exception $e)
			{
			  	echo $_GET['callback']."(".json_encode($e).");";
			}
		} elseif ($task == 'setBookmark'){
			try
			{				 
				$result = $this->getModel('control')->setBookmark(	$jinput->get('bookmark', 0, 'STRING'),
																	$jinput->get('publish', 0, 'INT'),
																	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('tu', 0, 'BASE64') );
				
				echo $_GET['callback']."(".json_encode($result).");";
			}
				catch(Exception $e)
			{
			  	echo $_GET['callback']."(".json_encode($e).");";
			}
		} elseif ($task == 'setbookmarks'){
			try
			{				 
				$result = $this->getModel('control')->setBookmarks(	$jinput->get('bookmark', 0, 'BASE64'),
																	$jinput->get('act', 0, 'INT'),
																	$jinput->get('publish', 0, 'INT'),
																	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('tu', 0, 'BASE64') );
				
				echo $_GET['callback']."(".json_encode($result).");";
			}
				catch(Exception $e)
			{
			  	echo $_GET['callback']."(".json_encode($e).");";
			}
		} elseif ($task == 'getbookmarks'){
			try
			{				 
				$result = $this->getModel('control')->getBookmarks(	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('tu', 0, 'BASE64') );
				
				echo $_GET['callback']."(".json_encode($result).");";
			}
				catch(Exception $e)
			{
			  	echo $_GET['callback']."(".json_encode($e).");";
			}
		} elseif ($task == 'clearbookmarks'){
			try
			{				 
				$result = $this->getModel('control')->clearBookmarks(	$jinput->get('jsonKey', 0, 'ALNUM'),
																		$jinput->get('tu', 0, 'BASE64') );
				
				echo $_GET['callback']."(".json_encode($result).");";
			}
				catch(Exception $e)
			{
			  	echo $_GET['callback']."(".json_encode($e).");";
			}
		}
	}
}
