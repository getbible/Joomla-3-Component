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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$versions = $this->cpanel;
?>
<button class="uk-button searchbuttons" data-uk-offcanvas="{target:'#search_scripture'}" style="display:none;">Search</button>
<button class="uk-button searchbuttons" onClick="highScripture()" style="display:none;">Highlight</button>

<div id="cPanel">
    <form class="uk-form">
        <fieldset data-uk-margin="">
        	<button class="uk-button" data-uk-offcanvas="{target:'#search_scripture'}">Search</button>
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
</div>
<br/>

<div id="t_loader" style="text-align:center;"><?php echo JText::_('COM_GETBIBLE_LOADING'); ?></div>

<div id="getbible" style="display:none;">

    <div id="chapters" style="display:none;"></div>
    
    <div id="scripture"></div>
	<?php if($this->params->get('app_mode') == 2): ?>
    <button id="button_top" class="uk-button uk-button-primary" type="button" style="display:none; position: fixed; bottom: 0px; z-index: 3;" onClick="gotoTop()"><?php echo JText::_('COM_GETBIBLE_GO_TOP'); ?></button>
    <div class="navigation uk-panel uk-width-1-2 uk-container-center uk-text-center" style="display:none;"><div class="uk-button-group"><a class="uk-button button" type="button"  onClick="showChapters(true)"><?php echo JText::_('COM_GETBIBLE_SELECT_ANOTHER'); ?></a><a id="prev" class="uk-button" href="javascript:void(0)" onClick="prevChapter()"><i class="uk-icon-fast-backward"></i> <?php echo JText::_('COM_GETBIBLE_PREV'); ?></a><a class="uk-button" href="javascript:void(0)" onClick="nextChapter()"><?php echo JText::_('COM_GETBIBLE_NEXT'); ?> <i class="uk-icon-fast-forward"></i></a></div></div>
    <?php else: ?>
	<button class="uk-button uk-button-primary button" type="button" style="display:none; position: fixed; bottom: 0px; z-index: 3;" onClick="showChapters(true)"><?php echo JText::_('COM_GETBIBLE_SELECT_ANOTHER'); ?></button>
    <button id="button_top" class="uk-button uk-button-primary" type="button" style="display:none; position: fixed; bottom: 0px; z-index: 3;" onClick="gotoTop()"><?php echo JText::_('COM_GETBIBLE_GO_TOP'); ?></button>
    <div class="navigation uk-panel uk-width-1-2 uk-container-center uk-text-center" style="display:none;"><a class="uk-button" href="javascript:void(0)" onClick="nextChapter()"><?php echo JText::_('COM_GETBIBLE_NEXT'); ?> <i class="uk-icon-fast-forward"></i></a></div>
    <?php endif; ?>
</div>
<div id="b_loader" style="display:none; text-align:center;"><?php echo JText::_('COM_GETBIBLE_LOADING'); ?></div>

<div id="search_scripture" class="uk-offcanvas">
    <div class="uk-offcanvas-bar">
        <form class="uk-search" id="search_form" method="post">
            <input class="uk-search-field" type="input" name="search" placeholder="search...">
            <div class="uk-margin">
           		<input class="uk-button" type="submit"  value="Search">
            </div>
            <input class="uk-hidden" type="hidden" name="search_crit" id="search_crit" value="1_1_1">
        	<input class="uk-hidden" type="hidden" name="search_type" id="search_type" value="all">
        	<input class="uk-hidden" type="hidden" name="search_version" id="search_version" value="kjv">
            <input class="uk-hidden" type="hidden" name="search_app" id="search_app" value="true">
        </form>
       <div class="uk-panel uk-container-center">
            <div class="uk-margin">
                <div class="uk-button-group"  data-uk-button-radio="">
                    <button type="button" class="uk-button uk-button-primary uk-active search_crit1" value="1">All Words</button>
                    <button type="button" class="uk-button uk-button-primary search_crit1" value="2">Any Words</button>
                    <button type="button" class="uk-button uk-button-primary search_crit1" value="3">Phrase</button>
                </div>
            </div>
            <div class="uk-margin">
                <div class="uk-button-group"  data-uk-button-radio="">
                    <button type="button" class="uk-button uk-button-primary uk-active search_type" value="all">Bible</button>
                    <button type="button" class="uk-button uk-button-primary search_type" value="ot">OT</button>
                    <button type="button" class="uk-button uk-button-primary search_type" value="nt">NT</button>
                    <button type="button" class="uk-button uk-button-primary search_type" id="search_book" value="john">This Book</button>
                </div>
            </div>
            <div class="uk-margin">
            	<div class="uk-button-group"  data-uk-button-radio="">
                    <button class="uk-button uk-button-mini uk-button-primary uk-active search_crit2" type="button" value="1">Exact Match</button>
                    <button class="uk-button uk-button-mini uk-button-primary search_crit2" type="button" value="2">Partial Match</button>
                 </div>
            </div>
            <div class="uk-margin">
            	<div class="uk-button-group"  data-uk-button-radio="">
                    <button class="uk-button uk-button-mini uk-button-primary uk-active search_crit3" type="button" value="1">Case Insensitive</button>
                    <button class="uk-button uk-button-mini uk-button-primary search_crit3" type="button" value="2">Case Sensitive</button>
                 </div>
            </div>
        </div>
    </div>
</div>