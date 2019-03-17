
-- Thanaseelan Return return_check
ALTER TABLE `sales_voucher_details` ADD `return_check` VARCHAR(255) NULL DEFAULT 'no' AFTER `sales_voucher_products_narration`;