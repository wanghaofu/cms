<?php echo   '<script src='.INCLUDE_PATH.'/ckeditor_4.6/ckeditor.js></script>'?>
<textarea name="data_<?php echo $var["FieldName"] ?>"
	id="data_<?php echo $var["FieldName"] ?>" rows="10" cols="80">
               <?php  echo htmlspecialchars($pInfo[$var["FieldName"]]) ?>
            </textarea>
<script>

CKEDITOR.replace( 'data_<?php echo $var["FieldName"] ?>', {
	filebrowserBrowseUrl: <?php echo "'./admin_select.php?sId={$IN['sId']}&o=psn_picker&psn='"?>,
	filebrowserUploadUrl: <?php echo "'./upload_cke.php?sId={$IN['sId']}&type=img&o=upload&mode=one&NodeID={$IN['NodeID']}'" ?>,
	filebrowserWindowWidth : "760",
    filebrowserWindowHeight : "266",
    filebrowserImageBrowseLinkUrl : false,
    filebrowserFlashUploadUrl : <?php echo "'./upload_cke.php?sId={$IN['sId']}&type=flash&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}'" ?>,
    filebrowserAttachfileUploadUrl : <?php echo "'./upload_cke.php?sId={$IN['sId']}&type=attach&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}'" ?> 
});




</script>


