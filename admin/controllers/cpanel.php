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

// import Joomla controller library
jimport('joomla.application.component.controller');

class GetbibleControllerCpanel extends JControllerLegacy
{
	public function __construct($config)
	{
		parent::__construct($config);
		
		$this->registerTask('cpanel', 'save');
	}
	
	public function save()
	{
		$task = $this->getTask();
		if($task == 'save'){
			
			$set = $this->getModel('cpanel')->setCpanel();
			if($set){
				$this->setRedirect('index.php?option=com_getbible',JText::_('COM_GETBIBLE_SUCCESSFUL_SETUP_FOR_USER_CPANEL'));
			} else {
				$this->setRedirect('index.php?option=com_getbible',JText::_('COM_GETBIBLE_ERROR_SETUP_FOR_USER_CPANEL'), 'error');
			}
		}
	}
}