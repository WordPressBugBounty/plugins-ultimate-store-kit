<?php

namespace UltimateStoreKit\Modules\PageOrder;

use UltimateStoreKit\Base\Ultimate_Store_Kit_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Ultimate_Store_Kit_Module_Base {

	public function get_name() {
		return 'page-order';
	}

	public function get_widgets() {

		$widgets = [
			'Page_Order',
		];

		return $widgets;
	}
}