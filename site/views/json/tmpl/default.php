<?php
/**
* 
* 	@version 	1.0.9  June 24, 2016
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

	// get the application
	$app	= JFactory::getApplication();
	$jinput = $app->input;
	if ($this->item)
	{
		// update the document mime
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		// set the headers
		$app->setHeader('Content-Disposition','attachment;filename="gebible.json"');
		$app->setHeader("Access-Control-Allow-Origin", "*");
		// check if we should return raw
		$returnRaw = $jinput->get('raw', false, 'BOOLEAN');
		// set the call back
		if($callback = $jinput->get('getbible', null, 'CMD'))
		{
			echo $callback . '(' . $this->item . ');';
		}
		elseif($callback = $jinput->get('callback', null, 'CMD'))
		{
			echo $callback . '(' . $this->item . ');';
		} 
		elseif($returnRaw)
		{
			echo $this->item;
		}
		else
		{
			echo '(' . $this->item . ');';
		}
	}
	else
	{
		echo('NULL');
	}
	// close the application
	$app->close(); 

?>
