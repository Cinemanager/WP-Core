<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cinemanager
 * @subpackage Cinemanager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cinemanager
 * @subpackage Cinemanager/admin
 * @author     Roman Avery <roman@cinemanager.co.nz>
 */
class Cinemanager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cinemanager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cinemanager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cinemanager-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cinemanager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cinemanager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cinemanager-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * 
	 */
	public function setup_roles() {
		remove_role('contributor');
		remove_role('author');
	}

	public function setup_types() {
		// Classification type
		register_post_type('cm-classification',
			array(
				'labels' => array(
					'name' => __('Classifications'),
					'singular_name' => __('Classification'),
				),
				'public' => true,
				'publicly_queryable' => true,
				'show_in_nav_menu' => true,
				'has_archive' => false,
			)
		);

		// Movie type
		register_post_type('cm-movie',
			array(
				'labels' => array(
					'name' => __('Movies'),
					'singular_name' => __('Movie'),
				),
				'public' => true,
				'has_archive' => false,
				'rewrite' => array( 'slug' => 'movies' ),
				'supports' => array(
					'title'
				)
			)
		);
	}

	public function load_carbon_fields() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		\Carbon_Fields\Carbon_Fields::boot();
	}

	public function register_carbon_fields() {
		\Carbon_Fields\Container::make('post_meta', 'Details')
			->where('post_type', '=', 'cm-movie')
			->add_fields(array(
				Field::make('association', 'classification')
					->set_types(array(
						array(
							'type' => 'post',
							'post_type' => 'cm-classification',
						)
					))
					->set_max(1),
				//Field::make()
				Field::make('text', 'trailer_url'),
				Field::make('textarea', 'synopsis'),
				Field::make('date', 'release_date'),
				Field::make('checkbox', 'use_custom_poster'),
				Field::make('image', 'custom_poster')
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => 'use_custom_poster',
							'value' => true,
							'compare' => '='
						)
					))
					->set_required(true),
			));
	}

	public function add_admin_column($column_name, $column_label, $post_type, $cb = null){

		// Column Header
		add_filter( 'manage_' . $post_type . '_posts_columns', function($columns) use ($column_name, $column_label) {
			$columns[$column_name] = $column_label;
			return $columns;
		} );
	
		// Column Content
		add_action( 'manage_' . $post_type . '_posts_custom_column' , function( $column, $post_id ) use ($column_name, $column_label, $cb) {
	
			if($column_name === $column){
				if ($cb === null) {
					$meta = get_post_meta($post_id, 'release_date', true);
					echo (($meta && $meta !== '') ? $meta : 'N/A');
				} else {
					$cb($post_id);
				}
			}
	
		}, 10, 2 );
	}

	public function remove_admin_column($column_name, $post_type) {
		add_filter( 'manage_' . $post_type . '_posts_columns', function ($columns) use ($column_name) {
			unset($columns[$column_name]);
			return $columns;
		});
	}

	public function set_admin_columns() {
		// Movie columns
		$this->remove_admin_column('date', 'cm-movie');
		$this->add_admin_column(__('Poster'), 'cm-movie', function ($id) {
			$using_custom_poster = get_post_meta($id, 'use_custom_poster', true);
			echo 'pp';
			if ($using_custom_poster) {
				$custom_poster = get_post_meta($id, 'custom_poster', true);
				echo $custom_poster;
			} else {
				
			}
		});
		$this->add_admin_column(__('Release Date'), 'cm-movie');
	}

}