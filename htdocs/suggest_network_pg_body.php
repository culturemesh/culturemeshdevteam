<?php
if($_POST){
    $built_string = 'People ';
    foreach($_POST as $key=>$val):
        $val = ucwords(strtolower($val));
        if($key == 'suggest_language' && $val==true){
            if(!getRowQuery("SELECT * FROM suggested_networks WHERE language='$val'")){
                insertQuery("INSERT INTO suggested_networks (language, date) values('".$val."', ".time().")");
            }
            $built_string .= "\r\n" . ':who speak '.$val;
        }
        if($key == 'suggest_from_location' && $val==true){
            if(!getRowQuery("SELECT * FROM suggested_networks WHERE location='$val'")){
                insertQuery("INSERT INTO suggested_networks (location,date) values('".$val."', ".time().")");
            }
            $built_string .= "\r\n" . ':who are from '.$val;
        }
    endforeach;
    $se = new SiteEmail();
    $se->setSubject("Someone suggested a network!");
    $se->setTo("jenki221@gmail.com");
    /*$se->setFrom("CultureMesh Site Form <noreply@culturemesh.com>");
    $se->setReplyTo("<noreply@culturemesh.com>");*/
    $msg = 'Someone suggested the following networks:'. "\r\n" .$built_string;
    $se->setMessage($msg);
    $se->Send();
    echo '<span class="label label-success center-elem text-center">We\'ve received your suggestion. We\'ll look into adding it shortly!</span>'; 
    ?>
<?php } ?>
<script>
    $("#menu-suggest").addClass("active");
</script>
<style>
    h5{
        font:18px 'Lato';
        color: #333;
    }
    .suggest_box_form{
        margin-left: auto;
        margin-right: auto;
        width: 620px;
        background: #f5f5f5;
        padding: 30px;
    }
    .cm-red{
        color: #e34036;
    }
    .input_sub{
        font: 14px 'Lato';
        font-weight: 300;
        color: #666;
    }
    .small_head{
        font: 14px 'Lato';
        font-weight: 300;
        color: #666;
    }
    .suggest_box_form input[type=text]{
        width: 410px !important;
    }
    .suggest_box_form h5{
        display: inline-block;
        padding-right: 30px;
    }
    .input_with_sub{
        position:relative;
        left: 150px;
        bottom: 50px;
    }
</style>
<h3 class="text-center">Suggest Networks</h3>
<form method="post" action="" class="center-elem suggest_box_form">
    <label><h5>People <br><span class="cm-red">who speak</span></h5>
        <div class="input_with_sub">
        <input type="text" name="suggest_language" placeholder="Language"/>
        <br><span class="input_sub">Any spoken, written, or signed form of communication</span>
        </div>
    </label>
    <span class="small_head">-OR-</span>
    <label><h5>People <br><span class="cm-red">who are from</span></h5>
        <div class="input_with_sub">
        <input type="text" name="suggest_from_location" placeholder="Location"/>
        <br><span class="input_sub">Countries, states, provinces, cities, or regions</span>
        </div>
    </label>
    
    <input type="submit" class="cm-button center-elem" value="Submit" />
</form>