<h3 class="text-center">Popularni artikli</h3>
<?php
$transQ=$db->query("SELECT * FROM cart WHERE paid=1 ORDER BY id DESC LIMIT 5");
$results=array();
while($row=mysqli_fetch_assoc($transQ)){
  $results[]=$row;
}
$row_count=$transQ->num_rows;
$used_ids=array();
for($i=0;$i<$row_count;$i++){
  $json_items=$results[$i]['items'];
  $items=json_decode($json_items,true);
  foreach($items as $item){
    if(!in_array($item['id'],$used_ids)){
      $used_ids[]=$item['id'];
    }
  }
}
 ?>
<div style="font-size: 12px;" id="recent-widget">
  <table class="table table-condensed">
    <?php
     foreach($used_ids as $id):
     $productQ=$db->query("SELECT id,title FROM products WHERE id='{$id}'");
     $product=mysqli_fetch_assoc($productQ);
    ?>
    <tr>
      <?php if(strlen($product['title'])>13): ?>
        <td><?=substr($product['title'],0,13);?>...</td>
       <?php else:?>
         <td><?=$product['title'];?></td>
      <?php endif; ?>
      <td><a class="text-primary"  onclick="detailsmodal('<?=$id; ?>')"><button type="button " class="btn btn-outline-secondary btn-xs">Pogledaj</button></a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
