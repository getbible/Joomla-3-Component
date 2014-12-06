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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app		= JFactory::getApplication();
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_getbible&view=setbooks'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
    <?php
    // Search tools bar
    //echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
    <div id="filter-bar" class="btn-toolbar">
        <div class="filter-search btn-group pull-left">
            <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_CONTACT_FILTER_SEARCH_DESC');?></label>
            <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_GETBIBLE_SEARCH_BOOK_NAMES'); ?>" />
        </div>
        <div class="btn-group pull-left">
            <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
            <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
        </div>
        <div class="btn-group pull-right hidden-phone">
            <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
            <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <div class="btn-group pull-right hidden-phone">
            <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
            <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
            </select>
        </div>
        <div class="btn-group pull-right">
            <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
            <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
            </select>
        </div>
    </div>
    <div class="clearfix"> </div>
    <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>
	<table class="table table-striped" id="articleList">
		<thead>
            <tr>
                <th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
                <th>
					<?php echo JHtml::_('grid.sort', 'COM_GETBIBLE_FIELD_BOOK_NAME', 'book_name', $listDirn, $listOrder); ?>
                </th>
				<th class="center">
					<?php echo JHtml::_('grid.sort', 'COM_GETBIBLE_FIELD_VERSION', 'version', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" class="nowrap hidden-phone center">
					<?php echo JHtml::_('grid.sort', 'COM_GETBIBLE_FIELD_BOOK_NR', 'book_nr', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" class="nowrap hidden-phone">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" class="nowrap hidden-phone">
					<?php echo JHtml::_('grid.sort',  'COM_GETBIBLE_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" class="nowrap hidden-phone">
                    <?php echo JHtml::_('grid.sort', 'COM_GETBIBLE_CREATED_ON', 'a.created_on', $listDirn, $listOrder); ?>
                </th>
				<th width="2%" class="hidden-phone center">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                </th>
                <th width="1%" class="nowrap hidden-phone">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item): ?>
				<tr class="row<?php echo $i % 2 ?>">
                	<td class="hidden-phone">
						<?php if ($item->checked_out) : ?>
                            <?php if ($item->checked_out == $userId) : ?>
                            	<?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            <?php else: ?>
                            	&#35;
                            <?php endif; ?>
                        <?php else: ?>
                        	<?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        <?php endif; ?>
                    </td>
					<td>
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->book_name, $item->checked_out_time, 'setbooks.'); ?>
                            <?php if ($item->checked_out == $userId) : ?>
                           		<a href="<?php echo $item->url; ?>">
                                    <?php echo $this->escape($item->book_name) ?>
                                </a>
							<?php else: ?>
                                <?php echo $this->escape($item->book_name) ?>
                            <?php endif; ?>
						<?php else: ?>
							<a href="<?php echo $item->url; ?>">
                            	<?php echo $this->escape($item->book_name) ?>
                            </a>
                        <?php endif; ?>
					</td>
                    <td class="center"><?php echo $this->escape($item->version) ?></td>
                    <td class="hidden-phone center"><?php echo $item->book_nr ?></td>
                    <td class="nowrap hidden-phone"><?php echo $item->access_level ?></td>
                    <td class="small hidden-phone">
                    	<a href="<?php echo $item->createduser; ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
						<?php echo $item->created_by; ?></a>
                    </td>
                    <td class="nowrap small hidden-phone">
							<?php echo $item->created_on; ?>
						</td>
                    <td class="hidden-phone center">
						<?php if ($item->checked_out) : ?>
                            <?php if ($item->checked_out == $userId) : ?>
                            	<?php echo JHtml::_('jgrid.published', $item->published, $i, 'setbooks.', true, 'cb'); ?>
                            <?php else: ?>
								&#35;
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'setbooks.', true, 'cb'); ?>
                        <?php endif; ?>
                    </td>
                    <td class="hidden-phone center"><?php echo $item->id ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</div>
</form>