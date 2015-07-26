<?php
/**
* 
* 	@version 	1.0.7  January 16, 2015
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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_getbible')){
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
};

// require helper files
JLoader::register('GetHelper', dirname(__FILE__) . '/helpers/get.php');
JLoader::register('ContentHelper', dirname(__FILE__) . '/helpers/content.php');

// No access check.
$controller	= JControllerAdmin::getInstance('Getbible');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
