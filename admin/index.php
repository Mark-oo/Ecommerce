<?php
  require_once '../core/initialization.php';
  if(!is_logged_in()){
     header('Location: login.php');
  }

  include 'includes/head.php';
  include 'includes/navigation.php';
    // session_destroy();
?>
  <!-- orders to fill -->
  <?php
  $txnQuery="SELECT t.id, t.cart_id, t.full_name, t.description, t.txn_date, t.final_price, c.items, c.paid, c.shipped
             FROM transactions t
             LEFT JOIN cart c
             ON t.cart_id=c.id
             WHERE c.paid = 1 AND c.shipped=0
             ORDER BY t.txn_date";
  $txnResults = $db->query($txnQuery);

   ?>
  <div class="col-md-12">
    <h3 class="text-center">Narudzbine za slanje</h3><hr>
    <table class="table table-condensed table-bordered table-striped">
     <thead>
       <th></th><th>Ime</th><th>Opis</th><th>Ukupno</th><th>Datum</th>
     </thead>
     <tbody>
       <?php while($order=mysqli_fetch_assoc($txnResults)): ?>
         <tr>
           <td><a href="orders.php?txn_id=<?=$order['id'];?>" class="btn btn-xs btn-info">Info</a></td>
           <td><?=$order['full_name'];?></td>
           <td><?=$order['description']; ?></td>
           <td><?=money($order['final_price']); ?></td>
           <td><?=pretty_date($order['txn_date']);?></td>
         </tr>
       <?php endwhile; ?>
     </tbody>
    </table>
  </div>

  <div class="row">
    <!-- MONTHLY SALES -->
    <?php
      $thisYr=date("Y");
      $lastYr=$thisYr-1;
      $thisYrQ=$db->query("SELECT final_price,txn_date FROM transactions WHERE YEAR(txn_date)='{$thisYr}'");
      $lastYrQ=$db->query("SELECT final_price,txn_date FROM transactions WHERE YEAR(txn_date)='{$lastYr}'");
      $current=array();
      $last=array();
      $currentTotal=0;
      $lastTotal=0;
      while($x=mysqli_fetch_assoc($thisYrQ)){
        $month=date("m",strtotime($x['txn_date']));
        if(!array_key_exists($month,$current)){
          $current[(int)$month]=$x['final_price'];
        }else{
          $current[(int)$month]+=$x['final_price'];
        }
        $currentTotal+=$x['final_price'];
      }
      while($y=mysqli_fetch_assoc($lastYrQ)){
        $month=date("m",strtotime($y['txn_date']));
        if(!array_key_exists($month,$last)){
          $last[(int)$month]=$y['final_price'];
        }else{
          $last[(int)$month]+=$y['final_price'];
        }
        $lastTotal+=$y['final_price'];
      }
     ?>
    <div class="col-md-4">
      <h3 class="text-center">Mesecna prodaja</h3>
      <table class="table table-bordered table-striped table-condensed">
        <thead>
          <th></th>
          <th><?=$lastYr;?></th>
          <th><?=$thisYr;?></th>
        </thead>
        <tbody>
          <?php for($i=1;$i<=12;$i++):
                $dt=DateTime::createFromFormat('!m',$i);
             ?>
            <tr <?=((date("m") == $i)?' class="info"':'');?>>
              <td><?=$dt->format("F");?></td>
              <td><?=((array_key_exists($i,$last))?money($last[$i]): money(0));?></td>
              <td><?=((array_key_exists($i,$current))?money($current[$i]): money(0));?></td>
            </tr>
          <?php endfor; ?>
        </tbody>
        <tr>
          <td>Ukupno</td>
          <td><?=money($lastTotal);?></td>
          <td><?=money($currentTotal);?></td>
        </tr>
      </table>
    </div>
    <!-- IVENTORY -->
    <?php
      $iQuery=$db->query("SELECT * FROM products WHERE deleted=0");
      $lowItems=array();
      while($product = mysqli_fetch_assoc($iQuery)){
        $item=array();
        $sizes=sizesToArray($product['sizes']);
        foreach($sizes as $size){
          if($size['quantity']<=$size['threshold']){
            $cat=get_category($product['categories']);
            $item=array(
              'title'    =>$product['title'],
              'size'     =>$size['size'],
              'quantity' =>$size['quantity'],
              'threshold'=>$size['threshold'],
              'category' =>$cat['parent'].' ~ '.$cat['child'],
            );
            $lowItems[]=$item;
          }
        }
      }
     ?>
    <div class="col-md-8">
      <h3 class="text-center">Kniticno stanje</h3>
      <table class="table table-condensed table-stripped table-bordered">
        <thead>
          <th>Porizvod</th>
          <th>Kategorija</th>
          <th>Velicina</th>
          <th>Kolicina</th>
          <th>Donja granica</th>
        </thead>
        <tbody>
          <?foreach($lowItems as $item):?>
           <tr <?=(($item['quantity']==0)?' class="danger"':''); ?>>
            <td><?=$item['title'];?></td>
            <td><?=$item['category'];?></td>
            <td><?=$item['size'];?></td>
            <td><?=$item['quantity'];?></td>
            <td><?=$item['threshold'];?></td>
           </tr>
          <?endforeach;?>
        </tbody>
      </table>
    </div>
  </div>



 <?php include 'includes/footer.php'; ?>
