<table class="vcard">
  <tr>
    <td><b>Телефон:</b></td>
    <td class="tel"><?=$phone2;?></td>
  </tr>
  <tr>
    <td><b>E-mail:</b></td>
    <td class="email"><a href="mailto:<?=$email;?>"><?=$email;?></a></td>
  </tr>
  <? if ( mb_strlen($address) > 5 ):?>
  <tr>
    <td><b>Адрес:</b></td>
    <td class="adr"><?=$address;?></td>
  </tr>
  <? endif;?>
  <tr>
    <td><b>Часы работы:</b></td>
    <td class="adr"><?=$worktime;?></td>
  </tr>
</table>

<br /><br />

<? if ( mb_strlen($address) > 5 ):?>
<a name="map"></a>
<script src="http://api-maps.yandex.ru/1.1/index.xml?key=<?=$mapKey;?>" type="text/javascript"></script>
<div style="width:100%;height:400px" id="YMapsID">&nbsp;</div>
<script type="text/javascript">
  window.onload = function () {
    var map = new YMaps.Map(document.getElementById("YMapsID"));
    map.setCenter(new YMaps.GeoPoint(60.615072, 56.83975), 15);
    map.enableRuler();
    map.enableScrollZoom();
    map.addControl(new YMaps.TypeControl());
    map.addControl(new YMaps.ToolBar());
    map.addControl(new YMaps.Zoom());
    map.addControl(new YMaps.ScaleLine());
    var placemark_1 = new YMaps.Placemark(new YMaps.GeoPoint(60.615072, 56.83975));
    placemark_1.description = "<?=$address;?>";
    map.addOverlay(placemark_1);
  }
</script>
<br /><br />
<? endif;?>