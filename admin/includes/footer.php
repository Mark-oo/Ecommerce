</div>
</div>

 <footer class="text-center" id="footer">&copy;
   Copyright 2019-2020 Andjelkine umotvorine
 </footer>

</div>
<script>

// zatvaranje na x i na close za modal  kopirana iz detail modala
  function closeModal(){
    jQuery('#sizesModal').modal('hide');
  }

// izbor velicina
  function updateSizes(){
    var sizeString= '';
    for(var i=1;i<=12;i++){
      if(jQuery('#size'+i).val() !='') {
        sizeString += jQuery('#size'+i).val()+':'+jQuery('#quantity'+i).val()+':'+jQuery('#threshold'+i).val()+',';
      }
    }
    jQuery('#sizes').val(sizeString);
  }

// za pozivanje child kategorija zavisno koji parent se izabere
  function get_child_options(selected){
    if(typeof selected === 'undefined'){
      var selected = '';
    }

    var parentID = jQuery('#parent').val();
    jQuery.ajax({
      url: '/andjelismrdenoge/admin/parsers/child_categories.php',
      type: 'POST',
      data: {parentID : parentID, selected: selected},
      success: function(data){
        jQuery('#child').html(data);
      },
      error: function(){alert("Something went wrong with the child options")},
    });
  }
  jQuery('select[name="parent"]').change(function(){
    get_child_options();
  });
</script>
</body>
</html>
