<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

class VirtueMartModelInpostparcels extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('inpostparcels');
        //$this->addvalidOrderingFieldName(array('parcel_id' ) );
    }

    public function getParcelsList($uid = 0, $noLimit = false)
    {
        $this->_noLimit = $noLimit;
        $select = '*';
        $from = $this->getParcelsListQuery();

        if ($search = JRequest::getString('search', false)){

            $search = '"%' . $this->_db->getEscaped( $search, true ) . '%"' ;

            $searchFields = array();
            $searchFields[] = 'parcel_target_machine_id';
            $searchFields[] = 'parcel_detail';
            $searchFields[] = 'parcel_target_machine_detail';
            $where[] = implode (' LIKE '.$search.' OR ', $searchFields) . ' LIKE '.$search.' ';
        }

        if ($parcel_status = JRequest::getString('parcel_status', false)){
            $where[] = ' parcel_status = "'.$parcel_status.'" ';
        }

        if (count ($where) > 0) {
            $whereString = ' WHERE (' . implode (' AND ', $where) . ') ';
        }
        else {
            $whereString = '';
        }

        if ( JRequest::getCmd('view') == 'inpostparcels') {
            $ordering = $this->_getOrdering();
        } else {
            $ordering = ' order by created_by DESC';
        }

        $this->_data = $this->exeSortSearchListQuery(0,$select,$from,$whereString,'',$ordering);

        return $this->_data ;
    }

    private function getParcelsListQuery()
    {
        return ' FROM #__virtuemart_shipment_plg_inpostparcels';
    }


}

// No closing tag
