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
                          <option value=""></option><option value="AA">AA</option><option value="AE">AE</option><option value="AK">AK</option><option value="AL">AL</option><option value="AP">AP</option><option value="AR">AR</option><option value="AS">AS</option><option value="AZ">AZ</option><option value="CA">CA</option><option value="CO">CO</option><option value="CT">CT</option><option value="DC">DC</option><option value="DE">DE</option><option value="FL">FL</option><option value="FM">FM</option><option value="GA">GA</option><option value="GU">GU</option><option value="HI">HI</option><option value="IA">IA</option><option value="ID">ID</option><option value="IL">IL</option><option value="IN">IN</option><option value="KS">KS</option><option value="KY">KY</option><option value="LA">LA</option><option value="MA">MA</option><option value="MD">MD</option><option value="ME">ME</option><option value="MH">MH</option><option value="MI">MI</option><option value="MN">MN</option><option value="MO">MO</option><option value="MP">MP</option><option value="MS">MS</option><option value="MT">MT</option><option value="NC">NC</option><option value="ND">ND</option><option value="NE">NE</option><option value="NH">NH</option><option value="NJ">NJ</option><option value="NM">NM</option><option value="NV">NV</option><option value="NY">NY</option><option value="OH">OH</option><option value="OK">OK</option><option value="OR">OR</option><option value="PA">PA</option><option value="PR">PR</option><option value="PW">PW</option><option value="RI">RI</option><option value="SC">SC</option><option value="SD">SD</option><option value="TN">TN</option><option value="TX">TX</option><option value="UT">UT</option><option value="VA">VA</option><option value="VI">VI</option><option value="VT">VT</option><option value="WA">WA</option><option value="WI">WI</option><option value="WV">WV</option><option value="WY">WY</option></select>	
			<label for="bmonth">Date of Birth</label>
				<select name="bmonth" class="input-medium" id="Birth Month">
					<option>January</option>
					<option>February</option>
					<option>March</option>
					<option>April</option>
					<option>May</option>
					<option>June</option>
					<option>July</option>
					<option>August</option>
					<option>September</option>
					<option>October</option>
					<option>November</option>
					<option>December</option>
				</select>
				<select name="bday" class="input-mini" id="Birth Day">
					<?php
                                         for($i=1;$i<=31;$i++){
                                            echo '<option>'.$i.'</option>';
                                          }
                                         ?>	
				</select>
				<select name="byear" class="input-small" id="Birth Year">
                                <?php
                                for($i=2013;$i>1900;$i--){
                                    echo '<option>'.$i.'</option>';
                                }
                                ?>	
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