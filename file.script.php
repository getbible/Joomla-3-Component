<?php
/**
* 
* 	@version 	1.0.3  November 25, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

class com_getbibleInstallerScript {
	
	/**
	* Method to install the component
	*
	* @param mixed $parent The class calling this method
	* @return void
	*/
	public function install($parent)
	{
		 echo JText::_('Installed successfully');
	}
	
	/**
	* Method to update the component
	*
	* @param mixed $parent The class calling this method
	* @return void
	*/
	public function update($parent)
	{
		echo JText::_('Updated successfully');
	}
	
	/**
	* method to run before an install/update/uninstall method
	*
	* @param mixed $parent The class calling this method
	* @return void
	*/
	public function preflight($type, $parent)
	{
	
	}
	 
	public function postflight($type, $parent)
	{
		//set the default confic setings
		if ($type == 'install') {
				// Set Global Settings
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->update('#__extensions');
				$defaults = '{"vdm_logo":"1","vdm_link":"1","vdm_name":"getBible.net","vdm_url":"http:\/\/getbible.net\/","vdm_owner":"Llewellyn van der Merwe","auto_loading_chapter":"0","app_mode":"2","highlight_textcolor":"#ffffff","highlight_background":"#52a9ca","highlight_linecolor":"#ffffff","highlight_linetype":"none","highlight_padding":"0","highlight_option":"1","up_button":"2","search_display":"1","search_button":"Search","advanced_button":"Advanced","search_phrase":"search...","search_options":"1","search_crit1":"1","search_type":"all","search_crit2":"1","search_crit3":"1","jsonQueryOptions":"0","jsonAPIaccess":"0","jsonAPIkey":"","installOptions":"1","localInstallFolder":"scriptureinstall","defaultStartVersion":"kjv","defaultStartBook":"John","defaultStartChapter":"1","check_in":"-1 day","nameGlobal":"Llewellyn van der Merwe","emailGlobal":"info@getbible.net","titleWorker1":"Application Engineer","nameWorker1":"Llewellyn van der Merwe","emailWorker1":"llewellyn@vdm.io","linkWorker1":"http:\/\/vdm.io","useWorker1":"2","showWorker1":"3","titleWorker2":"","nameWorker2":"","emailWorker2":"","linkWorker2":"","useWorker2":"0","showWorker2":"0","titleWorker3":"","nameWorker3":"","emailWorker3":"","linkWorker3":"","useWorker3":"0","showWorker3":"0","titleWorker4":"","nameWorker4":"","emailWorker4":"","linkWorker4":"","useWorker4":"0","showWorker4":"0"}';
				$query->set("params =  '{$defaults}'");
				$query->where("element = 'com_getbible'"); 
				$db->setQuery($query);
				$db->query();
				
		
		echo '	<p>'.JText::_('Congratulations! Now you can start using Get Bible!').'</p>
				<a target="_blank" href="http://getbible.net/" title="Get Bible">
				<img src="../media/com_getbible/images/Bible.jpg"/>
				</a>';
		} 
		if ($type == 'update') {
				// fix update string in db
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				// Fields to update.
				$fields = array(
					$db->quoteName('type') . ' = ' . $db->quote('extension'), 
					$db->quoteName('enabled') . ' = 1'
				);
				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('location') . ' = ' . $db->quote('http://getbible.net/updates/joomla_three.xml')
				);
				$query->update($db->quoteName('#__update_sites'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$result = $db->execute();
				
		echo '	<p>'.JText::_('Congratulations! Now you can start using Get Bible!').'</p>
				<a target="_blank" href="http://getbible.net/" title="Get Bible">
				<img src="../media/com_getbible/images/Bible.jpg"/>
				</a>';
		}
	}
}