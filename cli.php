<?php

	const months = array(
    "January", "February", "March",
    "April", "May", "June",
    "July", "August", "September",
    "October", "November", "December"
	);

	$valid_year = false;
	$input_year_label = "Please enter year ('yyyy'): ";
	while (!$valid_year)
	{
		echo $input_year_label;
		$chosen_year = trim(fgets(STDIN, 1024));
		 if (strtotime($chosen_year) === false) {
		  $valid_year = false;
		  $input_year_label = "Please enter valid year (E.g 2018): ";
		}else{
			$valid_year = true;
		}
	}
 
	//Setup date-month
	function setup_year_month($year,$month)
	{
		if ($month < 10)
		{
			$month = "0" . $month;
		}
		return $year . "-" . $month;
	}
	
	//Setup date-month-day
	function setup_year_month_day($year,$month,$day)
	{
		$year_month = setup_year_month($year,$month);
		if ($day < 10)
		{
			$day = "0" . $day;
		}
		return $year_month . "-" . $day;
	}
	
	//Check if the date given falls on a weekend. Return [ Sunday = 2, Saturday = 1]
	function getChangeFactor($day)
	{
		if (substr($day, 0,1) == "S")
		{
			if ($day == "Saturday")
			{
				return 1;
			}else
			{
				//Sunday
				return 2;
			}
		}
		return 0;
	}
	
	//Get Salary Date for specific month
	function getSalaryDate($chosen_year,$month)
	{
		//Setup year-month date for current month
		$date_year_month = setup_year_month($chosen_year,$month);
		//Get maximum days in month
		$max_days_in_month = cal_days_in_month( CAL_GREGORIAN, $month , $chosen_year );
		//Get last day of month
		$last_day_of_month = date("l", strtotime($date_year_month."-".$max_days_in_month));
		//Check if last day falls on weekend, return change_factor
		$change_factor = getChangeFactor($last_day_of_month);
		//Apply change factor to date(last day)
		$salary_day = $max_days_in_month - $change_factor;
		return $date_year_month . "-" . $salary_day;
	}
	
	//Get Expenses Date. Check if provided date is on a weekend, if so, return next monday's date
	function getExpensesDate($date)
	{
		$expenses_day_of_week = date("l", strtotime($date));
		if (substr($expenses_day_of_week, 0,1) == "S")
		{
			return date("Y-m-d", strtotime("next monday ". $date));
		}
		return $date;
	}
	
	//$table_headers = getTableHeaders($broswer_language_code);
	$table_headers = "Month Name, 1st expenses day, 2nd expenses day, Salary day";
	
	//Output results to user. Store output in output_array
	echo $table_headers . "\n";
	for ($x = 1; $x <= 12; $x++)
	{ 
		$expenses_day_one = getExpensesDate(setup_year_month_day($chosen_year,$x,1));
		$expenses_day_two = getExpensesDate(setup_year_month_day($chosen_year,$x,15));
		$output_array[$x -1] = months[$x - 1] . "," . getSalaryDate($chosen_year ,$x) . ", " . $expenses_day_one . ", " . $expenses_day_two . "\n";	
		echo $output_array[$x - 1];
	}
	
	function checkFilenameExists($file_name)
	{
		if (file_exists($file_name)) 
	{
		return true;
		}else 
		{
			return false;
		}
	}
	
	//Check if filename exists. 
	$filename_exists = true;
	$filename_label = "Please enter file name to save: ";
	while ($filename_exists)
	{
		echo $filename_label;
		$output_filename = trim(fgets(STDIN, 1024));
		$has_extension = strpos($output_filename, ".");
		if ($has_extension)
		{
			$output_filename = substr($output_filename, 0, strpos($output_filename, "."));
		}
		$filename = $output_filename . ".txt";
		
		if (checkFilenameExists($filename))
		{
			$filename_exists = true;
			$filename_label = "That filename already exists. Please enter new file name to save: ";
		}else{
			$filename_exists = false;
		}
	}
	//Add Headers
	file_put_contents($filename, $table_headers);
	//Check if output_array is populated. Output each line in file.
	if (is_array($output_array))
	{
		foreach ($output_array as $new_line)
		{
			file_put_contents($filename, "\r\n" . $new_line, FILE_APPEND | LOCK_EX);
		}
	}
	
	if (file_exists($filename)) 
	{
		echo "The file $filename has been created.";
	}else 
	{
		echo "Sorry, something went wrong. The file $filename does not exist";
	}

 
?>