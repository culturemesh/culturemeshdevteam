<table class="admin_panel">
    <th>City</th>
    <?php foreach(getNetworkCities() as $city): ?>
        <tr class="network_row">
            <td>
                <?=$city['city'];?>
                <ul class="network_ul_actions hide">
                    <li><a data-toggle="modal" href="#admin_edit_network_modal" id="admin_edit_network<?=$index?>" title="Edit"><i class="icon icon-pencil"></i></a></li>
                    <li><a data-toggle="modal" href="#" id="confirm_network_btn" title="Approve"><i class="icon icon-ok"></i></a></li>
                    <li><a data-toggle="modal" href="#admin_remove_network_modal" id="admin_remove_network<?=$index?>" title="Remove"><i class="icon icon-remove"></i></a></li>
                    <script>
                        $("#admin_edit_network<?=$index?>").click(function(){
                           $("#modal_network_name").html("<?=$displayed_network?>"); 
                           $("#admin_add_network_input").val("<?=$displayed_network?>"); 
                           $("#modal_network_type").html("<?=$network_type?>"); 
                        });
                        $("#admin_remove_network<?=$index?>").click(function(){
                           $("#modal_network_name").html("<?=$displayed_network?>"); 
                           $("#modal_network_type").html("<?=$network_type?>"); 
                        });
                    </script>
                </ul>
            </td></tr>
    <?php endforeach; ?>
</table>