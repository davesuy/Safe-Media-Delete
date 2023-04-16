<?php

class Test_Safe_Media_Endpoints extends WP_UnitTestCase {

	
	public function test_delete_attachment_func() {

		$attachment_id = 15040;

		$thumb_queries = new Safe_Media_Delete_Endpoints;

		$thumbnail_queries = $thumb_queries->get_thumb_queries();
		$queries = $thumb_queries->get_thumb_attachment_queries($thumbnail_queries);

		$data = [];
		$data_arr = [];

		foreach( $queries as $query ) { 


			$data_arr[] =  $this->get_attachment_id_func($attachment_id);

				
		}

		$data_arr = array_map("unserialize", array_unique(array_map("serialize", $data_arr)));
		$data_filter = array_filter($data_arr);
		$data_val = array_values($data_filter);
		$data = $data_val;



		$this->assertIsArray($data);
		

	}

	public function test_get_attachments_func() {

		$thumb_queries = new Safe_Media_Delete_Endpoints;

		$thumbnail_queries = $thumb_queries->get_thumb_queries();
		$queries = $thumb_queries->get_thumb_attachment_queries($thumbnail_queries);

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
		
		$this->assertIsArray($data_val);
	}


}
