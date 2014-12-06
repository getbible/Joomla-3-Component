<?php
/**
* 
* 	@version 	1.0.4  December 06, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.database.databasequery');

function GetbibleBuildRoute(&$query)
{
	$segments = array();
	
	if (isset($query['view'])) {
		$segments[] = $query['view'];
		unset($query['view']);
	}
	
	return $segments;
}

function GetbibleParseRoute($segments)
{
	$vars = array();
	switch($segments[0])
	{
		   case 'app':
				   $vars['view'] = 'app';
				   break;
		   case 'json':
				   $vars['view'] = 'json';
				   break;
	}
	return $vars;
}