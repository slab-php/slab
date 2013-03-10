<?php

class SlabInternalsController extends AppController {
	function rhps() {
		return $this->physical_file(SLAB_LIB.'/plugins/slab_internals/views/slab_internals/rhps.jpg');
	}
	function show_exception() {
		$this->set('ex', $this->data['ex']);
		$this->view->viewFilename = SLAB_LIB.'/plugins/slab_internals/views/slab_internals/show_exception.php';
	}
}

?>