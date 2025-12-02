<?php
/**
 * Service để tạo QR code và PDF cho vé
 */

// Kiểm tra xem vendor/autoload.php có tồn tại không
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($vendorAutoload)) {
    $errorMsg = 'Thư viện chưa được cài đặt! Vui lòng làm theo các bước sau:' . PHP_EOL;
    $errorMsg .= '1. Cài đặt Composer từ https://getcomposer.org/download/' . PHP_EOL;
    $errorMsg .= '2. Mở Command Prompt/PowerShell tại thư mục dự án' . PHP_EOL;
    $errorMsg .= '3. Chạy lệnh: composer install' . PHP_EOL;
    $errorMsg .= 'Xem file HUONG_DAN_CAI_DAT_QR_PDF.md để biết thêm chi tiết.';
    error_log($errorMsg);
    throw new Exception('Thư viện QR Code và PDF chưa được cài đặt. Vui lòng chạy "composer install" hoặc xem file HUONG_DAN_CAI_DAT_QR_PDF.md để biết hướng dẫn chi tiết.');
}

require_once $vendorAutoload;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;

class TicketQRService {
    private $qrCodeDir;
    private $pdfDir;
    private $baseUrl;
    
    public function __construct() {
        $this->qrCodeDir = __DIR__ . '/../data/qr_codes';
        $this->pdfDir = __DIR__ . '/../data/ticket_pdfs';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($this->qrCodeDir)) {
            mkdir($this->qrCodeDir, 0755, true);
        }
        if (!is_dir($this->pdfDir)) {
            mkdir($this->pdfDir, 0755, true);
        }
        
