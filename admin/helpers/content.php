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
			JText::_('COM_GETBIBLE_INFO'),
			'index.php?option=com_getbible&view=getbible&tab=1',
			$vName == 'getbible'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_API_DOC'),
			'index.php?option=com_getbible&view=getbible&tab=2',
			$vName == 'getbible'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_VERSIONS'),
			'index.php?option=com_getbible&view=getbible&tab=3',
			$vName == 'getbible'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_ACTIVITY'),
			'index.php?option=com_getbible&view=getbible&tab=4',
			$vName == 'getbible'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_BOOK_NAMES'),
			'index.php?option=com_getbible&view=setbooks',
			$vName == 'setbooks'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_INSTALL_BIBLES'),
			'index.php?option=com_getbible&view=import',
			$vName == 'import'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_GETBIBLE_INSTALLED_BIBLES'),
			'index.php?option=com_getbible&view=versions',
			$vName == 'versions'
		);
		$canDo = JHelperContent::getActions('com_getbible', 'getbible');
		if ($canDo->get('core.admin')) {
			// setu the return url
			$uri = (string) JUri::getInstance();
			$return = urlencode(base64_encode($uri));
			// Global Settings		
			JHtmlSidebar::addEntry(
				JText::_('COM_GETBIBLE_OPTIONS'),
				'index.php?option=com_config&amp;view=component&amp;component=com_getbible&amp;path=&amp;return=' . $return,
				$vName == 'component'
			);
		}
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
