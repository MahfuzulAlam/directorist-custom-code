<?php
/**
 * Directorist Payment Receipt (with Company/VAT/Address/Phone/Birthdate)
 * Place in: /wp-content/themes/your-theme/directorist/payment-receipt.php
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) { die(); }

/* -------------------------
   Company details (edit if needed)
------------------------- */
$company = [
    'name'     => 'Marinero Trade s.r.o',
    'address'  => 'Budatínska 3230/16<br>Bratislava – mestská časť Petržalka 851 04<br>Slovak Republic',
    'uid'      => 'VAT ID: SK 2121369085',
    'ico'      => 'Company ID (ICO): 53273389',
    'register' => 'Registration No: 148779/B – City Court Bratislava III',
    'logo'     => 'https://i0.wp.com/marinerotrade.com/wp-content/uploads/2025/04/marinero-logo_original.png',
];

/* -------------------------
   Gather Order & Customer data
------------------------- */
$customer_name    = get_user_meta( $order_id ? get_post_field('post_author', $order_id) : 0, 'first_name', true ) . ' ' . get_user_meta( $order_id ? get_post_field('post_author', $order_id) : 0, 'last_name', true );
$customer_company = get_user_meta( $order_id ? get_post_field('post_author', $order_id) : 0, 'company_name', true );
$customer_vat     = get_user_meta( $order_id ? get_post_field('post_author', $order_id) : 0, 'vat_number', true );
$customer_address = get_user_meta( $order_id ? get_post_field('post_author', $order_id) : 0, 'address', true );
$customer_phone   = get_user_meta( $order_id ? get_post_field('post_author', $order_id) : 0, 'phone', true );
$customer_birth   = get_user_meta( $order_id ? get_post_field('post_author', $order_id) : 0, 'birth_date', true );

/* -------------------------
   Currency & Totals
------------------------- */
$symbol = function_exists('get_directorist_option') ? get_directorist_option('currency_symbol') : '$';
$c_position = function_exists('get_directorist_option') ? get_directorist_option('currency_symbol_position') : 'before';
$before = ($c_position === 'before') ? $symbol : '';
$after  = ($c_position === 'after') ? $symbol : '';

// $subtotal = isset( $data[ 'price' ] ) && ! empty( $data[ 'price' ] ) ? $data[ 'price' ] : 0;
// if( $subtotal < 1  ){
// 	$subtotal = isset( $data[ 'o_metas' ]['_amount'][0] ) && ! empty( $data[ 'o_metas' ]['_amount'][0] ) ? $data[ 'o_metas' ]['_amount'][0] : 0;
// }

/* -------------------------
   VAT calculation (optional)
------------------------- */
$vat_rate = 0;
$tax_type = 'flat';
$fm_price = 0;
$fm_description = '';
$fm_title = '';
$pricing_plan = isset( $data[ 'o_metas' ][ '_fm_plan_ordered' ][0] ) && ! empty( $data[ 'o_metas' ][ '_fm_plan_ordered' ][0] ) ? $data[ 'o_metas' ][ '_fm_plan_ordered' ][0] : null;
if( $pricing_plan ){
	$vat_rate = get_post_meta( $pricing_plan, 'fm_tax', true );
	$tax_type = get_post_meta( $pricing_plan, 'plan_tax_type', true );
    $fm_price = get_post_meta( $pricing_plan, 'fm_price', true );
    $fm_description = get_post_meta( $pricing_plan, 'fm_description', true );
    $fm_title = get_the_title( $pricing_plan );
}

$order_items = [
    [
        'title' => $fm_title,
        'desc' => $fm_description,
        'price' => $fm_price,
    ]
];

$vat_html = $tax_type == 'percent' ? $vat_rate . '%' : $symbol . $vat_rate;
$vat_rate = $tax_type == 'percent' ? $vat_rate / 100 : $vat_rate; // Standard, kann angepasst werden
$subtotal = $fm_price;
$vat_amount = round($fm_price * $vat_rate, 2);
$total = round($subtotal + $vat_amount, 2);

