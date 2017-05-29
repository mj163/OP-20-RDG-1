<?php	
@$country = ($_POST['country'] != "") ? sanitize_text_field($_POST['country']) : "";
@$state = ($_POST['state']!= "") ? sanitize_text_field($_POST['state']) : "";
@$beds = ($_POST['beds']!= "") ? intval($_POST['beds']) : "";
@$status = (isset($_POST['status'])) ? sanitize_text_field($_POST['status']) : "";
@$type = (isset($_POST['type'])) ? sanitize_text_field($_POST['type']) : "";

@$amount = $_POST['amount'];
@$amount = explode("-",$amount);
// var_dump($amount);
$min = intval(trim($amount[0]));
$max = intval(trim($amount[1]));

$metaquery = array("relation" => "AND");

if(!empty($country))
{
	$metaquery[] = array(
						'key' => '1_emgtfld_country',
						'value' => $country,
						'compare' => 'LIKE',
						);
}
if(!empty($city))
{
	$metaquery[] = array(
						'key' => '1_emgtfld_state',
						'value' => $city,
						'compare' => '=',
						);
}
if(!empty($beds))
{
	$metaquery[] = array(
						'key' => '2_emgtfld_bedrooms',
						'value' => $beds,
						"type" => "NUMERIC",
						'compare' => '=',
						);
}
if(!empty($status))
{
	$metaquery[] = array(
						'key' => '1_emgtfld_for',
						'value' => $status,
						'compare' => '=',
						);
}
if(!empty($type))
{
	$metaquery[] = array(
						'key' => '1_emgtfld_type',
						'value' => $type,
						'compare' => '=',
						);
}

$metaquery[] = array(
					'key' =>'1_emgtfld_price',
					'value' => array($min,$max),
					"type" => "NUMERIC",
					'compare' => 'BETWEEN',
					);
					
$args = array(
	'post_type'  => 'emgt_add_listing',	
	'meta_query' => $metaquery
	);
	
$post = query_posts($args);
?>