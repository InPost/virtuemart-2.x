<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage
 * @author
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

if (JVM_VERSION === 2) {
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'easypack24' . DS . 'easypack24' . DS . 'helpers' . DS . 'define.php');
} else {
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'easypack24' . DS . 'helpers' . DS . 'define.php');
}

require_once (JPATH_VMEASYPACK24PLUGIN . DS . 'easypack24' . DS . 'helpers' . DS . 'easypack24_helper.php');


/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewEasypack extends VmView {

	function display($tpl = null) {

        $this->loadHelper('html');

        if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');

        $curTask = JRequest::getWord('task');

        if($curTask == 'edit'){

            $this->setLayout('parcel');
            $id = JRequest::getVar('id');

            $config = easypack24_helper::getParameters();

            $db = JFactory::getDBO();
            $q = "SELECT * FROM #__virtuemart_shipment_plg_easypack24 WHERE id='".(int)$id."'";
            $db->setQuery($q);
            $result_db = $db->loadObject();

            if ($result_db->id || $id == 0) {
                $parcelTargetMachineDetailDb = json_decode($result_db->parcel_target_machine_detail);
                $parcelDetailDb = json_decode($result_db->parcel_detail);

                $allMachines = easypack24_helper::connectEasypack24(
                    array(
                        'url' => $config['API_URL'].'machines',
                        'token' => $config['API_KEY'],
                        'methodType' => 'GET',
                        'params' => array(
                        )
                    )
                );

                $parcelTargetAllMachinesId = array();
                $parcelTargetAllMachinesDetail = array();
                $machines = array();
                if(is_array(@$allMachines['result']) && !empty($allMachines['result'])){
                    foreach($allMachines['result'] as $key => $machine){
                        $parcelTargetAllMachinesId[$machine->id] = $machine->id.', '.@$machine->address->city.', '.@$machine->address->street;
                        $parcelTargetAllMachinesDetail[$machine->id] = array(
                            'id' => $machine->id,
                            'address' => array(
                                'building_number' => @$machine->address->building_number,
                                'flat_number' => @$machine->address->flat_number,
                                'post_code' => @$machine->address->post_code,
                                'province' => @$machine->address->province,
                                'street' => @$machine->address->street,
                                'city' => @$machine->address->city
                            )
                        );
                        if($machine->address->post_code == @$parcelTargetMachineDetailDb->address->post_code){
                            $machines[$key] = $machine;
                            continue;
                        }elseif($machine->address->city == @$parcelTargetMachineDetailDb->address->city){
                            $machines[$key] = $machine;
                        }
                    }
                    //Mage::getSingleton('checkout/session')->setParcelTargetAllMachinesDetail($parcelTargetAllMachinesDetail);
                }
                $this->assignRef('parcelTargetAllMachinesId', $parcelTargetAllMachinesId);
                $this->assignRef('parcelTargetAllMachinesDetail', $parcelTargetAllMachinesDetail);


                $parcelTargetMachinesId = array();
                $parcelTargetMachinesDetail = array();
                $defaultSelect = 'Select Machine..';
                if(is_array(@$machines) && !empty($machines)){
                    foreach($machines as $key => $machine){
                        $parcelTargetMachinesId[$machine->id] = $machine->id.', '.@$machine->address->city.', '.@$machine->address->street;
                        $parcelTargetMachinesDetail[$machine->id] = array(
                            'id' => $machine->id,
                            'address' => array(
                                'building_number' => @$machine->address->building_number,
                                'flat_number' => @$machine->address->flat_number,
                                'post_code' => @$machine->address->post_code,
                                'province' => @$machine->address->province,
                                'street' => @$machine->address->street,
                                'city' => @$machine->address->city
                            )
                        );
                    }
                }else{
                    $this->assignRef('defaultMachine', JText::_('COM_VIRTUEMART_EASYPACK24_DEFAULT_SELECT'));
                }
                $this->assignRef('defaultSelect', $defaultSelect);
                $this->assignRef('parcelTargetMachinesId', $parcelTargetMachinesId);
                $this->assignRef('parcelTargetMachinesDetail', $parcelTargetMachinesDetail);

                $easypack24Data = array(
                    'id' => $result_db->id,
                    'parcel_target_machine_id' => $result_db->parcel_target_machine_id,
                    'parcel_description' => @$parcelDetailDb->description,
                    'parcel_size' => @$parcelDetailDb->size,
                    'parcel_status' => $result_db->parcel_status,
                    'parcel_id' => $result_db->parcel_id
                );

                $this->assignRef('easypack24Data', $easypack24Data);
                $this->assignRef('defaultParcelSize', @$parcelDetailDb->size);
                $disabled = 'disabled';
                $this->assignRef('disabledMachines', $disabled);

                if($result_db->parcel_status != 'Created' || $result_db->parcel_status == ''){
                    $this->assignRef('disabledParcelSize', $disabled);
                }
            } else {
                vmError('COM_VIRTUEMART_EASYPACK24_VIEW_ERR_1');
            }
            JToolBarHelper::save('update', JText::_('COM_VIRTUEMART_EASYPACK24_VIEW_BUTTON_4'));

        }else{
            $this->setLayout('parcels');

            $model = VmModel::getModel();
            $this->addStandardDefaultViewLists($model,'created_on');
            $this->lists['state_list'] = $this->renderParcelstatesList();
            $parcelslist = $model->getParcelsList();

            /* Toolbar */
            JToolBarHelper::save('massStickers', JText::_('COM_VIRTUEMART_EASYPACK24_VIEW_BUTTON_1'));
            JToolBarHelper::save('massRefreshStatus', JText::_('COM_VIRTUEMART_EASYPACK24_VIEW_BUTTON_2'));
            JToolBarHelper::save('massCancel', JText::_('COM_VIRTUEMART_EASYPACK24_VIEW_BUTTON_3'));

            /* Assign the data */
            $this->assignRef('parcelslist', $parcelslist);

            $pagination = $model->getPagination();
            $this->assignRef('pagination', $pagination);
        }

		parent::display();
	}

    public function renderParcelstatesList() {
        $parcelstates = JRequest::getWord('parcel_status','');
        return VmHTML::select( 'parcel_status', easypack24_helper::getParcelStatus(),  $parcelstates,'class="inputbox" onchange="this.form.submit();"');
    }

}