<?php
    require_once('log.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'headinclude.php';?>
</head>

<body id="registration">
<?php
include 'header.php';
?>

<div class="container">
		<h1>Registration</h1>
        <form id="reg_form" name="registration_form" method="post" enctype="multipart/form-data" action="" onsubmit="return Validate();">
			<label for="name">First Name*</label>
				<input type="text" name="firstname" required>
			<label for="lastname">Last Name*</label>	
				<input type="text" name="lastname" required>
                        <label for="city">City*</label>
				<input type="text" name="city" required>
			<label for="state">State*</label>
                 	<select id="state" name="state" class="input-mini">
                            <?php foreach(getStates() as $state):?>
                                <option><?=strtoupper($state)?></option>
                            <?php endforeach;?>
                                
			<label for="bmonth">Date of Birth</label>
				<select name="bmonth" class="input-medium" id="Birth Month">
                                    <?php foreach(getMonths() as $month):?>
                                    <option><?=ucfirst($month)?></option>
                                    <?php endforeach;?>
				</select>
				<select name="bday" class="input-mini" id="Birth Day">
                                    <?php for($i=1;$i<=31;$i++):?>
                                    <option><?=$i?></option>
                                    <?php endfor;?>
				</select>
				<select name="byear" class="input-small" id="Birth Year">
                                <?php for($i=2013;$i>1900;$i--):?>
                                    <option><?=$i?></option>
                                <?php endfor;?>
				</select>
			<label for="email">Email Address*</label>
				<input type="email" name="email" required>
			<label for="username">Username*</label>
				<input type="text" name="username" required>
			<label for="password">Password*</label>
				<input type="password" name="password" required>
			<label for="password_confirmation">Re-enter Password*</label>
				<input type="password" name="password_confirmation" required>

			<label for="profile-pic">Upload Profile Picture (optional)
				<input type="file" class="inline" name="profile-pic">
                        </label>

			<label class="checkbox">
                            <input type="checkbox" id="read_privacy" class="inline" name="read_privacy">I have read & agreed to <a href='privacy.php' target="_blank">Nommful's privacy policy</a>*
                        </label>
				
			<label>Please enter the words you see into the box below.</label>
			<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LfFWuMSAAAAAJce_7lJC6JW3YBMWh-XURZfmNHl"></script>

	<noscript>
  		<iframe src="http://www.google.com/recaptcha/api/noscript?k=6LfFWuMSAAAAAJce_7lJC6JW3YBMWh-XURZfmNHl" height="300" width="500" frameborder="0"></iframe><br/>
  		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
  		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>			<input type="submit" value="Register" class="btn btn-primary btn-large"/>
</form>

<?php
include 'footer.php';
?>
</div>

</body>
</html>