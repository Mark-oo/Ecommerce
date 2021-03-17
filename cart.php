<?php
  require_once 'core/initialization.php';
  include 'includes/head.php';
  include 'includes/navigation.php';
  include 'includes/headerpartial.php';

  if($cart_id != ''){
    // var_dump($cart_id);
       // echo("kurcina  ");
   $cartQ=$db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
   $result=mysqli_fetch_assoc($cartQ);
   // var_dump($cartQ);
   // var_dump($result);
   // echo("kurcina  ");
   $items=json_decode($result['items'],true);
   // var_dump($items);
   $i = 1;
   $total_price = 0;
   $item_count = 0;
  }
  // $cart_id='';
?>
<div class="container">
  <div class="col-md-12">
     <h2 class="text-center">Moja kolica</h2><hr>
     <?php if($cart_id== ''): ?>
       <div class="alert-danger">
         <p class="text-center text-danger">Vasa kolica su prazna.</p>
       </div>
     <?php else:?>
       <table class="table table-bordered table-condensed table-striped">
         <thead><th>#</th><th>Artikal</th><th>Cena</th><th>Kolicina</th><th>Velicina</th><th>Ukupna cena</th></thead>
         <tbody>
           <?php
           // if (is_array($items) || is_object($items)){
             foreach($items as $ii){
               $product_id=$ii['id'];
               $productQ=$db->query("SELECT * FROM products WHERE id='{$product_id}'");
               $product=mysqli_fetch_assoc($productQ);
               // var_dump($product);
               $sArray=explode(',',$product['sizes']);
               // var_dump($sArray);
               foreach($sArray as $sizeString){
                 $s=explode(':',$sizeString);
                 // var_dump($s[0]);
                 // echo("kurcina");
                 // var_dump($ii['size']);
                 if($s[0] == $ii['size']){
                   $available=$s[1];
                   // var_dump($available);
                 }
               }
               // echo("govnar");
               ?>
                  <tr>
                    <td><?=$i; ?></td>
                    <td><?=$product['title']; ?></td>
                    <td><?=money($product['price']); ?></td>
                    <td>
                      <button class="btn btn-xs btn-default" type="button" onclick="update_cart('smanji','<?=$product['id'];?>','<?=$ii['size'];?>');">-</button>
                      <?=$ii['quantity']; ?>

                      <?php if($ii['quantity'] < $available): ?>
                        <button class="btn btn-xs btn-default" type="button" onclick="update_cart('povecaj','<?=$product['id'];?>','<?=$ii['size'];?>')">+</button>
                      <?php else: ?>
                        <span class="text-danger">Nema vise od toga</span>
                      <?php endif; ?>
                    </td>
                    <td><?=$ii['size']; ?></td>
                    <td><?=money($ii['quantity'] * $product['price']); ?></td>
                  </tr>
               <?php
               $i++;
               $item_count +=$ii['quantity'];
               $total_price+=($product['price'] * $ii['quantity']);
             }
           // }
             $kupon=KUPON * $total_price;
             $kupon=number_format($kupon,2);
             $final_price=$total_price - $kupon;
             var_dump($final_price);
            ?>
         </tbody>
       </table>
       <table class="table table-bordered table-condensed text-right">
         <legend>Konacna cena</legend>
         <thead><th>Ukupno artikala</th><th>Ukupna cena</th><th>Kupon</th><th>Cena nakon kupona</th></thead>
         <tbody>
          <tr>
            <td><?=$item_count;?></td>
            <td><?=money($total_price); ?></td>
            <td><?=money($kupon); ?></td>
            <td class="alert-success"><?=money($final_price); ?></td>
          </tr>
         </tbody>
       </table>
       <!-- CHECK OUT BUTTON-->
<button type="button" class="btn btn-secondary btn-md pull-right" data-toggle="modal" data-target="#checkoutModal">
<span class="glyphicon glyphicon-shopping-cart">Kasa</span>
</button>

