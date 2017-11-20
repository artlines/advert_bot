<a id="banner_container_<?=$banner->alias;?>" class="flash-replaced" href="<?=$banner->link;?>">
  <script>
  $("#banner_container_<?=$banner->alias;?>").flash({src: '<?=$banner->filename;?>', width: <?=$banner->width;?>, height: <?=$banner->height;?>});
  </script>
</a>