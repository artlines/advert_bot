<div style="display:inline;" id="tovar_paginator" class="pagination"></div>

<br style="clear:both;" />

<div id="tovar_list_container"></div>

<div style="display:inline;" id="tovar_paginator_down" class="pagination"></div>

<script>
$(function() {

  $("#tovar_paginator").pagination(<?=(int)$count;?>, {
    items_per_page:<?=(int)$limit;?>,
    prev_text:'<<',
    next_text:'>>',
    num_display_entries:10,
    num_edge_entries:2,
    callback:handlePaginationClick,
    link_to:'javascript:void(0)'
  });
  
  /*$("#tovar_paginator").clone(true, true).insertAfter($("#tovar_list_container"));*/

/*  $("#tovar_paginator_down").pagination(<?=(int)$count;?>, {
    items_per_page:<?=(int)$limit;?>,
    prev_text:'<<',
    next_text:'>>',
    num_display_entries:10,
    num_edge_entries:2,
    callback:handlePaginationClickDown,
    link_to:'javascript:void(0)'
  });*/

});

function handlePaginationClick(new_page_index, pagination_container) {
  $('#tovar_list_container').html(Loader);
  $('#tovar_list_container').load('/catalog/tovar/', {page:(new_page_index+1)});
  $('#tovar_list_container').find('a').attr('class', 'current');
  return;
}

/*function handlePaginationClickDown(new_page_index, pagination_container) {
  if (new_page_index==0) {
    return;
  }
  $('#tovar_list_container').html(Loader);
  $('#tovar_list_container').load('/catalog/tovar/', {page:(new_page_index+1)});
  return;
}*/

</script>
