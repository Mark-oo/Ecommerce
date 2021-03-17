<?php
define('BASEURL',$_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/');

define('CART_COOKIE','vSQyqjwDEdG3');
define('CART_COOKIE_EXPIRE',time() + (86400*30));
define('KUPON',0.010);

define('CURRENCY','usd');
define('CHECKOUTMODE','TEST'); #Change TEST to LIVE to go LIVE ;)

if(CHECKOUTMODE == 'TEST'){
  define('STRIPE_PRIVATE','sk_test_51GuhmiKpPm5E47u4goVoEQZMaeS6p18C0a4bnd87MxHBNg82eqspr4rX0ykMpbL4W1YryyFS0PdnoozVZMMoSKMV00RTcSh7V0');
  define('STRIPE_PUBLIC','pk_test_51GuhmiKpPm5E47u4WzEjJ5pMdK7oPELQ2RnVJ7wwtQ3t1ImLrVuc6t4R3ANtzyjEo5a0UTorvNhmP6VLb8VyZoAi00YDvy5Zg7');
}
if(CHECKOUTMODE == 'LIVE'){
  define('STRIPE_PRIVATE','');
  define('STRIPE_PUBLIC','');
}
?>
