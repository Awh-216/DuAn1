<?php
/**
 * Service để tạo QR code và PDF cho vé
 * Sử dụng bacon/bacon-qr-code (tương thích PHP 7.4+)
 */

// Kiểm tra xem vendor/autoload.php có tồn tại không
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($vendorAutoload)) {
    $errorMsg = 'Thư viện chưa được cài đặt! Vui lòng chạy: composer install';
    error_log($errorMsg);
    throw new Exception($errorMsg);
}

require_once $vendorAutoload;

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
        return '';
    }

    /**
     * Tạo QR code cho một vé
     */
    public function generateQRCode($qrCodeString, $ticketId) {
        try {
            $dataToEncode = $this->getQRCodeData($qrCodeString, $ticketId);
            $filename = 'ticket_' . $ticketId . '_' . md5($qrCodeString) . '.svg';
            $filePath = $this->qrCodeDir . '/' . $filename;
            
            // Tạo QR code sử dụng bacon/bacon-qr-code
            $this->createQRCodeImage($dataToEncode, $filePath);
            
            $url = $this->baseUrl . '/data/qr_codes/' . $filename;
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename,
                'url' => $url,
                'relative_url' => '/data/qr_codes/' . $filename
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
            $filePath = $this->qrCodeDir . '/' . $filename;
            
            // Tạo QR code
            $this->createQRCodeImage($dataToEncode, $filePath);
            
            $url = $this->baseUrl . '/data/qr_codes/' . $filename;
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename,
                'url' => $url,
                'relative_url' => '/data/qr_codes/' . $filename
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
     * Tạo file ảnh QR code (SVG format - tương thích mọi browser)
     */
    private function createQRCodeImage($data, $filePath) {
        $renderer = new ImageRenderer(
            new RendererStyle(300, 2),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        
        // Lưu file SVG (đổi extension)
        $svgPath = preg_replace('/\.png$/i', '.svg', $filePath);
        $writer->writeFile($data, $svgPath);
        
        // Cập nhật filePath để trả về SVG
        return $svgPath;
    }

    /**
     * Lấy dữ liệu để mã hóa vào QR code cho booking
     */
    private function getBookingQRCodeData($qrCodeString, $bookingId) {
        $baseUrl = $this->getBaseUrl();
        $url = $baseUrl . '/?route=booking/verify&booking=' . urlencode($qrCodeString) . '&booking_id=' . $bookingId . '&html=1';
        return $url;
    }
    
    /**
     * Lấy dữ liệu để mã hóa vào QR code cho vé
     */
    private function getQRCodeData($qrCodeString, $ticketId) {
        $baseUrl = $this->getBaseUrl();
        return $baseUrl . '/?route=booking/verify&ticket=' . urlencode($qrCodeString) . '&id=' . $ticketId . '&html=1';
    }
    
    /**
     * Tạo PDF chứa QR code và thông tin vé
     */
    public function generateTicketPDF($ticket, $showtime, $movie, $theater, $user) {
        try {
            $qrResult = $this->generateQRCode($ticket['qr_code'], $ticket['id']);
            if (!$qrResult['success']) {
                throw new Exception("Không thể tạo QR code: " . ($qrResult['error'] ?? 'Unknown error'));
            }
            
            require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
            
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('CineHub');
            $pdf->SetAuthor('CineHub');
            $pdf->SetTitle('Vé xem phim - ' . htmlspecialchars($movie['title']));
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->AddPage();
            
            // Header
            $pdf->SetFillColor(229, 9, 20);
            $pdf->Rect(0, 0, 210, 50, 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 24);
            $pdf->SetXY(10, 15);
            $pdf->Cell(0, 10, 'CineHub', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 14);
            $pdf->SetXY(10, 25);
            $pdf->Cell(0, 10, 'Vé xem phim điện tử', 0, 1, 'L');
            
            // Content
            $pdf->SetTextColor(0, 0, 0);
            $y = 60;
            $pdf->SetFont('helvetica', 'B', 18);
            $pdf->SetXY(10, $y);
            $pdf->Cell(0, 10, htmlspecialchars($movie['title']), 0, 1, 'L');
            $y += 12;
            
            $pdf->SetFont('helvetica', '', 11);
            $lineHeight = 7;
            
            $info = [
                'Rạp' => $theater['name'],
                'Ngày chiếu' => date('d/m/Y', strtotime($showtime['show_date'])),
                'Giờ chiếu' => date('H:i', strtotime($showtime['show_time'])),
                'Ghế' => $ticket['seat'],
                'Giá vé' => number_format($ticket['price'], 0, ',', '.') . ' đ',
                'Mã vé' => $ticket['qr_code']
            ];
            
            foreach ($info as $label => $value) {
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, $lineHeight, $label . ': ' . htmlspecialchars($value), 0, 1, 'L');
                $y += $lineHeight;
            }
            
            $y += 10;
            
            // QR Code
            if (file_exists($qrResult['file_path'])) {
                $pdf->Image($qrResult['file_path'], 10, $y, 60, 60, '', '', '', false, 300);
                $pdf->SetXY(75, $y + 20);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 8, 'Quét mã QR để xác thực vé', 0, 1, 'L');
            }
            
            $pdfFilename = 'ticket_' . $ticket['id'] . '_' . time() . '.pdf';
            $pdfPath = $this->pdfDir . '/' . $pdfFilename;
            $pdf->Output($pdfPath, 'F');
            
            return [
                'success' => true,
                'file_path' => $pdfPath,
                'filename' => $pdfFilename,
                'url' => '/data/ticket_pdfs/' . $pdfFilename
            ];
        } catch (Exception $e) {
            error_log("Error generating ticket PDF: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
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
            
            foreach ($tickets as $index => $ticket) {
                if ($index > 0) $pdf->AddPage();
                else $pdf->AddPage();
                
                $qrResult = $this->generateQRCode($ticket['qr_code'], $ticket['id']);
                
                // Header
                $pdf->SetFillColor(229, 9, 20);
                $pdf->Rect(0, 0, 210, 50, 'F');
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('helvetica', 'B', 24);
                $pdf->SetXY(10, 15);
                $pdf->Cell(0, 10, 'CineHub', 0, 1, 'L');
                $pdf->SetFont('helvetica', '', 14);
                $pdf->SetXY(10, 25);
                $pdf->Cell(0, 10, 'Vé xem phim điện tử', 0, 1, 'L');
                
                // Content
                $pdf->SetTextColor(0, 0, 0);
                $y = 60;
                $pdf->SetFont('helvetica', 'B', 18);
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, 10, htmlspecialchars($movie['title']), 0, 1, 'L');
                $y += 15;
                
                $pdf->SetFont('helvetica', '', 11);
                $pdf->SetXY(10, $y); $pdf->Cell(0, 7, 'Rạp: ' . htmlspecialchars($theater['name']), 0, 1, 'L'); $y += 7;
                $pdf->SetXY(10, $y); $pdf->Cell(0, 7, 'Ngày: ' . date('d/m/Y', strtotime($showtime['show_date'])), 0, 1, 'L'); $y += 7;
                $pdf->SetXY(10, $y); $pdf->Cell(0, 7, 'Giờ: ' . date('H:i', strtotime($showtime['show_time'])), 0, 1, 'L'); $y += 7;
                $pdf->SetXY(10, $y); $pdf->Cell(0, 7, 'Ghế: ' . htmlspecialchars($ticket['seat']), 0, 1, 'L'); $y += 7;
                $pdf->SetXY(10, $y); $pdf->Cell(0, 7, 'Giá: ' . number_format($ticket['price'], 0, ',', '.') . ' đ', 0, 1, 'L'); $y += 15;
                
                if ($qrResult['success'] && file_exists($qrResult['file_path'])) {
                    $pdf->Image($qrResult['file_path'], 10, $y, 60, 60, '', '', '', false, 300);
                }
            }
            
            $bookingId = $tickets[0]['booking_pending_id'] ?? time();
            $pdfFilename = 'booking_' . $bookingId . '_' . time() . '.pdf';
            $pdfPath = $this->pdfDir . '/' . $pdfFilename;
            $pdf->Output($pdfPath, 'F');
            
            return [
                'success' => true,
                'file_path' => $pdfPath,
                'filename' => $pdfFilename,
                'url' => '/data/ticket_pdfs/' . $pdfFilename
            ];
        } catch (Exception $e) {
            error_log("Error generating booking PDF: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Lấy tên loại ghế
     */
    private function getSeatTypeName($seatType) {
        $types = ['normal' => 'Thường', 'vip' => 'VIP', 'couple' => 'Đôi'];
        return $types[$seatType] ?? 'Thường';
    }
}
