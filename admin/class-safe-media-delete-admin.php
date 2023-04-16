<?php


class Safe_Media_Delete_Admin {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	public function enqueue_styles() {

	
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/safe-media-delete-admin.css', array(), $this->version, 'all' );

	}


	public function enqueue_scripts() {


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/safe-media-delete-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function cmb2_sample_metaboxes() {

		/**
		 * Initiate the metabox
		 */
		$cmb_term = new_cmb2_box( array(
			'id'            => 'test_metabox',
			'title'         => __( 'Test Metabox', 'cmb2' ),
			'object_types'  => array( 'term' ), 
			'taxonomies'       => array( 'category' ),
			// 'new_term_section' => true, // Will display in the "Add New Category" section
		) );
	
		$cmb_term->add_field( array(
			'name' => esc_html__( 'Term Image', 'cmb2' ),
			'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
			'id'   => 'term_attached_image',
			'type' => 'file',
		) );
	}

	public function delete_attachment_func($id) {

		$find_posts_using_attachment = new Find_Media_Attachment;

		$used_in = $find_posts_using_attachment->get_posts_using_attachment($id, 'column');
		$referer = filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL);
	
		if(  $used_in != '(Unused)' ) {
			exit("
			
			<h3>To delete an attachment image, go to the Featured image, Post content, or Term edit page and remove the attachment from that location.</h3>
			
				<p><b>Attachment id: </b> $id </p>
				<p><b>Attached Object: $used_in</p>
				<p><a href=".$referer.">Go back to previous page</a></p>
			"
			);
		} 

	}


}
