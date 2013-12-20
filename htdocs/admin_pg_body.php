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

<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade active in" id="admin_regions">
        <ul>
            <li class="thirdbox">Country</li>
            <li class="thirdbox">State</li>
            <li class="thirdbox">
                <table>
                    <th>City</th>
                    <?php foreach(getNetworkCities() as $city): ?>
                        <tr><td><?=$city['city'];?></td></tr>
                    <?php endforeach; ?>
                </table>
            </li>
            
        </ul>
    </div>
    <div class="tab-pane fade in" id="admin_languages">
      <p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit. Keytar helvetica VHS salvia yr, vero magna velit sapiente labore stumptown. Vegan fanny pack odio cillum wes anderson 8-bit, sustainable jean shorts beard ut DIY ethical culpa terry richardson biodiesel. Art party scenester stumptown, tumblr butcher vero sint qui sapiente accusamus tattooed echo park.</p>
    </div>
    <div class="tab-pane fade in" id="admin_suggested">
      <p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit. Keytar helvetica VHS salvia yr, vero magna velit sapiente labore stumptown. Vegan fanny pack odio cillum wes anderson 8-bit, sustainable jean shorts beard ut DIY ethical culpa terry richardson biodiesel. Art party scenester stumptown, tumblr butcher vero sint qui sapiente accusamus tattooed echo park.</p>
    </div>
</div>