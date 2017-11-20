<?=$text;?>
<?=in_bs_button('category-option-save', 'Сохранить');?>
<hr class="clear" />
<script>
  $("#category-option-save").click(function () {
    saveCategoryValue();
  });
</script>