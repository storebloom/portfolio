<?php

/**
 * Create task schedule based on estimates and a 16 hour work day starting now.
 * You can copy and paste this code into an online fiddle ex. http://phpfiddle.org/.
 */
class Logic_Test {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->json_array = $this->get_json_array();
	}

	/**
	 * @return string
	 */
	private function get_json_array() {

		return '[{"id": "FTW-383",
			"label": "Make RSS feed work with Apple News",
			"timeEstimate": 10800,
			"dueDate": "2015-08-21"
           },{"id": "LAW-216",
			"label": "Microsoft Edge: Sticky sidebar hops around when scrolling on Windows 10",
			"timeEstimate": 21600,
			"dueDate": "2015-08-23"
           },{"id": "SLW-136",
			"label": "Migrate Plugin Toggles",
			"timeEstimate": 21600,
			"dueDate": "2015-08-24"
           },{"id": "SLW-68",
			"label": "Move core plugins to Lawrence module",
			"timeEstimate": 108000,
			"dueDate": "2015-08-24"
           },{"id": "ALM-83",
			"label": "NASCAR: Configure new instance to accept voting",
			"timeEstimate": 14400,
			"dueDate": "2015-08-26"
           },{"id": "SLW-138",
			"label": "Create improved template loading functionality",
			"timeEstimate": 21600,
			"dueDate": "2015-09-05"}]';
	}

	/**
	 * Create an array based on information provided by a JSON string showing the estimated start and end date
	 * of a task.
	 *
	 * @return array
	 */
	public function create_task_calendar() {
		// Setting default timezone to PST.  Sorry other timezones.
		date_default_timezone_set( 'America/Los_Angeles' );

		// The decoded json array.
		$array = json_decode( $this->json_array, true );

		// Default variable values.
		$end_date = date( 'D, d M Y', strtotime( 'NOW' ) );
		$count = 1;
		$project_hour = 1;
		$project_total = '';
		$new_array = array();

		// Build a new task array utilizing the compounding time estimates as a key.
		// Also add all project hours together for loop.
		foreach ( $array as $task ) {
			$project_total += $task['timeEstimate'] / 3600;
			$project_estimates[ $project_total ] = array(
				'id' => $task['id'],
				'label' => $task['label'],
			);
		}

		// Loop through the project hours adding 8 hours every 16 completed as our work day is not 24 hours long.
		for ( $x = 1; $x <= $project_total; $x ++ ) {
			if ( 17 === $count ) {
				$project_hour = $project_hour + 8 ;
				$count = 1;
			}

			// Once you hit a project estimate point add the new date data to final array.
			if ( isset( $project_estimates[ $x ] ) ) {
				$start_date = $end_date;
				$end_date = $this->get_end_date( $project_hour );
				$new_array[] = array(
					'ID'         => $project_estimates[ $x ]['id'],
					'Label'      => $project_estimates[ $x ]['label'],
					'Start Date' => $start_date,
					'End Date'   => $end_date,
				);
			}

			$project_hour++;
			$count++;
		}

		return $new_array;
	}

	/**
	 * Create an end date based on provided hours of work.
	 *
	 * @param integer $hours_of_work Amount of hours completed so far.
	 *
	 * @return false|string
	 */
	private function get_end_date( $hours_of_work ) {
		$date = date( 'D, d M Y', strtotime( 'NOW' ) + $hours_of_work * 3600 );
		return $date;
	}
}

global $logic_test;

$logic_test = new Logic_Test();

// Echo out this array in a readable fashion.
echo '<pre>'; print_r( $logic_test->create_task_calendar() ); echo '</pre>';
?>