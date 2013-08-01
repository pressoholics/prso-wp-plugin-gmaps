<?php
/**
 * General App Functions
 *
 * Contents:
 * 	** PRSO PLUGIN FRAMEWORK METHODS **
 *		__construct()		- Magic method construct
 *		admin_init()		- Helps to consolidate all plugin wide calls to Wordpress action hooks that must be added during 'admin_init'
 *		save_post()			- Call any methods that must be called during saving a post here
 *		enqueue_scripts()	- Call all plugin wp_enqueue_script or wp_enqueue_style here
 *		add_actions()		- Add any calls to Wordpress add_action() here
 *		add_filters()		- Add any calls to Wordpress add_filter() here
 *
 *	** METHODS SPECIFIC TO THIS PLUGIN **
 *		save_fields()				- Carries out tasks to be run during post save
 *
 */
class PrsoGmapsFunctions extends PrsoGmapsAppController {
	
	//*** PRSO PLUGIN FRAMEWORK METHODS - Edit at your own risk (go nuts if you just want to add to them) ***//
	
	function __construct() {
		
		//Ensure vars set in config are available
 		parent::__construct();
 		
 		//Hook into WP admin_init
 		$this->admin_init();
 		
 		//Hook into WP save_post
 		$this->save_post();
 		
	}
	
	/**
	* admin_init
	* 
	* Called in __construct() to fire any methods for
	* WP Action Hook 'admin_init'
	* 
	* @access 	private
	* @author	Ben Moody
	*/
	private function admin_init() {
		
		//*** PRSO PLUGIN CORE ACTIONS ***//
		
		//Enqueue any custom scripts or styles
		add_action( 'admin_init', array( $this, 'enqueue_scripts' ) );
		
		//Add any custom actions
		add_action( 'admin_init', array( $this, 'add_actions' ) );
		
		//Add any custom filter
		add_action( 'admin_init', array( $this, 'add_filters' ) );
		
		
		//*** ADD CUSTOM ACTIONS HERE ***//

		
	}
	
	/**
	* save_post
	* 
	* Called in __construct() to fire any methods for
	* WP Action Hook 'save_post'
	* 
	* @access 	private
	* @author	Ben Moody
	*/
	private function save_post() {
		
		//Call wp function to setup saving meta box
		add_action( 'save_post', array( $this, 'save_fields' ) );
		
	}
	
	/**
	* enqueue_scripts
	* 
	* Called by $this->admin_init() to queue any custom scripts or stylesheets
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function enqueue_scripts() {
		
	}
	
	/**
	* add_actions
	* 
	* Called in $this->admin_init() to add any custom WP Action Hooks
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_actions() {
		
	}
	
	/**
	* add_filters
	* 
	* Called in $this->admin_init() to add any custom WP Filter Hooks
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_filters() {
		
	}
	
	
	
	
	
	
	//*** CUSTOM METHODS SPECIFIC TO THIS PLUGIN ***//
	
	
	/* When the post is saved, saves our custom data */
	public function save_fields( $post_id ) {
		//Init vars
		$custom_field_key = NULL;
		$clean_meta_value = NULL; //Cache sanitized data strings
		
		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return;

	  	if( isset($_POST['post_type']) ) {
			// Check permissions
			if ( 'page' == $_POST['post_type'] ) {
				if ( !current_user_can( 'edit_page', $post_id ) )
				    return;
			} else {
				if ( !current_user_can( 'edit_post', $post_id ) )
				    return;
			}
			
			// OK, we're authenticated: lets get all custom posts ad check for data
			
			
		}
	}

}