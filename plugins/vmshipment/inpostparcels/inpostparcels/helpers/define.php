<?php
defined('JPATH_BASE') or die();

if (JVM_VERSION === 2) {
    if (!defined('JPATH_VMINPOSTPARCELSPLUGIN'))
	define('JPATH_VMINPOSTPARCELSPLUGIN', JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'inpostparcels');

} else {
    if (!defined('JPATH_VMINPOSTPARCELSPLUGIN'))
	define('JPATH_VMINPOSTPARCELSPLUGIN', JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment');
}