        // Lấy base URL từ config
        $this->baseUrl = $this->getBaseUrl();
    }
    
    /**
     * Lấy base URL cho QR code (public URL)
     */
    private function getBaseUrl() {
        if (!class_exists('UrlHelper')) {
            require_once __DIR__ . '/UrlHelper.php';
        }
        return UrlHelper::getBaseUrl();
    }
    
    /**
     * Lấy app path từ config
     */
    private function getAppPath() {
        if (defined('APP_PATH')) {
            return APP_PATH;
        }
        // Tự động detect từ script name
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $appPath = dirname($scriptName);
        if ($appPath === '/' || $appPath === '\\') {
            return '';
        }
        return $appPath;
    }
    
    /**
     * Tạo QR code cho một vé
     */
    public function generateQRCode($qrCodeString, $ticketId) {
        try {
            $dataToEncode = $this->getQRCodeData($qrCodeString, $ticketId);
            
            $qrCode = QrCode::create($dataToEncode)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelMedium())
                ->setSize(300)
                ->setMargin(10)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
            
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            $filename = 'ticket_' . $ticketId . '_' . md5($qrCodeString) . '.png';
            $filePath = $this->qrCodeDir . '/' . $filename;
            $result->saveToFile($filePath);
            
            // Tạo URL đầy đủ sử dụng public URL
            $appPath = $this->getAppPath();
            $url = $this->baseUrl . $appPath . '/data/qr_codes/' . $filename;
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename,
                'url' => $url,
                'relative_url' => $appPath . '/data/qr_codes/' . $filename
            ];
        } catch (Exception $e) {
            error_log("Error generating QR code: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Tạo PDF chứa QR code và thông tin vé
     */
    public function generateTicketPDF($ticket, $showtime, $movie, $theater, $user) {
        try {
            // Tạo QR code trước
            $qrResult = $this->generateQRCode($ticket['qr_code'], $ticket['id']);
            if (!$qrResult['success']) {
                throw new Exception("Không thể tạo QR code: " . ($qrResult['error'] ?? 'Unknown error'));
            }
            
            // Sử dụng TCPDF để tạo PDF
            require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
            
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Thiết lập metadata
            $pdf->SetCreator('CineHub');
            $pdf->SetAuthor('CineHub');
            $pdf->SetTitle('Vé xem phim - ' . htmlspecialchars($movie['title']));
            $pdf->SetSubject('Vé xem phim');
            
            // Bỏ header và footer mặc định
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Thêm trang
            $pdf->AddPage();
            
            // Màu sắc
            $primaryColor = array(229, 9, 20); // #e50914
            $secondaryColor = array(0, 0, 0);
            $lightGray = array(245, 245, 245);
            
            // Header với gradient effect
            $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
            $pdf->Rect(0, 0, 210, 50, 'F');
            
            // Logo/Title
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 24);
            $pdf->SetXY(10, 15);
            $pdf->Cell(0, 10, 'CineHub', 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 14);
            $pdf->SetXY(10, 25);
            $pdf->Cell(0, 10, 'Vé xem phim điện tử', 0, 1, 'L');
            
            // Nội dung chính
            $pdf->SetTextColor(0, 0, 0);
            $y = 60;
            
            // Thông tin phim
            $pdf->SetFont('helvetica', 'B', 18);
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, 10, htmlspecialchars($movie['title']), 0, 1, 'L');
            $y += 12;
            
            // Thông tin chi tiết
            $pdf->SetFont('helvetica', '', 11);
            $lineHeight = 7;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Rạp: ' . htmlspecialchars($theater['name']), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Địa chỉ: ' . htmlspecialchars($theater['address'] ?? $theater['location'] ?? ''), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Ngày chiếu: ' . date('d/m/Y', strtotime($showtime['show_date'])), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Giờ chiếu: ' . date('H:i', strtotime($showtime['show_time'])), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Ghế: ' . htmlspecialchars($ticket['seat']), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Loại ghế: ' . $this->getSeatTypeName($ticket['seat_type']), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Giá vé: ' . number_format($ticket['price'], 0, ',', '.') . ' đ', 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Mã vé: ' . htmlspecialchars($ticket['qr_code']), 0, 1, 'L');
            $y += 15;
            
            // QR Code
            $qrImagePath = $qrResult['file_path'];
            if (file_exists($qrImagePath)) {
                $pdf->SetXY(10, $y);
                $pdf->Image($qrImagePath, 10, $y, 60, 60, 'PNG', '', '', false, 300, '', false, false, 1, false, false, false);
                
                // Thông tin bên cạnh QR code
                $pdf->SetXY(75, $y + 10);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 8, 'Quét mã QR để xác thực vé', 0, 1, 'L');
                
                $pdf->SetXY(75, $y + 20);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 6, 'Vui lòng đến rạp trước 15 phút', 0, 1, 'L');
                $pdf->SetXY(75, $y + 26);
                $pdf->Cell(0, 6, 'để làm thủ tục vào rạp', 0, 1, 'L');
            }
            
            $y += 70;
            
            // Thông tin khách hàng
            $pdf->SetFillColor($lightGray[0], $lightGray[1], $lightGray[2]);
            $pdf->Rect(10, $y, 190, 30, 'F');
            
            $pdf->SetXY(10, $y + 5);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 8, 'Thông tin khách hàng', 0, 1, 'L');
            
            $pdf->SetXY(10, $y + 13);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 6, 'Tên: ' . htmlspecialchars($user['name']), 0, 1, 'L');
            
            $pdf->SetXY(10, $y + 19);
            $pdf->Cell(0, 6, 'Email: ' . htmlspecialchars($user['email']), 0, 1, 'L');
            
            $y += 35;
            
            // Footer
            $pdf->SetXY(10, $y);
            $pdf->SetFont('helvetica', 'I', 9);
            $pdf->SetTextColor(128, 128, 128);
            $pdf->Cell(0, 6, 'Vé này được tạo tự động bởi hệ thống CineHub', 0, 1, 'C');
            $pdf->SetXY(10, $y + 6);
            $pdf->Cell(0, 6, 'Vui lòng giữ vé này để vào rạp. Cảm ơn bạn đã sử dụng dịch vụ!', 0, 1, 'C');
            
            // Lưu PDF
            $pdfFilename = 'ticket_' . $ticket['id'] . '_' . time() . '.pdf';
            $pdfPath = $this->pdfDir . '/' . $pdfFilename;
            $pdf->Output($pdfPath, 'F');
            
            return [
                'success' => true,
                'file_path' => $pdfPath,
                'filename' => $pdfFilename,
                'url' => '/DuAn1/data/ticket_pdfs/' . $pdfFilename
            ];
            
        } catch (Exception $e) {
            error_log("Error generating ticket PDF: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Tạo PDF cho nhiều vé (booking)
     */
    public function generateBookingPDF($tickets, $showtime, $movie, $theater, $user) {
        try {
            require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
            
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('CineHub');
            $pdf->SetAuthor('CineHub');
            $pdf->SetTitle('Vé xem phim - ' . htmlspecialchars($movie['title']));
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            $primaryColor = array(229, 9, 20);
            $lightGray = array(245, 245, 245);
            
            foreach ($tickets as $index => $ticket) {
                if ($index > 0) {
                    $pdf->AddPage();
                }
                
                // Tạo QR code cho vé này
                $qrResult = $this->generateQRCode($ticket['qr_code'], $ticket['id']);
                if (!$qrResult['success']) {
                    continue; // Bỏ qua vé này nếu không tạo được QR
                }
                
                // Header
                $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
                $pdf->Rect(0, 0, 210, 50, 'F');
                
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('helvetica', 'B', 24);
                $pdf->SetXY(10, 15);
                $pdf->Cell(0, 10, 'CineHub', 0, 1, 'L');
                
                $pdf->SetFont('helvetica', '', 14);
                $pdf->SetXY(10, 25);
                $pdf->Cell(0, 10, 'Vé xem phim điện tử', 0, 1, 'L');
                
                // Nội dung
                $pdf->SetTextColor(0, 0, 0);
                $y = 60;
                
                $pdf->SetFont('helvetica', 'B', 18);
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, 10, htmlspecialchars($movie['title']), 0, 1, 'L');
                $y += 12;
                
                $pdf->SetFont('helvetica', '', 11);
                $lineHeight = 7;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Rạp: ' . htmlspecialchars($theater['name']), 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Ngày chiếu: ' . date('d/m/Y', strtotime($showtime['show_date'])), 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Giờ chiếu: ' . date('H:i', strtotime($showtime['show_time'])), 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Ghế: ' . htmlspecialchars($ticket['seat']), 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Giá vé: ' . number_format($ticket['price'], 0, ',', '.') . ' đ', 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Mã vé: ' . htmlspecialchars($ticket['qr_code']), 0, 1, 'L');
                $y += 15;
                
                // QR Code
                $qrImagePath = $qrResult['file_path'];
                if (file_exists($qrImagePath)) {
                    $pdf->SetXY(10, $y);
                    $pdf->Image($qrImagePath, 10, $y, 60, 60, 'PNG', '', '', false, 300, '', false, false, 1, false, false, false);
                    
                    $pdf->SetXY(75, $y + 10);
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->Cell(0, 8, 'Quét mã QR để xác thực vé', 0, 1, 'L');
                }
                
                $y += 70;
                
                // Thông tin khách hàng
                $pdf->SetFillColor($lightGray[0], $lightGray[1], $lightGray[2]);
                $pdf->Rect(10, $y, 190, 20, 'F');
                
                $pdf->SetXY(10, $y + 5);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 6, 'Tên: ' . htmlspecialchars($user['name']), 0, 1, 'L');
                
                $pdf->SetXY(10, $y + 11);
                $pdf->Cell(0, 6, 'Email: ' . htmlspecialchars($user['email']), 0, 1, 'L');
            }
            
            // Lưu PDF
            $bookingId = $tickets[0]['booking_pending_id'] ?? time();
            $pdfFilename = 'booking_' . $bookingId . '_' . time() . '.pdf';
            $pdfPath = $this->pdfDir . '/' . $pdfFilename;
            $pdf->Output($pdfPath, 'F');
            
            return [
                'success' => true,
                'file_path' => $pdfPath,
                'filename' => $pdfFilename,
                'url' => '/DuAn1/data/ticket_pdfs/' . $pdfFilename
            ];
            
        } catch (Exception $e) {
            error_log("Error generating booking PDF: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Tạo QR code cho booking (1 QR code cho cả booking)
     */
    public function generateBookingQRCode($qrCodeString, $bookingId) {
        try {
            $dataToEncode = $this->getBookingQRCodeData($qrCodeString, $bookingId);
            
            $qrCode = QrCode::create($dataToEncode)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelMedium())
                ->setSize(300)
                ->setMargin(10)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
            
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            $filename = 'booking_' . $bookingId . '_' . md5($qrCodeString) . '.png';
            $filePath = $this->qrCodeDir . '/' . $filename;
            $result->saveToFile($filePath);
            
            // Tạo URL đầy đủ
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
            $baseUrl = $protocol . "://" . $host;
            $url = $baseUrl . '/DuAn1/data/qr_codes/' . $filename;
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename,
                'url' => $url,
                'relative_url' => $this->getAppPath() . '/data/qr_codes/' . $filename
            ];
        } catch (Exception $e) {
            error_log("Error generating booking QR code: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy dữ liệu để mã hóa vào QR code cho booking
     * QR code sẽ trỏ đến route verify để hiển thị tất cả vé trong booking
     */
    private function getBookingQRCodeData($qrCodeString, $bookingId) {
        // Tạo URL để xác thực booking và hiển thị tất cả vé
        // Thêm ?html=1 để hiển thị HTML trên mobile (dễ xem hơn PDF)
        $baseUrl = $this->getBaseUrl();
        
        // Nếu baseUrl đã chứa appPath, không thêm nữa
        $appPath = $this->getAppPath();
        if (strpos($baseUrl, $appPath) !== false) {
            // baseUrl đã chứa appPath (ví dụ: https://ngrok.com/DuAn1)
            $url = $baseUrl . '/?route=booking/verify&booking=' . urlencode($qrCodeString) . '&booking_id=' . $bookingId . '&html=1';
        } else {
            // baseUrl chưa chứa appPath (ví dụ: https://ngrok.com)
            $url = $baseUrl . $appPath . '/?route=booking/verify&booking=' . urlencode($qrCodeString) . '&booking_id=' . $bookingId . '&html=1';
        }
        
        return $url;
    }
    
    /**
     * Lấy dữ liệu để mã hóa vào QR code
     * QR code sẽ trỏ đến route verify để hiển thị PDF khi quét
     */
    private function getQRCodeData($qrCodeString, $ticketId) {
        // Tạo URL để xác thực vé và hiển thị PDF
        $appPath = $this->getAppPath();
        return $this->baseUrl . $appPath . '/?route=booking/verify&ticket=' . urlencode($qrCodeString) . '&id=' . $ticketId;
    }
    
    /**
     * Lấy tên loại ghế
     */
    private function getSeatTypeName($seatType) {
        $types = [
            'normal' => 'Thường',
            'vip' => 'VIP',
            'couple' => 'Đôi'
        ];
        return $types[$seatType] ?? 'Thường';
    }
    
    /**
     * Xóa file cũ (nếu cần)
     */
    public function cleanupOldFiles($daysOld = 30) {
        // Implementation để xóa file cũ nếu cần
    }
}

