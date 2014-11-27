<?php
/**
* 
* 	@version 	1.0.3  November 25, 2014
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
<div class="searchbuttons" style="display:none;">
<?php if($this->params->get('search_display') == 1): ?>
<form class="uk-form">
    <fieldset data-uk-margin="">
		<button class="uk-button submit_search" data-uk-offcanvas="{target:'#search_scripture'}"><i class="uk-icon-search"></i> <?php echo $this->params->get('search_button'); ?></button>
<?php elseif($this->params->get('search_display') == 2):?>
<form class="uk-form uk-search" id="search_form" method="post">
	<fieldset data-uk-margin="">
        <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
        <input type="submit" style="display:none;" >
        <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
        value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
        <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
        <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
        <input class="uk-hidden search_app" type="hidden" name="search_app" value="1">
<?php elseif($this->params->get('search_display') == 3):?>
<form class="uk-form uk-search" id="search_form" method="post">
	<fieldset data-uk-margin="">
        <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
        <input class="uk-button submit_search" type="submit"  value="<?php echo $this->params->get('search_button'); ?>">
        <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
        value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
        <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
        <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
        <input class="uk-hidden search_app" type="hidden" name="search_app" value="1">
<?php elseif($this->params->get('search_display') == 4):?>
<form class="uk-form uk-search" id="search_form" method="post">
	<fieldset data-uk-margin="">
        <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
        <input class="uk-button submit_search" type="submit"  value="<?php echo $this->params->get('search_button'); ?>">
        <?php if($this->params->get('search_options') == 1): ?>
        <button class="uk-button" data-uk-offcanvas="{target:'#search_scripture'}"><?php echo $this->params->get('advanced_button'); ?></button>
        <?php endif; ?>
        <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
        value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
        <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
        <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
        <input class="uk-hidden search_app" type="hidden" name="search_app" value="1">
<?php elseif($this->params->get('search_display') == 5):?>
<form class="uk-form uk-search" id="search_form" method="post">
	<fieldset data-uk-margin="">
        <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
        <input type="submit" style="display:none;" >
        <?php if($this->params->get('search_options') == 1): ?>
        <button class="uk-button" data-uk-offcanvas="{target:'#search_scripture'}"><?php echo $this->params->get('advanced_button'); ?></button>
        <?php endif; ?>
        <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
        value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
        <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
        <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
        <input class="uk-hidden search_app" type="hidden" name="search_app" value="1"> 
<?php endif; ?>
    
<?php if($this->params->get('highlight_option') == 2): ?>
		<a class="uk-button" onClick="highScripture()" href="#highlight"><?php echo JText::_('Highlight'); ?></a>
   	</fieldset>
</form>
<?php else: ?>
	</fieldset>
</form>
<?php endif ?>
</div>
<div id="cPanel">
	<?php if($this->params->get('search_display') == 1): ?>
    <form class="uk-form">
        <fieldset data-uk-margin="">
            <button class="uk-button submit_search" data-uk-offcanvas="{target:'#search_scripture'}"><i class="uk-icon-search"></i> <?php echo $this->params->get('search_button'); ?></button>
    <?php elseif($this->params->get('search_display') == 2):?>
    <form class="uk-form uk-search" id="search_form" method="post">
        <fieldset data-uk-margin="">
            <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
            <input type="submit" style="display:none;" >
            <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
            value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
            <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
            <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
            <input class="uk-hidden search_app" type="hidden" name="search_app" value="1">
    <?php elseif($this->params->get('search_display') == 3):?>
    <form class="uk-form uk-search" id="search_form" method="post">
        <fieldset data-uk-margin="">
            <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
            <input class="uk-button submit_search" type="submit"  value="<?php echo $this->params->get('search_button'); ?>">
            <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
            value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
            <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
            <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
            <input class="uk-hidden search_app" type="hidden" name="search_app" value="1"> 
    <?php elseif($this->params->get('search_display') == 4):?>
    <form class="uk-form uk-search" id="search_form" method="post">
        <fieldset data-uk-margin="">
            <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
            <input class="uk-button submit_search" type="submit"  value="<?php echo $this->params->get('search_button'); ?>">
            <?php if($this->params->get('search_options') == 1): ?>
            <button class="uk-button" data-uk-offcanvas="{target:'#search_scripture'}"><?php echo $this->params->get('advanced_button'); ?></button>
            <?php endif; ?>
            <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
            value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
            <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
            <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
            <input class="uk-hidden search_app" type="hidden" name="search_app" value="1"> 
    <?php elseif($this->params->get('search_display') == 5):?>
    <form class="uk-form uk-search" id="search_form" method="post">
        <fieldset data-uk-margin="">
            <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
            <input type="submit" style="display:none;" >
            <?php if($this->params->get('search_options') == 1): ?>
            <button class="uk-button" data-uk-offcanvas="{target:'#search_scripture'}"><?php echo $this->params->get('advanced_button'); ?></button>
            <?php endif; ?>
            <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
            value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
            <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
            <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
            <input class="uk-hidden search_app" type="hidden" name="search_app" value="1">
    <?php endif; ?>
        	<select id="versions" class="uk-margin-small-top">
                    <option value=""><?php echo JText::_('COM_GETBIBLE_SELECT_VERSION'); ?></option>
                <?php foreach($versions as $key => $version): ?>
                    <?php if($key == $this->AppDefaults['version']) :?>
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
            <button class="uk-button button" type="button" style="display:none;" onClick="showChapters()"><i class="uk-icon-list-ol"></i> <?php echo JText::_('COM_GETBIBLE_SELECT_CHAPTER'); ?></button>
        </fieldset>
    </form>
</div>
<br/>

<div id="t_loader" style="text-align:center;"><?php echo JText::_('COM_GETBIBLE_LOADING'); ?></div>

<div id="getbible" style="display:none;">

    <div id="chapters" style="display:none;"></div>
    
    <div id="scripture"></div>
	<?php if($this->params->get('app_mode') == 2): ?>
		<?php if($this->params->get('up_button') == 1): ?>
            <button id="button_top" class="uk-button uk-button-primary" type="button" style="display:none; position: fixed; bottom: 0px; z-index: 3;" onClick="gotoTop()"><?php echo JText::_('COM_GETBIBLE_GO_TOP'); ?> <i class="uk-icon-arrow-up"></i></button>
        <?php elseif($this->params->get('up_button') == 2): ?>
            <div id="button_top" class="uk-panel uk-width-1-2 uk-container-center uk-text-center" style="display:none;" >
                <a class="uk-button searchbuttons" type="button" onClick="gotoTop()"><?php echo JText::_('COM_GETBIBLE_GO_TOP'); ?> <i class="uk-icon-arrow-up"></i></a>
            </div>
        <?php endif; ?>
        <div class="navigation uk-panel uk-width-1-2 uk-container-center uk-text-center" style="display:none;">
            <div class="uk-button-group">
                <a class="uk-button button" type="button"  onClick="showChapters(true)"><i class="uk-icon-list-ol"></i> <?php echo JText::_('COM_GETBIBLE_SELECT_CHAPTER'); ?></a>
                <a id="prev" class="uk-button" href="javascript:void(0)" onClick="prevChapter()"><i class="uk-icon-fast-backward"></i> <?php echo JText::_('COM_GETBIBLE_PREV'); ?></a>
                <a class="uk-button" href="javascript:void(0)" onClick="nextChapter()"><?php echo JText::_('COM_GETBIBLE_NEXT'); ?> <i class="uk-icon-fast-forward"></i></a>
            </div>
        </div>
    <?php else: ?>
	<button class="uk-button uk-button-primary button" type="button" style="display:none; position: fixed; bottom: 0px; z-index: 3;" onClick="showChapters(true)"><i class="uk-icon-list-ol"></i> <?php echo JText::_('COM_GETBIBLE_SELECT_CHAPTER'); ?></button>
    <?php if($this->params->get('up_button') == 1): ?>
        <button id="button_top" class="uk-button uk-button-primary" type="button" style="display:none; position: fixed; bottom: 0px; z-index: 3;" onClick="gotoTop()"><?php echo JText::_('COM_GETBIBLE_GO_TOP'); ?> <i class="uk-icon-arrow-up"></i></button>
	<?php elseif($this->params->get('up_button') == 2): ?>
        <div id="button_top" class="uk-panel uk-width-1-2 uk-container-center uk-text-center" style="display:none;" >
            <a class="uk-button searchbuttons" type="button"  onClick="gotoTop()"><?php echo JText::_('COM_GETBIBLE_GO_TOP'); ?> <i class="uk-icon-arrow-up"></i></a>
        </div>
    <?php endif; ?>
        <div class="navigation uk-panel uk-width-1-2 uk-container-center uk-text-center" style="display:none;">
            <a class="uk-button" href="javascript:void(0)" onClick="nextChapter()"><?php echo JText::_('COM_GETBIBLE_NEXT'); ?> <i class="uk-icon-fast-forward"></i></a>
        </div>
    <?php endif; ?>
</div>
<div id="b_loader" style="display:none; text-align:center;"><?php echo JText::_('COM_GETBIBLE_LOADING'); ?></div>

<div id="search_scripture" class="uk-offcanvas">
    <div class="uk-offcanvas-bar">
    	<?php if($this->params->get('search_display') == 1):?>
            <form class="uk-form uk-search" id="search_form" method="post">
                <input class="search_field uk-search-field" type="input" name="search" placeholder="<?php echo $this->params->get('search_phrase'); ?>">
                <?php if($this->params->get('search_options') == 1): ?>
                    <div class="uk-margin">
                        <input class="uk-button submit_search" type="submit"  value="<?php echo $this->params->get('search_button'); ?>">
                    </div>
                <?php endif; ?>
                <input  class="uk-hidden search_crit" type="hidden" name="search_crit"  
                value="<?php echo $this->params->get('search_crit1'); ?>_<?php echo $this->params->get('search_crit2'); ?>_<?php echo $this->params->get('search_crit3'); ?>" >
                <input class="uk-hidden search_type" type="hidden" name="search_type" value="<?php echo $this->params->get('search_type'); ?>">
                <input class="uk-hidden search_version" type="hidden" name="search_version" value="<?php echo $this->params->get('version'); ?>">
                <input class="uk-hidden search_app" type="hidden" name="search_app" value="1"> 
            </form>
        <?php endif; ?>
        <div class="uk-panel uk-container-center">
           	<?php if($this->params->get('search_options') == 1): ?>
            <div class="uk-margin">
                <div class="uk-button-group"  data-uk-button-radio="">
                    <?php 
                        switch($this->params->get('search_crit1')){
                            case 1:
                            $active_crit1_1 = 'uk-active'; $active_crit1_2 = ''; $active_crit1_3 = '';
                            break;
                            
                            case 2:
                            $active_crit1_1 = ''; $active_crit1_2 = 'uk-active'; $active_crit1_3 = '';
                            break;
                            
                            case 3:
                            $active_crit1_1 = ''; $active_crit1_2 = ''; $active_crit1_3 = 'uk-active';
                            break;
                        }
                    ?>
                    <button type="button" class="uk-button uk-button-primary <?php echo $active_crit1_1 ?> search_crit1" value="1"><?php echo JText::_('COM_GETBIBLE_ALL_WORDS'); ?></button>
                    <button type="button" class="uk-button uk-button-primary <?php echo $active_crit1_2 ?> search_crit1" value="2"><?php echo JText::_('COM_GETBIBLE_ANY_WORDS'); ?></button>
                    <button type="button" class="uk-button uk-button-primary <?php echo $active_crit1_3 ?> search_crit1" value="3"><?php echo JText::_('COM_GETBIBLE_PHRASE'); ?></button>
                </div>
            </div>
            <div class="uk-margin">
                <div class="uk-button-group"  data-uk-button-radio="">
                    <?php 
                        switch($this->params->get('search_type')){
                            case 'all':
                            $active_type_all = 'uk-active'; $active_type_ot = ''; $active_type_nt = '';
                            break;
                            
                            case 'ot':
                            $active_type_all = ''; $active_type_ot = 'uk-active'; $active_type_nt = '';
                            break;
                            
                            case 'nt':
                            $active_type_all = ''; $active_type_ot = ''; $active_type_nt = 'uk-active';
                            break;
                        }
                    ?>
                    <button type="button" class="uk-button uk-button-primary <?php echo $active_type_all ?> search_type_select" value="all"><?php echo JText::_('COM_GETBIBLE_BIBLE'); ?></button>
                    <button type="button" class="uk-button uk-button-primary <?php echo $active_type_ot ?> search_type_select" value="ot"><?php echo JText::_('COM_GETBIBLE_OT'); ?></button>
                    <button type="button" class="uk-button uk-button-primary <?php echo $active_type_nt ?> search_type_select" value="nt"><?php echo JText::_('COM_GETBIBLE_NT'); ?></button>
                    <button type="button" class="uk-button uk-button-primary search_type_select" id="search_book" value="john">This Book</button>
                </div>
            </div>
            <div class="uk-margin">
                <div class="uk-button-group"  data-uk-button-radio="">
                    <?php 
                        switch($this->params->get('search_crit2')){
                            case 1:
                            $active_crit2_1 = 'uk-active'; $active_crit2_2 = '';;
                            break;
                            
                            case 2:
                            $active_crit2_1 = ''; $active_crit2_2 = 'uk-active';;
                            break;
                        }
                    ?>
                    <button class="uk-button uk-button-mini uk-button-primary <?php echo $active_crit2_1 ?> search_crit2" type="button" value="1"><?php echo JText::_('COM_GETBIBLE_EXACT_MATCH'); ?></button>
                    <button class="uk-button uk-button-mini uk-button-primary <?php echo $active_crit2_2 ?> search_crit2" type="button" value="2"><?php echo JText::_('COM_GETBIBLE_PARTIAL_MATCH'); ?></button>
                 </div>
            </div>
            <div class="uk-margin">
                <div class="uk-button-group"  data-uk-button-radio="">
                    <?php 
                        switch($this->params->get('search_crit3')){
                            case 1:
                            $active_crit3_1 = 'uk-active'; $active_crit3_2 = '';;
                            break;
                            
                            case 2:
                            $active_crit3_1 = ''; $active_crit3_2 = 'uk-active';;
                            break;
                        }
                    ?>
                    <button class="uk-button uk-button-mini uk-button-primary <?php echo $active_crit3_1 ?> search_crit3" type="button" value="1"><?php echo JText::_('COM_GETBIBLE_CASE_INSENSITIVE'); ?></button>
                    <button class="uk-button uk-button-mini uk-button-primary <?php echo $active_crit3_2 ?> search_crit3" type="button" value="2"><?php echo JText::_('COM_GETBIBLE_CASE_SENSITIVE'); ?></button>
                 </div>
            </div>
        	<?php endif; ?>
        </div>
    </div>
</div>
<?php if ($this->params->get('vdm_logo') == 1): ?>
	<?php if ($this->params->get('vdm_link') == 1): ?><a href="<?php echo $this->params->get('vdm_url');  ?>" target="_blank"><?php endif; ?>
    	<span class="uk-align-right" data-uk-tooltip="{pos:'left'}" title="The words of eternal life!" ><img src="/media/com_getbible/images/icon.png" /></span>
    <?php if ($this->params->get('vdm_link') == 1): ?></a><?php endif; ?>
<?php else: ?>
	<?php if ($this->params->get('vdm_link') == 1): ?><a href="<?php echo $this->params->get('vdm_url');  ?>" target="_blank">
    	<span class="uk-align-right" data-uk-tooltip="{pos:'left'}" title="The words of eternal life!" ><?php echo $this->params->get('vdm_name');  ?></span>
    </a><?php endif; ?>
<?php endif; ?>