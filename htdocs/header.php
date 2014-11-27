<div id="header">
    <div id="top-logo">
        <a href="index.php"><img src="<?php echo HOME_PATH; ?>images/logo_header.png"></a>
    </div>
    <div id="right-top-btns">
        <ul id="top-links">
            <li><a id="menu-about" href="about.php">About</a></li>
            <!--<li><a id="menu-suggest" href="about.php">Suggest Networks</a></li>-->
            <li><a id="login-link" href="#login_modal" data-toggle="modal">Log In</a></li>
            <li><a id="register-link" href="#register_modal" data-toggle="modal">Sign Up</a></li>
            <li><a href="profile_edit.php" id="welcome">Welcome, <?php echo $user->first_name; ?></a></li>
            	<li><a href="#" id="sign-out" onclick="signOut();">Sign Out</a></li>
        </ul>
    </div>

<?php /*
    <?php
        $reg_header = 'Join the</br></br><b>CultureMesh Community!</b>';
        $reg_body = '<form id="reg_form" method="post" action="r.php">
        	<div id="register-div">
                <input type="email" id="reg-email" name="email" placeholder="Email" class="modal-text-input" required /></br>
                </br>
                <span id="email_dup_txt" class="label label-important">That email address is already registered. If you\'ve forgotten your password, <a href="#">click here.</a></span>
                <div id="login-passwords">
                <input type="password" name="password" id="password" class="modal-text-input-conf" placeholder="Password" onchange="validateInput(this, document.getElementById(\'password_validation\'), 18)" required />
                <input type="password" name="password_conf" id="password_conf" class="modal-text-input-conf" onchange="comparePasswordInput(this, document.getElementById(\'password\'), document.getElementById(\'password_validation\'))"placeholder="Confirm Password" required />
                <div class="clear"></div>
                <span id="password_validation"></span>
                <span id="server_error"></span>
                </div>
                </br>
                </br>
                <span id="pass_mism_txt" class="label label-important">Passwords don\'t match. Please try again.</span>
                <div class="clear"></div>
                <input type="submit" id="reg_submit_btn" class="submit" data-loading-text="Checking..." value="Join Us" />
                </div>
                </form>
                    <script>
			$("#email_dup_txt").hide();
			$("#pass_mism_txt").hide();

			$("#reg_submit_btn").click(function(event){
				event.preventDefault();

				
				var email = $("#reg-email").val();
				var password = $("#password").val();
				var password_conf = $("#password_conf").val();
				var datastring = "email=" + email + "&password=" + password + "&password_conf=" + password_conf;

				$.ajax({
				 type:"POST",
				 url:"r.php",
				 data: datastring,
				 success: function(data)
				 {
				    var res_data = jQuery.parseJSON(data)
				    $("#server_error").text(res_data["message"]);
				    // if successful
				    if (res_data["error"] == 5) {
					    if (document.URL.indexOf("network") == -1)
					      { window.location.assign("profile_edit.php"); }
					    $("#login_modal").modal("hide");
					    $("#login-link").hide();
					    $("#register-link").hide();
					    $("#welcome").show(); 
					    $("#welcome").text("Welcome, " + email);
					    $("#sign-out").show();
					    $(".guest").hide();
				    }
				 }
				});
			});
                    </script>
                    <!--<hr class="hr-modal" width="100"> or <hr class="hr-modal" width="100">-->
                    </br>
                    <!--<button class="submit-fbk">Join with Facebook</button>-->
                    <br>
                    <p>It\'s fast and easy - and we\'ll never share your info or post without your permission, we promise!</p>';
        $reg_footer = 'Already a member? <a href="#" id="sign_in_mod">Sign in!</a>';
        
        $log_header = '<b>Welcome Back!</b>';
        $log_body = '<form id="log_form" method="post" action="l.php">
                <div id="login-div">
                <input type="email" name="email" id="log_email" class="modal-text-input" placeholder="Email" required />
                <input type="password" name="password" id="log_password" class="modal-text-input" onchange="validateInput(this, document.getElementById(\'log_validation\'))" placeholder="Password" required />
                </br>
                <span id="log_validation"></span>
                </br>
                </br>
                <input type="submit" id="log_submit_btn" class="submit" data-loading-text="Checking..." value="Sign In" />
                </div>    
                </form>
                    <script>
			/*
                    $("#pass_mism_txt").hide();
                    
                    $("#log_submit_btn").click(function(event){
                        event.preventDefault();
                        var email = $("input#log_email").val();
                        var password = $("input#log_password").val();
                        var datastring = "email=" + email + "&password=" + password;
                        
                        $.ajax({
                         type:"POST",
                         url:"l.php",
                         data: datastring,
                         success: function(data)
                         {
                            var res_data = jQuery.parseJSON(data);
                            
                            switch(res_data.error){
                                case null:
				    if (document.URL.indexOf("network") == -1)
			      	      { window.location.assign("profile_edit.php?confirm=true"); }
                                    $("#login_modal").modal("hide");
                                    $("#login-link").hide();
                                    $("#register-link").hide();
                                    $("#welcome").show(); 
                                    $("#welcome").text("Welcome, " + res_data.title);
                                    $("#sign-out").show();
                                    $(".guest").hide();
                                    
                                    if (res_data.member === true)
                                    	$(".member").show();
                                    else
                                    	$(".reg-guest").show();
                                    	
				    if ("events" in window) {
					    // get user\'s events
					    var userEvents = res_data.events;

					    	for (var i = 0; i < events.length; i++) {
							$("#join-event-form-"+events[i].id).show();
						}
						for (var i = 0; i < userEvents.length; i++) {
							// hide if joined
							$("#join-event-form-"+userEvents[i].id_event).hide();
							$("#attending_div-" + userEvents[i].id_event).show();
						}
					    }

                                    break;
                                case 2:
                                    $("#log_validation").text("Your username/password combination is incorrect");
                                    break;
                                case 3:
                                    $("#log_validation").text("Your email is too long.");
                                    break;
                                case 4:
                                    $("#log_validation").text("Your password is too long.");
                                    break;
                                case 5:
                                    $("#log_validation").text("Our servers are misbehaving. Come back later.");
                                    break;
                                default:
                                    $("#log_validation").text("Something has gone wrong. Try again later.");
                                    //$("#login_modal").modal("hide");
                                    break;
                                }
                          },
                          error: function(jqXHR, exception)
                          {
                              //alert(jqXHR.status + "\n" + exception);
			      $("#log_validation").text("We\'re having server troubles. Try again later.");
                          }
                        });
                    });
                    </script>
                    <!--<hr class="hr-modal" width="100"> or <hr class="hr-modal" width="100">-->
                    <!--<button class="submit-fbk">Join with Facebook</button>-->
		    <a href="forgotpass.php">Forgot your password?</a>
                    <br><p>It\'s fast and easy - and we\'ll never share your info or post without your permission, we promise!</p>';
        $log_footer = 'Not a member yet? <a href="#" id="sign_in_mod">Join Us!</a>';
        
        echo buildModal($reg_header, $reg_body, $reg_footer, "register_modal");
        echo buildModal($log_header, $log_body, $log_footer, "login_modal");

		*/
    ?>
		<div id="register_modal" class="modal hide fade" tabindex="-1" role="dialog"  aria-labelledby="blogPostLabel" aria-hidden="true">
			<div class="modal-header">
				Join the</br></br><b>CultureMesh Community!</b>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#10006</button>
			</div>
			<div class="modal-body">
				<form id="reg_form" method="post" action="r.php">
					<div id="register-div">
						<input type="email" id="reg-email" name="email" placeholder="Email" class="modal-text-input" required /></br>
						</br>
						<input type="text" name="fname" id="fname" class="modal-text-input" placeholder="First Name" required /></br>
						</br>
						<input type="text" name="lname" id="lname" class="modal-text-input" placeholder="Last Name" required /></br>
						</br>
						<span id="email_dup_txt" style="display:none" class="label label-important"></span>
						<div id="login-passwords">
						<input type="password" name="password" id="password" class="modal-text-input-conf" placeholder="Password" onchange="validateInput(this, document.getElementById(\'password_validation\'), 18)" required />
						<input type="password" name="password_conf" id="password_conf" class="modal-text-input-conf" onchange="comparePasswordInput(this, document.getElementById(\'password\'), document.getElementById(\'password_validation\'))"placeholder="Confirm Password" required />
						<div class="clear"></div>
						<span id="password_validation"></span>
						<span id="server_error"></span>
						</div>
						</br>
						</br>
						<span id="pass_mism_txt" class="label label-important"></span>
						<div class="clear"></div>
						<input type="submit" id="reg_submit_btn" class="submit" data-loading-text="Checking..." value="Join Us" />
					</div>
				</form>
			</div>

			<div class="modal-footer">
				Already a member? <a href="#" onclick="toggleLogin()" id="sign_in_mod">Sign in!</a>
			</div>
		</div>
		<div id="login_modal" class="modal hide fade" tabindex="-1" role="dialog"  aria-labelledby="blogPostLabel" aria-hidden="true">
			<div class="modal-header">
				<b>Welcome Back!</b>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#10006</button>
			</div>
			<div class="modal-body">
				<form id="log_form" method="post" action="l.php">
					<div id="login-div">
						<input type="email" name="email" id="log_email" class="modal-text-input" placeholder="Email" required />
						<input type="password" name="password" id="log_password" class="modal-text-input" onchange="validateInput(this, document.getElementById(\'log_validation\'))" placeholder="Password" required />
						</br>
						<span id="log_validation"></span>
						</br>
						</br>
						<input type="submit" id="log_submit_btn" class="submit" data-loading-text="Checking..." value="Sign In" />
						</br>
						</br>
						<a href="forgotpass.php">Forgot your password?</a>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				Not a member yet? <a href="#" id="sign_in_mod" onclick='toggleRegister()'>Join Us!</a>
			</div>
		</div>
		<script>
			function toggleLogin() {
				$("#register_modal").modal('hide');
				$("#login_modal").modal('show');
			}
			function toggleRegister() {
				$("#login_modal").modal('hide');
				$("#register_modal").modal('show');
			}
		</script>
		<?php if(isset($_GET['lerror']) && $_GET['lerror'] != 'success') : ?>
		<script>
			var teddy = new QueryStringParser();
			$("#login_modal").modal('show');
			$('#log_validation').text(teddy.qsGet['lerror']);
		</script>
		<?php endif; ?>
		<?php if(isset($_GET['rerror']) && $_GET['rerror'] != 'success') : ?>
		<script>
			var teddy = new QueryStringParser();
			$("#register_modal").modal('show');
			$('#server_error').text(teddy.qsGet['rerror']);
		</script>
		<?php endif; ?>
	<div class="clear"></div>
</div>
<div id="signout_panel" style="display:none">
	<p>You have successfully signed out.</p>
</div>
