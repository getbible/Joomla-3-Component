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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$versions = $this->cpanel;
?>
<form class="uk-form">
    <fieldset data-uk-margin="">
        <select id="versions" class="uk-margin-small-top">
                <option value=""><?php echo JText::_('COM_GETBIBLE_SELECT_VERSION'); ?></option>
            <?php foreach($versions as $key => $version): ?>
                <?php if($key == $this->params->get('defaultStartVersion')) :?>
                <option value="<?php echo $key; ?>" selected="selected">(<?php echo $version->language; ?>) <?php echo $version->version_name; ?></option>
                <?php else: ?>
                <option value="<?php echo $key; ?>" >(<?php echo $version->language; ?>) <?php echo $version->version_name; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <select id="f_loader" class="uk-margin-small-top"  style="display:none;">
            <option value="" selected="selected"><?php echo JText::_('COM_GETBIBLE_LOADING'); ?></option>
        </select>
        <select id="books" class="uk-margin-small-top"  style="display:none;">
        </select>
        <button class="uk-button uk-button-primary button" type="button"style="display:none;" onClick="showChapters()"><?php echo JText::_('COM_GETBIBLE_SELECT_ANOTHER_CHAPTER'); ?></button>
    </fieldset>
</form>
<br/>
<div id="t_loader" style="text-align:center;"><?php echo JText::_('COM_GETBIBLE_LOADING'); ?></div>
<div id="getbible" style="display:none;">

    <div id="chapters" style="display:none;"></div>
    
    <div id="scripture"></div>
	<button class="uk-button uk-button-primary button" type="button" style="display:none; position: fixed; bottom: 0px; z-index: 3;" onClick="showChapters(true)"><?php echo JText::_('COM_GETBIBLE_SELECT_ANOTHER'); ?></button>
</div>
<div id="more" style="text-align:center;"><a href="javascript:void(0)" onClick="nextChapter()"><?php echo JText::_('COM_GETBIBLE_NEXT_CHAPTER'); ?></a></div>
<div id="b_loader" style="display:none; text-align:center;"><?php echo JText::_('COM_GETBIBLE_LOADING'); ?></div>