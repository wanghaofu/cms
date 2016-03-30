<?php 
if($iwpc_debug && !$diableDebug) {
	$totaltime = $debugger->endTimer();
	printf("<center><span  class=\"process\" > Processed in %f second(s), %d queries, %d cached </span></center>",$totaltime,$EXECS+$CACHED, $CACHED);

	if(!empty($db->errorinfo)) {
		foreach($db->errorinfo as $var) {
			echo $var.'<br>';
		}
	
	}
	echo $db->report;
}
?>