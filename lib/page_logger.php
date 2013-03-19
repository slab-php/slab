<?php

class PageLogger {
	var $events;
	var $start;

	function __construct() {
		$this->events = array();
		$this->log('log', 'log', 'start');
		$this->start = $this->events[0]['time'];
	}

	function log($source, $location, $event, $detail = '') {
		$this->events[] = array(
			'time' => get_microtime(),
			'source' => $source,
			'location' => $location,
			'event' => $event,
			'detail' => $detail
		);
	}

	function to_table() {
		?>
			<p>
				<a class="btn btn-mini btn-info" href="#" id="slab_show_page_log" onclick="document.getElementById('slab_page_log').style.display = 'block'; document.getElementById('slab_show_page_log').style.display = 'none'; return false;">
					Show page log
				</a>
			</p>
			<table class="table table-condensed" style="display: none" id="slab_page_log">
				<caption>Page log</caption>
				<thead>
					<tr>
						<th>Time</th>
						<th>Elapsed (ms)</th>
						<th>Time from last (ms)</th>
						<th>Source (ms)</th>
						<th>Location</th>
						<th>Event</th>
						<th>Detail</th>
					</tr>
				</thead>
				<tbody>
					<?php $last = $this->start; ?>
					<?php foreach ($this->events as $e): ?>
						<?php $time = $e['time']; ?>
						<?php $elapsed = $e['time'] - $this->start; ?>
						<?php $timeFromLast = $e['time'] - $last; ?>
						<tr class="<?php e($this->__get_class($timeFromLast)); ?>">
							<td><?php eh($this->__udate('Y-m-d H:i:s:u', $time)); ?></td>
							<td><?php eh($elapsed * 1000); ?></td>
							<td><?php eh($timeFromLast * 1000); ?></td>
							<td><?php eh($e['source']); ?></td>
							<td><?php eh($e['location']); ?></td>
							<td><?php eh($e['event']); ?></td>
							<td><?php eh($e['detail']); ?></td>
						</tr>
						<?php $last = $e['time']; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php
	}

	// http://stackoverflow.com/questions/4184769/how-to-get-current-time-in-ms-in-php
	function __udate($format, $utimestamp = null) {
		if (is_null($utimestamp)) {
			$utimestamp = microtime(true);
		}

		$timestamp = floor($utimestamp);
		$milliseconds = round(($utimestamp - $timestamp) * 1000000);

		return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
	}

	function __get_class($time) {
		if ($time > 0.900) return 'error';
		if ($time > 0.300) return 'warning';
		return '';
	}
}

?>