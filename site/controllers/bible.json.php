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

// import Joomla controller library
jimport('joomla.application.component.controller');

class GetbibleControllerBible extends JControllerLegacy
{
	public function __construct($config)
	{
		parent::__construct($config);
		// make sure all json stuff are set
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="gebible.json"');
		JResponse::setHeader("Access-Control-Allow-Origin", "*");
		// load the tasks
		$this->registerTask('books', 'bible');
		$this->registerTask('chapter', 'bible');
		$this->registerTask('defaults', 'bible');
		$this->registerTask('sethighlight', 'bible');
		$this->registerTask('sethighlights', 'bible');
		$this->registerTask('gethighlights', 'bible');
		$this->registerTask('clearhighlights', 'bible');
		$this->registerTask('setnote', 'bible');
		$this->registerTask('getnotes', 'bible');
		$this->registerTask('settaged', 'bible');
		$this->registerTask('gettaged', 'bible');
		$this->registerTask('settags', 'bible');
		$this->registerTask('gettags', 'bible');
	}
	
	public function bible()
	{
		$jinput 	= JFactory::getApplication()->input;
		$task 		= $this->getTask();
		switch($task){
			case 'books':
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
			break;
			case 'chapter':
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
			break;
			case 'defaults':
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
			break;
			case 'setHighlight':
				try
				{				 
					$result = $this->getModel('control')->setHighlight(	$jinput->get('highlight', 0, 'STRING'),
																		$jinput->get('publish', 0, 'INT'),
																		$jinput->get('jsonKey', 0, 'ALNUM'),
																		$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
			case 'sethighlights':
				try
				{				 
					$result = $this->getModel('control')->setHighlights(	$jinput->get('highlight', 0, 'BASE64'),
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
			break;
			case 'gethighlights':
				try
				{				 
					$result = $this->getModel('control')->getHighlights(	$jinput->get('jsonKey', 0, 'ALNUM'),
																		$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
			case 'clearhighlights':
				try
				{				 
					$result = $this->getModel('control')->clearHighlights(	$jinput->get('jsonKey', 0, 'ALNUM'),
																			$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
			case 'setnote':
				try
				{				 
					$result = $this->getModel('control')->setNote(	$jinput->get('note', 0, 'STRING'),
																	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('verse', 0, 'STRING'),
																	$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
			case 'getnotes':
				try
				{				 
					$result = $this->getModel('control')->getNotes(	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
			case 'settaged':
				try
				{				 
					$result = $this->getModel('control')->setTaged(	$jinput->get('action', 0, 'INT'),
																	$jinput->get('tag', 0, 'STRING'),
																	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('verse', 0, 'STRING'),
																	$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
			case 'gettaged':
				try
				{				 
					$result = $this->getModel('control')->getTaged(	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('verse', 0, 'STRING'),
																	$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
			case 'settags':
				try
				{	 
					$result = $this->getModel('control')->setTags(	$jinput->get('name', 0, 'STRING'),
																	$jinput->get('note', 0, 'STRING'),
																	$jinput->get('access', 0, 'INT'),
																	$jinput->get('published', 0, 'INT'),
																	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
			case 'gettags':
				try
				{				 
					$result = $this->getModel('control')->getTags(	$jinput->get('jsonKey', 0, 'ALNUM'),
																	$jinput->get('tu', 0, 'BASE64') );
					
					echo $_GET['callback']."(".json_encode($result).");";
				}
					catch(Exception $e)
				{
					echo $_GET['callback']."(".json_encode($e).");";
				}
			break;
		}
	}
}
