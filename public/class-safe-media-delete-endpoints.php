<?php


class Safe_Media_Delete_Endpoints {

	public function register_soft_media_delete()
	{
		register_rest_route('assignment/v1/', 'attached-media', [
			'methods'  => 'GET',
			'callback' => array($this,'get_attachments_func')
		]);

		register_rest_route('assignment/v1/', 'attached-media/(?P<id>\d+)', [
			'methods'  => 'GET',
			'callback' => array($this, 'get_attachment_by_id_func')
		]);

		register_rest_route('assignment/v1/', 'attached-media-delete/(?P<id>\d+)', [
			'methods'  => 'DELETE',
			'callback' => array($this, 'delete_attachment_func')
		]);
		
	}

	public function delete_attachment_func($id) {

		$attachment_id = $id['id'];

		$thumbnail_queries = $this->get_thumb_queries();
		$queries = $this->get_thumb_attachment_queries($thumbnail_queries);

		$data = [];
		$data_arr = [];

		foreach( $queries as $query ) { 


			$attachment_id = $id['id'];

			$data_arr[] =  $this->get_attachment_id_func($attachment_id);

				
		}

		$data_arr = array_map("unserialize", array_unique(array_map("serialize", $data_arr)));
		$data_filter = array_filter($data_arr);
		$data_val = array_values($data_filter);
		$data = $data_val;


		if(!$data['attached_object'][0]['post_id'] &&  !$data['attached_object'][0]['term_id']) {
			wp_delete_attachment($attachment_id);

			return new WP_REST_Response(true, 200);

		} else {
			
			return new WP_REST_Response(true, 404);

		}
		

	}

	public function get_attachment_by_id_func($id) {

		$thumbnail_queries = $this->get_thumb_queries();
		$queries = $this->get_thumb_attachment_queries($thumbnail_queries);

		$data = [];
		$data_arr = [];

		foreach( $queries as $query ) { 


			$attachment_id = $id['id'];

			$data_arr[] = $this->get_attachment_id_func($attachment_id);	
				
		}

		$data_arr = array_map("unserialize", array_unique(array_map("serialize", $data_arr)));
		$data_filter = array_filter($data_arr);
		$data_val = array_values($data_filter);
		$data = $data_val;

		return $data;

	}

	
	public function get_attachments_func($data)
	{	

		$thumbnail_queries = $this->get_thumb_queries();
		$queries = $this->get_thumb_attachment_queries($thumbnail_queries);

		$data = [];
		$data_arr = [];

		foreach( $queries as $query ) { 

			$attachment_id = $query->ID;

			$data_arr[] = $this->get_attachment_id_func($attachment_id);	
			$data_arr[] = $this->get_attachment_term_id_func($attachment_id);
				
		}

	
		$data_arr = array_map("unserialize", array_unique(array_map("serialize", $data_arr)));
		$data_filter = array_filter($data_arr);
		$data_val = array_values($data_filter);

		$_data_val = array();

		foreach( $data_val as $data_va) {

			if (isset($_data_val[$data_va['id']])) {
				
				continue;
			  }

		
			$_data_val[$data_va['id']] = $data_va;

		}

		$data_val = array_values($_data_val);
		
		return $data_val;
		

	}

	protected function get_attachment_term_id_func($attachment_id) {

		if ( wp_attachment_is_image( $attachment_id ) ) {
			
			$thumbnail_query = new WP_Query( array(
				'meta_key'       => '_thumbnail_id',
				'meta_value'     => $attachment_id,
				'post_type'      => 'any',	
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => -1,
			) );

			$used_as_thumbnail = $thumbnail_query->posts;


			/////// Get Term Meta ////////

			$terms = get_terms( array(
				'taxonomy'   => 'category',
				'hide_empty' => false,
			) );
			

			$term_id = [];

			foreach( $terms as $term) {

				$term_vals = get_term_meta($term->term_id,'term_attached_image_id', true);
				
				if($term_vals == $attachment_id) {

				
					$term_id[] = $term->term_id;

					$data['id'] = $attachment_id;

					$post_thumb_query  = get_post($attachment_id, ARRAY_A);

					$data['id'] = $post_thumb_query['ID'];
					$data['post_datess'] = $post_thumb_query['post_date'];
					$data['post_name'] = $post_thumb_query['post_name'];
					$data['type'] = $post_thumb_query['post_mime_type'];
					$data['link'] = $post_thumb_query['guid'];
					$data['alt_text'] = $post_thumb_query['post_title'];
					$data['attached_object']['post_id'] = $post_thumb_ids;
					$data['attached_object']['term_id'] = $term_id;

					return $data;

					
				}
			

			}	
			

		}

	}

	

	protected function get_attachment_id_func($attachment_id) {

		if ( wp_attachment_is_image( $attachment_id ) ) {
			
			$thumbnail_query = new WP_Query( array(
				'meta_key'       => '_thumbnail_id',
				'meta_value'     => $attachment_id,
				'post_type'      => 'any',	
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => -1,
			) );

			$used_as_thumbnail = $thumbnail_query->posts;


			/////// Get Term Meta ////////

			$terms = get_terms( array(
				'taxonomy'   => 'category',
				'hide_empty' => false,
			) );
			

			$term_id = [];

			foreach( $terms as $term) {

				$term_vals = get_term_meta($term->term_id,'term_attached_image_id', true);
				
				if($term_vals == $attachment_id) {

				
					$term_id[] = $term->term_id;

					
				}

			}	
			

			$post_thumb_ids = [];

			foreach( $used_as_thumbnail as $thumbnail_query_arr ) { 

				$post_thumb_ids[] = $thumbnail_query_arr;
			}
			
				
			foreach( $used_as_thumbnail as $thumbnail_query ) { 

				$post_thumb = get_post_thumbnail_id( $thumbnail_query );
				$post_thumb_query   = get_post($post_thumb, ARRAY_A);
				
				$data['id'] = $post_thumb_query['ID'];
				$data['post_datess'] = $post_thumb_query['post_date'];
				$data['post_name'] = $post_thumb_query['post_name'];
				$data['type'] = $post_thumb_query['post_mime_type'];
				$data['link'] = $post_thumb_query['guid'];
				$data['alt_text'] = $post_thumb_query['post_title'];
				$data['attached_object']['post_id'] = $post_thumb_ids;
				$data['attached_object']['term_id'] = $term_id;

				$data_arr = $data;

				return $data_arr;
					
			
			}

		}

	}


	public function get_thumb_queries() {

		$thumbnail_queries = new WP_Query( array(
			'meta_key'       => '_thumbnail_id',
			'post_type'      => 'any',	
			'posts_per_page' => -1,
		) );

		return $thumbnail_queries->get_posts();

	}

	public function get_thumb_attachment_queries($thumbnail_queries) {

		$i = 0;

		foreach( $thumbnail_queries as $thumbnail_query ) { 

			$data[$i]['id'] = $thumbnail_query->ID;
			$data[$i]['date'] = $thumbnail_query->post_date;
			$data[$i]['slug'] = $thumbnail_query->post_name;

			$i++;
			
		}


		$queries = new WP_Query( array(
			'posts_per_page' => -1,
			'post_mime_type' =>'image',
			'post_status' => 'all',
			'post_type' => 'attachment',
		));

		$queries = $queries->get_posts();

		return $queries;

	}


}
