<?php
require 'db/connect.php';
require 'functions/security.php';
error_reporting(E_ALL);

// this is the form part
if(!empty($_POST['textboxform'])&&$_POST['textboxform']=='Save') { //check to see if form is empty & save button clicked
	if(isset($_POST['msgtxt'],$_POST['author'])){
		$msgtxt = trim($_POST['msgtxt']);
		$author= $_POST['author'];
		if(!empty($msgtxt)){
		$insert = $db->prepare("INSERT INTO messages (msgtxt, author, created) VALUES (?, ?, now())");
		$insert->bind_param('si',$_POST['msgtxt'],$author);
		
		if($insert->execute()){
			header('Location: index.php');
			die();
		}else{echo 'did not execute';}
	
	}else{echo 'msg empty';}


}
}

//echo 'db connected'.'<br>';

// next comes the list of users
if($result =$db->query('SELECT * FROM shadows')) {
	if($count = $result->num_rows){
		//echo 'board table retrieved and it has ', $count, ' rows','<br>';
		$nicknames = array();
		while($row = $result->fetch_object()) {
			$nicknames[] = $row;
				
		}
		
		$result->free();
		}
	else($db->error);
}
	
$records = array();

if($results = $db ->query("SELECT * FROM board ORDER BY created DESC")){
	if($results->num_rows) {
		while($row =  $results->fetch_object()) {
			$records[] = $row;
		}
		$results->free();
	}	
}	

// here is the pagination 	(I hope )
$sql = "SELECT COUNT(msgtxt) FROM board ";
$query = mysqli_query($db, $sql);
$row = mysqli_fetch_row($query);
// Here we have the total row count
$rows = $row[0];
// This is the number of results we want displayed per page
$page_rows = 10;
// This tells us the page number of our last page
$last = ceil($rows/$page_rows);
// This makes sure $last cannot be less than 1
if($last < 1){
	$last = 1;
}
// Establish the $pagenum variable
$pagenum = 1;
// Get pagenum from URL vars if it is present, else it is = 1
if(isset($_GET['pn'])){
	$pagenum = preg_replace('#[^0-9]#', '', $_GET['pn']);
}
// This makes sure the page number isn't below 1, or more than our $last page
if ($pagenum < 1) { 
    $pagenum = 1; 
} else if ($pagenum > $last) { 
    $pagenum = $last; 
}
// This sets the range of rows to query for the chosen $pagenum
$limit = 'LIMIT ' .($pagenum - 1) * $page_rows .',' .$page_rows;
// This is your query again, it is for grabbing just one page worth of rows by applying $limit
$sql = "SELECT * FROM board ORDER BY created DESC $limit";
$query = mysqli_query($db, $sql);
// This shows the user what page they are on, and the total number of pages
$textline1 = "Messages : $rows ";
$textline2 = "Page <b>$pagenum</b> of <b>$last</b>";
// Establish the $paginationCtrls variable
$paginationCtrls = '';
// If there is more than 1 page worth of results
if($last != 1){
	/* First we check if we are on page one. If we are then we don't need a link to 
	   the previous page or the first page so we do nothing. If we aren't then we
	   generate links to the first page, and to the previous page. */
	if ($pagenum > 1) {
        $previous = $pagenum - 1;
		$paginationCtrls .= '<a href="'.$_SERVER['PHP_SELF'].'?pn='.$previous.'">Previous</a> &nbsp; &nbsp; ';
		// Render clickable number links that should appear on the left of the target page number
		for($i = $pagenum-4; $i < $pagenum; $i++){
			if($i > 0){
		        $paginationCtrls .= '<a href="'.$_SERVER['PHP_SELF'].'?pn='.$i.'">'.$i.'</a> &nbsp; ';
			}
	    }
    }
	// Render the target page number, but without it being a link
	$paginationCtrls .= ''.$pagenum.' &nbsp; ';
	// Render clickable number links that should appear on the right of the target page number
	for($i = $pagenum+1; $i <= $last; $i++){
		$paginationCtrls .= '<a href="'.$_SERVER['PHP_SELF'].'?pn='.$i.'">'.$i.'</a> &nbsp; ';
		if($i >= $pagenum+4){
			break;
		}
	}
	// This does the same as above, only checking if we are on the last page, and then generating the "Next"
    if ($pagenum != $last) {
        $next = $pagenum + 1;
        $paginationCtrls .= ' &nbsp; &nbsp; <a href="'.$_SERVER['PHP_SELF'].'?pn='.$next.'">Next</a> ';
    }
}


$list = '';
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
	$id = $row["id"];
	$msgtxt = escape($row["msgtxt"]);
	$msgtxt= nl2br($msgtxt);
	$name = escape($row["name"]);
	$created = $row["created"];
	//$created = strftime("%b %d, %Y", strtotime($created));
	$roTime = new DateTime(escape($created), new DateTimeZone('America/New_York'));
$roTime->setTimezone(new DateTimeZone('Europe/Bucharest'));
$timestamp = strtotime($roTime->format('Y-m-d H:i'));		
if (date('I', $timestamp)) {
	$timestamp += 3600;
	$timestamp= date("Y-m-d H:i", $timestamp); } else {$timestamp= $roTime->format('Y-m-d H:i'); }

	$list .= '<div class="display">
			<div>'.$msgtxt.'</div> 
			<div class="signature"> Author: '.$name.'</div>
			<div class="signature"> Written: '.$timestamp.'</div>
			</div>';
}	

// this should give me the current page name
function curPageURL() {
 $pageURL = 'http';
// if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
$currentpage = curPageURL();
?>
<!DOCTYPE html>
<html>
	<head>
		<title name="title"> Shadows' Den </title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script type="text/javascript" src="jquery-1.11.1.min.js"> </script>
	</head>
	<body>
		<div class='scratchpad'>
			<form  method="post" >
				Shadow:			
				
					<select  name="author">
					<?php 
					foreach ($nicknames as $n){
					?>
				
						<option value="<?php echo $n->id;?>"><?php echo escape($n->name);?></option>
						<?php
						}
						?>
					</select><br>

					<textarea type="text" name="msgtxt" cols="30" rows="20" class="msgtxt"></textarea><br>
					

					<input type="submit" name="textboxform" value="Save">
					
			</form>
		</div>
		<div class='history'>
		<div class="refresh1">
  <h3><?php echo $textline1;?></h3>
  <p><?php echo $textline2; ?></p>
  <div id="pagination_controls"><?php echo $paginationCtrls; ?></div>
  <div ><?php echo $list; ?></div>
  <div id="pagination_controls"><?php echo $paginationCtrls; ?></div>
</div>
		<script type="text/javascript" async > 
			$(document).ready( 
			function(){
				$auto_refresh = setInterval(
				function (){$('.history').load(<?php $currentpage.' .refresh1'?>);}, 3000);});
			
		</script>
		</div>

	
	</body>
</html>

