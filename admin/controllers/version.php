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

jimport('joomla.application.component.controllerform');

class GetbibleControllerVersion extends JControllerForm
{
	protected $text_prefix = 'COM_GETBIBLE_VERSIONS';
	
	protected $view_list = 'versions';
}