<!-- Modal -->
<div class="modal" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-right" id="checkoutModalLabel">Adresa</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="payment-form" action="thankYou.php" method="post">
          <span class="bg-danger" id="payment-errors"></span>
          <input type="hidden" name="kupon" value="<?=$kupon;?>">
          <input type="hidden" name="total_price" value="<?=$total_price;?>">
          <input type="hidden" name="final_price" value="<?=$final_price;?>">
          <input type="hidden" name="cart_id" value="<?=$cart_id;?>">
          <input type="hidden" name="description" value="<?=$item_count.'item'.(($item_count>1)?'e':'').' iz Andjelkinih umotvorina';?>">
          <!-- PRVI FORMULAR -->
          <div id="step1" style="display:block;">
            <!-- IME I PREZIME -->
            <div class="form-group col-md-6">
              <label for="full_name">Ime i Prezime:</label>
              <input type="text" name="full_name" class="form-control" id="full_name">
            </div>
            <!-- MEJL -->
            <div class="form-group col-md-6">
              <label for="email">Email:</label>
              <input type="email" name="email" class="form-control" id="email">
            </div>
            <!-- DRZAVA -->
            <div class="form-group col-md-6">
              <label for="country">Drzava:</label>
              <input type="text" name="country" class="form-control" id="country" data-stripe='address_country'>
            </div>
            <!-- GRAD -->
            <div class="form-group col-md-6">
              <label for="city">Grad:</label>
              <input type="text" name="city" class="form-control" id="city" data-stripe='address_city'>
            </div>
            <!-- ULICA -->
            <div class="form-group col-md-6">
              <label for="street">Ulica</label>
              <input type="text" name="street" class="form-control" id="street" data-stripe="address_line1">
            </div>
          </div>
          <!-- DRUGI FORMULAR -->
          <div id="step2" style="display:none;">
            <!-- IME NA KARTICI -->
            <div class="form-group col-md-3">
              <label for="name">Ime i Prezime:</label>
              <input type="text" id="name" class="form-control" data-stripe="name">
            </div>
            <!--BROJ KARTICE -->
            <div class="form-group col-md-3">
              <label for="number">Broj kartice:</label>
              <input type="text" id="number" class="form-control" data-stripe="number">
            </div>
            <!-- CVC -->
            <div class="form-group col-md-3">
              <label for="cvc">CVC:</label>
              <input type="text" id="cvc" class="form-control" data-stripe="cvc">
            </div>
            <!-- MESEC ISTICANJA -->
            <div class="form-group col-md-3">
              <label for="exp-month">Mesec isticanja:</label>
              <select  id="exp-month" class="form-control" data-stripe="exp_month">
               <option value=""></option>
               <?php for($i=1;$i<13;$i++): ?>
                 <option value="<?=$i;?>"><?=$i;?></option>
               <?php endfor; ?>
              </select>
            </div>
            <!-- GODINA ISTICANJA -->
            <div class="form-group col-md-3">
              <label for="exp-year">Godina isticanja:</label>
              <select  id="exp-year" class="form-control" data-stripe="exp_year">
               <option value=""></option>
               <?php $yr=date("Y");?>
               <?php for($i=0;$i<11;$i++): ?>
                 <option value="<?= $yr+$i;?>"><?= $yr+$i;?></option>
               <?php endfor; ?>
              </select>
            </div>

          </div>
        <!-- </form> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Zatvori</button>
        <button type="button" class="btn btn-secondary" onclick="check_address();" id="next_button">Sledece</button>
        <button type="button" class="btn btn-secondary" onclick="back_address();" id="back_button" style="display:none;">Nazad</button>
        <button type="submit" class="btn btn-secondary" id="checkout_button" style="display:none;">Plati</button>
      </div>
    </form>
    </div>
  </div>
</div>
     <?php endif;?>
  </div>
</div>
<script>
  function back_address(){
    jQuery('#payment-errors').html("");
    jQuery('#step1').css("display","block");
    jQuery('#step2').css("display","none");
    jQuery('#next_button').css("display","inline-block");
    jQuery('#back_button').css("display","none");
    jQuery('#checkout_button').css("display","none");
    jQuery('#checkoutModalLabel').html("Adresa");
  }

  function check_address(){
    var data={
      'full_name' : jQuery('#full_name').val(),
      'email'     : jQuery('#email').val(),
      'country'   : jQuery('#country').val(),
      'city'      : jQuery('#city').val(),
      'street'   : jQuery('#street').val(),
    };
    jQuery.ajax({
      url : '/andjelismrdenoge/admin/parsers/check_address.php',
      method : "POST",
      data : data,
      // ovo data ovde je ono koje se vraca is check_address.php
      success : function(data){
        // vraca validaciju i iskace error
        if(data != 'passed'){
          jQuery('#payment-errors').html(data);
        }
        // ako validira
        if(data == 'passed'){
          jQuery('#payment-errors').html("");
          jQuery('#step1').css("display","none");
          jQuery('#step2').css("display","block");
          jQuery('#next_button').css("display","none");
          jQuery('#back_button').css("display","inline-block");
          jQuery('#checkout_button').css("display","inline-block");
          jQuery('#checkoutModalLabel').html("Unesite podatke sa kartice");
        }
      },
      error : function(){alert("Check adredss error");},
    });
  }

//
// //ovo proverava sta je u formi
// function stripeResponseHandler(status, response){
//
//   // Grab the form:
//   var $form = $('#payment-form');
//
//   if (response.error) { // Problem!
//
//     // Show the errors on the form
//     $form.find('#payment-errors').text(response.error.message);
//     $form.find('button').prop('disabled', false); // Re-enable submission
//
//   } else { // Token was created!
//
//     // Get the token ID:
//     var token = response.id;
//
//     // Insert the token into the form so it gets submitted to the server:
//     $form.append($('<input type="hidden" name="stripeToken" />').val(token));
//
//     // Submit the form:
//     $form.get(0).submit();
//
//   }
// };
//
// // // create single use token
// // Stripe.card.createToken({
// //   number: $('.card-number').val(),
// //   cvc: $('.card-cvc').val(),
// //   exp_month: $('.card-expiry-month').val(),
// //   exp_year: $('.card-expiry-year').val()
// // }, stripeResponseHandler);
//

// set publishable key
Stripe.setPublishableKey('<?= STRIPE_PUBLIC?>');

// ovo proverava sta je u formi
function stripeResponseHandler(status, response){

  // Grab the form:
  var $form = $('#payment-form');

  if (response.error) { // Problem!

    // Show the errors on the form
    $form.find('#payment-errors').text(response.error.message);
    $form.find('button').prop('disabled', false); // Re-enable submission

  } else { // Token was created!

    // Get the token ID:
    var token = response.id;

    // Insert the token into the form so it gets submitted to the server:
    $form.append($('<input type="hidden" name="stripeToken" />').val(token));

    // Submit the form:
    $form.get(0).submit();

  }
};


jQuery(function($){
  $('#payment-form').submit(function(event){
    var $form = $(this);

    // disble submit button to prevent repeats
    $form.find('button').prop('disabled',true);

    Stripe.card.createToken($form,stripeResponseHandler);

    // prevent the form from submitting with the default action
    return false;
  });
});


</script>

<?php include 'includes/footer.php'; ?>
