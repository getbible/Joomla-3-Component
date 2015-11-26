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

class GetbibleModelGetbible extends JModelList
{
	protected $app_params;
	protected $tab_id;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params	= JComponentHelper::getParams('com_getbible');
		// get tab ID
		$jinput				= JFactory::getApplication()->input;
		$this->tab_id 		= $jinput->get('tab', 0, 'INT');
	}
	
	public function getTabs()
	{	
		$APIkey = $this->getAPIkey();
		
		$div_cPanel = '<div class="span9">
						<h2 class="nav-header">'.JText::_('COM_GETBIBLE_CPANEL_HEADER').'</h2>
                        <div class="well well-small">
							'. $this->setIcons() .'
							<div class="clearfix"></div>
                    	</div>
                    </div>
                    <div class="span3">
                        <div>
							<h2 class="nav-header">'.JText::_('COM_GETBIBLE_EXTENSION_DETAILS').'</h2>
                            <a target="_blank" style="float: right;"href="https://www.vdm.io/joomla" title="Vast Development Method"><img src="components/com_getbible/assets/images/vdm.jpg" height="300"/></a>
							<ul class="list-group">
  								<li class="list-group-item"><img src="../media/com_getbible/images/icon.png" height="21"/> &#8482;</li>
  								<li class="list-group-item">Copyright &#169; <a href="http://vdm.io" target="_blank">Vast Development Method</a>.<br />All rights reserved.</li>
								<li class="list-group-item">Distributed under the GNU GPL <br />Version 2 or later</li>
								<li class="list-group-item">See <a href="https://getbible.net/license" target="_blank">License details</a></li>';
		$workers = $this->getWorkers();
		if(count($workers)){
			foreach($workers as $worker){						
				$div_cPanel .= '<li class="list-group-item">'.$worker.'</li>';
			}
		}
		$div_cPanel .= '</ul></div></div>';
					
		$div_info = '<div class="span12">
                            <a target="_blank" style="float: right;"href="http://getbible.net/" title="Get Bible"><img src="../media/com_getbible/images/Bible.jpg" height="233"/></a>
                            <div>
                                <p>The purpose with this application is to take the Word of God to every person in their own language for free! We would also like it to be fast, stable, and easy to use.</p>
                                
                                <p>Get Bible is a member of <a href="http://www.wefeargod.com/" title="The Fear of God" target="_blank">we fear God</a> and also supports the <a href="http://www.whybible.com/" title="Why Bible" target="_blank">Why? Bible</a> initiative. We therefore strongly affirm that we choose to believe the Bible because it is a reliable collection of historical documents written by eyewitnesses during the lifetime of other eyewitnesses. They report supernatural events that took place in fulfillment of specific prophesies and claim that their writings are divine rather than human in origin. We believe the Bible is the verbally inspired Word of God and is the sole, infallible rule of faith and practice.</p>
                                
                                <h2 class="nav-header">This is a <a href="http://www.mountainofsuccess.com/" title="Success" target="_blank">Mountain of Success</a> project.</h2>
                                <div>
                                  <h3>What is Success? Well God\'s word says:</h3>
                                  <blockquote>This book of the law shall not depart out of thy mouth; but   thou shalt meditate therein day and night, that thou mayest observe to   do according to all that is written therein: for then thou shalt make   thy way prosperous, and then thou shalt have good success. ~ Joshua 1:8</blockquote>
                                  <p>Therefore a mountain of success can only be found in obedience to God. Looking back at the cross of Jesus planted on Golgotha; we see a mountain of success. Therefore we read in the scriptures the following</p>
                                  <p> </p>
                                  <blockquote>...and the stone that smote the image became a great mountain, and filled the whole earth. ~  Daniel 2:35</blockquote>
                                  <p>Mountain of Success is an online Christian mission initiative where this victory of our Lord is proclaimed.</p>
                                  <p>So it is all about God\'s Success!</p>
                                </div>
                            </div>
                        </div>';
					
		$div_APIDoc = '<div class="span12">
						<h1>'.JText::_('COM_GETBIBLE_API_HEADER').'</h1>
                            <div>
                            
                                <h2>How does the API Work?</h2>
        
                                <p>The API render the scripture in JSON, in many different translation/languages.</p>
                                <p>Getting a JSON response from the set url "http://getbible.net/json?" with a simple query string "p=Jn3:16" forms the base of the API.</p>
                                
                                <h2>Parameters</h2>
                                
                                <p>There are just two parameters available, both are self-explanatory, <strong>passage</strong> and <strong>version</strong>.
                                
								<p>Yet you can also use <strong>v</strong>, <strong>ver</strong>, <strong>lang</strong> and <strong>translation</strong> in place of <i>version</i> and <strong>p</strong>, <strong>text</strong>, <strong>scrip</strong> and <strong>scripture</strong> in place of <i>passage</i>.</p>
								<p> You can call a book, chapter or a single verse, or even a string of verses. When the <strong>Version</strong> is omitted the KJV is provided by default. </p>
								<p>The following are all valid:</p>
								<ul>
								  <li><a target="_blank" href="http://getbible.net/json?passage=1Jn3:16">http://getbible.net/json?passage=Jn3:16</a></li>
								  <li><a target="_blank" href="http://getbible.net/json?p=James">http://getbible.net/json?p=James</a></li>
								  <li><a target="_blank" href="http://getbible.net/json?text=ps119">http://getbible.net/json?text=ps119</a></li>
								  <li><a target="_blank" href="http://getbible.net/json?scrip=Acts%203:17-4;2:1">http://getbible.net/json?scrip=Acts 3:17-4;2:1</a></li>
								  <li><a target="_blank" href="http://getbible.net/json?scripture=Psa%20119:4-16;23:1-6&v=amp">http://getbible.net/json?scripture=Psa 119:4-16;23:1-6&v=amp</a></li>
								  <li><a target="_blank" href="http://getbible.net/json?passage=Acts%2015:1-5,%2010,%2015&version=aov">http://getbible.net/json?passage=Acts 15:1-5, 10, 15&version=aov</a></li>
								</ul>
								<h2>VERY IMPORTANT!</h2>
								<p>Once you have installed your own Bibles on your own website/database via the <a href="index.php?option=com_getbible&view=import">Install Bibles</a> menu you no longer need to use getBible.net\'s API, but can then use your own API.</p>
								<p>Using the local API will make your application faster and more stable. Please watch the following <a href="https://getbible.net/main-component?tab=two" target="_blank">video</a> form more info.<br />
								You can only query versions that is installed locally! Check the <a href="index.php?option=com_getbible&view=versions">Installed Bibles</a> list to see what versions is installed. If the list is empty then no Bibles have yet been installed locally.</p>
								<p>Remeber to first setup the <b>json page</b>, so that '.JURI::root().'json is a published menu item on the front-end of your website.</p>
								<p>Then the following are all valid:</p>
								<ul>
								  <li><a target="_blank" href="'.JURI::root().'index.php?option=com_getbible&view=json&passage=1Jn3:16">'.JURI::root().'json?passage=Jn3:16</a></li>
								  <li><a target="_blank" href="'.JURI::root().'index.php?option=com_getbible&view=json&p=James">'.JURI::root().'json?p=James</a></li>
								  <li><a target="_blank" href="'.JURI::root().'index.php?option=com_getbible&view=json&text=ps119">'.JURI::root().'json?text=ps119</a></li>
								  <li><a target="_blank" href="'.JURI::root().'index.php?option=com_getbible&view=json&scrip=Acts%203:17-4;2:1">'.JURI::root().'json?scrip=Acts 3:17-4;2:1</a></li>
								  <li><a target="_blank" href="'.JURI::root().'index.php?option=com_getbible&view=json&scripture=Psa%20119:4-16;23:1-6">'.JURI::root().'json?scripture=Psa 119:4-16;23:1-6</a></li>
								  <li><a target="_blank" href="'.JURI::root().'index.php?option=com_getbible&view=json&passage=Acts%2015:1-5,%2010,%2015">'.JURI::root().'json?passage=Acts 15:1-5, 10, 15</a></li>
								</ul>
								<h2>Now for some Code!</h2>
								<p>Here is a jQuery script to make an API call from your own application</p>
							
							<pre>';
		$div_APIDoc .= "
jQuery.ajax({
	url:'http://getbible.net/json',
	dataType: 'jsonp',
	data: 'p=John1&v=kjv',
	jsonp: 'getbible',
	success:function(json){
		// set text direction
		if (json.direction == 'RTL'){
			var direction = 'rtl';
		} else {
			var direction = 'ltr'; 
		}
		// check response type
		if (json.type == 'verse'){
			var output = '';
				jQuery.each(json.book, function(index, value) {
					output += '&lt;center&gt;&lt;b&gt;'+value.book_name+'&#160;'+value.chapter_nr+'&lt;/b&gt;&lt;/center&gt;&lt;br/&gt;&lt;p class=\"'+direction+'\"&gt;';
					jQuery.each(value.chapter, function(index, value) {
						output += '&#160;&#160;&lt;small class=\"ltr\"&gt;' +value.verse_nr+ '&lt;/small&gt;&#160;&#160;';
						output += value.verse;
						output += '&lt;br/&gt;';
					});
					output += '&lt;/p&gt;';
				});
			jQuery('#scripture').html(output);  // <---- this is the div id we update
		} else if (json.type == 'chapter'){
			var output = '&lt;center&gt;&lt;b&gt;'+json.book_name+'&#160;'+json.chapter_nr+'&lt;/b&gt;&lt;/center&gt;&lt;br/&gt;&lt;p class=\"'+direction+'\"&gt;';
			jQuery.each(json.chapter, function(index, value) {
				output += '&#160;&#160;&lt;small class=\"ltr\"&gt;' +value.verse_nr+ '&lt;/small&gt;&#160;&#160;';
				output += value.verse;
				output += '&lt;br/&gt;';
			});
			output += '&lt;/p&gt;';
			jQuery('#scripture').html(output);  // <---- this is the div id we update
		} else if (json.type == 'book'){
			var output = '';
			jQuery.each(json.book, function(index, value) {
				output += '&lt;center&gt;&lt;b&gt;'+json.book_name+'&#160;'+value.chapter_nr+'&lt;/b&gt;&lt;/center&gt;&lt;br/&gt;&lt;p class=\"'+direction+'\"&gt;';
				jQuery.each(value.chapter, function(index, value) {
					output += '&#160;&#160;&lt;small class=\"ltr\"&gt;' +value.verse_nr+ '&lt;/small&gt;&#160;&#160;';
					output += value.verse;
					output += '&lt;br/&gt;';
				});
			output += '&lt;/p&gt;';
		});
		if(addTo){
			jQuery('#scripture').html(output);  // <---- this is the div id we update
		}
	},
	error:function(){
		jQuery('#scripture').html('&lt;h2&gt;No scripture was returned, please try again!&lt;/h2&gt;'); // <---- this is the div id we update
	 },
});  
		</pre>
		";
		$div_APIDoc .= '<p>To see more example code, take a look at the [ <a href="https://github.com/getbible/Joomla-3-Component/blob/master/media/js/app.js#L713" target="_blank">App Page</a> ] javascript on Github.</p>
						<p>If you are a Brother in the Lord and an advanced programmer we can do with some help, please contact me at <a href="mailto:'.$this->app_params->get("emailGlobal").'" title="'.$this->app_params->get("nameGlobal").'">'.$this->app_params->get("emailGlobal").'</a></p>
										
										<h2>Restrictions</h2>
										<p>All of the texts currently available are in the public domain, so there are no restrictions on how the results can be stored or used.</p>
									</div>
							</div>';
		// set the version tab
		$div_version = '<div class="span12">
					<p>Most of the translations are provided by The Unbound Bible. The code that should be passed as the version parameter is shown in brackets.</p>
					<p>If you want a translation that is not currently listed below, contact me at <a href="mailto:'.$this->app_params->get("emailGlobal").'" title="'.$this->app_params->get("nameGlobal").'">'.$this->app_params->get("emailGlobal").'</a>. <br/>Please note that version that are currently in copyright (e.g. NIV, NKJV, etc.) cannot be added unless you are able to secure copyright permission. </p>
					<p><a href="http://www.4-14.org.uk/xml-bible-web-service-api" target="_blank">Permission</a> has been granted for the NASB and Amplified Bibles.</p>';
		$versions = $this->getAvailableVersions();
        if($versions){
			$div_version .= '<ul>';
        	foreach($versions as $version){
				if(isset($version['not']) && $version['not']){
					$div_version .= '<li>'. $version["versionLang"].' '. $version["versionName"].' ('.$version['versionCode'].') </li>';
				} else {
					$div_version .= '<li><a target="_blank" href="index.php?option=com_getbible&view=versions">'. $version["versionLang"].' '. $version["versionName"].' ('.$version['versionCode'].')</a></li>';
				}
			}
			$div_version .= '</ul>';
		} else {
			$div_version .= '<p>Get Bible is offline, so we can\'t check what versions is available at this time.</p>';
		}
        $div_version .= '</div>';
		
		$div_activity = '<div class="span12"><h2>View Activity</h2><div class="well well-small">';
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_getbible/helpers/activityCron.php')) {
			// plugin was installed bit not active
			$div_activity .= '<p>You need to <a href="'.JURI::base().'index.php?option=com_plugins&view=plugins&filter_search=System - getBible Activity Cron" >activate</a> the <b>System - getBible Activity Cron</b> to view the API activiy.</p>';
		} else {
			$div_activity .= '<p>You need to <a href="https://getbible.net/downloads?tab=cron" target="_blank">install</a> the <b>System - getBible Activity Cron</b> to view the API activiy.</p>';
		}
		$div_activity .= '</div></div>';
		
		$tabs 				= array();
		// cPanel setup
		$tab_cPanel 		= new stdClass();	
		$tab_cPanel->alias 	= 'cpanel';
		$tab_cPanel->name 	= 'COM_GETBIBLE_CPANEL';
		$tab_cPanel->div 	= $div_cPanel;
		$tabs[0]			= $tab_cPanel;
		// About us
		$tab_info			= new stdClass();
		$tab_info->alias 	= 'info';
		$tab_info->name 	= 'COM_GETBIBLE_INFO';
		$tab_info->div 		= $div_info;
		$tabs[1]			= $tab_info;
		// API setup
		$tab_APIDoc			= new stdClass();
		$tab_APIDoc->alias	= 'api_doc';
		$tab_APIDoc->name 	= 'COM_GETBIBLE_API_DOC';
		$tab_APIDoc->div 	= $div_APIDoc;
		$tabs[2]			= $tab_APIDoc;
		// versions
		$tab_version 		= new stdClass();
		$tab_version->alias = 'versions';
		$tab_version->name 	= 'COM_GETBIBLE_VERSIONS';
		$tab_version->div 	= $div_version;
		$tabs[3]			= $tab_version;
		// versions
		$tab_activity 			= new stdClass();
		$tab_activity->alias	= 'activity';
		$tab_activity->name 	= 'COM_GETBIBLE_ACTIVITY';
		$tab_activity->div 		= $div_activity;
		$tabs[4]				= $tab_activity;
						
		$mainframe = JFactory::getApplication();
		//Trigger Event - getbible_bk_onBefore_cPanel_display
		$mainframe->triggerEvent('getbible_bk_onBefore_cPanel_display',array('tabs'=>&$tabs));
		
		return $tabs;
	}
	
	public function getTabactive()
	{
		switch($this->tab_id){
			case 0:
			return 'cpanel';
			break;
			case 1:
			return 'info';
			break;
			case 2:
			return 'api_doc';
			break;
			case 3:
			return 'versions';
			break;
			case 4:
			return 'activity';
			break;
			default:
			return 'cpanel';
			break;
		}
		
	}
	
	protected function getWorkers()
	{
		$workForce = array();
		// get all workers
		$workers = range(1,4);
		foreach($workers as $nr){
			if($this->app_params->get("showWorker".$nr) == 1 || $this->app_params->get("showWorker".$nr) == 3){
				if($this->app_params->get("useWorker".$nr) == 1){
					$link_front = '<a href="mailto:'.$this->app_params->get("emailWorker".$nr).'" target="_blank">';
					$link_back = '</a>';
				} elseif($this->app_params->get("useWorker".$nr) == 2) {
					$link_front = '<a href="'.$this->app_params->get("linkWorker".$nr).'" target="_blank">';
					$link_back = '</a>';
				} else {
					$link_front = '';
					$link_back = '';
				}
				$workForce[] = $this->app_params->get("titleWorker".$nr).' '.$link_front.$this->app_params->get("nameWorker".$nr).$link_back;
			}
		}
		return $workForce;
	}
	
	protected function getAvailableVersions()
	{
		// Base this model on the backend version.
		require_once JPATH_ADMINISTRATOR.'/components/com_getbible/models/import.php';
		$versions_model 		= new GetbibleModelImport;
		$availableVersions 		= $versions_model->availableVersions;
		$availableVersionsList 	= $versions_model->availableVersionsList;
		$installedVersions 		= $versions_model->installedVersions;
		
		if($availableVersionsList){
			if ($installedVersions){
				$availableVersionsList = array_diff($availableVersionsList, $installedVersions);
			}
			foreach($availableVersionsList as $version){
				$availableVersions[$version]['not'] = 1;
			}
			return $availableVersions;
		} return false;
	}
	
	protected function setIcons()
	{
		// setup icons
		$icons 			= array();
		// INFO
		$info 			= new stdClass();	
		$info->other	= 'data-toggle="tab" onclick="changeTab(\'info\');"';
		$info->url 		= '#info';
		$info->name 	= 'COM_GETBIBLE_INFO';
		$info->title 	= 'COM_GETBIBLE_INFO_DESC';
		$info->image 	= 'administrator/components/com_getbible/assets/images/icons/info.png';
		$icons[0]		= $info;
		// API
		$api_doc 			= new stdClass();	
		$api_doc->other		= 'data-toggle="tab" onclick="changeTab(\'api_doc\');"';
		$api_doc->url 		= '#api_doc';
		$api_doc->name 		= 'COM_GETBIBLE_API_DOC';
		$api_doc->title 	= 'COM_GETBIBLE_API_DOC_DESC';
		$api_doc->image 	= 'administrator/components/com_getbible/assets/images/icons/api_doc.png';
		$icons[1]			= $api_doc;
		// Exchange Rate Updater
		$versions 			= new stdClass();
		$versions->other	= 'data-toggle="tab" onclick="changeTab(\'versions\');"';
		$versions->url 		= '#versions';
		$versions->name 	= 'COM_GETBIBLE_VERSIONS';
		$versions->title 	= 'COM_GETBIBLE_VERSIONS_DESC';
		$versions->image 	= 'administrator/components/com_getbible/assets/images/icons/versions.png';
		$icons[2]			= $versions;
		// Activity
		$activity 			= new stdClass();
		$activity->other	= 'data-toggle="tab" onclick="changeTab(\'activity\');"';
		$activity->url		= '#activity';
		$activity->name 	= 'COM_GETBIBLE_ACTIVITY';
		$activity->title 	= 'COM_GETBIBLE_ACTIVITY_DESC';
		$activity->image 	= 'administrator/components/com_getbible/assets/images/icons/activity.png';
		$icons[3]			= $activity;
		// Book Names
		$book_names 		= new stdClass();	
		$book_names->other	= '';
		$book_names->url 	= 'index.php?option=com_getbible&view=setbooks';
		$book_names->name 	= 'COM_GETBIBLE_BOOK_NAMES';
		$book_names->title 	= 'COM_GETBIBLE_BOOK_NAMES_DESC';
		$book_names->image 	= 'administrator/components/com_getbible/assets/images/icons/book_names.png';
		$icons[4]			= $book_names;
		// Install Bibles
		$install_bibles 		= new stdClass();	
		$install_bibles->other	= '';	
		$install_bibles->url 	= 'index.php?option=com_getbible&view=import';
		$install_bibles->name 	= 'COM_GETBIBLE_INSTALL_BIBLES';
		$install_bibles->title	= 'COM_GETBIBLE_INSTALL_BIBLES_DESC';
		$install_bibles->image	= 'administrator/components/com_getbible/assets/images/icons/install_bibles.png';
		$icons[5]				= $install_bibles;
		// Installed Bibles
		$installed_bibles 			= new stdClass();	
		$installed_bibles->other	= '';	
		$installed_bibles->url 		= 'index.php?option=com_getbible&view=versions';
		$installed_bibles->name 	= 'COM_GETBIBLE_INSTALLED_BIBLES';
		$installed_bibles->title	= 'COM_GETBIBLE_INSTALLED_BIBLES_DESC';
		$installed_bibles->image	= 'administrator/components/com_getbible/assets/images/icons/installed_bibles.png';
		$icons[6]					= $installed_bibles;
		// First check user access
		$canDo = JHelperContent::getActions('com_getbible', 'getbible');
		if ($canDo->get('core.admin')) {
			// setu the return url
			$uri = (string) JUri::getInstance();
			$return = urlencode(base64_encode($uri));
			// Global Settings
			$global_settings 			= new stdClass();	
			$global_settings->other		= '';	
			$global_settings->url 		= 'index.php?option=com_config&amp;view=component&amp;component=com_getbible&amp;path=&amp;return=' . $return;
			$global_settings->name		= 'COM_GETBIBLE_OPTIONS';
			$global_settings->title		= 'COM_GETBIBLE_OPTIONS_DESC';
			$global_settings->image		= 'administrator/components/com_getbible/assets/images/icons/options.png';
			$icons[111]					= $global_settings;
		}
		
		$mainframe = JFactory::getApplication();
		// Trigger Event - ipdata_bk_onBefore_icon_display
		$mainframe->triggerEvent('ipdata_bk_onBefore_icon_display',array('icons'=>&$icons));
		
		// setup template
		$temp = '';
		foreach($icons as $icon){
			$temp .= '<div class="dashboard-wraper"><div class="dashboard-content"><a class="icon hasTip" '.$icon->other.' href="'.$icon->url.'" title="';
			$temp .= JText::_($icon->title);
			$temp .= '">';
            $temp .= JHTML::_('image', $icon->image, JText::_($icon->name));
            $temp .= '<span class="dashboard-title">';
			$temp .= JText::_($icon->name);
			$temp .= '</span></a></div></div>';
        }
		return $temp;
	}
	
	protected function getAPIkey()
	{
		if($this->app_params->get('api_access') > 0){
			$privateKey = $this->app_params->get('api_privatekey');
			if(strlen($privateKey) > 0){
				switch($this->app_params->get('api_access')){
					case 1:
					// only private key
					return md5($privateKey);
					break;
					case 2:
					// all users					
					return md5(JFactory::getUser()->username.'_'.$privateKey);
					break;
					case 3:
					// only users in certain groups
					if(is_array($this->app_params->get('api_accessgroup'))){
						$ids = $this->getUserIdsInGroups($this->app_params->get('api_accessgroup'));
						if(is_array($ids)){
							$users = $this->getUserNames($ids);
							if(is_array($users)){
								foreach($users as $user){
									return md5($user.'_'.$privateKey);
								}
							}
						}
					}
					break;
					case 4:
					// only users with certain access level
					if(is_array($this->app_params->get('api_accesslevel'))){
						$groups = $this->getUserGroupsWithAccess($this->app_params->get('api_accesslevel'));
						if(is_array($groups)){
							$ids = $this->getUserIdsInGroups($groups);
							if(is_array($ids)){
								$users = $this->getUserNames($ids);
								if(is_array($users)){
									foreach($users as $user){
										return md5($user.'_'.$privateKey);
									}
								}
							}
						}
					}
					break;
					case 5:
					// only selected users
					if(is_array($this->app_params->get('api_accessuser'))){
						foreach($this->app_params->get('api_accessuser') as $user){
							return md5($user.'_'.$privateKey);
						}
					}						
					break;
				}
			}
		}

		return 0;
	}
	
	protected function getUserNames($ids)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__users.username');
		$query->from('#__users');
		if(is_array($ids)){
			$query->where('#__users.id IN ('.implode(',', $ids).')');
		}
		$db->setQuery((string)$query);
		return $db->loadColumn();
	}
	
	protected function getUserIdsInGroups($groups)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__user_usergroup_map.user_id');
		$query->from('#__user_usergroup_map');
		$query->where('#__user_usergroup_map.group_id IN ('.implode(',', $groups).')');
		$db->setQuery((string)$query);
		return $db->loadColumn();
	}
	
	protected function getUserGroupsWithAccess($levels)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__viewlevels.rules');
		$query->from('#__viewlevels');
		$query->where('#__viewlevels.id IN ('.implode(',', $levels).')');
		$db->setQuery((string)$query);
		$group_levels =  $db->loadColumn();
		if(is_array($group_levels)){
			$groups = array();
			foreach($group_levels as $level){
				$group_ids = json_decode($level, true);
				if(is_array($group_ids)){
					$groups = $groups + $group_ids;
				}
			}
			return array_unique($groups);
		}
		return false;
	}
}
