<?php defined('_JEXEC') or die('Restricted access');

//JHTML::stylesheet('style.css', VMKLARNAPLUGINWEBROOT . '/klarna/assets/css/', false);
//JHTML::script('klarna_pp.js', VMKLARNAPLUGINWEBASSETS.'/js/', false);
//JHTML::script('klarnapart.js', 'https://static.klarna.com:444/external/js/', false);
//$document = JFactory::getDocument();


?>
<script type="text/javascript" src="https://geowidget.inpost.co.uk/dropdown.php?field_to_update=name&field_to_update2=address&user_function=user_function"></script>
<table id="easypack24_detail" width="350">
    <tr>
        <td>
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select id="shipping_easypack24" name="shipping_easypack24[parcel_target_machine_id]">
                <option value='' <?php if(@$_POST['shipping_easypack24']['parcel_target_machine_id'] == ''){ echo "selected=selected";} ?>><?php echo $viewData['easypack24']['defaultSelect'];?></option>
                 <?php foreach(@$viewData['easypack24']['parcelTargetMachinesId'] as $key => $parcelTargetMachineId): ?>
                    <option value='<?php echo $key ?>' <?php if(@$_POST['shipping_easypack24']['parcel_target_machine_id'] == $key){ echo "selected=selected";} ?>><?php echo @$parcelTargetMachineId;?></option>
                 <?php endforeach; ?>
            </select>
            <input type="hidden" id="name" name="name" disabled="disabled" />
            <input type="hidden" id="box_machine_town" name="box_machine_town" disabled="disabled" />
            <input type="hidden" id="address" name="address" disabled="disabled" />
            <br>&nbsp; &nbsp; &nbsp; &nbsp;
            <a href="#" onclick="openMap(); return false;"><?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_VIEW_MAP'); ?></a>&nbsp|&nbsp<input type="checkbox" name="show_all_machines"><?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_SHOW_TERMINALS'); ?>
            <br>
            <br>&nbsp; &nbsp; &nbsp; &nbsp;<b><?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_VIEW_MOB_EXAMPLE'); ?> </b>
            <br>&nbsp; &nbsp; &nbsp; &nbsp;<?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_MOB_PREFIX'); ?><input type='text' name='shipping_easypack24[receiver_phone]' id="easypack24_phone" title="<?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_VIEW_MOB_TITLE'); ?>" value='<?php echo @$_POST['shipping_easypack24']['receiver_phone']?@$_POST['shipping_easypack24']['receiver_phone']:$viewData['easypack24']['address']['phone_2']; ?>' />

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

                jQuery(document).ready(function(){
                    jQuery('input[type="checkbox"][name="show_all_machines"]').click(function(){
                        var machines_list_type = jQuery(this).is(':checked');

                        if(machines_list_type == true){
                            //alert('all machines');
                            var machines = {
                                '' : '<?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_VIEW_SELECT_MACHINE');?>',
                            <?php foreach($viewData['easypack24']['parcelTargetAllMachinesId'] as $key => $parcelTargetAllMachineId): ?>
                                '<?php echo $key ?>' : '<?php echo addslashes($parcelTargetAllMachineId) ?>',
                                <?php endforeach; ?>
                            };
                        }else{
                            //alert('criteria machines');
                            var machines = {
                                '' : '<?php echo JText::_ ('COM_VIRTUEMART_EASYPACK24_VIEW_SELECT_MACHINE');?>',
                            <?php foreach($viewData['easypack24']['parcelTargetMachinesId'] as $key => $parcelTargetMachineId): ?>
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

                    jQuery("#easypack24_detail").hide();
                    if(jQuery('#<?php echo $viewData['easypack24']['radio_id'];?>').is(':checked')) {
                        jQuery("#easypack24_detail").show();
                    }

                    jQuery('input[type="radio"][name="virtuemart_shipmentmethod_id"]').click(function(){
                        if(jQuery('#<?php echo $viewData['easypack24']['radio_id'];?>').is(':checked')) {
                            jQuery("#easypack24_detail").show();
                        }else{
                            jQuery("#easypack24_detail").hide();
                        }
                    });

                });

            </script>
        </td>
    </tr>
</table>




