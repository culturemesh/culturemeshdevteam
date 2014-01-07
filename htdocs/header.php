<div id="header">
    <div id="top-logo">
        <a href="index.php"><img src="images/logo_header.png"></a>
    </div>
    <div id="right-top-btns">
        <ul id="top-links">
            <li><a id="menu-about" href="about.php">About</a></li>
            <li><a id="menu-suggest" href="about.php">Suggest Networks</a></li>
            <li><a href="#login_modal" data-toggle="modal">Log In</a></li>
            <li><a href="#register_modal" data-toggle="modal">Sign Up</a></li>
        </ul>
    </div>
    <div class="clear"></div>
    <?php
        $header = '<h4 class="text-center">Join the</h4>
                    <h3 class="text-center">CultureMesh Community!</h3>';
        $body = '<div id="modcontent">
                <form id="reg_form" method="post" action="r.php">
                <img src="ajx/cm_loader_small.gif" id="email_loader" class="hide"/>
                <input type="email" id="email" name="email" placeholder="Email" required />
                
                <span id="email_dup_txt" class="label label-important">That email address is already registered. If you\'ve forgotten your password, <a href="#">click here.</a></span>
                <input type="password" name="password" placeholder="Password" required />
                <input type="password" name="password_conf" placeholder="Confirm Password" required />
                <span id="pass_mism_txt" class="label label-important">Passwords don\'t match. Please try again.</span>
                <input type="submit" id="reg_submit_btn" class="btn" data-loading-text="Checking..." value="Join Us" />
                    </form>
                    <script>
                    $("#email_dup_txt").hide();
                    $("#pass_mism_txt").hide();
                    $("#email").keyup(function(){
                        delay(function(){
                            if($("#email").val().length >= 0){
                            $("#email_loader").show();
                            $.post("ajx/ps.php",{"reg_email":$("#email").val()}, function(data){
                                $("#email_loader").hide(); // hide the spinner
                                if(data == "1"){
                                    $("#email_dup_txt").hide();
                                    $("#valid_email").show();
                                }
                                else{
                                    $("#email_dup_txt").show();
                                }
                                
                            });
                            }
                        }, 1000 );
                    });

                    /*$("#reg_sumbit_btn").click(function(){
                        $.post("ajax/ps.php", {"reg_sub":$("#reg_form").serialize()})
                        .done(function(data){
                            switch(data){
                                case "1":
                                    window.location("profile_settings.php");
                                    break;
                                case "2":
                                    $("#email_dup_txt").hide();
                                    break;
                            }
                        });
                    });*/
                    </script>
                    <hr>or
                    <a>Join with Facebook</a>
                    <br>It\'s fast and easy - and we\'ll never share your info or post without your permission, we promise!
                    </div>';//end modcontent div
        $footer = '<span class="text-center">Already a member? <a href="login.php" id="sign_in_mod">Sign in!</a></span>';
        echo buildModal($header, $body, $footer, "register_modal");
    ?>
</div>
