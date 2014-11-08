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

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('joomla.application.component.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldVersion extends JFormFieldList
{
	protected $type = 'version';
	
	public function getOptions()
	{
		$options = array();
		// get params
		$params = JComponentHelper::getParams('com_getbible');
		
		if ($params->get('jsonQueryOptions') == 1){
			
			$path 		= JPATH_ROOT.DS.'media'.DS.'com_getbible'.DS.'json'.DS.'cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE || $cpanel === NULL || $cpanel == ''){
				$setVersions[]  = JHtml::_('select.option', 'kjv', 'Please Install Local Versions');
			} else {
			
				$options =  json_decode($cpanel);
				
				foreach ($options as $key => $values){
					$name = $values->version_name. ' ('.$values->language.')';
					$setVersions[]  = JHtml::_('select.option', $values->version, $name);
				}
			}
			
		} elseif ($params->get('jsonQueryOptions') == 2) {
			
			$path 		= 'https://getbible.net/media/com_getbible/json/cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE || $cpanel === NULL || $cpanel == ''){
				$setVersions[]  = JHtml::_('select.option', 'kjv', 'Get Bible is Offline');
			} else {
			
				$options = json_decode($cpanel);
			
				foreach ($options as $key => $values){
					$name = $values->version_name. ' ('.$values->language.')';
					$setVersions[]  = JHtml::_('select.option', $values->version, $name);
				}
			}
			
		} else {
			
			$path 		= 'http://getbible.net/media/com_getbible/json/cpanel.json';
			$cpanel 	= @file_get_contents($path);
			
			if($cpanel === FALSE || $cpanel === NULL || $cpanel == ''){
				$setVersions[]  = JHtml::_('select.option', 'kjv', 'Get Bible is Offline');
			} else {
				$options = json_decode($cpanel);
			
				foreach ($options as $key => $values){
					$name = $values->version_name. ' ('.$values->language.')';
					$setVersions[]  = JHtml::_('select.option', $values->version, $name);
				}
			}
			
		}
		
		array_unshift($setVersions, JHtml::_('select.option', '', JText::_('Select an option')));
		
		return $setVersions;
	}
}