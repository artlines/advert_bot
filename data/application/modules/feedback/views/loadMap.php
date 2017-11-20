<iframe 
    frameborder="no" 
    style="border: 1px solid #a3a3a3; box-sizing: border-box;" 
    width="100%" 
    height="400" 
    src="<?='http://widgets.2gis.com/widget?type=firmsonmap&options=' . 
      urlencode('{"pos":{"lon":"' . $page->current_city_shop->lon . '","lat":"' . $page->current_city_shop->lat . '","zoom":"15"},' .
        '"opt":{"ref":"hidden","card":["name"],"city":"' . $page->current_city_shop->city_translite . '"},' .
        '"org":"' . $page->current_city_shop->dgis_firm_id . '"}');?>">
</iframe>