<?php 
/**
* 
* 	@version 	1.0.2  November 10, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$form = $this->form;
$hiddenFields = $this->get('hidden_fields') ?: array();

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'version.cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			<?php //echo $this->form->getField('versiontext')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_getbible&view=version&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
   
	<div class="form-horizontal">
		    <div class="row-fluid">
                <div class="span6">
                    <fieldset class="form-vertical">
                         <?php foreach ($this->form->getFieldset('essential') as $field): ?>
                                <?php echo $field->label; ?>
                                <?php echo $field->input; ?>
                        <?php endforeach ?>
                    </fieldset>
                </div>
                <div class="span6">
                    <fieldset class="form-vertical">
                         <?php foreach ($this->form->getFieldset('optional') as $field): ?>
                                <?php echo $field->label; ?>
                                <?php echo $field->input; ?>
                        <?php endforeach ?>
                    </fieldset> 
                </div>
            </div>
        
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
	</div>
</form>