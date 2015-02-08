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

jimport('joomla.application.component.helper');

class GetbibleModelApp extends JModelList
{
	protected $app_params;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params = JFactory::getApplication()->getParams();
		
	}
	
	public function getCpanel()
	{	
		if ($this->app_params->get('jsonQueryOptions') == 1){
			
			$path 		= JPATH_SITE.DS.'media'.DS.'com_getbible'.DS.'json'.DS.'cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE){
				return false;
			}
			
			return json_decode($cpanel);
			
		} elseif ($this->app_params->get('jsonQueryOptions') == 2) {
			
			$path 		= 'https://getbible.net/media/com_getbible/json/cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE){
				return false;
			}
			
			return json_decode($cpanel);
			
		} else {
			
			$path 		= 'http://getbible.net/media/com_getbible/json/cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE){
				return false;
			}
			
			return json_decode($cpanel);
			
		}

	}
	
	public function getBooksDate()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select($db->quoteName('a.created_on').' AS date');
		$query->select($db->quoteName('a.version'));
		$query->from('#__getbible_setbooks AS a');		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order($db->quoteName('a.created_on') . ' DESC');
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$date[] = $db->loadObject();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select($db->quoteName('a.modified_on').' AS date');
		$query->select($db->quoteName('a.version'));
		$query->from('#__getbible_setbooks AS a');		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order($db->quoteName('a.modified_on') . ' DESC');
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$date[] = $db->loadObject();
		// get highest date;
		$topDate = 0;
		$result = 'kjv_0';
		foreach($date as $value){
			$checkDate = strtotime($value->date);
			if($checkDate > $topDate){
				$topDate = strtotime($value->date);
				$result = $value->version.'_'.$topDate;
			}
		}		
		return $result;
		
	}
	
	public function getAppDefaults()
	{	
		// set the default version
		$appDefaults['version'] = $this->app_params->get('defaultStartVersion');
		// check if search form is used
		$jinput 					= JFactory::getApplication()->input;
		$search_app					= $jinput->post->get('search_app', 0, 'INT');
		if($search_app === 1){
			$appDefaults['version'] = $jinput->post->get('search_version', null, 'ALNUM');
			$appDefaults['request'] = '&search_app='.$search_app;
			$appDefaults['request'] .= '&search='.$jinput->post->get('search', null, 'SAFE_HTML');
			$appDefaults['request'] .= '&search_version='.$jinput->post->get('search_version', null, 'ALNUM');
			$appDefaults['request'] .= '&search_crit='.$jinput->post->get('search_crit', null, 'CMD');
			$appDefaults['request'] .= '&search_type='.$jinput->post->get('search_type', null, 'ALNUM');	
		} else {
			$appDefaults['request'] = '&search_app=1';
			$appDefaults['request'] .= '&search='.$this->app_params->get('defaultStartBook').' '.$this->app_params->get('defaultStartChapter');
			$appDefaults['request'] .= '&search_version='.$appDefaults['version'];
		}
		// set key
		$appDefaults['defaultKey'] 	= md5(  $appDefaults['version'].
											$appDefaults['request'].
											$this->app_params->get('defaultStartVersion').
											$this->app_params->get('defaultStartBook').
											$this->app_params->get('defaultStartChapter') );
		return $appDefaults;

	}
}
