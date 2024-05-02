<?php
function individualData($jsonData){
        $dataToAnalyse = json_decode($jsonData,true);
        $totalHoursSpent = 0;
        foreach($dataToAnalyse as $task){
                $totalHoursSpent += $task['hours_spent'];
        }
        $returnData = $totalHoursSpent;
        return $returnData;
}
function projectData($jsonData){
        $dataToAnalyse = json_decode($jsonData,true);
        $totalTasks = count($dataToAnalyse);
        $completeTasks = 0;
        $inProgTasks = 0;
        $unstartedTasks = 0;
        foreach($dataToAnalyse as $task){
                if($task['is_completed'] == 1){
                        echo(++$completeTasks);
                }else if($task['hours_spent'] != 0){
                        $inProgTasks++;
                }else{
                        $unstartedTasks++;
                }
        }
        $returnData = json_encode(array('total_tasks'=>$totalTasks,'complete_tasks'=>$completeTasks,'in_progress_tasks'=>$inProgTasks,'non-started_tasks'=>$unstartedTasks));
        return $returnData;
}
?>