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

/**
 * Get Bible API component helper.
 */
abstract class GetHelper
{
	
	public static $current 	= null;
	public static $local 	= null;
	
	/**
	 *	Load the Component xml manifests. 
	 */
	 protected static function setXML(){
		self::$local 	= simplexml_load_file(JPATH_ADMINISTRATOR."/components/com_getbible/getbible.xml");
		$updates 		= simplexml_load_file(self::$local->updateservers->server);
		// set local
		list($localMain,$localDesign,$localTail) = explode('.', self::$local->version);
		foreach ($updates as $update){
			list($currentMain,$currentDesign,$currentTail) = explode('.', $update->version);
			if (($currentTail >= $localTail) || ($currentDesign > $localDesign) || ($currentMain > $localMain)){
				self::$current = $update;
			}
		}
	}
	
	public static function update(){
		// set xml info
		self::setXML();
		// check if we must update
		if((string)self::$local->version !== (string)self::$current->version){
			$notice = "You are still on version(" . self::$local->version ."). Get the latest getBible version(" . self::$current->version .") <a class=\"btn btn-mini\"  href=\"". JRoute::_( 'index.php?option=com_installer&view=update', false )."\">Upgrade Now!</a> it only gets better.";
			JFactory::getApplication()->enqueueMessage($notice, 'notice');
			return true;
		}
		return false;
	}
	
	public static function htmlEscape($val)
	{
		return htmlentities($val, ENT_COMPAT, 'UTF-8');
	}	
}