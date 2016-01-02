<?php

function zip() {
  $params = func_get_args();
  if (count($params) === 1){ // this case could be probably cleaner
    // single iterable passed
    $result = array();
    foreach ($params[0] as $item){
        $result[] = array($item);
    };
    return $result;
  };
  $result = call_user_func_array('array_map',array_merge(array(null),$params));
  $length = min(array_map('count', $params));
  return array_slice($result, 0, $length);
};

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://ingmmo.com
 * @since      1.0.0
 *
 * @package    Maplet
 * @subpackage Maplet/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Maplet
 * @subpackage Maplet/includes
 * @author     Marco Montanari <marco.montanari@gmail.com>
 */
class Maplet {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Maplet_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'maplet';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Maplet_Loader. Orchestrates the hooks of the plugin.
	 * - Maplet_i18n. Defines internationalization functionality.
	 * - Maplet_Admin. Defines all hooks for the admin area.
	 * - Maplet_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-maplet-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-maplet-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-maplet-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-maplet-public.php';

		$this->loader = new Maplet_Loader();


		// Add menu item
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		// Add Settings link to the plugin
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
		$this->loader->add_action('admin_init', $plugin_admin, 'options_update');
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

	}

	protected $maps = array(
		"osm" => array("name"=>"", "url"=>"http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", "attribution"=>"Map data © <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors"),

		"stamen.toner" => array("name"=>"", "url"=>"", "attribution"=>""),
		"stamen.toner-light" => array("name"=>"", "url"=>"", "attribution"=>""),
		"stamen.watercolor" => array("name"=>"", "url"=>"", "attribution"=>""),

		"mapbox.pirates" => array("name"=>"Pirates", "url"=>"", "attribution"=>""),
		"mapbox.vintage",//mslee.cif5p01n202nisaktvljx9mv3 => array("name"=>"", "url"=>"", "attribution"=>""),

		"thunderforest.opencyclemap" => array("name"=>"OpenCycleMap", "url"=>"https://[abc].tile.thunderforest.com/cycle/{z}/{x}/{y}.png", "attribution"=>""),
		"thunderforest.transport" => array("name"=>"OSM Transport", "url"=>"https://[abc].tile.thunderforest.com/transport/{z}/{x}/{y}.png", "attribution"=>""),
		"thunderforest.landscape" => array("name"=>"Landscape", "url"=>"https://[abc].tile.thunderforest.com/landscape/{z}/{x}/{y}.png", "attribution"=>""),
		"thunderforest.outdoors" => array("name"=>"", "url"=>"https://[abc].tile.thunderforest.com/outdoors/{z}/{x}/{y}.png", "attribution"=>""),
		"thunderforest.transport-dark" => array("name"=>"", "url"=>"https://[abc].tile.thunderforest.com/transport-dark/{z}/{x}/{y}.png", "attribution"=>""),
		"thunderforest.spinal" => array("name"=>"", "url"=>"https://[abc].tile.thunderforest.com/pioneer/{z}/{x}/{y}.png", "attribution"=>""),
		"thunderforest.pioneer" => array("name"=>"", "url"=>"https://[abc].tile.thunderforest.com/pioneer/{z}/{x}/{y}.png", "attribution"=>""),

		"cartodb.positron" => array("name"=>"", "url"=>"http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png", "attribution"=>"&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors, &copy; <a href=\"http://cartodb.com/attributions\">CartoDB</a>"),
		"cartodb.positron-nolabels" => array("name"=>"", "url"=>"http://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}.png", "attribution"=>"&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors, &copy; <a href=\"http://cartodb.com/attributions\">CartoDB</a>"),
		"cartodb.positron-onlylabels" => array("name"=>"", "url"=>"http://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}.png", "attribution"=>"&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors, &copy; <a href=\"http://cartodb.com/attributions\">CartoDB</a>"),
		"cartodb.darkmatter" => array("name"=>"", "url"=>"http://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png", "attribution"=>"&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors, &copy; <a href=\"http://cartodb.com/attributions\">CartoDB</a>"),
		"cartodb.darkmatter-nolabels" => array("name"=>"", "url"=>"http://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}.png", "attribution"=>"&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors, &copy; <a href=\"http://cartodb.com/attributions\">CartoDB</a>"),
		"cartodb.darkmatter-onlylabels" => array("name"=>"", "url"=>"http://{s}.basemaps.cartocdn.com/dark_only_labels/{z}/{x}/{y}.png", "attribution"=>"&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors, &copy; <a href=\"http://cartodb.com/attributions\">CartoDB</a>"),
	)


	public function register_shortcodes(){
		function maplet_shortcode($atts){
			$a = shortcode_atts( array(
					"base" => "osm",
					"types" => array(),
					"icons" => array(),
					"colors" => array(),
					"sizes" => array(),
					"minzoom" => 1,
					"maxzoom" => 20,
			), $atts );

			$icns = zip($a["types"], $a["icons"], $a["sizes"]);

			$les = array();
			foreach ($types as $t) {
				$les[$t] = get_posts(array("post_type"=>$t, "posts_per_page"=>-1));
				foreach ($les[$t] as $e) {
					$e->meta = get_post_meta($e->ID);
				}
			}

			$ret = "";

			$ret .= "<div id='map'></div>";
			$ret .= '<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />';
			$ret .= '<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>';
			$ret .= "<script>";
			$ret .= "var maplet = {};";
			$ret .= "maplet.icons = {}";
			foreach ($icns as $icn) {
				$ret .= 'maplet.icons['.$icn[0].'] = L.MakiMarkers.icon({icon: "'.$icn[1].'", color: "'.$icn[2].'", size: "'.$icn[3].'"});';
			}
			$ret .= "maplet.els = ".json_encode($les).";";
			$ret .= "maplet.map = L.map('map', {minZoom:<? echo $a["minzoom"]; ?>, maxZoom:<?echo $a["maxzoom"]; ?>});";
			$ret .= "maplet.bac = L.tileLayer('".$maps[$a["base"]]["url"].", {
			    attribution: '".$maps[$a["base"]]["attribution"]."',
			}).addTo(maplet.map);";
			$ret .= "maplet.vec = L.featureGroup().addTo(maplet.map);";

			$ret .= "</script>";


			return $ret;
		}
		add_shortcode("maplet", "maplet_shortcode");
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Maplet_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Maplet_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Maplet_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Maplet_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Maplet_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
