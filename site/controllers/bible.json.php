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

// import Joomla controller library
jimport('joomla.application.component.controller');

class GetbibleControllerBible extends JControllerLegacy
{
	public function __construct($config)
	{
		parent::__construct($config);
		
		$this->registerTask('books', 'bible');
		$this->registerTask('chapter', 'bible');
	}
	
	public function bible()
	{
		$task = $this->getTask();
		if ($task == 'books'){
			try
			{
				$version = JFactory::getApplication()->input->get('v', NULL, 'ALNUM');
				
				if($version){
					$result = $this->getModel('control')->getBooks($version);
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
				$book_nr = JFactory::getApplication()->input->get('nr', NULL, 'INT');
				$version = JFactory::getApplication()->input->get('v', NULL, 'ALNUM');
				 
				$result = $this->getModel('control')->getChapters($book_nr,$version);
				
				echo $_GET['callback']."(".json_encode($result).");";
			}
				catch(Exception $e)
			{
			  	echo $_GET['callback']."(".json_encode($e).");";
			}
		}
	}
}