<?php


class Safe_Media_Delete_i18n {


	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'safe-media-delete',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
