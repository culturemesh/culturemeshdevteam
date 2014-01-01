<form id="msg_compose_form">
    <input type="text" data-provide="typeahead" data-items="4" data-source="<?=getJSCompatibleArrayFromDBData(getMemberNames())?>" name="msg_to" placeholder="Name" />
    <textarea placeholder="Write a message..."></textarea>
    <a href="#<?=MSG_MODAL_PICTURE_ATTACH_ID?>"><i class="icon icon-camera"></i></a>
    <a href="#<?=MSG_MODAL_VIDEO_ATTACH_ID?>"><i class="icon icon-facetime-video"></i></a>
    <button class="btn cm-button cm-button-small">Post</button>
</form>