<?php
/* Payment Notify
 * IPN URL: Ghi nh?n k?t qu? thanh toán t? VNPAY
 * Các bu?c th?c hi?n:
 * Ki?m tra checksum 
 * Tìm giao d?ch trong database
 * Ki?m tra s? ti?n gi?a hai h? th?ng
 * Ki?m tra tình tr?ng c?a giao d?ch tru?c khi c?p nh?t
 * C?p nh?t k?t qu? vào Database
 * Tr? k?t qu? ghi nh?n l?i cho VNPAY
 */

require_once("./config.php");
$inputData = array();
$returnData = array();
foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

$vnp_SecureHash = $inputData['vnp_SecureHash'];
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashData = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
$vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao d?ch t?i VNPAY
$vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
$vnp_Amount = $inputData['vnp_Amount']/100; // S? ti?n thanh toán VNPAY ph?n h?i

$Status = 0; // Là tr?ng thái thanh toán c?a giao d?ch chua có IPN luu t?i h? th?ng c?a merchant chi?u kh?i t?o URL thanh toán.
$orderId = $inputData['vnp_TxnRef'];

try {
    //Check Orderid    
    //Ki?m tra checksum c?a d? li?u
    if ($secureHash == $vnp_SecureHash) {
        //L?y thông tin don hàng luu trong Database và ki?m tra tr?ng thái c?a don hàng, mã don hàng là: $orderId            
        //Vi?c ki?m tra tr?ng thái c?a don hàng giúp h? th?ng không x? lý trùng l?p, x? lý nhi?u l?n m?t giao d?ch
        //Gi? s?: $order = mysqli_fetch_assoc($result);   

        $order = NULL;
        if ($order != NULL) {
            if($order["Amount"] == $vnp_Amount) //Ki?m tra s? ti?n thanh toán c?a giao d?ch: gi? s? s? ti?n ki?m tra là dúng. //$order["Amount"] == $vnp_Amount
            {
                if ($order["Status"] != NULL && $order["Status"] == 0) {
                    if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                        $Status = 1; // Tr?ng thái thanh toán thành công
                    } else {
                        $Status = 2; // Tr?ng thái thanh toán th?t b?i / l?i
                    }
                    //Cài d?t Code c?p nh?t k?t qu? thanh toán, tình tr?ng don hàng vào DB
                    //
                    //
                    //
                    //Tr? k?t qu? v? cho VNPAY: Website/APP TMÐT ghi nh?n yêu c?u thành công                
                    $returnData['RspCode'] = '00';
                    $returnData['Message'] = 'Confirm Success';
                } else {
                    $returnData['RspCode'] = '02';
                    $returnData['Message'] = 'Order already confirmed';
                }
            }
            else {
                $returnData['RspCode'] = '04';
                $returnData['Message'] = 'invalid amount';
            }
        } else {
            $returnData['RspCode'] = '01';
            $returnData['Message'] = 'Order not found';
        }
    } else {
        $returnData['RspCode'] = '97';
        $returnData['Message'] = 'Invalid signature';
    }
} catch (Exception $e) {
    $returnData['RspCode'] = '99';
    $returnData['Message'] = 'Unknow error';
}
//Tr? l?i VNPAY theo d?nh d?ng JSON
echo json_encode($returnData);
