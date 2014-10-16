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

jimport('joomla.application.component.controlleradmin');

class GetbibleControllerVersions extends JControllerAdmin
{
	protected $text_prefix = 'COM_GETBIBLE_VERSIONS';

	public function getModel($name = 'Version', $prefix = 'GetbibleModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}