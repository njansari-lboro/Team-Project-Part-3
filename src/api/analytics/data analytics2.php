<?php
function individualData(jsonData){
	$dataToAnalyse = json_decode(jsonData);
	$totalHoursSpent = 0;
	foreach($dataToAnalyse as $task){
		$totalHoursSpent += $task['hours_spent'];
	$returnData = $totalHoursSpent;
	return $returnData;
}
function projectData(jsonData){
	$dataToAnalyse = json_decode(jsonData);
	$totalTasks = count($dataToAnalyse);
	$completeTasks = 0;
	$inProgTasks = 0;
	$unstartedTasks = 0;
	foreach($dataToAnalyse as $task){
		if($task['is_completed'] == 1){
			$completeTasks++;
		}else if($task['hours_spent'] != 0){
			$inProgTasks++;
		}else{
			$unstartedTasks++;
		}
	}
}
//no tasks
//no comp tasks
// no in prog tasks
// no unstarted tasks
?>
