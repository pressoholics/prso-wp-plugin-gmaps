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
	
	private $google_maps_api_url 	= 'http://maps.google.com/maps/api/js';
	
	private $view_template_path 	= '';
	
	private $map_data_cache			= array();
	
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
		add_action( 'admin_init', array( $this, 'admin_enqueue_scripts' ) );
		
		//Add any custom actions
		add_action( 'admin_init', array( $this, 'admin_add_actions' ) );
		
		//Add any custom filter
		add_action( 'admin_init', array( $this, 'admin_add_filters' ) );
		
		
		//*** ADD CUSTOM ACTIONS HERE ***//
		
		//Add front end actions
		add_action( 'init', array( $this, 'add_actions' ) );
		
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
		//add_action( 'save_post', array( $this, 'save_fields' ) );
		
	}
	
	/**
	* enqueue_scripts
	* 
	* Called by $this->admin_init() to queue any custom scripts or stylesheets
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function admin_enqueue_scripts() {
		
		
	}
	
	/**
	* add_actions
	* 
	* Called in $this->admin_init() to add any custom WP Action Hooks
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function admin_add_actions() {
		
	}
	
	/**
	* add_filters
	* 
	* Called in $this->admin_init() to add any custom WP Filter Hooks
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function admin_add_filters() {
		
	}
	
	
	
	
	
	
	//*** CUSTOM METHODS SPECIFIC TO THIS PLUGIN ***//
	
	/**
	* add_actions
	* 
	* Add any custom wp actions for the FRONT END of the plugin here
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_actions() {
		
		//Register custom action to init map plugin
		add_action( 'prso_init_gmap', array( $this, 'init_gmaps' ), 10, NULL );
		
	}
	
	/**
	* init_gmaps
	* 
	* Called by WP Action: 'prso_init_gmap'
	*
	* Calls plugin methods required to init the google map
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function init_gmaps() {
		
		//Cache path to view templates
		$this->view_template_path = $this->plugin_root . '/templates';
		
		//Format maps api url with any options
		$this->format_api_url_options();
		
		//Enqueue and scripts needed for the plugin
		$this->enqueue_scripts();
		
		//Cache Map data
		$this->get_map_data();
		
		//Localize map params
		$this->localize_map_params();
		
		//Render view template
		$this->render_view();
		
	}
	
	/**
	* format_api_url_options
	* 
	* Helper to add any url based options to the gmaps api url
	* Gets options from plugin wp options page
	* 
	* @param	string	$this->google_maps_api_url - class global, holds base url to gmaps api
	* @var		array	$plugin_options	- plugin options from plugin wp admin
	* @access 	private
	* @author	Ben Moody
	*/
	private function format_api_url_options() {
		
		//Init vars
		$plugin_options = array();
		
		//TESTING ONLY PROBABLY NEED TO GRAB FROM OPTIONS
		$plugin_options['sensor'] = "false";
		
		//Append any options to api url
		$this->google_maps_api_url.= "?sensor=" . $plugin_options['sensor'];
		
	}
	
	/**
	* init_gmaps
	* 
	* Called by: $this->init_gmaps()
	*
	* Enqueues any scripts for plugin front end
	* 
	* @access 	private
	* @author	Ben Moody
	*/
	private function enqueue_scripts() {
		
		//Enqueue jQuery
		wp_enqueue_script( 'jquery' );
		
		//Enqueue Google Maps API
		wp_register_script( 'google_maps_api',
			$this->google_maps_api_url,
			array(),
			'3.0',
			TRUE
		);
		wp_enqueue_script( 'google_maps_api' );
		
		//Enqueue plugin maps script
		wp_register_script( 'prso_google_maps',
			plugins_url( 'js/prso_gmaps.js', PrsoGmapsConfig::$plugin_file_path ),
			array( 'google_maps_api', 'jquery' ),
			'1.0',
			TRUE
		);
		wp_enqueue_script( 'prso_google_maps' );
		
		//Enqueue plugin styles
		wp_register_style( 'prso_google_maps',
			plugins_url( 'styles/app.css', PrsoGmapsConfig::$plugin_file_path ),
			array(),
			'1.0'
		);
		wp_enqueue_style( 'prso_google_maps' );
		
	}
	
	/**
	* get_map_data
	* 
	* Gets any data from external sources to build map
	*
	* Note required data: lat, lng, content. The Content array is passed
	* to the Info Window view template global $prso_google_maps_infowindow.
	* This can then be used in the view or in another function to build the view html
	* 
	* @var		global	$prso_google_maps_main
	* @var		array	$map_data
	* @access 	private
	* @author	Ben Moody
	*/
	private function get_map_data() {
		
		//Init vars
		global $prso_google_maps_main;
		$map_data = array();
		
		//Setup dummy test data
		$map_data = array(
			array(
				'lat'		=>	'40.756',
				'lng'		=>	'-73.986',
				'title'		=> 'example title',
				'content'	=>	array(
									'title'	=> 'example_1'
								)
			),
			array(
				'lat'		=>	'37.775',
				'lng'		=>	'-122.419',
				'title'		=> 'example title',
				'content'	=>	array(
									'title'	=> 'example_2'
								)
			),
			array(
				'lat'		=>	'47.620',
				'lng'		=>	'-122.347',
				'title'		=> 'example title',
				'content'	=>	array(
									'title'	=> 'example_3'
								)
			),
			array(
				'lat'		=>	'-22.933',
				'lng'		=>	'-43.184',
				'title'		=> 'example title',
				'content'	=>	array(
									'title'	=> 'example_4'
								)
			)
		);
		
		
		//Cache data in global var
		$this->map_data_cache = $map_data;
		
		//Cache data array for use within main viwe template
		$prso_google_maps_main = $map_data;
		
	}
	
	/**
	* localize_map_params
	* 
	* Loops map data ($this->map_data_cache) caching lat/lng and html content
	* for each map marker and info window.
	*
	* Note:: 	For each marker the content array is cached in global $prso_google_maps_infowindow.
	*			This can then be used either in the view template or another function to build
	*			info window html using the data in the array.
	*
	* Once the content data has been parsed, the info_window_view.php view template is then included and
	* buffered as a string to be localized along with the lat/lng params.
	* 
	* @var		global	$prso_google_maps_infowindow
	* @var		array	$map_params
	* @var		array	$marker_data
	* @var		string	$view_path
	* @var		array	$map_data
	* @access 	private
	* @author	Ben Moody
	*/
	private function localize_map_params() {
		
		//Init vars
		global $prso_google_maps_infowindow;
		$map_params 	= array();
		$marker_data	= array();
		$view_path 		= $this->view_template_path . '/info_window_view.php';
		$map_data		= $this->map_data_cache;
		
		//Setup data for creating map markers/info windows
		if( !empty($map_data) ) {
			
			//Cache map places LatLng and any rich content
			foreach( $map_data as $key => $dataArray ) {
				
				$marker_data[$key]['lat'] 	= $dataArray['lat'];
				$marker_data[$key]['lng'] 	= $dataArray['lng'];
				
				$marker_data[$key]['title'] = $dataArray['title'];
				
				//Set content param for view template
				$prso_google_maps_infowindow = $dataArray['content'];
				
				//Cache html content
				ob_start();
					$this->include_file( $view_path, FALSE );
					$marker_data[$key]['html'] 	= ob_get_contents();
				ob_end_clean();
				
			}
				
		}
		
		//Setup data for map api params - get these from database?
		$map_params = array(
			'canvasID' 	=> 'prso-gmaps-map',
			'zoom'		=> 3,
			'center'	=> array(
								'lat' => '',
								'lng' => ''	
							),
			'mapType'	=> ''
						
		);
		
		//Localize params from plugin js
		wp_localize_script( 'prso_google_maps', 'prsoGmapPlaces', $marker_data );
		wp_localize_script( 'prso_google_maps', 'prsoGmapOptions', $map_params );
		
	}
	
	/**
	* render_view
	* 
	* Simple helper to include the main view template file
	* 
	* @access 	private
	* @author	Ben Moody
	*/
	private function render_view() {
		
		//Init vars
		$view_path = NULL;
		
		//Include main map view
		$view_path = $this->view_template_path . '/main_view.php';
		$this->include_file( $view_path );
		
	}
	
	/**
	* include_file 
	* 
	* Helper to include external files
	*
	* Defaults to include_once, set $include_once param to TRUE to use include()
	* 
	* @param	string	$file_path
	* @param	bool	$include_once
	* @return	bool	
	* @access 	private
	* @author	Ben Moody
	*/
	private function include_file( $file_path, $include_once = TRUE ) {
		
		if( file_exists($file_path) ) {
			
			if( $include_once === TRUE ) {
				include_once( $file_path );
			} else {
				include( $file_path );
			}
			
			return TRUE;
		}
		
		return FALSE;
	}
	
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