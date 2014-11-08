<?php
/**
* 
* 	@version 	1.0.1  August 16, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

// Added for Joomla 3.0
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
};

JLoader::register('ContentHelper', __DIR__ . DS.'helpers'.DS.'content.php');

// No access check.
$controller	= JControllerAdmin::getInstance('Getbible');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
