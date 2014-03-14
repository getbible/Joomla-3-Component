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

jimport('joomla.database.databasequery');

function GetbibleBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		if ($query['view'] == 'category' || $query['view'] == 'book') {
			$segments[] = GetbibleGetAlias($query['id'], $query['view']);
			unset($query['id']);
		}

		// look up Itemid
		$query['Itemid'] = GetbibleGetItemid($query['view']);

		unset($query['view']);
	}

	return $segments;
}

function GetbibleParseRoute($segments)
{
	$vars = array();

	$item = JFactory::getApplication()->getMenu()->getActive();

	if (isset($item)) {
		$vars['view'] = $item->query['view'];
	}

	if (count($segments) == 1) {
		$vars['id'] = GetbibleGetIDFromAlias($segments[0], $vars['view']);
	}

	return $vars;
}

function GetbibleGetAlias($id, $view)
{
	return '';
}

function GetbibleGetIDFromAlias($alias, $view)
{
	return 0;
}

function GetbibleGetItemid($view)
{

	return NULL;
}