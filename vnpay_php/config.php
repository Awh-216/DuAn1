<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
/*
 * Cấu hình VNPay
 */
  
$vnp_TmnCode = "FK2JNB94"; //Mã định danh merchant kết nối (Terminal Id)
$vnp_HashSecret = "6CJXOQ0GAO04RL7SOVVX2BB5AHW5ORGL"; //Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

// Sử dụng UrlHelper để lấy base URL đúng
require_once __DIR__ . '/../core/UrlHelper.php';
$baseUrl = UrlHelper::getBaseUrl();

// Return URL trỏ về BookingController vnpayReturn method
$vnp_Returnurl = $baseUrl . "/?route=booking/vnpay-return";
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
