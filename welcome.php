<html>
<body>

Welcome <?php echo $_POST["shadow"]; ?><br>

The message was: <?php echo $_POST["textbox"]; ?><br>

The time is:  <?php date_default_timezone_set('Europe/Bucharest');
					echo localtime(time(),true)[tm_hour];
					echo ":";
					echo localtime(time(),true)[tm_min]; ?> <br>
					
Date is : <?php  echo "Year ". (1900+localtime(time(),true)[tm_year]).","; 
					echo " month ". localtime(time(),true)[tm_mon].",";
					echo " day ". localtime(time(),true)[tm_mday]; 
					?>
</body>
</html>