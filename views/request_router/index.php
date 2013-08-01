<?php
/**
* PrsoGmaps Plugin View.
*
*/

class PrsoGmapsIndexView extends PrsoGmapsAdminView {
	
	public function index() {
		//Start page output
		$this->start_option_page();
	}
	
	/**
	* start_option_page
	* 
	* Used by wp function add_menu_page to create and parse the admin options page
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	private function start_option_page() {
		
		
	}
		
	
	
	/**
	*
	* END - OPTIONS PAGE SETUP
	*
	*/
	

	function __construct() {
		//Get page options from database and store for later
		$this->get_options( $this->box_options_slug );
	}
	
}