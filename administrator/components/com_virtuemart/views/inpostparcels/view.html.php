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
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'inpostparcels' . DS . 'inpostparcels' . DS . 'helpers' . DS . 'define.php');
} else {
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'inpostparcels' . DS . 'helpers' . DS . 'define.php');
}

require_once (JPATH_VMINPOSTPARCELSPLUGIN . DS . 'inpostparcels' . DS . 'helpers' . DS . 'inpostparcels_helper.php');


/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewInpostparcels extends VmView {

	function display($tpl = null) {

        $this->loadHelper('html');

        if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');

        $curTask = JRequest::getWord('task');

        if($curTask == 'edit'){
            $this->setLayout('parcel');
            $id = JRequest::getVar('id');

            $config = inpostparcels_helper::getParameters();

            $db = JFactory::getDBO();
            $q = "SELECT * FROM #__virtuemart_shipment_plg_inpostparcels WHERE id='".(int)$id."'";
            $db->setQuery($q);
            $parcel = $db->loadObject();
            $this->assignRef('parcel', $parcel);

            if ($parcel->id || $id == 0) {
                $parcelTargetMachineDetailDb = json_decode($parcel->parcel_target_machine_detail);
                $parcelDetailDb = json_decode($parcel->parcel_detail);

                // set disabled
                $disabledCodAmount = '';
                $disabledDescription = '';
                $disabledInsuranceAmount = '';
                $disabledReceiverPhone = '';
                $disabledReceiverEmail = '';
                $disabledParcelSize = '';
                $disabledParcelStatus = '';
                $disabledSourceMachine = '';
                $disabledTmpId = '';
                $disabledTargetMachine = '';

                if($parcel->parcel_status != 'Created' && $parcel->parcel_status != '0'){
                    $disabledCodAmount = 'disabled';
                    $disabledDescription = 'disabled';
                    $disabledInsuranceAmount = 'disabled';
                    $disabledReceiverPhone = 'disabled';
                    $disabledReceiverEmail = 'disabled';
                    $disabledParcelSize = 'disabled';
                    $disabledParcelStatus = 'disabled';
                    $disabledSourceMachine = 'disabled';
                    $disabledTmpId = 'disabled';
                    $disabledTargetMachine = 'disabled';
                }
                if($parcel->parcel_status == 'Created'){
                    $disabledCodAmount = 'disabled';
                    //$disabledDescription = 'disabled';
                    $disabledInsuranceAmount = 'disabled';
                    $disabledReceiverPhone = 'disabled';
                    $disabledReceiverEmail = 'disabled';
                    //$disabledParcelSize = 'disabled';
                    //$disabledParcelStatus = 'disabled';
                    $disabledSourceMachine = 'disabled';
                    $disabledTmpId = 'disabled';
                    $disabledTargetMachine = 'disabled';
                }

                $this->assignRef('disabledCodAmount', $disabledCodAmount);
                $this->assignRef('disabledDescription', $disabledDescription);
                $this->assignRef('disabledInsuranceAmount', $disabledInsuranceAmount);
                $this->assignRef('disabledReceiverPhone', $disabledReceiverPhone);
                $this->assignRef('disabledReceiverEmail', $disabledReceiverEmail);
                $this->assignRef('disabledParcelSize', $disabledParcelSize);
                $this->assignRef('disabledParcelStatus', $disabledParcelStatus);
                $this->assignRef('disabledSourceMachine', $disabledSourceMachine);
                $this->assignRef('disabledTmpId', $disabledTmpId);
                $this->assignRef('disabledTargetMachine', $disabledTargetMachine);

                $allMachines = inpostparcels_helper::connectInpostparcels(
                    array(
                        'url' => $config['API_URL'].'machines',
                        'token' => $config['API_KEY'],
                        'methodType' => 'GET',
                        'params' => array(
                        )
                    )
                );

                // target machines
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
                }
                $this->assignRef('parcelTargetAllMachinesId', $parcelTargetAllMachinesId);
                $this->assignRef('parcelTargetAllMachinesDetail', $parcelTargetAllMachinesDetail);

                $parcelTargetMachinesId = array();
                $parcelTargetMachinesDetail = array();
                $defaultTargetMachine = JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_SELECT_MACHINE');
                //print_r($parcelTargetMachineDetailDb);
                if(is_array(@$machines) && !empty($machines)){
                    foreach($machines as $key => $machine){
                        $parcelTargetMachinesId[$machine->id] = $machine->id.', '.@$machine->address->city.', '.@$machine->address->street;
                        $parcelTargetMachinesDetail[$machine->id] = $parcelTargetAllMachinesDetail[$machine->id];
                    }
                }else{
                    $defaultTargetMachine = JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_DEFAULT_SELECT');
                }
                $this->assignRef('parcelTargetMachinesId', $parcelTargetMachinesId);
                $this->assignRef('parcelTargetMachinesDetail', $parcelTargetMachinesDetail);
                $this->assignRef('defaultTargetMachine', $defaultTargetMachine);

                //$parcel['api_source'] = 'PL';
                $parcelInsurancesAmount = array();
                $defaultInsuranceAmount = JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_SELECT_INSURANCE');
                switch($parcel->api_source){
                    case 'PL':
                        $api = inpostparcels_helper::connectInpostparcels(
                            array(
                                'url' => $config['API_URL'].'customer/pricelist',
                                'token' => $config['API_KEY'],
                                'methodType' => 'GET',
                                'params' => array(
                                )
                            )
                        );

                        if(isset($api['result']) && !empty($api['result'])){
                            $parcelInsurancesAmount = array(
                                'insurance_price1' => $api['result']->insurance_price1,
                                'insurance_price2' => $api['result']->insurance_price2,
                                'insurance_price3' => $api['result']->insurance_price3
                            );
                        }

                        $parcelSourceAllMachinesId = array();
                        $parcelSourceAllMachinesDetail = array();
                        $machines = array();
                        $shopCities = explode(',',$config['SHOP_CITIES']);
                        if(is_array(@$allMachines['result']) && !empty($allMachines['result'])){
                            foreach($allMachines['result'] as $key => $machine){
                                $parcelSourceAllMachinesId[$machine->id] = $machine->id.', '.@$machine->address->city.', '.@$machine->address->street;
                                $parcelSourceAllMachinesDetail[$machine->id] = array(
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
                                if(in_array($machine->address->city, $shopCities)){
                                    $machines[$key] = $machine;
                                }
                            }
                        }
                        $this->assignRef('parcelInsurancesAmount', $parcelInsurancesAmount);
                        $_SESSION['inpostparcels']['parcelInsurancesAmount'] = $parcelInsurancesAmount;
                        $this->assignRef('defaultInsuranceAmount', $defaultInsuranceAmount);
                        $this->assignRef('parcelSourceAllMachinesId', $parcelSourceAllMachinesId);
                        $this->assignRef('parcelSourceAllMachinesDetail', $parcelSourceAllMachinesDetail);
                        $this->assignRef('shopCities', $shopCities);

                        $parcelSourceMachinesId = array();
                        $parcelSourceMachinesDetail = array();
                        $defaultSourceMachine = JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_SELECT_MACHINE');
                        if(is_array(@$machines) && !empty($machines)){
                            foreach($machines as $key => $machine){
                                $parcelSourceMachinesId[$machine->id] = $machine->id.', '.@$machine->address->city.', '.@$machine->address->street;
                                $parcelSourceMachinesDetail[$machine->id] = $parcelSourceAllMachinesDetail[$machine->id];
                            }
                        }else{
                            $defaultTargetMachine = JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_DEFAULT_SELECT');
                        }

                        $this->assignRef('parcelSourceMachinesId', $parcelSourceMachinesId);
                        $this->assignRef('parcelSourceMachinesDetail', $parcelSourceMachinesDetail);
                        $this->assignRef('defaultSourceMachine', $defaultTargetMachine);

                        break;
                }

                $inpostparcelsData = array(
                    'id' => $parcel->id,
                    'parcel_id' => $parcel->parcel_id,

                    'parcel_cod_amount' => @$parcelDetailDb->cod_amount,
                    'parcel_description' => @$parcelDetailDb->description,
                    'parcel_insurance_amount' => @$parcelDetailDb->insurance_amount,
                    'parcel_receiver_phone' => @$parcelDetailDb->receiver->phone,
                    'parcel_receiver_email' => @$parcelDetailDb->receiver->email,
                    'parcel_size' => @$parcelDetailDb->size,
                    'parcel_status' => $parcel->parcel_status,
                    'parcel_source_machine_id' => @$parcelDetailDb->source_machine,
                    'parcel_tmp_id' => @$parcelDetailDb->tmp_id,
                    'parcel_target_machine_id' => @$parcelDetailDb->target_machine,
                );
                $this->assignRef('inpostparcelsData', $inpostparcelsData);

                $defaultParcelSize = @$parcelDetailDb->size;
                $this->assignRef('defaultParcelSize', $defaultParcelSize);
            } else {
                vmError('COM_VIRTUEMART_INPOSTPARCELS_VIEW_ERR_1');
            }
            JToolBarHelper::save('update', JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_BUTTON_4'));

        }else{
            $this->setLayout('parcels');

            $model = VmModel::getModel();
            $this->addStandardDefaultViewLists($model,'created_on');
            $this->lists['state_list'] = $this->renderParcelstatesList();
            $parcelslist = $model->getParcelsList();

            /* Toolbar */
            JToolBarHelper::save('massStickers', JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_BUTTON_1'));
            JToolBarHelper::save('massRefreshStatus', JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_BUTTON_2'));
            JToolBarHelper::save('massCancel', JText::_('COM_VIRTUEMART_INPOSTPARCELS_VIEW_BUTTON_3'));

            /* Assign the data */
            $this->assignRef('parcelslist', $parcelslist);

            $pagination = $model->getPagination();
            $this->assignRef('pagination', $pagination);
        }

		parent::display();
	}

    public function renderParcelstatesList() {
        $parcelstates = JRequest::getWord('parcel_status','');
        return VmHTML::select( 'parcel_status', inpostparcels_helper::getParcelStatus(),  $parcelstates,'class="inputbox" onchange="this.form.submit();"');
    }

}