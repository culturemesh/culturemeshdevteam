<div id="header">
    <div id="top-logo">
        <a href=""><img src="images/logo_header.png"></a>
    </div>
    <?php
        $header = 'Join the CultureMesh Community!';
        $body = '<form id="reg_form" method="post" action="r.php">
                <input type="email" name="email" placeholder="Email" required />
                <span id="email_dup_txt" class="label label-important">That email address is already registered. If you\'ve forgotten your password, <a href="#">click here.</a></span>
                <input type="password" name="password" placeholder="Password" required />
                <input type="password" name="password_conf" placeholder="Confirm Password" required />
                <span id="pass_mism_txt" class="label label-important">Passwords don\'t match. Please try again.</span>
                <input type="submit" id="reg_submit_btn" class="btn" data-loading-text="Checking..." value="Join Us" />
                    </form>
                    <script>
                    $("#email_dup_txt").hide();
                    $("#pass_mism_txt").hide();
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
                    <br>It\'s fast and easy - and we\'ll never share your info or post without your permission, we promise!';
        $footer = 'Already a member? <a href="#" id="sign_in_mod">Sign in!</a>';
        echo buildModal($header, $body, $footer, "register_modal");
    ?>
    <div id="right-top-btns">
        <ul id="top-links">
            <li><a id="menu-about" href="about.php">About</a></li>
            <li><a id="menu-suggest" href="about.php">Suggest Networks</a></li>
            <li><a href="#login_modal" data-toggle="modal">Log In</a></li>
            <li><a href="#register_modal" data-toggle="modal">Sign Up</a></li>
        </ul>
    </div>
</div>