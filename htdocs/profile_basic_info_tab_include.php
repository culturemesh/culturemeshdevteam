<label class="label label-success hide" id="basic_info_update_success_txt">Information successfully updated!</label>
<label class="label label-important hide" id="basic_info_update_failure_txt">Update failed. Please try again.</label>
<form id="basic_info_form">
	<table>
	<thead></thead>
	<tfoot><tfoot>
	<tbody>
		<input type="hidden" name="bi_update" value="1"/>
		<tr class="dashboard">
			<td class="dashboard"><h3 class="dashboard">First Name:</h3></td>
			<td class="dashboard"><input class="dashboard" type="text" name="first_name" value="<?=getMemberFirstName($_SESSION['uid'])?>" placeholder="First Name"></td>
		</tr>
		<tr class="dashboard">
			<td class="dashboard"><h3 class="dashboard">Last Name:</h3></td>
			<td class="dashboard"><input class="dashboard" type="text" name="last_name" value="<?=getMemberLastName($_SESSION['uid'])?>" placeholder="Last Name"></td>
		</tr>
		<tr class="dashboard">
			<td class="dashboard"><h3 class="dashboard">Gender:</h3></td>
			<td class="dashboard">
			    <select class="dashboard" name="gender">
				<option <?php if(getMemberGender($_SESSION['uid']) == 'm'){echo 'selected';}?>>Male</option>
				<option <?php if(getMemberGender($_SESSION['uid']) == 'f'){echo 'selected';}?>>Female</option>
			    </select>
			</td>
		</tr>
		<tr class="dashboard">
			<td class="dashboard"><h3 class="dashboard">About Me:</h3></td>
			<td class="dashboard"><textarea class="dashboard" name="about_me" placeholder="Tell us about yourself..."><?=getMemberAboutMe($_SESSION['uid'])?></textarea></td>
		</tr>
	</tbody>
	</table>
	<a class="btn cm-button btn-gray dash" id="basic_info_cancel_btn">Cancel</a>
	<a class="btn cm-button btn-green dash" id="basic_info_update_btn">Update</a>
</form>

<script>
    $("#basic_info_update_btn").click(function(){
        $.post("ajx/ps.php", $("#basic_info_form").serialize())
        .done(function(data){
            if(data == "1"){
                $("#basic_info_update_success_txt").fadeIn();
                delay(function(){$("#basic_info_update_success_txt").fadeOut()}, 2000);
            }
        });
    });
</script>