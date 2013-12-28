<form id="">
<label>First Name
    <input type="text" name="first_name" value="<?=getMemberFirstName($_SESSION['uid'])?>" placeholder="First Name">
</label>
<label>Last Name
    <input type="text" name="last_name" value="<?=getMemberLastName($_SESSION['uid'])?>" placeholder="Last Name">
</label>
<label>Email
    <input type="email" name="email" value="<?=getMemberEmail($_SESSION['uid'])?>" placeholder="Email Address">
</label>
<label>About Me
    <textarea name="about_me" value="<?=getMemberAboutMe($_SESSION['uid'])?>" placeholder="Tell us about yourself..."></textarea>
</label>
</form>