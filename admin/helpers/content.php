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


class ContentHelper extends JHelperContent
{
	public static $extension = 'com_getbible';

	/**
	 * Configure the submenu.
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_HOME'),
			'index.php?option=com_getbible&view=getbible',
			$vName == 'getbible'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_SET_BOOKS'),
			'index.php?option=com_getbible&view=setbooks',
			$vName == 'setbooks'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_IMPORT'),
			'index.php?option=com_getbible&view=import',
			$vName == 'import'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_VERSIONS'),
			'index.php?option=com_getbible&view=versions',
			$vName == 'versions'
		);
	}

	/**
	 * Applies the content tag filters to arbitrary text as per settings for current user group
	 *
	 * @param   text  $text  The string to filter
	 *
	 * @return  string  The filtered string
	 *
	 * @deprecated  4.0  Use JComponentHelper::filterText() instead.
	*/
	public static function filterText($text)
	{
		JLog::add('ContentHelper::filterText() is deprecated. Use JComponentHelper::filterText() instead.', JLog::WARNING, 'deprecated');

		return JComponentHelper::filterText($text);
	}
}
