</div>
</div>

<footer class="text-center" id="footer">&copy; Copyright 2019-2020 Andjelkine umotvorine</footer>

</div>

<script>
jQuery(window).scroll(function(){
  var vscroll = jQuery(this).scrollTop();
  jQuery('#logotext').css({ "transform": "translate(0px, "+vscroll/2+"px)"})
});

  // ne trebaju mi trenutno

  // var vscroll = jQuery(this).scrollTop();
  // jQuery('#back-flower').css({
  //   "transform" : "translate("+vscroll/5+"px,-"+vscroll/12+"px)"
  // });
  // var vscroll = jQuery(this).scrollTop();
  //    jQuery('#fore-svinja').css({
  //      "transform" : "translate(0px,-"+vscroll/2+"px)"
  //  });
// pali detaljnije i kupi iz baze sve sto mu treba iz baze
function detailsmodal(id){
 var data = { "id" : id };
 jQuery.ajax({
   url:'/andjelismrdenoge/includes/detailsmodal.php',
   method : 'post',
   data : data,
   success : function(data){
     jQuery('body').append(data);
     jQuery('#details-modal').modal('toggle');
   },
   error : function(){
     alert("details modal error");
   }
 });
}
// kupi pormenjive i baca na update_cart da ih o obradi
function update_cart(pedo,edit_id,edit_size){
  var data = {"pedo" : pedo,"edit_id" : edit_id, "edit_size" : edit_size};
  var burek = Object.entries(data);
  console.log(data);
  // debugger
  // var data =JSON.stringify(p);
  // alert(burek);
  jQuery.ajax({
    url : '/andjelismrdenoge/admin/parsers/update_cart.php',
    method : 'post',
    data : data,
    success : function(){
      // alert("koji kurac");
      location.reload();
    },
    error : function(){
      // alert("pojedi govno");
    },
  });
}

// puni kolica i proverava sta si uneo
// BUBA ako uneses broj tastaturom ne registruje else if ili ga vidi kao tacno ako uneses nisem onda je super
function add_to_cart(){
  // alert("works");
 jQuery('#modal_errors').html("");
 var size = jQuery('#size').val();
 var quantity = jQuery('#quantity').val();
 var available = jQuery('#available').val();
 var error = '';
 var data = jQuery('#add_product_form').serialize();
 if(size == '' || quantity == '' || quantity == 0){
   error += '<p class="text-danger text-center">Moras izabrati kolicinu i velicinu</p>';
   jQuery('#modal_errors').html(error);
   return;
 }else if(quantity > available){
   alert("big peepee");
   error += '<p class="text-danger text-center">Na lageru trenutno ima '+available+' artkala</p>';
   jQuery('#modal_errors').html(error);
   return;
 }else{
   jQuery.ajax({
     url : '/andjelismrdenoge/admin/parsers/add_cart.php',
     method : 'post',
     data : data,
     success : function(){
       location.reload();
     },
     error : function(){alert("add_to_cart error");}
   });
 }
}
</script>
</body>
</html>
