<h2>Změna Loga</h2>
<?php echo form_open_multipart('admin/appearance/logoUpload','',$hidden) ?>

<div class="form-group">
    <label for="userfile">Logo:</label>
    <?php if(!empty($appearance) && $appearance['logo_path'] != NULL) { echo '<img src="'.base_url().$appearance['logo_path'].'" />'; } ?>
    <input name="userfile" type="file">
</div>

<input class="btn btn-default" type="submit" value="Potvrdit" />

</form>