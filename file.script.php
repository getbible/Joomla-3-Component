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
				$defaults = '{"vdm_logo":"1","vdm_link":"1","vdm_name":"getBible.net","vdm_url":"http:\/\/getbible.net\/","vdm_owner":"Llewellyn van der Merwe","auto_loading_chapter":"0","app_mode":"2","toolbar":"2","line_mode":"1","font_small":"14","font_medium":"17","font_large":"20","hover_textcolor":"#828282","hover_background":"#ffffff","hover_linecolor":"#216a94","hover_linetype":"dotted","mark_a_name":"Gold","mark_a_textcolor":"#ffd700","mark_a_background":"#ffffff","mark_a_linecolor":"#ffffff","mark_a_linetype":"none","mark_b_name":"Dark Red","mark_b_textcolor":"#8b0000","mark_b_background":"#ffffff","mark_b_linecolor":"#ffffff","mark_b_linetype":"none","mark_c_name":"Orange","mark_c_textcolor":"#ff6600","mark_c_background":"#ffffff","mark_c_linecolor":"#ffffff","mark_c_linetype":"none","mark_d_name":"Yellow","mark_d_textcolor":"#ffff00","mark_d_background":"#d4d4d4","mark_d_linecolor":"#ffffff","mark_d_linetype":"none","mark_e_name":"Dark Blue","mark_e_textcolor":"#000080","mark_e_background":"#ffffff","mark_e_linecolor":"#ffffff","mark_e_linetype":"none","mark_f_name":"Light Blue","mark_f_textcolor":"#63b8ff","mark_f_background":"#ffffff","mark_f_linecolor":"#ffffff","mark_f_linetype":"none","mark_g_name":"Teal","mark_g_textcolor":"#008080","mark_g_background":"#ffffff","mark_g_linecolor":"#ffffff","mark_g_linetype":"none","mark_h_name":"Dark Purple","mark_h_textcolor":"#663399","mark_h_background":"#ffffff","mark_h_linecolor":"#ffffff","mark_h_linetype":"none","mark_i_name":"Light Purple","mark_i_textcolor":"#cc99ff","mark_i_background":"#ffffff","mark_i_linecolor":"#ffffff","mark_i_linetype":"none","mark_j_name":"Dark Green","mark_j_textcolor":"#006400","mark_j_background":"#ffffff","mark_j_linecolor":"#ffffff","mark_j_linetype":"none","mark_k_name":"Light Green","mark_k_textcolor":"#00ee76","mark_k_background":"#ffffff","mark_k_linecolor":"#ffffff","mark_k_linetype":"none","mark_l_name":"Brown","mark_l_textcolor":"#d2691e","mark_l_background":"#ffffff","mark_l_linecolor":"#ffffff","mark_l_linetype":"none","mark_m_name":"Turquoise","mark_m_textcolor":"#40e0d0","mark_m_background":"#ffffff","mark_m_linecolor":"#ffffff","mark_m_linetype":"none","highlight_textcolor":"#ffffff","highlight_background":"#52a9ca","highlight_linecolor":"#ffffff","highlight_linetype":"none","highlight_padding":"1","highlight_option":"1","up_button":"2","search_display":"1","search_button":"Search","advanced_button":"Advanced","search_phrase":"search...","search_options":"1","search_crit1":"1","search_type":"all","search_crit2":"1","search_crit3":"1","jsonQueryOptions":"0","jsonAPIaccess":"0","jsonAPIkey":"","installOptions":"1","localInstallFolder":"scriptureinstall","defaultStartVersion":"kjv","defaultStartBook":"John","defaultStartChapter":"1","check_in":"-1 day","nameGlobal":"Llewellyn van der Merwe","emailGlobal":"info@getbible.net","titleWorker1":"Application Engineer","nameWorker1":"Llewellyn van der Merwe","emailWorker1":"llewellyn@vdm.io","linkWorker1":"http:\/\/vdm.io","useWorker1":"2","showWorker1":"3","titleWorker2":"","nameWorker2":"","emailWorker2":"","linkWorker2":"","useWorker2":"0","showWorker2":"0","titleWorker3":"","nameWorker3":"","emailWorker3":"","linkWorker3":"","useWorker3":"0","showWorker3":"0","titleWorker4":"","nameWorker4":"","emailWorker4":"","linkWorker4":"","useWorker4":"0","showWorker4":"0"}';
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