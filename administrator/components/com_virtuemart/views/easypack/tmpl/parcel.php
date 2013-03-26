<?php
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
//AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_ORDER_PRINT_PO_LBL');

// Get the plugins
//JPluginHelper::importPlugin('vmpayment');
//JPluginHelper::importPlugin('vmshopper');
//JPluginHelper::importPlugin('vmshipment');

?>
<script type="text/javascript" src="https://geowidget.inpost.co.uk/dropdown.php?field_to_update=name&field_to_update2=address&user_function=user_function"></script>
<script type="text/javascript">
    function user_function(value) {
        var address = value.split(';');
        //document.getElementById('town').value=address[1];
        //document.getElementById('street').value=address[2]+address[3];
        var box_machine_name = document.getElementById('name').value;
        var box_machine_town = document.value=address[1];
        var box_machine_street = document.value=address[2];


        var is_value = 0;
        document.getElementById('shipping_easypack24').value = box_machine_name;
        var shipping_easypack24 = document.getElementById('shipping_easypack24');

        for(i=0;i<shipping_easypack24.length;i++){
            if(shipping_easypack24.options[i].value == document.getElementById('name').value){
                shipping_easypack24.selectedIndex = i;
                is_value = 1;
            }
        }

        if (is_value == 0){
            shipping_easypack24.options[shipping_easypack24.options.length] = new Option(box_machine_name+','+box_machine_town+','+box_machine_street, box_machine_name);
            shipping_easypack24.selectedIndex = shipping_easypack24.length-1;
        }
    }
</script>

<form name='adminForm' id="adminForm" method="POST">
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="easypack24" />
    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="parcel_id" value="<?php echo $this->easypack24Data['parcel_id']; ?>" />
    <input type="hidden" name="id" value="<?php echo $this->easypack24Data['id']; ?>" />




<table class="adminlist" style="table-layout: fixed;">
    <tr>
        <td>
            <select id="shipping_easypack24" name="parcel_target_machine_id" <?php echo $this->disabledMachines; ?>>
                <option value='' <?php if(@$this->easypack24Data['parcel_target_machine_id'] == ''){ echo "selected=selected";} ?>><?php echo $this->defaultMachine;?></option>
                <?php foreach($this->parcelTargetMachinesId as $key => $parcelTargetMachine): ?>
                <option value='<?php echo $key ?>' <?php if($this->easypack24Data['parcel_target_machine_id'] == $key){ echo "selected=selected";} ?>><?php echo $parcelTargetMachine;?></option>
                <?php endforeach; ?>
            </select>
            <?php if($this->disabledMachines != 'disabled'): ?>
            <input type="hidden" id="name" name="name" disabled="disabled" />
            <input type="hidden" id="box_machine_town" name="box_machine_town" disabled="disabled" />
            <input type="hidden" id="address" name="address" disabled="disabled" />
            <a href="#" onclick="openMap(); return false;"><?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_VIEW_MAP'); ?></a>
            &nbsp|&nbsp<input type="checkbox" name="show_all_machines"><?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_SHOW_TERMINALS'); ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td><textarea name="parcel_description" rows="10" cols="35"><?php echo $this->easypack24Data['parcel_description']; ?></textarea></td>
    </tr>
    <tr>
        <td>
            <select id="parcel_size" name="parcel_size" <?php echo $this->disabledParcelSize; ?>>
                <option value='' <?php if($this->easypack24Data['parcel_size'] == ''){ echo "selected=selected";} ?>><?php echo $this->defaultParcelSize;?></option>
                <option value='A' <?php if($this->easypack24Data['parcel_size'] == 'A'){ echo "selected=selected";} ?>>A</option>
                <option value='B' <?php if($this->easypack24Data['parcel_size'] == 'B'){ echo "selected=selected";} ?>>B</option>
                <option value='C' <?php if($this->easypack24Data['parcel_size'] == 'C'){ echo "selected=selected";} ?>>C</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><input class="input-text required-entry" name="parcel_status" value="<?php echo $this->easypack24Data['parcel_status']; ?>" <?php ?>/></td>
    </tr>
</table>

</form>

<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea(); ?>


<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('input[type="checkbox"][name="show_all_machines"]').click(function(){
            var machines_list_type = jQuery(this).is(':checked');

            if(machines_list_type == true){
                //alert('all machines');
                var machines = {
                    '' : '<?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_VIEW_SELECT_MACHINE..');?>',
                <?php foreach($this->parcelTargetAllMachinesId as $key => $parcelTargetAllMachineId): ?>
                    '<?php echo $key ?>' : '<?php echo addslashes($parcelTargetAllMachineId) ?>',
                    <?php endforeach; ?>
                };
            }else{
                //alert('criteria machines');
                var machines = {
                    '' : '<?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_VIEW_SELECT_MACHINE..');?>',
                <?php foreach($this->parcelTargetMachinesId as $key => $parcelTargetMachineId): ?>
                    '<?php echo $key ?>' : '<?php echo addslashes($parcelTargetMachineId) ?>',
                    <?php endforeach; ?>
                };
            }

            jQuery('#shipping_easypack24 option').remove();
            jQuery.each(machines, function(val, text) {
                jQuery('#shipping_easypack24').append(
                        jQuery('<option></option>').val(val).html(text)
                );
            });
        });
    });
</script>

