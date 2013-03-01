<?php

class SlabInternalsController extends AppController {
	function rhps() {
		return $this->physicalFile(SLAB_LIB.'/views/rhps.jpg');
	}
	function show_exception() {
		$this->set('ex', $this->data['ex']);
		$this->view->viewFilename = SLAB_LIB.'/views/show_exception.php';
	}
}

?>