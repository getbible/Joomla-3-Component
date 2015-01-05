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

jimport('joomla.environment.browser');

$browser =& JBrowser::getInstance();
$browserType = $browser->getBrowser();
$browserVersion = $browser->getMajor();
if(($browserType == 'msie') && ($browserVersion == 9))
{
   $document->addScript('http://html5shiv.googlecode.com/svn/trunk/html5.js');
}
if(($browserType == 'msie') && ($browserVersion == 8))
{
	$document->addStyleSheet(JURI::base() . 'media/com_jmcrm/css/ie8.css');
}