<?php
/**
 *
 * Orders controller
 *
 * @package	VirtueMart
 * @subpackage
 * @author RolandD
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: orders.php 6188 2012-06-29 09:38:30Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');

if (JVM_VERSION === 2) {
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'easypack24' . DS . 'easypack24' . DS . 'helpers' . DS . 'define.php');
} else {
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'easypack24' . DS . 'helpers' . DS . 'define.php');
}

if (!class_exists ('vmPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmplugin.php');
}

require_once (JPATH_VMEASYPACK24PLUGIN . DS . 'easypack24' . DS . 'helpers' . DS . 'easypack24_helper.php');
require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'parameterparser.php');

/**
 * Orders Controller
 *
 * @package    VirtueMart
 * @author
 */
class VirtuemartControllerEasypack extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 * @author
	 */

    protected $config;

	function __construct() {
		parent::__construct();

        //$parameters = new vmParameters('TableShipmentmethods', 'easypack24', 'plugin', 'vmshipment');
        //print_r($parameters);
        //easypack24_helper::setLang();
        $this->config = easypack24_helper::getParameters();
    }

	/**
	 * Shows the order details
	 */
	public function edit($layout='parcel'){

        $id = JRequest::getVar('id');
        $mainframe = Jfactory::getApplication();

        if (empty($id)) {
            vmError('Id is empty');
            $mainframe->redirect('index.php?option=com_virtuemart&view=easypack');
        }

		parent::edit($layout);
	}

    public function massStickers(){
        $mainframe = Jfactory::getApplication();
        $model = VmModel::getModel();
        $parcelsIds = JRequest::getVar('cid',  0, '', 'array');

        $countSticker = 0;
        $countNonSticker = 0;
        $pdf = null;
        $parcelsCode = array();
        $parcelsToPay = array();

        foreach ($parcelsIds as $key => $id) {
            $db = JFactory::getDBO();
            $q = "SELECT * FROM #__virtuemart_shipment_plg_easypack24 WHERE id='".(int)$id."'";
            $db->setQuery($q);
            $result_db = $db->loadObject();

            if($result_db->parcel_id != ''){
                $parcelsCode[$id] = $result_db->parcel_id;
                if($result_db->sticker_creation_date == '0000-00-00 00:00:00'){
                    $parcelsToPay[$id] = $result_db->parcel_id;
                }
            }else{
                continue;
            }
        }

        if(empty($parcelsCode)){
            vmError('Parcel ID is empty');
        }else{
            if(!empty($parcelsToPay)){
                $parcelApiPay = easypack24_helper::connectEasypack24(array(
                    'url' => $this->config['API_URL'].'parcels/'.implode(';', $parcelsToPay).'/pay',
                    'token' => $this->config['API_KEY'],
                    'methodType' => 'POST',
                    'params' => array(
                    )
                ));

                if(@$parcelApiPay['info']['http_code'] != '204'){
                    $countNonSticker = count($parcelsIds);
                    if(!empty($parcelApiPay['result'])){
                        foreach(@$parcelApiPay['result'] as $key => $error){
                            vmError('Parcel '.$key.' '.$error);
                        }
                    }
                }
            }

            $parcelApi = easypack24_helper::connectEasypack24(array(
                'url' => $this->config['API_URL'].'stickers/'.implode(';', $parcelsCode),
                'token' => $this->config['API_KEY'],
                'methodType' => 'GET',
                'params' => array(
                    'format' => 'Pdf',
                    'type' => 'normal'
                )
            ));
        }

        if(@$parcelApi['info']['http_code'] != '200'){
            $countNonSticker = count($parcelsIds);
            if(!empty($parcelApi['result'])){
                foreach(@$parcelApi['result'] as $key => $error){
                    vmError('Parcel '.$key.' '.$error);
                }
            }
        }else{
            foreach ($parcelsIds as $parcelId) {
                $fields = array(
                    'parcel_status' => 'Prepared',
                    'sticker_creation_date' => date('Y-m-d H:i:s')
                );
                if(isset($parcelsToPay[$parcelId])){
                    $db = JFactory::getDBO();
                    $q = "UPDATE #__virtuemart_shipment_plg_easypack24 SET
                        parcel_status='".$fields['parcel_status']."',
                        sticker_creation_date ='".$fields['sticker_creation_date']."'
                        WHERE id ='".(int)$parcelId."'";
                    $db->setQuery($q);
                    $db->query();
                }
                $countSticker++;
            }
            $pdf = base64_decode(@$parcelApi['result']);
        }

        if ($countNonSticker) {
            if ($countNonSticker) {
                vmError($countNonSticker.' '.JText::_ ('COM_VIRTUEMART_EASYPACK24_MSG_STICKER_1'));
            } else {
                vmError('COM_VIRTUEMART_EASYPACK24_MSG_STICKER_2');
            }
        }
        if ($countSticker) {
            vmInfo($countSticker.' '.JText::_ ('COM_VIRTUEMART_EASYPACK24_MSG_STICKER_3'));
        }

        if(!is_null($pdf)){
            header('Content-type', 'application/pdf');
            header('Content-Disposition: attachment; filename=stickers_'.date('Y-m-d_H-i-s').'.pdf');
            print_r($pdf);
        }else{
            $mainframe->redirect('index.php?option=com_virtuemart&view=easypack');
        }
    }

    public function massRefreshStatus(){
        $mainframe = Jfactory::getApplication();
        $model = VmModel::getModel();
        $parcelsIds = JRequest::getVar('cid',  0, '', 'array');

        $countRefreshStatus = 0;
        $countNonRefreshStatus = 0;

        $parcelsCode = array();
        foreach ($parcelsIds as $key => $id) {
            $db = JFactory::getDBO();
            $q = "SELECT * FROM #__virtuemart_shipment_plg_easypack24 WHERE id='".(int)$id."'";
            $db->setQuery($q);
            $result_db = $db->loadObject();

            if($result_db->parcel_id != ''){
                $parcelsCode[$id] = $result_db->parcel_id;
            }else{
                continue;
            }
        }

        if(empty($parcelsCode)){
            vmError('Parcel ID is empty');
        }else{
            $parcelApi = easypack24_helper::connectEasypack24(array(
                'url' => $this->config['API_URL'].'parcels/'.implode(';', $parcelsCode),
                'token' => $this->config['API_KEY'],
                'methodType' => 'GET',
                'params' => array()
            ));
        }

        if(@$parcelApi['info']['http_code'] != '200'){
            $countNonRefreshStatus = count($parcelsIds);
            if(!empty($parcelApi['result'])){
                foreach(@$parcelApi['result'] as $key => $error){
                    vmError('Parcel '.$key.' '.$error);
                }
            }
        }else{
            if(!is_array(@$parcelApi['result'])){
                @$parcelApi['result'] = array(@$parcelApi['result']);
            }
            foreach (@$parcelApi['result'] as $parcel) {
                $fields = array(
                    'parcel_status' => @$parcel->status
                );

                $db = JFactory::getDBO();
                $q = "UPDATE #__virtuemart_shipment_plg_easypack24 SET
                        parcel_status='".$fields['parcel_status']."'
                        WHERE parcel_id ='".@$parcel->id."'";
                $db->setQuery($q);
                $db->query();

                $countRefreshStatus++;
            }
        }

        if ($countNonRefreshStatus) {
            if ($countNonRefreshStatus) {
                vmError($countNonRefreshStatus.' '.JText::_ ('COM_VIRTUEMART_EASYPACK24_MSG_PARCEL_1'));
            } else {
                vmError($countNonRefreshStatus.' '.JText::_ ('COM_VIRTUEMART_EASYPACK24_MSG_PARCEL_2'));
            }
        }
        if ($countRefreshStatus) {
            vmInfo($countRefreshStatus.' '.JText::_ ('COM_VIRTUEMART_EASYPACK24_MSG_PARCEL_3'));
        }

        $mainframe->redirect('index.php?option=com_virtuemart&view=easypack');
    }

    public function massCancel(){
        $mainframe = Jfactory::getApplication();
        $model = VmModel::getModel();
        $parcelsIds = JRequest::getVar('cid',  0, '', 'array');

        $countCancel = 0;
        $countNonCancel = 0;

        $parcelsCode = array();
        foreach ($parcelsIds as $key => $id) {
            $db = JFactory::getDBO();
            $q = "SELECT * FROM #__virtuemart_shipment_plg_easypack24 WHERE id='".(int)$id."'";
            $db->setQuery($q);
            $result_db = $db->loadObject();

            if($result_db->parcel_id != ''){
                $parcelsCode[$id] = $result_db->parcel_id;
            }else{
                continue;
            }
        }

        if(empty($parcelsCode)){
            vmError('Parcel ID is empty');
        }else{
            foreach($parcelsCode as $id => $parcelId){
                $parcelApi = easypack24_helper::connectEasypack24(array(
                    'url' => $this->config['API_URL'].'parcels',
                    'token' => $this->config['API_KEY'],
                    'methodType' => 'PUT',
                    'params' => array(
                        'id' => $parcelId,
                        'status' => 'cancelled'
                    )
                ));

                if(@$parcelApi['info']['http_code'] != '204'){
                    $countNonCancel = count($parcelsIds);
                    if(!empty($parcelApi['result'])){
                        foreach(@$parcelApi['result'] as $key => $error){
                            if(is_array($error)){
                                foreach($error as $subKey => $subError){
                                    vmError('Parcel '.$parcelId.' '.$subError);
                                }
                            }else{
                                vmError('Parcel '.$parcelId.' '.$error);
                            }
                        }
                    }
                }else{
                    foreach (@$parcelApi['result'] as $parcel) {
                        $fields = array(
                            'parcel_status' => @$parcel->status
                        );
                        $db = JFactory::getDBO();

                        $q = "UPDATE #__virtuemart_shipment_plg_easypack24 SET
                            parcel_status='".$fields['parcel_status']."'
                            WHERE parcel_id ='".@$parcel->id."'";
                        $db->setQuery($q);
                        $db->query();

                        $countCancel++;
                    }
                }
            }
        }

        if ($countNonCancel) {
            if ($countNonCancel) {
                vmError($countNonCancel.' '.JText::_ ('COM_VIRTUEMART_EASYPACK24_MSG_PARCEL_4'));
            } else {
                vmError('COM_VIRTUEMART_EASYPACK24_MSG_PARCEL_5');
            }
        }
        if ($countCancel) {
            vmInfo($countNonCancel.' '.JText::_ ('COM_VIRTUEMART_EASYPACK24_MSG_PARCEL_6'));
        }

        $mainframe->redirect('index.php?option=com_virtuemart&view=easypack');
    }

    public function update(){
        $mainframe = Jfactory::getApplication();
        //$model = VmModel::getModel();
        $id = JRequest::getVar('id');

        try {
            $postData = JRequest::get();

            $db = JFactory::getDBO();
            $q = "SELECT * FROM #__virtuemart_shipment_plg_easypack24 WHERE id='".(int)$id."'";
            $db->setQuery($q);
            $result_db = $db->loadObject();

            $parcelTargetMachineDetailDb = json_decode($result_db->parcel_target_machine_detail);
            $parcelDetailDb = json_decode($result_db->parcel_detail);

            // update Inpost parcel
            $params = array(
                'url' => $this->config['API_URL'].'parcels',
                'token' => $this->config['API_KEY'],
                'methodType' => 'PUT',
                'params' => array(
                    'description' => !isset($postData['parcel_description']) || $postData['parcel_description'] == @$parcelDetailDb->description?null:$postData['parcel_description'],
                    'id' => $postData['parcel_id'],
                    'size' => !isset($postData['parcel_size']) || $postData['parcel_size'] == @$parcelDetailDb->size?null:$postData['parcel_size'],
                    'status' => !isset($postData['parcel_status']) || $postData['parcel_status'] == $result_db->parcel_status?null:$postData['parcel_status'],
                    //'target_machine' => !isset($postData['parcel_target_machine_id']) || $postData['parcel_target_machine_id'] == $result_db->parcel_target_machine_id?null:$postData['parcel_target_machine_id']
                )
            );
            $parcelApi = easypack24_helper::connectEasypack24($params);

            if(@$parcelApi['info']['http_code'] != '204'){
                if(!empty($parcelApi['result'])){
                    foreach(@$parcelApi['result'] as $key => $error){
                        if(is_array($error)){
                            foreach($error as $subKey => $subError){
                                vmError('Parcel '.$key.' '.$postData['parcel_id'].' '.$subError);
                            }
                        }else{
                            vmError('Parcel '.$key.' '.$error);
                        }
                    }
                }
                return;
            }else{
                $fields = array(
                    'parcel_status' => isset($postData['parcel_status'])?$postData['parcel_status']:$result_db->parcel_status,
                    'parcel_detail' => json_encode(array(
                        'description' => $postData['parcel_description'],
                        'receiver' => array(
                            'email' => $parcelDetailDb->receiver->email,
                            'phone' => $parcelDetailDb->receiver->phone
                        ),
                        'size' => isset($postData['parcel_size'])?$postData['parcel_size']:@$parcelDetailDb->size,
                        'tmp_id' => $parcelDetailDb->tmp_id,
                    ))
                );

                $db = JFactory::getDBO();

                $q = "UPDATE #__virtuemart_shipment_plg_easypack24 SET
                    parcel_status='".$fields['parcel_status']."',
                    parcel_detail='".$fields['parcel_detail']."'
                    WHERE id ='".(int)$id."'";
                $db->setQuery($q);
                $db->query();

            }
            vmInfo('COM_VIRTUEMART_EASYPACK24_MSG_PARCEL_MODIFIED');
        } catch (Exception $e) {
            vmError($e->getMessage());
        }
        $mainframe->redirect('index.php?option=com_virtuemart&view=easypack');
    }


}
// pure php no closing tag

