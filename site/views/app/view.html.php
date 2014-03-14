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

class GetbibleViewApp extends JViewLegacy
{
	/**
	 * @var bool import success
	 */
	protected $params;
	protected $cpanel;
	protected $bookdefaults;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->cpanel	= $this->get('Cpanel');
		// get the Book Defaults
		$this->bookdefaults = $this->get('Bookdefaults');
		// Get app Params
		$this->params = JFactory::getApplication()->getParams();
		
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
		//require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jquery_app.php' );
		//require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'css_app.php' );
		
		require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'script_checker.php' );
		// The css
		$this->document->addStyleSheet(JURI::base( true ) .DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'app.css');
		if (!HeaderCheck::css_loaded('uikit')) {
			$this->document->addStyleSheet(JURI::base( true ) .DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'uikit.css');
		}
		$this->document->addStyleSheet(JURI::base( true ) .DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'offline.css');
		//$this->document->addStyleDeclaration( $style );
		// The JS
		// Load jQuery check
		if (!HeaderCheck::js_loaded('jquery')) {
			//$this->document->addScript(JURI::base( true ) .DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery-1.10.2.min.js');
			JHtml::_('jquery.ui');
		}
		// set defaults
		$setApp .= 	'var BIBLE_BOOK = "'.$this->bookdefaults->book_ref.'";';
		$setApp .= 	'var BIBLE_CHAPTER = "'.$this->bookdefaults->chapter.'";';
		$setApp .= 	'var BIBLE_VERSION = "'.$this->bookdefaults->version.'";';
		$setApp .= 	'var defaultVersion = "'.$this->bookdefaults->version.'";';
		$setApp .= 	'var defaultBook = "'.$this->bookdefaults->book_ref.'";';
		$setApp .= 	'var defaultBookNr = "'.$this->bookdefaults->book_nr.'";';
		$setApp .= 	'var defaultChapter = "'.$this->bookdefaults->chapter.'";';
		$setApp .= 	'var setQuery = "p="+defaultBook+defaultChapter+"&v="+defaultVersion;';
		$setApp .= 	'var jsonUrl = '.$jsonUrl.';';
		$this->document->addScriptDeclaration($setApp);  
		$this->document->addScript(JURI::base( true ) .DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'app.js');
		
		// Load Uikit check
		if (!HeaderCheck::js_loaded('uikit')) {
			$this->document->addScript(JURI::base( true ) .DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'uikit.min.js');
		}
		$this->document->addScript(JURI::base( true ) .DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'com_getbible'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'offline.min.js');
		
		// to check in app is online
		$offline	= "Offline.options = {checks: {image: {url: '" . JURI::base( true ) .DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."com_getbible".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."vdm.png'}}};
						var run = function(){
						  if (Offline.state === 'up')
							Offline.check();
						}
						setInterval(run, 3000);";
		$this->document->addScriptDeclaration($offline);  
		//$this->document->addScriptDeclaration($settings); 
	}
}