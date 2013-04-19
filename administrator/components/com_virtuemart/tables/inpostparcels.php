<?php
/**
*
* Orders table
*
* @package	VirtueMart
* @subpackage Orders
* @author RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: orders.php 6210 2012-07-04 00:15:41Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Orders table class
 * The class is is used to manage the orders in the shop.
 *
 * @package	VirtueMart
 * @author RolandD
 * @author Max Milbers
 */
class TableInpostparcels extends VmTable {

	/** @var int Primary key */
	var $id = 0;
	/** @var int Order ID */
	var $virtuemart_order_id = 0;
    /** @var varchar Parcel ID */
    var $parcel_id = NULL;
    /** @var varchar Parcel status */
    var $parcel_status = NULL;
    /** @var text Parcel detail */
    var $parcel_detail = NULL;
    /** @var varchar Parcel target machine */
    var $parcel_target_machine_id = NULL;
    /** @var text Parcel target machine detail*/
    var $parcel_target_machine_detail = NULL;
    /** @var timestamp Sticker creation date*/
    var $sticker_creation_date = NULL;
    /** @var int Order number */
    var $order_number = NULL;
    /** @var timestamp created on*/
    var $created_on = NULL;


	/**
	 *
	 * @author Max Milbers
	 * @param $db Class constructor; connect to the database
	 *
	 */
	function __construct($db) {
		parent::__construct('#__virtuemart_shipment_plg_inpostparcels', 'id', $db);

		$this->setUniqueName('order_number');
		$this->setLoggable();

		//$this->setTableShortCut('o');
	}

	function check(){
		return parent::check();
	}


}

