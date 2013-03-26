<?php
defined('JPATH_BASE') or die();

if (JVM_VERSION === 2) {
    if (!defined('JPATH_VMEASYPACK24PLUGIN'))
	define('JPATH_VMEASYPACK24PLUGIN', JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'easypack24');

} else {
    if (!defined('JPATH_VMEASYPACK24PLUGIN'))
	define('JPATH_VMEASYPACK24PLUGIN', JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment');
}
