<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage
 * @author
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: orders.php 6408 2012-09-08 11:23:40Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
AdminUIHelper::startAdminArea ();

?>


<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div id="header">
        <div id="filterbox">
            <table>
                <tr>
                    <td align="left" width="100%">
                        <?php echo $this->displayDefaultViewSearch ('name'); ?>
                        <?php echo JText::_ ('COM_VIRTUEMART_INPOSTPARCELS_VIEW_PARCEL_STATUS') . ':' . $this->lists['state_list']; ?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="resultscounter"><?php echo $this->pagination->getResultsCounter (); ?></div>
    </div>
    <table class="adminlist" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count ($this->parcelslist); ?>')"/></th>
            <th><?php echo $this->sort ('id', 'ID')  ?></th>
            <th><?php echo $this->sort ('virtuemart_order_id', 'COM_VIRTUEMART_INPOSTPARCELS_VIEW_ORDER_ID')  ?></th>
            <th><?php echo $this->sort ('parcel_id', 'COM_VIRTUEMART_INPOSTPARCELS_VIEW_PARCEL_ID')  ?></th>
            <th><?php echo $this->sort ('parcel_status', 'COM_VIRTUEMART_INPOSTPARCELS_VIEW_STATUS')  ?></th>
            <th><?php echo $this->sort ('parcel_target_machine_id', 'COM_VIRTUEMART_INPOSTPARCELS_VIEW_MACHINE_ID')  ?></th>
            <th><?php echo $this->sort ('sticker_creation_date', 'COM_VIRTUEMART_INPOSTPARCELS_VIEW_STICKER_CREATION_DATE')  ?></th>
            <th><?php echo $this->sort ('created_on', 'COM_VIRTUEMART_INPOSTPARCELS_VIEW_CREATION_DATE')  ?></th>
            <th><?php echo JText::_ ('COM_VIRTUEMART_INPOSTPARCELS_VIEW_ACTIONS')  ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count ($this->parcelslist) > 0) {
            $i = 0;
            $k = 0;
            $keyword = JRequest::getWord ('keyword');

            foreach ($this->parcelslist as $key => $parcel) {
                $checked = JHTML::_ ('grid.id', $i, $parcel->id);
                ?>
            <tr class="row<?php echo $k; ?>">
                <!-- Checkbox -->
                <td><?php echo $checked; ?></td>
                <td><?php echo $parcel->id; ?>
                <!-- Order id -->
                <?php
                $link_order = 'index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=' . $parcel->virtuemart_order_id;
                ?>
                <td><?php echo JHTML::_ ('link', JRoute::_ ($link_order), $parcel->order_number, array('title' => JText::_ ('COM_VIRTUEMART_INPOSTPARCELS_VIEW_EDIT_ORDER') . ' ' . $parcel->order_number)); ?></td>
                <!-- Parcel id -->
                <?php
                $link_parcel = 'index.php?option=com_virtuemart&view=inpostparcels&task=edit&id=' . $parcel->id;
                ?>
                <td><?php echo JHTML::_ ('link', JRoute::_ ($link_parcel), $parcel->parcel_id, array('title' => JText::_ ('COM_VIRTUEMART_INPOSTPARCELS_VIEW_EDIT_PARCEL') . ' ' . $parcel->parcel_id)); ?></td>
                <td><?php echo $parcel->parcel_status; ?>
                <td><?php echo $parcel->parcel_target_machine_id; ?>
                <td><?php echo $parcel->sticker_creation_date; ?>
                <td><?php echo $parcel->created_on; ?>
                <!-- Actions -->
                <?php
                if($parcel->parcel_id == '0'){
                    $link_name = JText::_ ('COM_VIRTUEMART_INPOSTPARCELS_VIEW_CREATE_PARCEL');
                }else{
                    $link_name = JText::_ ('COM_VIRTUEMART_INPOSTPARCELS_VIEW_EDIT_PARCEL');
                }
                $link_edit = 'index.php?option=com_virtuemart&view=inpostparcels&task=edit&id=' . $parcel->id;
                $cell = JHTML::_ ('link', JRoute::_ ($link_edit), $link_name, array('title' => JText::_ ('COM_VIRTUEMART_INPOSTPARCELS_VIEW_EDIT_PARCEL') . ' ' . $parcel->id));
                ?>
                <td><?php echo $cell; ?></td>
            </tr>
                <?php
                $k = 1 - $k;
                $i++;
            }
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="11">
                <?php echo $this->pagination->getListFooter (); ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <!-- Hidden Fields -->
    <?php echo $this->addStandardHiddenToForm (); ?>
</form>
<?php AdminUIHelper::endAdminArea (); ?>
<script type="text/javascript">
    <!--

        jQuery('.show_comment').click(function() {
        jQuery(this).prev('.element-hidden').show();
        return false
        });

        jQuery('.element-hidden').mouseleave(function() {
        jQuery(this).hide();
        });
        jQuery('.element-hidden').mouseout(function() {
        jQuery(this).hide();
        });
        -->
</script>
