<!DOCTYPE html>
<html class="no-js">
	<head>
	</head>
	<body class="style-3">
		
	<form action="send_not_handler.php" method="post">

		<select style="width: 80%;" name="notification_type">
			<option value="">Choose NOTIFICATION TYPE</option>
			<option value="single_user">Single User</option>
			<option value="all_users">All Users</option>
		</select>

		<br>
		<br>

		<input style="width: 80%;" type="text" required="required" name="pott_name" placeholder="POTT NAME (COMPULSORY FOR SINGLE USER OPTION)">
		<br>
		<br>

		<input  style="width: 80%;" required="required" type="text" name="notification_title" placeholder="TITLE">
		<br>
		<br>

		<textarea style="width: 80%;" name="notification_text" placeholder="NOTIFICATION TEXT"></textarea> <br> <br>

		<input style="width: 80%;" type="text" required="required" name="your_password" placeholder="PASSWORD">
		 <br> <br>
		<input type="submit">
		
	</form>
	</body>
</html>