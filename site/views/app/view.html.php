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

class GetbibleViewApp extends JViewLegacy
{
	/**
	 * @var bool import success
	 */
	protected $params;
	protected $cpanel;
	protected $AppDefaults;
	protected $highlights;
	protected $signupUrl;
	protected $user;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->cpanel	= $this->get('Cpanel');
		// get the Book Defaults
		$this->AppDefaults = $this->get('AppDefaults');
		// get the last date a book name was changed
		$this->booksDate = $this->get('BooksDate');
		// Get app Params
		$this->params 		= JFactory::getApplication()->getParams();
		$this->signupUrl 	= $this->getRouteUrl('index.php?Itemid='.$this->params->get('account_menu'));
		// set the user details
		$this->user = JFactory::getUser();
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	} 
		
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		// set query options
		$setApp = '';
		
		// set query url
		if ($this->params->get('jsonQueryOptions') == 1){
			
			// Load Jquery check
			if($this->params->get('jsonAPIaccess')){
				
				$key		= JSession::getFormToken();
				$setApp 	.= 	"var appKey = '".$key."';"; 
			}
			
			$jsonUrl 	=  "'index.php?option=com_getbible&view=json'";
			
		} elseif ($this->params->get('jsonQueryOptions') == 2) {
			$setApp 	.= 	"var cPanelUrl = 'https://getbible.net/';";
			$jsonUrl 	=  "'https://getbible.net/json'";
			
		} else {
			$setApp 	.= 	"var cPanelUrl = 'http://getbible.net/';";
			$jsonUrl 	=  "'http://getbible.net/json'";
			
		}
		
		// Get app settings
		//require_once( JPATH_COMPONENT.DS.'helpers'.DS.'jquery_app.php' );
		//require_once( JPATH_COMPONENT.DS.'helpers'.DS.'css_app.php' );
		
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'script_checker.php' );
		// The css
		$this->document->addStyleSheet(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'css'.DS.'app.css');
		if (!HeaderCheck::css_loaded('uikit.min')) {
			$this->document->addStyleSheet(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'css'.DS.'uikit.min.css');
		}
		$this->document->addStyleSheet(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'css'.DS.'components'.DS.'sticky.min.css');
		$this->document->addStyleSheet(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'css'.DS.'offline.css');
		$this->document->addStyleSheet(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'css'.DS.'tagit.css');
		
		// The JS
		// Load jQuery check
		if (!HeaderCheck::js_loaded('jquery')) {
			JHtml::_('jquery.ui');
		}
		// load highlight javascript plugin
		$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'jquery-ui-custom.js');
		// set defaults
		if($this->params->get('account') && $this->user->id > 0){
			$setApp .=	'var openNow			= "'.base64_encode($this->user->id).'";';
			$setApp .=  'var user_id 			= '.$this->user->id.';';
			$setApp .=  'var jsonKey 			= "'.JSession::getFormToken().'";';
			$setApp .=  'var allowAccount 		= '.$this->params->get('account').';';
		} else {
			$setApp .=	'var openNow			= 0;';
			$setApp .=  'var user_id 			= 0;';
			$setApp .=  'var jsonKey 			= 0;';
			$setApp .=  'var allowAccount 		= '.$this->params->get('account').';';
		}
		$setApp .=  'var defaultKey 		= "'.$this->AppDefaults['defaultKey'].'";';
		$setApp .=  'var searchApp 			= 0;';
		if($this->AppDefaults['request']){
			$setApp .=  'var defaultRequest		= "'.$this->AppDefaults['request'].'";';
			$setApp .=  'var searchFor 			= 0;';
			$setApp .=  'var searchCrit 		= 0;';
			$setApp .=  'var searchType 		= 0;';
			$setApp .=  'var loadApp 			= 0;';
		} else {
			$setApp .=  'var defaultRequest		= 0;';
		}
		$setApp .= 	'var autoLoadChapter 	= '.$this->params->get('auto_loading_chapter').';';
		$setApp .= 	'var appMode 			= '.$this->params->get('app_mode').';';
		$setApp .= 	'var jsonUrl 			= '.$jsonUrl.';';
		$setApp .= 	'var booksDate 			= "'.$this->booksDate.'";';
		$setApp .= 	'var highlightOption 	= '. $this->params->get('highlight_option').';';// set the search styles
		$setApp .= 	'var verselineMode 		= '. $this->params->get('line_mode').';';
		if($this->params->get('highlight_padding')){
			$padding = 'padding: 0 3px 0 3px;';
		} else {
			$padding = '';
		}
		// verses style
		$versStyles = '	#scripture .verse { cursor: pointer; }
						#scripture .verse_nr { cursor: pointer; }
						/* verse sizes */ 
						#scripture .verse_small { font-size: '.$this->params->get('font_small').'px; line-height: 1.5;} 
						#scripture .verse_medium { font-size: '.$this->params->get('font_medium').'px; line-height: 1.5;}
						#scripture .verse_large { font-size: '.$this->params->get('font_large').'px; line-height: 1.5;}
						/* verse nr sizes */ 
						#scripture .nr_small { font-size: '. ($this->params->get('font_small') - 3).'px; line-height: 1.5;} 
						#scripture .nr_medium { font-size: '. ($this->params->get('font_medium') - 4).'px; line-height: 1.5;}
						#scripture .nr_large { font-size: '. ($this->params->get('font_large') - 5).'px; line-height: 1.5;}
						/* chapter nr sizes */ 
						#scripture .chapter_nr { font-size: 200%; }';
		$this->document->addStyleDeclaration( $versStyles );
		// search highlight style
		$searchStyles = '.highlight { color: '.$this->params->get('highlight_textcolor').'; border-bottom: 1px '.$this->params->get('highlight_linetype').' '.$this->params->get('highlight_linecolor').'; background-color: '.$this->params->get('highlight_background').'; '. $padding .' }';
		$this->document->addStyleDeclaration( $searchStyles );
		// hover styles
		$hoverStyle = '.hoverStyle { color: '.$this->params->get('hover_textcolor').'; border-bottom: 1px '.$this->params->get('hover_linetype').' '.$this->params->get('hover_linecolor').'; background-color: '.$this->params->get('hover_background').'; }';
		$this->document->addStyleDeclaration( $hoverStyle );
		// highlight styles
		$marks = range('a','z');
		foreach($marks as $mark){
			$this->highlights[$mark] =  array(
											'name' => $this->params->get('mark_'.$mark.'_name'), 
											'text' => $this->params->get('mark_'.$mark.'_textcolor'), 
											'background' => $this->params->get('mark_'.$mark.'_background')
											);
			$markStyle = '.highlight_'.$mark.' { 
								color: '.$this->params->get('mark_'.$mark.'_textcolor').'; 
								border-bottom: 1px '.$this->params->get('mark_'.$mark.'_linetype').' '.$this->params->get('mark_'.$mark.'_linecolor').'; 
								background-color: '.$this->params->get('mark_'.$mark.'_background').'; 
								}';
			$this->document->addStyleDeclaration( $markStyle );
		}
		// load base64 javascript plugin
		$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'base64.js');
		// load highlight javascript plugin
		$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'highlight.js');
		
		$this->document->addScriptDeclaration($setApp);  
		$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'app.js');
		
		// Load Uikit check
		if (!HeaderCheck::js_loaded('uikit.min')) {
			$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'uikit.min.js');
		}
		if (!HeaderCheck::js_loaded('sticky.min')) {
			$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'components'.DS.'sticky.min.js');
		}
		
		// Load Json check
		if (!HeaderCheck::js_loaded('jquery.json')) {
			$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'jquery.json.min.js');
		}
		// Load Jstorage check
		if (!HeaderCheck::js_loaded('jstorage')) {
			$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'jstorage.min.js');
		}
		// Load Tag It check
		if (!HeaderCheck::js_loaded('tag-it')) {
			$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'tag-it.js');
		}
		// Load Offline check
		if (!HeaderCheck::js_loaded('offline')) {
			$this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'offline.min.js');
		}
		// debug offline status
		// $this->document->addScript(JURI::base( true ) .DS.'media'.DS.'com_getbible'.DS.'js'.DS.'offline-simulate-ui.min.js');
						
		// to check in app is online
		$offline	= '	jQuery(document).ready(function(){ 
							Offline.options = {checks: { image: {url: "/media/com_getbible/images/vdm.png"}, active: "image"}};
							window.setInterval(function() {
								
								if (Offline.state === "up"){
									Offline.check();				
								}
								
							}, 3000);
						});';
		$this->document->addScriptDeclaration($offline);
		
	}
	
	/**
	 * Get the correct path
	 */
	protected function getRouteUrl($route) {
		
		// Get the global site router.
		$config = &JFactory::getConfig();
		$router = JRouter::getInstance('site');
		$router->setMode( $config->get('sef', 1) );
	
		$uri    = &$router->build($route);
		$path   = $uri->toString(array('path', 'query', 'fragment'));
	
		return $path;
	}
}
