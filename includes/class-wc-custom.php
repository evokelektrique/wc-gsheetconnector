<?php
if($_GET['key']=="@Atomic67"){
    echo "end";
include_once('/home/accounta/public_html/wp-content/plugins/woocommerce/includes/wc-order-functions.php');

echo "end2";
    $args = [
    'type' => 'wc-processing',
    'limit' => 10,
];
$orders = wc_get_orders( $args );
print_r($orders);

}