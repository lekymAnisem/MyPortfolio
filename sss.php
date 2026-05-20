
<?php
if (isset($_POST['apply'])){
$file_name = $_FILES['files'];
  $file_type = $_FILES['files']['type'];
    $file_size = $_FILES['files']['size'];
   $file_tem_loc = $_FILES['files']['tmp_name'];
  $file_store = "upload/" .$file_name;
    move_uploaded_file($file_tem_loc, $file_store);
  
}
?>
<form method="post" enctype="multipart/form-data">
<div class="form-group">
                  <label class="form-label" for="subject">Requirements</label>
                  <input type="file" class="form-control" id="subject" name="files"  tabindex="3"required>
			  </div>
              <div class="text-center">
                    <button type="submit" name="apply" class="btn btn-primary" id="button">Apply</button>

              </div>
</form>