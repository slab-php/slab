<?php
class BArray {
	var $arr;
	function get_array() { return $this->arr; }
	function to_array() { return $this->arr; }
	
	function __construct($arr) {
		$this->arr = $arr;
	}
	
	function where($predicate = null) { return $this->filter($predicate); }
	function filter($predicate = null) {
		if (!isset($predicate)) return $this;
		
		$filterMethod = 'return '.str_replace('@@', '$x', $predicate).';';

		$filtered = array();
		foreach ($this->arr as $x) {
			if (eval($filterMethod)) $filtered[] = $x;
		}
		
		return new BArray($filtered);
	}
	
	function select($predicate = null) { return $this->map($predicate); }
	function map($predicate = null) {
		if (!isset($predicate)) return $this;
	
		$mapMethod = 'return '.str_replace('@@', '$x', $predicate).';';
		
		$mapped = array();
		foreach ($this->arr as $x) {
			$mapped[] = eval($mapMethod);
		}
		
		return new BArray($mapped);
	}
	
	function select_many($predicate) { return $this->bind($predicate); }
	function bind($predicate) {
		$b = $this->select($predicate);
		
		$flat = array();
		foreach ($b->arr as $many) {
			$flat = array_merge($many, $flat);
		}
		
		return new BArray($flat);
	}
	
	function order_by($predicate) {
		$px = str_replace('@@', '$x', $predicate);
		$py = str_replace('@@', '$y', $predicate);
		$orderByMethod = "return substr_compare({$px}, {$py}, 0);";
		return $this->__order_by($orderByMethod);
	}
	function order_by_desc($predicate) {
		$px = str_replace('@@', '$x', $predicate);
		$py = str_replace('@@', '$y', $predicate);
		$orderByMethod = "return substr_compare({$py}, {$px}, 0);";
		return $this->__order_by($orderByMethod);
	}
	function __order_by($orderByMethod) {
		$arr = $this->arr;
		$this->__quicksort($arr, 0, count($this->arr) - 1, $orderByMethod);
		
		return new BArray($arr);
	}
	function __quicksort(&$arr, $left, $right, $orderByMethod) {
		if ($right > $left) {
			$pivotIndex = $left + ($right - $left) / 2;
			$pivotNewIndex = $this->__partition($arr, $left, $right, $pivotIndex, $orderByMethod);
			$this->__quicksort($arr, $left, $pivotNewIndex - 1, $orderByMethod);
			$this->__quicksort($arr, $pivotNewIndex + 1, $right, $orderByMethod);
		}
	}
	function __partition(&$arr, $left, $right, $pivotIndex, $orderByMethod) {
		$y = $arr[$pivotIndex];
		$arr[$pivotIndex] = $arr[$right];
		$arr[$right] = $y;
		$storeIndex = $left;
		for ($i = $left; $i < $right; $i ++) {
			$x = $arr[$i];
			$comp = eval($orderByMethod);
//e("x: {$x}, y: {$y}, comp: {$comp}<br/>");
			if ($comp < 0) {
				$a = $arr[$i];
				$arr[$i] = $arr[$storeIndex];
				$arr[$storeIndex] = $a;
				$storeIndex ++;
			}
		}
		$a = $arr[$right];
		$arr[$right] = $arr[$storeIndex];
		$arr[$storeIndex] = $a;
		return $storeIndex;
	}
	
	function count($predicate = null) {
		$b = $this->filter($predicate);
		return count($b->arr);
	}
	
	function sum($predicate = null) {
		$b = $this->map($predicate);
		$sum = 0;
		foreach ($b->arr as $x) $sum += $x;
		return $sum;
	}
	function min($predicate = null) {
		$b = $this->map($predicate);
		return min($b->arr);
	}
	function max($predicate = null) {
		$b = $this->map($predicate);
		return max($b->arr);
	}
	function average($predicate = null) { return $this->avg($predicate); }
	function avg($predicate = null) {
		$b = $this->map($predicate);
		return $b->sum() / $b->count();
	}
	
	function any($predicate = null) {
		return $this->count($predicate) !== 0;
	}
	function all($predicate = null) {
		return $this->count($predicate) == $this->count();
	}
}
?>