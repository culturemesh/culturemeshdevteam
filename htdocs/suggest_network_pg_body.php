<?php
if($_POST){
    print_r($_POST);?>
    <script>
        $("#suggest_success").show();
    </script>
<?php } ?>
<script>
    $("#menu-suggest").addClass("active");
</script>
<h3 class="text-center">Suggest Networks</h3>
<span id="suggest_success" class="label label-success text-center hide">We've received your suggestion. We'll look into adding it shortly!</span>
<form method="post" action="" class="center-elem">
    <label><h5>People <span class="cm-red">who speak</span></h5>
        <input type="text" name="suggest_language" placeholder="Language"/>
        Any spoken, written, or signed form of communication
    </label>
    -OR-
    <label><h5>People <span class="cm-red">who are from</span></h5>
        <input type="text" name="suggest_from_location" placeholder="Location"/>
        Countries, states, provinces, cities, or regions
    </label>
    -OR-
    <label><h5>People <span class="cm-red">who belong to</span></h5>
    <input type="text" name="suggest_culture" placeholder="Ethnicities or Religions"/>
    </label>
    
    <label><h5>People <span class="cm-red">who live in</span></h5>
        <input type="text" name="suggest_in_location" placeholder="Location"/>
        Countries, states, provinces, cities, or regions
    </label>
    
    <input type="submit" class="cm-button center-elem" value="Submit" />
</form>