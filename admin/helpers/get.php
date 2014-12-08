<?php
/**
* 
* 	@version 	1.0.5  December 08, 2014
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
	
	public static $currentVersion 	= false;
	public static $localVersion 	= false;
	
	/**
	 *	Load the Component xml manifests. 
	 */
	 protected static function setXML()
	 {
		// check if data is in session
		$session 		= JFactory::getSession();
		// $session->clear('get_xml_settings'); // to debug the session
		$xml_settings 	= $session->get('get_xml_settings', false);
		if($xml_settings !== false){
			$xml_settings 	= json_decode(base64_decode($xml_settings));
			self::$localVersion 	= $xml_settings->local;
			self::$currentVersion 	= $xml_settings->current;
		} else {
			// Parse the XML
			$local 			= @simplexml_load_file(JPATH_ADMINISTRATOR."/components/com_getbible/getbible.xml");
			$feed 			= @file_get_contents('http://getbible.net/updates/joomla_three.xml');
			$updates 		= @simplexml_load_string($feed);
			// load local version
			self::$localVersion 	= (string)$local->version;
			// set current version
			if(self::$localVersion !== false){
				list($localMain,$localDesign,$localTail) = explode('.', self::$localVersion);
				if(count($updates) > 0 && ($updates !== false)){
					foreach ($updates as $update){
						list($currentMain,$currentDesign,$currentTail) = explode('.', $update->version);
						if (($currentTail >= $localTail) || ($currentDesign > $localDesign) || ($currentMain > $localMain)){
							self::$currentVersion = (string)$update->version;
						}
					}
				} else {
					self::$currentVersion = false;
				}
			} else {
				self::$localVersion = false;
				self::$currentVersion = false;
			}
			// if both are set, then continue
			if(self::$currentVersion !== false && self::$localVersion !== false){
				$xml_settings 				= array();
				$xml_settings['current'] 	= self::$currentVersion;
				$xml_settings['local'] 		= self::$localVersion;
				// add to session to speedup the page load.
				$session->set('get_xml_settings', base64_encode(json_encode($xml_settings)));
			}
		}
	}
	
	public static function update(){
		// set xml info
		self::setXML();
		// check if we must update
		if(self::$currentVersion  !== false && self::$localVersion !== false ){
			$local 		= (int)str_replace('.', '', self::$localVersion);
			$current 	= (int)str_replace('.', '', self::$currentVersion);
			if($local !== $current){
				if($local < $current){
					$notice = "You are still on version(" . self::$localVersion ."). Get the latest getBible version(" . self::$currentVersion .") <a class=\"btn btn-mini\"  href=\"". JRoute::_( 'index.php?option=com_installer&view=update', false )."\">Upgrade Now!</a> it only gets better.";
					JFactory::getApplication()->enqueueMessage($notice, 'notice');
					return true;
				}
			}
		}
		return false;
	}
	
	public static function htmlEscape($val)
	{
		return htmlentities($val, ENT_COMPAT, 'UTF-8');
	}
	
	
	
	public static function getSetBook($version,$book_nr,$only_ref = true)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.book_name, a.book_nr, a.book_names AS ref');
		$query->from('#__getbible_setbooks AS a');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->where($db->quoteName('a.book_nr') . ' = ' . $book_nr);
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// echo nl2br(str_replace('#__','api_',$query)); die;
		$db->execute();
		$num_rows = $db->getNumRows();
		
		if($num_rows){
			// Load the results
			$result 		= $db->loadObject();
			if($only_ref){
				$result->ref = json_decode($result->ref)->name2;
			} else {
				$result->ref = json_decode($result->ref);
			}
			// return result
			return $result;
		} else {
			// fall back on default
			return self::getSetBook('kjv',$book_nr,$only_ref);
		}
				
	}
	
	public static function getSavedBooks($version)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.book_nr');
		$query->from('#__getbible_books AS a');
		$query->where($db->quoteName('a.version') . ' = ' . $db->quote($version));
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.book_nr ASC');
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// echo nl2br(str_replace('#__','api_',$query)); die;
		
		return $db->loadColumn();
				
	}
}
