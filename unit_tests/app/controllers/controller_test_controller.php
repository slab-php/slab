<?php
// ControllerTestController
// This is used by the controller test case to test controllers

class ControllerTestController extends Controller {
	var $executedConstruct = false;
	var $executedBeforeAction = false;
	var $executedAfterAction = false;
	var $executedAction = false;
	
	function __construct() {
		parent::__construct();
		$this->executedConstruct = true;
	}
	
	function beforeAction() {
		$this->executedBeforeAction = true;
	}
	function afterAction() {
		$this->executedAfterAction = true;
		if ($this->actionName == 'action_test') {
			// we're testing that each of the methods get called, but this gets called after the action has executed and the
			// view created, so add the $executedAfterAction field explicitly to the view
			$this->actionResult->s .= '$executedAfterAction='.($this->executedAfterAction?'true':'false').';';
		}
	}
	
	function action_test() {
		$this->executedAction = true;
		
		$result = '';
		$result .= '$executedConstruct='.($this->executedConstruct?'true':'false').';';
		$result .= '$executedBeforeAction='.($this->executedBeforeAction?'true':'false').';';
		// at this point, $executedAfterAction is always false, but it is overridden in afterAction()
		$result .= '$executedAfterAction='.($this->executedAfterAction?'true':'false').';';
		$result .= '$executedAction='.($this->executedAction?'true':'false').';';
		
		return $this->text($result);
	}
}


?>