<?php 
require 'db/connect.php';
require 'functions/security.php';

$records = array();
$limit = 10;
if($results = $db ->query("SELECT * FROM board ORDER BY created DESC")){
	if($results->num_rows) {
		while($row =  $results->fetch_object()) {
			$records[] = $row;
		}
		$results->free();
	}	
}		 

if (!count($records)) {
			echo 'no records';
		}else {foreach($records as $r){ 
		?>
		
<!DOCTYPE html>
<html>
	<head>
		<title name="title"> Shadows' Den history </title>
		<link rel="stylesheet" type="text/css" href="style.css">
	
	</head>
	<body>
		<div class="display">
				<div><?php echo nl2br(escape($r->msgtxt));?></div>
				<div class="signature"><?php echo escape($r->name);?></div>
				<div class="signature"><?php $roTime = new DateTime(escape($r->created), new DateTimeZone('America/New_York'));
							$roTime->setTimezone(new DateTimeZone('Europe/Bucharest'));
							$timestamp = strtotime($roTime->format('Y-m-d H:i'));		
							if (date('I', $timestamp)) {
							$timestamp += 3600;
							echo date("Y-m-d H:i", $timestamp); } else {echo $roTime->format('Y-m-d H:i'); }
							?></div>
		</div>
	</body>
</html>	
<?php 
	}
}
?>