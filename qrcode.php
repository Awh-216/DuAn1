<?php
// Tệp tin cần thiết để sử dụng các lớp đã cài đặt qua Composer
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

// 1. Dữ liệu cần mã hóa (đây là ID duy nhất của giao dịch/vé)
$ticketId = 'ABCD-1234-EFGH';
$dataToEncode = 'https://ten-mien-cua-ban/api/xac-thuc-ve?id=' . $ticketId;

// 2. Cấu hình và tạo đối tượng QrCode
$qrCode = QrCode::create($dataToEncode)
    ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
    ->setSize(300)
    ->setMargin(10);

// 3. Tạo ảnh PNG
$writer = new PngWriter();
$result = $writer->write($qrCode);

// 4. Lưu file hoặc trả về dữ liệu ảnh
$filePath = __DIR__ . '/qr_codes/' . $ticketId . '.png';
$result->saveToFile($filePath);

echo "Mã QR đã được lưu tại: " . $filePath;