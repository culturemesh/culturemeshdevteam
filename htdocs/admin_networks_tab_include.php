<style>
    #admin_add_network_inputs{
        display:inline;
    }
</style>
<script>
	
</script>
<div id="admin_add_network_inputs">
	<form id="admin_add_new_form" method="post">
		<label>Add New</label>
		<select name="admin_attr" id="admin_attr">
		    <option>City</option>
		    <option>Region</option>
		    <option>Country</option>
		    <option>Language</option>
		</select>
		<div id="admin_region_selector">

			<label>City Name</label>
			<input type="text" name="city_name">

			<label>Region ID</label>
			<input type="text" name="region_id">

			<label>Region Name</label>
			<input type="text" name="region_name">

			<label>Country ID</label>
			<input type="text" name="country_id">

			<label>Country Name</label>
			<input type="text" name="country_name">

<!--
			<label>Country</label>
			<select name="admin_network_country">
			    <?php //foreach(getCountries() as $country):?>
			    <option><?//=$country?></option>
			    <?php //endforeach; ?>
			</select>
-->
			<label>Population</label>
			<input type="text" name="population">

			<label>Latitude</label>
			<input type="text" name="latitude">

			<label>Longitude</label>
			<input type="text" name="longitude">
		</div>
		<div id="admin_region_selector" style='display:none;'>
			<label>Name</label>
			<input type="text" name="lang_name">

			<label>Country ID</label>
			<input type="text" name="country_id">

			<label>Country Name</label>
			<input type="text" name="country_name">

			<label>Population</label>
			<input type="text" name="population">

			<label>Latitude</label>
			<input type="text" name="latitude">

			<label>Longitude</label>
			<input type="text" name="longitude">
		</div>
		<div id="admin_country_selector" style='display:none;'>
			<label>Name</label>
			<input type="text" name="lang_name">

			<label>Population</label>
			<input type="text" name="population">

			<label>Latitude</label>
			<input type="text" name="latitude">

			<label>Longitude</label>
			<input type="text" name="longitude">
		</div>
		<div id="admin_language_selector" style='display:none;'>
			<label>Name</label>
			<input type="text" name="lang_name">

			<label>Number of Speakers (millions)</label>
			<input type="text" name="num_speakers">
		</div>

	</form>
	<button class="btn cm-button" id="admin_add_btn">Add</button>
	<script>
	    $("#admin_attr").change(function(){
	    if ($("#admin_attr").val() == "Language") {
		$("#admin_region_selector").hide();
		$("#admin_language_selector").show();
	    }
	    else{
		$("#admin_region_selector").show();
		$("#admin_language_selector").hide();
	    }
	    });
	    $("#admin_add_btn").click(function(){
		$.post("ps.php", $("#admin_add_new_form").serialize())
		.done(function(data){
		    if(data == "1"){
			refresh();
		    }
		});
	    });
	</script>
</div>

<ul class="nav nav-pills">
  <li class="active"><a href="#admin_regions" data-toggle="tab">Regions</a></li>
  <li><a href="#admin_languages" data-toggle="tab">Languages</a></li>
  <li><a href="#admin_suggested" data-toggle="tab">Suggested</a></li>
</ul>
<style>
    table.admin_panel{
        border: 1px solid #ccc;
        font: 18px 'Lato';
        font-weight:300;
        margin-left: auto;
        margin-right: auto;
        width:500px;
    }
    table.admin_panel th{
        background: #e5e5e5;
        text-align: center;
    }
    table.admin_panel tr{
        border: 1px solid #ccc;
    }
    table.admin_panel td{
        padding:15px;
    }
    ul.network_sections{
        list-style-type: none;
    }
    ul.network_sections li{
        display: inline-block;
    }
</style>
<!--
<div id="admin_networks_tabs" class="tab-content center-elem">
    <div class="tab-pane fade active in" id="admin_regions">
        <?php //include 'admin_panel_cities_table.php'?>
    </div>
    <div class="tab-pane fade in" id="admin_languages">
        <?php //include 'admin_panel_languages_table.php'?>
    </div>
    <div class="tab-pane fade in" id="admin_suggested">
        <?php //include 'admin_suggested_networks_include.php'; ?>
    </div>
    
    <script>
        $(this).find(".network_row").hover(function(){
            console.log("hovered");
            //$(".network_ul_actions").closest().show();
        }, function(){
            console.log("unhovered");
            //$(".network_ul_actions").closest().hide();
        });
    </script>
</div>
-->
