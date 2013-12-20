<style>
    .network_ul_actions li{
        display: inline;
    }
</style>
<ul class="networks">
<?php 
    echo buildAdminEditNetworkModal();
    echo buildAdminRemoveNetworkModal();
    foreach(getSuggestedNetworks() as $index=>$network): 
    //if network location is null, then it must be a language since those are the only 2 choices
    if($network['location'] != NULL){
        $network_type = 'location';
        $displayed_network = $network['location'];
    }
    else{
        $network_type = 'language';
        $displayed_network = $network['language'];
    }?>
    <li>
        <?=$displayed_network;?>
        <ul class="network_ul_actions">
            <li><a data-toggle="modal" href="#admin_edit_network_modal" id="admin_edit_network<?=$index?>" title="Edit"><i class="icon icon-pencil"></i></a></li>
            <li><a data-toggle="modal" href="#" id="confirm_network_btn" title="Approve"><i class="icon icon-ok"></i></a></li>
            <li><a data-toggle="modal" href="#admin_remove_network_modal" id="admin_remove_network<?=$index?>" title="Remove"><i class="icon icon-remove"></i></a></li>
            <script>
                $("#admin_edit_network<?=$index?>").click(function(){
                   $("#emodal_network_name").html("<?=$displayed_network?>"); 
                   $("#admin_add_network_input").val("<?=$displayed_network?>"); 
                   $("#emodal_network_type").html("<?=$network_type?>"); 
                });
                $("#admin_remove_network<?=$index?>").click(function(){
                   $("#rmmodal_network_name").html("<?=$displayed_network?>"); 
                   $("#rmmodal_network_type").html("<?=$network_type?>"); 
                });
            </script>
        </ul>
    </li>
<?php endforeach; ?>
</ul>
