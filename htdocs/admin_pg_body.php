<form id="admin_add_new_form" method="post">
<label>Add New</label>
<select name="admin_attr" id="admin_attr">
    <option>Country</option>
    <option>State</option>
    <option>City</option>
    <option>Language</option>
</select>
<label>Name</label>
<input type="text" name="admin_attr_name">

<div id="us_state_selector" style="display:none;">
<label>State</label>
<select class="input-mini" name="attr_city_state">
    <?php foreach(getStates() as $state): ?>
    <option><?=strtoupper($state);?></option>
    <?php endforeach; ?>
</select>
<input type="text" name="network_country" value="United States" readonly="true"/>
</div>

</form>
<button class="btn cm-button" id="admin_add_btn">Add</button>
<script>
    $("#admin_attr").change(function(){
    if ($("#admin_attr").val() == "City") {
        $("#us_state_selector").show();
    } 
    else {
        $("#us_state_selector").hide();
    }
    });
    $("#admin_add_btn").click(function(){
        $.post("ajx/ps.php", $("#admin_add_new_form").serialize())
        .done(function(data){
            if(data == "1"){
                $("#admin_add_new_form").find(":input").val("");
            }
        });
    });
</script>

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
    }
    table.admin_panel th{
        background: #e5e5e5;
        text-align: center;
    }
    table.admin_panel tr{
        border: 1px solid #ccc;
    }
    ul.network_sections{
        list-style-type: none;
    }
    ul.network_sections li{
        display: inline-block;
    }
</style>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade active in" id="admin_regions">
        <ul class="network_sections">
            <li class="thirdbox">
                <?php include 'admin_panel_countries_table.php'?>
            </li>
            <li class="thirdbox">
                <?php include 'admin_panel_cities_table.php'?>
            </li>
            
        </ul>
    </div>
    <div class="tab-pane fade in" id="admin_languages">
        <?php //include 'admin_languages_include.php'; ?>
    </div>
    <div class="tab-pane fade in" id="admin_suggested">
        <?php include 'admin_suggested_networks_include.php'; ?>
    </div>
    
    <script>
        $(".network_row").closest().hover(function(){
            $(".network_ul_actions").closest().show();
        }, function(){
            $(".network_ul_actions").closest().hide();
        });
    </script>
</div>