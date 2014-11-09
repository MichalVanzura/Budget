<h2>Nastavení vzhledu</h2>
<?php echo form_open('admin/appearance/colors','',$hidden) ?>

<div class="form-group">
    <label for="headercolor">Barva hlavičky:</label>
    <input name="headercolor" 
           id="headercolor" 
           class="form-control minicolors-input" 
           type="text" 
           value="<?php if(!empty($appearance)) { print_r($appearance['header_color']); } ?>">
</div>

<input class="btn btn-default" type="submit" value="Potvrdit" />

</form>
<script>
    $.minicolors.defaults.theme = 'bootstrap';
    $('.minicolors-input').minicolors();
</script>