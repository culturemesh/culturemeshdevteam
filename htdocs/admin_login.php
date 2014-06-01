<div>
	<p>Please login to access the admin panel</p>
	<form id="admin-login" method="POST" action="admin.php">
		<input type="text" name="username"></input>
		<input type="password" name="password"></input>
		<input type="submit" value="Log In"></input>
	</form>

	<?php if(isset($login)) :?>
	<p> Username and password incorrect. Try again. </p>
	<?php endif; ?>
</div>
