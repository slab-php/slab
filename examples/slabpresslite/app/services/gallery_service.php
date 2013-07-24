<?php

class GalleryService {
	function get_items() {
		return array(
			array(
				'url' => '/images/bootstrap-mdo-sfmoma-01.jpg', 
				'label' => 'First Thumbnail label',
				'text' => 'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.'
			),
			array(
				'url' => '/images/bootstrap-mdo-sfmoma-02.jpg', 
				'label' => 'Second Thumbnail label',
				'text' => 'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.'
			),
			array(
				'url' => '/images/bootstrap-mdo-sfmoma-03.jpg',
				'label' => 'Third Thumbnail label',
				'text' => 'Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.'
			)
		);
	}
}

?>