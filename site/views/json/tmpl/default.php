<?php
/**
* 
* 	@version 	1.0.8  December 3, 2015
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
if ($this->item){
	JFactory::getDocument()->setMimeEncoding( 'application/json' );
	JResponse::setHeader('Content-Disposition','attachment;filename="gebible.json"');
	JResponse::setHeader("Access-Control-Allow-Origin", "*");
	if(array_key_exists('getbible',$_GET)){
		echo $_GET['getbible'] . '(' . $this->item . ');';
	} elseif(array_key_exists('callback',$_GET)){
		echo $_GET['callback'] . '(' . $this->item . ');';
	} else {
		echo '(' . $this->item . ');';
	}
	JFactory::getApplication()->close(); // or jexit();
} else {
	echo('NULL');
	JFactory::getApplication()->close();
}

?>