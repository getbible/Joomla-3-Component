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
			$defaults = '{"vdm_logo":"1","vdm_text":"1","vdm_link":"1","vdm_name":"getBible.net","vdm_url":"http:\/\/getbible.net\/","vdm_owner":"Llewellyn van der Merwe","auto_loading_chapter":"0","app_mode":"2","toolbar":"1","line_mode":"1","font_small":"16","font_medium":"19","font_large":"23","right_click":"2","account":"0","account_header":"FREE_ACCOUNT","account_highlight_text":"SIGN_UP_HIGHLIGHT_DISCRIPTION","account_note_text":"SIGN_UP_NOTES_DISCRIPTION","account_button":"SIGN_UP_NOW","account_menu":"","login_menu":"","allow_spaces":"true","autocomplete_show":"true","autocomplete_min_length":"1","autocomplete_delay":"3","case_sensitive":"false","placeholder_text":"add tag","tags_defaults":"","hover_textcolor":"#515456","hover_background":"#ffffff","hover_linecolor":"#ff0000","hover_linetype":"dashed","mark_a_name":"GOLD","mark_a_textcolor":"#ffd700","mark_a_background":"#ffffff","mark_a_linecolor":"#ffffff","mark_a_linetype":"none","mark_b_name":"DARK_RED","mark_b_textcolor":"#8b0000","mark_b_background":"#ffffff","mark_b_linecolor":"#ffffff","mark_b_linetype":"none","mark_c_name":"ORANGE","mark_c_textcolor":"#ff6600","mark_c_background":"#ffffff","mark_c_linecolor":"#ffffff","mark_c_linetype":"none","mark_d_name":"YELLOW","mark_d_textcolor":"#ffff00","mark_d_background":"#d4d4d4","mark_d_linecolor":"#ffffff","mark_d_linetype":"none","mark_e_name":"DARK_BLUE","mark_e_textcolor":"#000080","mark_e_background":"#ffffff","mark_e_linecolor":"#ffffff","mark_e_linetype":"none","mark_f_name":"LIGHT_BLUE","mark_f_textcolor":"#63b8ff","mark_f_background":"#ffffff","mark_f_linecolor":"#ffffff","mark_f_linetype":"none","mark_g_name":"TEAL","mark_g_textcolor":"#008080","mark_g_background":"#ffffff","mark_g_linecolor":"#ffffff","mark_g_linetype":"none","mark_h_name":"DARK_PURPLE","mark_h_textcolor":"#663399","mark_h_background":"#ffffff","mark_h_linecolor":"#ffffff","mark_h_linetype":"none","mark_i_name":"LIGHT_PURPULE","mark_i_textcolor":"#cc99ff","mark_i_background":"#ffffff","mark_i_linecolor":"#ffffff","mark_i_linetype":"none","mark_j_name":"DARK_GREEN","mark_j_textcolor":"#006400","mark_j_background":"#ffffff","mark_j_linecolor":"#ffffff","mark_j_linetype":"none","mark_k_name":"LIGHT_GREEN","mark_k_textcolor":"#00ee76","mark_k_background":"#ffffff","mark_k_linecolor":"#ffffff","mark_k_linetype":"none","mark_l_name":"BROWN","mark_l_textcolor":"#d2691e","mark_l_background":"#ffffff","mark_l_linecolor":"#ffffff","mark_l_linetype":"none","mark_m_name":"TURQUOISE","mark_m_textcolor":"#40e0d0","mark_m_background":"#ffffff","mark_m_linecolor":"#ffffff","mark_m_linetype":"none","mark_n_name":"APRICOT","mark_n_textcolor":"#fbceb1","mark_n_background":"#ffffff","mark_n_linecolor":"#ffffff","mark_n_linetype":"none","mark_o_name":"BITTERSWEET","mark_o_textcolor":"#fe6f5e","mark_o_background":"#ffffff","mark_o_linecolor":"#ffffff","mark_o_linetype":"none","mark_p_name":"BRIGHT_PINK","mark_p_textcolor":"#ff007f","mark_p_background":"#ffffff","mark_p_linecolor":"#ffffff","mark_p_linetype":"none","mark_q_name":"CARNATION_PINK","mark_q_textcolor":"#ffa6c9","mark_q_background":"#ffffff","mark_q_linecolor":"#ffffff","mark_q_linetype":"none","mark_r_name":"AQUA","mark_r_textcolor":"#00ffff","mark_r_background":"#ffffff","mark_r_linecolor":"#ffffff","mark_r_linetype":"none","mark_s_name":"BRONZE","mark_s_textcolor":"#cd7f32","mark_s_background":"#ffffff","mark_s_linecolor":"#ffffff","mark_s_linetype":"none","mark_t_name":"CHERRY","mark_t_textcolor":"#de3163","mark_t_background":"#ffffff","mark_t_linecolor":"#ffffff","mark_t_linetype":"none","mark_u_name":"DARK_CHESTNUT","mark_u_textcolor":"#986960","mark_u_background":"#ffffff","mark_u_linecolor":"#ffffff","mark_u_linetype":"none","mark_v_name":"DARK_GOLDENROD","mark_v_textcolor":"#b8860b","mark_v_background":"#ffffff","mark_v_linecolor":"#ffffff","mark_v_linetype":"none","mark_w_name":"DARK_KHAKI","mark_w_textcolor":"#bdb76b","mark_w_background":"#ffffff","mark_w_linecolor":"#ffffff","mark_w_linetype":"none","mark_x_name":"SILVER","mark_x_textcolor":"#c0c0c0","mark_x_background":"#ffffff","mark_x_linecolor":"#ffffff","mark_x_linetype":"none","mark_y_name":"BABY_BLUE","mark_y_textcolor":"#89cff0","mark_y_background":"#ffffff","mark_y_linecolor":"#ffffff","mark_y_linetype":"none","mark_z_name":"LIME","mark_z_textcolor":"#bfff00","mark_z_background":"#ffffff","mark_z_linecolor":"#ffffff","mark_z_linetype":"none","search_display":"1","search_button":"Search","advanced_button":"Advanced","search_phrase":"search...","up_button":"2","highlight_option":"1","highlight_textcolor":"#ffffff","highlight_background":"#52a9ca","highlight_linecolor":"#ffffff","highlight_linetype":"none","highlight_padding":"0","search_options":"1","search_crit1":"1","search_type":"all","search_crit2":"1","search_crit3":"1","log":"0","jsonQueryOptions":"0","jsonAPIaccess":"0","jsonAPIkey":"","installOptions":"1","localInstallFolder":"scriptureinstall","defaultStartVersion":"kjv","defaultStartBook":"John","defaultStartChapter":"1","check_in":"-1 day","nameGlobal":"Llewellyn van der Merwe","emailGlobal":"info@getbible.net","titleWorker1":"Application Engineer","nameWorker1":"Llewellyn van der Merwe","emailWorker1":"llewellyn@vdm.io","linkWorker1":"http:\/\/vdm.io","useWorker1":"2","showWorker1":"3","titleWorker2":"","nameWorker2":"","emailWorker2":"","linkWorker2":"","useWorker2":"0","showWorker2":"0","titleWorker3":"","nameWorker3":"","emailWorker3":"","linkWorker3":"","useWorker3":"0","showWorker3":"0","titleWorker4":"","nameWorker4":"","emailWorker4":"","linkWorker4":"","useWorker4":"0","showWorker4":"0"}';
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
			$db->execute();
			// check if table is found
			$db->setQuery('SHOW TABLES LIKE '.$db->quote('api_getbible_bookmarks'));
			$db->execute();
			if($db->getNumRows()){
				// if found fix the bookmarks table issue
				$db->renameTable('#__getbible_bookmarks','#__getbible_highlights');
			}

			echo '	<p>'.JText::_('Congratulations! Now you can start using Get Bible!').'</p>
					<a target="_blank" href="http://getbible.net/" title="Get Bible">
					<img src="../media/com_getbible/images/Bible.jpg"/>
					</a>';
		}
	}
}