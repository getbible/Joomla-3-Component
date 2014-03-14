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

jimport('joomla.application.component.helper');

class GetbibleModelCpanel extends JModelList
{
	public function setCpanel()
	{
		// get versions installed
		$versions = $this->getVersions();
		
		if($versions){
			foreach($versions as $version){
				// start setup of Cpanel Object
				$Cpanel[$version->version] = new StdClass;
				// set version values
				$Cpanel[$version->version]->version_name 	= $version->version_name;
				$Cpanel[$version->version]->version 		= $version->version;
				$Cpanel[$version->version]->language 		= $version->language;
				$Cpanel[$version->version]->id 				= $version->id;
			}
			
			// save the result to server
			return $this->saveCpanel($Cpanel);
			
		}
		
		return false;			
	}
	
	protected function saveCpanel($Cpanel)
	{
		// set path
		$path = JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'cpanel.json';
		$fp = fopen($path, 'w');
		// check point
		if(fwrite($fp, json_encode($Cpanel)) && $Cpanel){
			// close file.	
			fclose($fp);
			return true;
		}
		// close file.	
		fclose($fp);
		return false;
						
	}
	
	protected function getVersions()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query->select('a.name AS version_name ,a.version ,a.language ,a.id');
		$query->from('#__getbible_versions AS a');		
		$query->where($db->quoteName('a.access') . ' = 1');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.language ASC');
			 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results
		return $db->loadObjectList();
				
	}
}