/* -------------------------
   Output Invoice
------------------------- */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice #<?php echo esc_html($order_id); ?></title>
<style>
/* Basic Styles for invoice */
body{font-family:Helvetica, Arial; margin:0; padding:0; background:#f8f9fa;}
#invoice-container{max-width:850px;margin:30px auto;background:#fff;padding:36px 48px;border:1px solid #ddd;}
.invoice-header{display:flex;justify-content:space-between;border-bottom:2px solid #eaeaea;padding-bottom:18px;margin-bottom:22px;}
.company-info{text-align:right;font-size:13px;line-height:1.55;}
.company-info img{width:140px;margin-bottom:8px;}
.invoice-title{text-align:center;font-size:20px;font-weight:600;margin:20px 0;}
.invoice-table{width:100%;border-collapse:collapse;margin-bottom:18px;}
.invoice-table th, .invoice-table td{border:1px solid #e6e6e6;padding:10px 12px;text-align:left;}
.invoice-table th{background:#f7f7f7;font-weight:600;}
.summary td{padding:6px 8px;}
.billto{font-size:13px;}
.footer{display:flex;justify-content:flex-end;margin-top:36px;font-size:13px;}
.print-btn{background:#0073aa;color:#fff;border:none;padding:10px 16px;border-radius:6px;cursor:pointer;font-size:14px;}
.print-btn:hover{background:#005f8a;}
@media print{body *{visibility:hidden;}#invoice-container, #invoice-container *{visibility:visible;}#invoice-container{position:absolute;left:0;top:0;width:100%;margin:0;padding:20px 28px;border:none;} .print-btn{display:none;}}
</style>
</head>
<body>
<div id="invoice-container">
    <div class="invoice-header">
        <div>
            <h2>Invoice</h2>
            <p style="font-size:13px;margin:8px 0 0 0;">
                <strong>Invoice No:</strong> <?php echo esc_html($order_id); ?><br>
                <strong>Date:</strong> <?php echo esc_html(get_the_time(get_option('date_format'), $order_id)); ?><br>
            </p>
            <div class="billto">
                <p>
                    <strong>Bill to:</strong><br>
                    <?php echo esc_html($customer_name); ?><br>
                    <?php if($customer_company) echo 'Company: ' . esc_html($customer_company) . '<br>'; ?>
                    <?php if($customer_vat) echo 'VAT ID: ' . esc_html($customer_vat) . '<br>'; ?>
                    <?php if($customer_address) echo 'Address: ' . esc_html($customer_address) . '<br>'; ?>
                    <?php if($customer_phone) echo 'Phone: ' . esc_html($customer_phone) . '<br>'; ?>
                    <?php if($customer_birth) echo 'Birth Date: ' . esc_html($customer_birth) . '<br>'; ?>
                </p>
            </div>
        </div>
        <div class="company-info">
            <img src="<?php echo esc_url($company['logo']); ?>" alt="<?php echo esc_attr($company['name']); ?>"><br>
            <strong><?php echo esc_html($company['name']); ?></strong><br>
            <?php echo wp_kses_post($company['address']); ?><br>
            <?php echo esc_html($company['uid']); ?><br>
            <?php echo esc_html($company['ico']); ?><br>
            <?php echo esc_html($company['register']); ?>
        </div>
    </div>

    <h3 class="invoice-title">Payment Receipt</h3>

    <table class="invoice-table">
        <thead>
            <tr><th>Description</th><th style="text-align:right;">Amount</th></tr>
        </thead>
        <tbody>
            <?php if(!empty($order_items)) : foreach($order_items as $it): ?>
            <tr>
                <td>
                    <?php echo esc_html($it['title']); ?>
                    <?php if($it['desc']) echo '<br><span style="font-size:12px;color:#666;">' . esc_html($it['desc']) . '</span>'; ?>
                </td>
                <td style="text-align:right;"><?php echo $before . number_format($it['price'], 2) . $after; ?></td>
            </tr>
            <?php endforeach; endif; ?>
            <tr>
                <td><strong>Subtotal</strong></td>
                <td style="text-align:right;"><?php echo $before . number_format($subtotal, 2) . $after; ?></td>
            </tr>
            <tr>
                <td><strong>VAT (<?php echo esc_html($vat_html); ?>)</strong></td>
                <td style="text-align:right;"><?php echo $before . number_format($vat_amount, 2) . $after; ?></td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td style="text-align:right;"><strong><?php echo $before . number_format($total, 2) . $after; ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div style="text-align:right;">
            <button class="print-btn" onclick="window.print();">🧾 Download / Print PDF</button>
            <p style="font-size:12px;color:#666;margin-top:8px;">No server storage — PDF generated by your browser.</p>
        </div>
    </div>
</div>
</body>
</html>
