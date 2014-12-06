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

// No access check.
jimport('joomla.application.component.controller');

// Added for Joomla 3.0
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
};

// require helper files
JLoader::register('GetHelper', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_getbible'. DS . 'helpers' . DS . 'get.php');

$controller = JControllerAdmin::getInstance('Getbible');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();