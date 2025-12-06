<?php
/**
 * Service để tạo QR code và PDF cho vé
 * Sử dụng bacon/bacon-qr-code (tương thích PHP 8.0+)
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

// Sử dụng bacon/bacon-qr-code thay vì endroid/qr-code
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

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
     * Tạo QR code sử dụng bacon/bacon-qr-code
     * Trả về đường dẫn file SVG
     */
    private function createQRCodeFile($data, $filename) {
        try {
            // Sử dụng SVG backend (không cần Imagick)
            $renderer = new ImageRenderer(
                new RendererStyle(300, 2),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            
            // Tạo file SVG
            $svgFilename = pathinfo($filename, PATHINFO_FILENAME) . '.svg';
            $filePath = $this->qrCodeDir . '/' . $svgFilename;
            $writer->writeFile($data, $filePath);
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $svgFilename
            ];
        } catch (Exception $e) {
            error_log("Error creating QR code: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Tạo QR code cho một vé
     */
    public function generateQRCode($qrCodeString, $ticketId) {
        try {
            $dataToEncode = $this->getQRCodeData($qrCodeString, $ticketId);
            
            $filename = 'ticket_' . $ticketId . '_' . md5($qrCodeString) . '.svg';
            $result = $this->createQRCodeFile($dataToEncode, $filename);
            
            if (!$result['success']) {
                throw new Exception($result['error'] ?? 'Unknown error');
            }
            
            // Tạo URL đầy đủ sử dụng public URL
            $appPath = $this->getAppPath();
            $url = $this->baseUrl . $appPath . '/data/qr_codes/' . $result['filename'];
            
            return [
                'success' => true,
                'file_path' => $result['file_path'],
                'filename' => $result['filename'],
                'url' => $url,
                'relative_url' => $appPath . '/data/qr_codes/' . $result['filename']
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
     * Tạo QR code cho booking (1 QR code cho cả booking)
     */
    public function generateBookingQRCode($qrCodeString, $bookingId) {
        try {
            $dataToEncode = $this->getBookingQRCodeData($qrCodeString, $bookingId);
            
            $filename = 'booking_' . $bookingId . '_' . md5($qrCodeString) . '.svg';
            $result = $this->createQRCodeFile($dataToEncode, $filename);
            
            if (!$result['success']) {
                throw new Exception($result['error'] ?? 'Unknown error');
            }
            
            // Tạo URL đầy đủ
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
            $baseUrl = $protocol . "://" . $host;
            $url = $baseUrl . '/DuAn1/data/qr_codes/' . $result['filename'];
            
            return [
                'success' => true,
                'file_path' => $result['file_path'],
                'filename' => $result['filename'],
                'url' => $url,
                'relative_url' => $this->getAppPath() . '/data/qr_codes/' . $result['filename']
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
     */
    private function getBookingQRCodeData($qrCodeString, $bookingId) {
        $baseUrl = $this->getBaseUrl();
        $appPath = $this->getAppPath();
        
        if (strpos($baseUrl, $appPath) !== false) {
            $url = $baseUrl . '/?route=booking/verify&booking=' . urlencode($qrCodeString) . '&booking_id=' . $bookingId . '&html=1';
        } else {
            $url = $baseUrl . $appPath . '/?route=booking/verify&booking=' . urlencode($qrCodeString) . '&booking_id=' . $bookingId . '&html=1';
        }
        
        return $url;
    }
    
    /**
     * Lấy dữ liệu để mã hóa vào QR code
     */
    private function getQRCodeData($qrCodeString, $ticketId) {
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
     * Tạo PDF chứa QR code và thông tin vé
     */
    public function generateTicketPDF($ticket, $showtime, $movie, $theater, $user) {
        try {
            // Tạo QR code trước
            $qrResult = $this->generateQRCode($ticket['qr_code'], $ticket['id']);
            if (!$qrResult['success']) {
                throw new Exception("Không thể tạo QR code: " . ($qrResult['error'] ?? 'Unknown error'));
            }
            
            // Kiểm tra TCPDF
            $tcpdfPath = __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
            if (!file_exists($tcpdfPath)) {
                throw new Exception("TCPDF chưa được cài đặt. Chạy: composer require tecnickcom/tcpdf");
            }
            require_once $tcpdfPath;
            
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('CineHub');
            $pdf->SetAuthor('CineHub');
            $pdf->SetTitle('Vé xem phim - ' . htmlspecialchars($movie['title']));
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->AddPage();
            
            $primaryColor = array(229, 9, 20);
            $lightGray = array(245, 245, 245);
            
            // Header
            $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
            $pdf->Rect(0, 0, 210, 50, 'F');
            
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 24);
            $pdf->SetXY(10, 15);
            $pdf->Cell(0, 10, 'CineHub', 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 14);
            $pdf->SetXY(10, 25);
            $pdf->Cell(0, 10, 'Ve xem phim dien tu', 0, 1, 'L');
            
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
            $pdf->Cell(0, $lineHeight, 'Rap: ' . htmlspecialchars($theater['name']), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Ngay chieu: ' . date('d/m/Y', strtotime($showtime['show_date'])), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Gio chieu: ' . date('H:i', strtotime($showtime['show_time'])), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Ghe: ' . htmlspecialchars($ticket['seat']), 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Gia ve: ' . number_format($ticket['price'], 0, ',', '.') . ' d', 0, 1, 'L');
            $y += $lineHeight;
            
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, $lineHeight, 'Ma ve: ' . htmlspecialchars($ticket['qr_code']), 0, 1, 'L');
            $y += 15;
            
            // QR Code (SVG)
            $qrImagePath = $qrResult['file_path'];
            if (file_exists($qrImagePath)) {
                $pdf->ImageSVG($qrImagePath, 10, $y, 60, 60, '', '', '', 0, false);
                
                $pdf->SetXY(75, $y + 10);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 8, 'Quet ma QR de xac thuc ve', 0, 1, 'L');
            }
            
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
            $tcpdfPath = __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
            if (!file_exists($tcpdfPath)) {
                throw new Exception("TCPDF chưa được cài đặt");
            }
            require_once $tcpdfPath;
            
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('CineHub');
            $pdf->SetAuthor('CineHub');
            $pdf->SetTitle('Ve xem phim - ' . htmlspecialchars($movie['title']));
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            $primaryColor = array(229, 9, 20);
            $lightGray = array(245, 245, 245);
            
            foreach ($tickets as $index => $ticket) {
                $pdf->AddPage();
                
                $qrResult = $this->generateQRCode($ticket['qr_code'], $ticket['id']);
                
                // Header
                $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
                $pdf->Rect(0, 0, 210, 50, 'F');
                
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('helvetica', 'B', 24);
                $pdf->SetXY(10, 15);
                $pdf->Cell(0, 10, 'CineHub', 0, 1, 'L');
                
                $pdf->SetFont('helvetica', '', 14);
                $pdf->SetXY(10, 25);
                $pdf->Cell(0, 10, 'Ve xem phim dien tu', 0, 1, 'L');
                
                $pdf->SetTextColor(0, 0, 0);
                $y = 60;
                
                $pdf->SetFont('helvetica', 'B', 18);
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, 10, htmlspecialchars($movie['title']), 0, 1, 'L');
                $y += 12;
                
                $pdf->SetFont('helvetica', '', 11);
                $lineHeight = 7;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Rap: ' . htmlspecialchars($theater['name']), 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Ngay chieu: ' . date('d/m/Y', strtotime($showtime['show_date'])), 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Gio chieu: ' . date('H:i', strtotime($showtime['show_time'])), 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Ghe: ' . htmlspecialchars($ticket['seat']), 0, 1, 'L');
                $y += $lineHeight;
                
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, 'Gia ve: ' . number_format($ticket['price'], 0, ',', '.') . ' d', 0, 1, 'L');
                $y += 15;
                
                // QR Code
                if ($qrResult['success'] && file_exists($qrResult['file_path'])) {
                    $pdf->ImageSVG($qrResult['file_path'], 10, $y, 60, 60, '', '', '', 0, false);
                }
                
                $y += 70;
                
                // Thông tin khách hàng
                $pdf->SetFillColor($lightGray[0], $lightGray[1], $lightGray[2]);
                $pdf->Rect(10, $y, 190, 20, 'F');
                
                $pdf->SetXY(10, $y + 5);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 6, 'Ten: ' . htmlspecialchars($user['name']), 0, 1, 'L');
                
                $pdf->SetXY(10, $y + 11);
                $pdf->Cell(0, 6, 'Email: ' . htmlspecialchars($user['email']), 0, 1, 'L');
            }
            
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
     * Xóa file cũ (nếu cần)
     */
    public function cleanupOldFiles($daysOld = 30) {
        // Implementation để xóa file cũ nếu cần
    }
}
