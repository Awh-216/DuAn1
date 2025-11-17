-- Migration: Thêm type 'deposit' vào bảng transactions
-- Chạy lệnh này trong phpMyAdmin hoặc MySQL client

ALTER TABLE `transactions` 
MODIFY `type` ENUM('ticket','subscription','deposit') NOT NULL;

