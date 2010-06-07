<?php
/* PartialResult
** A kind of ActionResult that renders the provided View without a blank layout
** (CC A-SA) 2009 Belfry Images [http://www.belfryimages.com.au | ben@belfryimages.com.au]
*/

class PartialResult extends ActionResult {
	var $view = null;
	
	function __construct($view) {
		$this->view = $view;
	}
	
	function render() {
		$layoutName = $this->view->layoutName;
		$this->view->layoutName = 'blank';
		e($this->view->render());
		$this->view->layoutName = $layoutName;
	}
	
};

?>