
-- Thanaseelan Return return_check
ALTER TABLE `sales_voucher_details` ADD `return_check` VARCHAR(255) NULL DEFAULT 'no' AFTER `sales_voucher_products_narration`;
-- Thanaseelan for sales created by and created on
ALTER TABLE `sales_details` ADD `sales_added_by` INT(11) NULL DEFAULT NULL AFTER `sales_remarks`, ADD `sales_added_on` DATETIME NULL DEFAULT NULL AFTER `sales_added_by`;