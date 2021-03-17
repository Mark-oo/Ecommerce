<h3 class="text-center"> Vasa Kolica</h3>
<div>
  <?php if(empty($cart_id)) : ?>
    <p>Vasa kolica su prazna.</p>
  <?php else:
    $cartQ=$db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
    $results=mysqli_fetch_assoc($cartQ);
    $items=json_decode($results['items'],true);
    $sub_total=0;
  ?>
  <table class="table table-condensed" style="font-size: 12px;"  id="cart_widget">
    <tbody>
      <?php
        foreach($items as $item):
        $productQ=$db->query("SELECT * FROM products where id={$item['id']}");
        $product=mysqli_fetch_assoc($productQ);
      ?>
      <tr>
        <td><?=$item['quantity'];?></td>
         <?php if(strlen($product['title'])>13): ?>
           <td><?=substr($product['title'],0,13);?>...</td>
          <?php else:?>
            <td><?=$product['title'];?></td>
         <?php endif; ?>
        <td><?=money($item['quantity']*$product['price']);?></td>
      </tr>
      <?php
       $sub_total +=($item['quantity'] * $product['price']);
       endforeach;
      ?>
      <tr>
        <td></td>
        <td>Cena:</td>
        <td><?=money($sub_total); ?></td>
      </tr>
    </tbody>
  </table>
  <a href="cart.php" class="btn btn-xs btn-secondary pull-right">Vasa kolica</a>
  <div class="clearfix"></div>
  <?php endif; ?>
</div>
