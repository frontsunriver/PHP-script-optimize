-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 27, 2021 at 07:10 AM
-- Server version: 10.3.31-MariaDB-log-cll-lve
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sixpenceco_raymond`
--

-- --------------------------------------------------------

--
-- Table structure for table `address_repository_1`
--

CREATE TABLE `address_repository_1` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `address_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `prefix` varchar(100) DEFAULT NULL,
  `suffix` varchar(100) DEFAULT NULL,
  `address_1` varchar(100) NOT NULL,
  `address_2` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `phone_company` varchar(100) DEFAULT NULL,
  `phone_toll_free` varchar(100) DEFAULT NULL,
  `phone_contact` varchar(100) DEFAULT NULL,
  `phone_cell` varchar(100) DEFAULT NULL,
  `phone_fax` varchar(100) DEFAULT NULL,
  `phone_company_ext` varchar(100) DEFAULT NULL,
  `phone_contact_ext` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `company_p_key` varchar(100) DEFAULT NULL,
  `people_p_key` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `address_repository_2`
--

CREATE TABLE `address_repository_2` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `address_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `prefix` varchar(100) DEFAULT NULL,
  `suffix` varchar(100) DEFAULT NULL,
  `address_1` varchar(100) NOT NULL,
  `address_2` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `phone_company` varchar(100) DEFAULT NULL,
  `phone_toll_free` varchar(100) DEFAULT NULL,
  `phone_contact` varchar(100) DEFAULT NULL,
  `phone_cell` varchar(100) DEFAULT NULL,
  `phone_fax` varchar(100) DEFAULT NULL,
  `phone_company_ext` varchar(100) DEFAULT NULL,
  `phone_contact_ext` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `company_p_key` varchar(100) DEFAULT NULL,
  `people_p_key` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contacts_1`
--

CREATE TABLE `contacts_1` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `contact_type` varchar(100) NOT NULL,
  `contact_address_id` varchar(100) NOT NULL,
  `export_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contacts_2`
--

CREATE TABLE `contacts_2` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `contact_type` varchar(100) NOT NULL,
  `contact_address_id` varchar(100) NOT NULL,
  `export_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers_1`
--

CREATE TABLE `customers_1` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `customer_id` varchar(100) NOT NULL,
  `customer_address_id` varchar(100) NOT NULL,
  `customer_specific_sales_tax` decimal(19,2) NOT NULL,
  `customer_specific_markup` decimal(19,2) NOT NULL,
  `export_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers_2`
--

CREATE TABLE `customers_2` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `customer_id` varchar(100) NOT NULL,
  `customer_address_id` varchar(100) NOT NULL,
  `customer_specific_sales_tax` decimal(19,2) NOT NULL,
  `customer_specific_markup` decimal(19,2) NOT NULL,
  `export_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `line_items_1`
--

CREATE TABLE `line_items_1` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `line_item_id` varchar(100) NOT NULL,
  `item_number` int(11) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `line_items_2`
--

CREATE TABLE `line_items_2` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `line_item_id` varchar(100) NOT NULL,
  `item_number` int(11) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `processed_purchase_orders`
--

CREATE TABLE `processed_purchase_orders` (
  `id` int(11) NOT NULL,
  `purchase_order_id` varchar(100) DEFAULT NULL,
  `po_number` varchar(100) DEFAULT NULL,
  `project_id` varchar(100) DEFAULT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `job_ref` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `edit_date` datetime DEFAULT NULL,
  `taxable` tinyint(1) NOT NULL,
  `ap_account_name` varchar(100) NOT NULL,
  `ar_account_name` varchar(100) DEFAULT NULL,
  `net_price` decimal(19,2) NOT NULL,
  `freight_net` decimal(19,2) NOT NULL,
  `is_exported` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `processed_vendors`
--

CREATE TABLE `processed_vendors` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) DEFAULT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `job_ref` varchar(100) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `export_id` varchar(100) DEFAULT NULL,
  `freight_sell` decimal(19,2) NOT NULL,
  `sales_tax_total` decimal(19,2) NOT NULL,
  `sell_total` decimal(10,0) NOT NULL,
  `texable` tinyint(1) NOT NULL,
  `ap_account_name` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects_1`
--

CREATE TABLE `projects_1` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `code` varchar(100) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `bid_date` datetime DEFAULT NULL,
  `good_until_date` datetime DEFAULT NULL,
  `job_ref` varchar(100) DEFAULT NULL,
  `project_address_id` varchar(100) NOT NULL,
  `freight_sell_override` decimal(19,2) NOT NULL,
  `installation_sell_override` decimal(19,2) NOT NULL,
  `freight_taxable` tinyint(1) NOT NULL,
  `installation_taxable` tinyint(1) NOT NULL,
  `lock_sell` tinyint(1) NOT NULL,
  `sales_tax_percent` double NOT NULL,
  `sales_tax_override` decimal(19,2) NOT NULL,
  `marketing_category` varchar(100) NOT NULL,
  `read_only` tinyint(1) NOT NULL,
  `read_only_description` varchar(100) NOT NULL,
  `password_protected` tinyint(1) NOT NULL,
  `status` varchar(100) NOT NULL,
  `custom_filter` varchar(100) NOT NULL,
  `memo` varchar(100) NOT NULL,
  `opportunity_id` varchar(100) DEFAULT NULL,
  `custom_column_1_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects_2`
--

CREATE TABLE `projects_2` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `code` varchar(100) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `bid_date` datetime DEFAULT NULL,
  `good_until_date` datetime DEFAULT NULL,
  `job_ref` varchar(100) DEFAULT NULL,
  `project_address_id` varchar(100) NOT NULL,
  `freight_sell_override` decimal(19,2) NOT NULL,
  `installation_sell_override` decimal(19,2) NOT NULL,
  `freight_taxable` tinyint(1) NOT NULL,
  `installation_taxable` tinyint(1) NOT NULL,
  `lock_sell` tinyint(1) NOT NULL,
  `sales_tax_percent` double NOT NULL,
  `sales_tax_override` decimal(19,2) NOT NULL,
  `marketing_category` varchar(100) NOT NULL,
  `read_only` tinyint(1) NOT NULL,
  `read_only_description` varchar(100) NOT NULL,
  `password_protected` tinyint(1) NOT NULL,
  `status` varchar(100) NOT NULL,
  `custom_filter` varchar(100) NOT NULL,
  `memo` varchar(100) NOT NULL,
  `opportunity_id` varchar(100) DEFAULT NULL,
  `custom_column_1_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `purchase_order_id` varchar(100) NOT NULL,
  `vendor_id` varchar(100) NOT NULL,
  `po_number` varchar(100) DEFAULT NULL,
  `mail_to_address_id` varchar(100) NOT NULL,
  `ship_to_address_id` varchar(100) NOT NULL,
  `bill_to_address` varchar(100) NOT NULL,
  `buyer_address_id` varchar(100) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `edit_date` datetime DEFAULT NULL,
  `freight_billing` varchar(100) NOT NULL,
  `preferred_carrier` varchar(100) NOT NULL,
  `shipping_instructions` varchar(100) NOT NULL,
  `terms` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `instructions` varchar(100) NOT NULL,
  `notes` varchar(100) NOT NULL,
  `fob_point` varchar(100) NOT NULL,
  `required_date` datetime DEFAULT NULL,
  `ship_date` datetime DEFAULT NULL,
  `received_date` datetime DEFAULT NULL,
  `po_sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_line_items_1`
--

CREATE TABLE `sub_line_items_1` (
  `id` int(11) NOT NULL,
  `line_item_id` varchar(100) NOT NULL,
  `sub_line_item_id` varchar(100) NOT NULL,
  `item_type_code` int(11) NOT NULL,
  `item_type_description` varchar(100) DEFAULT NULL,
  `vendor_id` varchar(100) DEFAULT NULL,
  `purchase_order_id` varchar(100) DEFAULT NULL,
  `freight_data_id` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `stock_model` varchar(100) DEFAULT NULL,
  `alt_stock_model` varchar(100) DEFAULT NULL,
  `alt_model` varchar(100) DEFAULT NULL,
  `mfr_model` varchar(100) DEFAULT NULL,
  `catalog_prod_id` varchar(100) DEFAULT NULL,
  `custom_item` tinyint(1) NOT NULL,
  `from_configuration` tinyint(1) NOT NULL,
  `stock_item` tinyint(1) NOT NULL,
  `spec` varchar(100) DEFAULT NULL,
  `notes` varchar(100) DEFAULT NULL,
  `status_code` int(11) NOT NULL,
  `status_description` varchar(100) DEFAULT NULL,
  `selling_unit` varchar(100) DEFAULT NULL,
  `units_per_case` int(11) NOT NULL,
  `special_code` int(11) NOT NULL,
  `special_description` varchar(100) DEFAULT NULL,
  `call_for_pricing` tinyint(1) NOT NULL,
  `sell_price` decimal(19,2) NOT NULL,
  `sell_total` decimal(19,2) NOT NULL,
  `freight_sell` decimal(19,2) NOT NULL,
  `installation_sell` decimal(19,2) NOT NULL,
  `net_price` decimal(19,2) NOT NULL,
  `freight_net` decimal(19,2) NOT NULL,
  `installation_net` decimal(19,2) NOT NULL,
  `discount` varchar(100) DEFAULT NULL,
  `list_price` decimal(19,2) NOT NULL,
  `is_net_priced_item` tinyint(1) NOT NULL,
  `taxable` tinyint(1) NOT NULL,
  `rebate` decimal(19,2) NOT NULL,
  `cash_discount` decimal(19,2) NOT NULL,
  `freight_class` varchar(100) DEFAULT NULL,
  `weight` decimal(19,2) NOT NULL,
  `cube` decimal(19,2) NOT NULL,
  `width` decimal(19,2) NOT NULL,
  `depth` decimal(19,2) NOT NULL,
  `height` decimal(19,2) NOT NULL,
  `serial_nbr` varchar(100) DEFAULT NULL,
  `gtin` varchar(100) DEFAULT NULL,
  `ship_from_address_id` varchar(100) DEFAULT NULL,
  `spec_remarks` varchar(100) DEFAULT NULL,
  `prime` varchar(100) DEFAULT NULL,
  `equal_1` varchar(100) DEFAULT NULL,
  `equal_2` varchar(100) DEFAULT NULL,
  `alt` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_line_items_2`
--

CREATE TABLE `sub_line_items_2` (
  `id` int(11) NOT NULL,
  `line_item_id` varchar(100) NOT NULL,
  `sub_line_item_id` varchar(100) NOT NULL,
  `item_type_code` int(11) NOT NULL,
  `item_type_description` varchar(100) DEFAULT NULL,
  `vendor_id` varchar(100) DEFAULT NULL,
  `purchase_order_id` varchar(100) DEFAULT NULL,
  `freight_data_id` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `stock_model` varchar(100) DEFAULT NULL,
  `alt_stock_model` varchar(100) DEFAULT NULL,
  `alt_model` varchar(100) DEFAULT NULL,
  `mfr_model` varchar(100) DEFAULT NULL,
  `catalog_prod_id` varchar(100) DEFAULT NULL,
  `custom_item` tinyint(1) NOT NULL,
  `from_configuration` tinyint(1) NOT NULL,
  `stock_item` tinyint(1) NOT NULL,
  `spec` varchar(100) DEFAULT NULL,
  `notes` varchar(100) DEFAULT NULL,
  `status_code` int(11) NOT NULL,
  `status_description` varchar(100) DEFAULT NULL,
  `selling_unit` varchar(100) DEFAULT NULL,
  `units_per_case` int(11) NOT NULL,
  `special_code` int(11) NOT NULL,
  `special_description` varchar(100) DEFAULT NULL,
  `call_for_pricing` tinyint(1) NOT NULL,
  `sell_price` decimal(19,2) NOT NULL,
  `sell_total` decimal(19,2) NOT NULL,
  `freight_sell` decimal(19,2) NOT NULL,
  `installation_sell` decimal(19,2) NOT NULL,
  `net_price` decimal(19,2) NOT NULL,
  `freight_net` decimal(19,2) NOT NULL,
  `installation_net` decimal(19,2) NOT NULL,
  `discount` varchar(100) DEFAULT NULL,
  `list_price` decimal(19,2) NOT NULL,
  `is_net_priced_item` tinyint(1) NOT NULL,
  `taxable` tinyint(1) NOT NULL,
  `rebate` decimal(19,2) NOT NULL,
  `cash_discount` decimal(19,2) NOT NULL,
  `freight_class` varchar(100) DEFAULT NULL,
  `weight` decimal(19,2) NOT NULL,
  `cube` decimal(19,2) NOT NULL,
  `width` decimal(19,2) NOT NULL,
  `depth` decimal(19,2) NOT NULL,
  `height` decimal(19,2) NOT NULL,
  `serial_nbr` varchar(100) DEFAULT NULL,
  `gtin` varchar(100) DEFAULT NULL,
  `ship_from_address_id` varchar(100) DEFAULT NULL,
  `spec_remarks` varchar(100) DEFAULT NULL,
  `prime` varchar(100) DEFAULT NULL,
  `equal_1` varchar(100) DEFAULT NULL,
  `equal_2` varchar(100) DEFAULT NULL,
  `alt` varchar(100) DEFAULT NULL,
  `is_exported` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `totals_1`
--

CREATE TABLE `totals_1` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `customer_id` varchar(100) DEFAULT NULL,
  `merchandise_sell` decimal(19,2) NOT NULL,
  `merchandise_markup` decimal(19,2) NOT NULL,
  `merchandise_net` decimal(19,2) NOT NULL,
  `freight_sell` decimal(19,2) NOT NULL,
  `freight_markup` decimal(19,2) NOT NULL,
  `freight_net` decimal(19,2) NOT NULL,
  `installation_sell` decimal(19,2) NOT NULL,
  `installation_markup` decimal(19,2) NOT NULL,
  `installation_net` decimal(19,2) NOT NULL,
  `gross_profit_percent` decimal(19,2) NOT NULL,
  `gross_profit_amount` decimal(19,2) NOT NULL,
  `sales_tax_total` decimal(19,2) NOT NULL,
  `sales_tax_percentage` decimal(19,2) NOT NULL,
  `grand_sell_total` decimal(19,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `totals_2`
--

CREATE TABLE `totals_2` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `customer_id` varchar(100) DEFAULT NULL,
  `merchandise_sell` decimal(19,2) NOT NULL,
  `merchandise_markup` decimal(19,2) NOT NULL,
  `merchandise_net` decimal(19,2) NOT NULL,
  `freight_sell` decimal(19,2) NOT NULL,
  `freight_markup` decimal(19,2) NOT NULL,
  `freight_net` decimal(19,2) NOT NULL,
  `installation_sell` decimal(19,2) NOT NULL,
  `installation_markup` decimal(19,2) NOT NULL,
  `installation_net` decimal(19,2) NOT NULL,
  `gross_profit_percent` decimal(19,2) NOT NULL,
  `gross_profit_amount` decimal(19,2) NOT NULL,
  `sales_tax_total` decimal(19,2) NOT NULL,
  `sales_tax_percentage` decimal(19,2) NOT NULL,
  `grand_sell_total` decimal(19,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vendors_1`
--

CREATE TABLE `vendors_1` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `vendor_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_name` varchar(100) NOT NULL,
  `catalog_vendor_id` varchar(100) NOT NULL,
  `vendor_address_id` varchar(100) NOT NULL,
  `rep_address_id` varchar(100) DEFAULT NULL,
  `agent_address_id` varchar(100) DEFAULT NULL,
  `prime_spec` varchar(100) NOT NULL,
  `terms` varchar(100) NOT NULL,
  `good_until_date` datetime DEFAULT NULL,
  `free_freight` tinyint(1) NOT NULL,
  `pop_email` varchar(100) NOT NULL,
  `export_id` varchar(100) NOT NULL,
  `vendor_notes` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vendors_2`
--

CREATE TABLE `vendors_2` (
  `id` int(11) NOT NULL,
  `project_id` varchar(100) NOT NULL,
  `vendor_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_name` varchar(100) NOT NULL,
  `catalog_vendor_id` varchar(100) NOT NULL,
  `vendor_address_id` varchar(100) NOT NULL,
  `rep_address_id` varchar(100) DEFAULT NULL,
  `agent_address_id` varchar(100) DEFAULT NULL,
  `prime_spec` varchar(100) NOT NULL,
  `terms` varchar(100) NOT NULL,
  `good_until_date` datetime DEFAULT NULL,
  `free_freight` tinyint(1) NOT NULL,
  `pop_email` varchar(100) NOT NULL,
  `export_id` varchar(100) NOT NULL,
  `vendor_notes` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address_repository_1`
--
ALTER TABLE `address_repository_1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `address_repository_2`
--
ALTER TABLE `address_repository_2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `contacts_1`
--
ALTER TABLE `contacts_1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `contacts_2`
--
ALTER TABLE `contacts_2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `customers_1`
--
ALTER TABLE `customers_1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `customers_2`
--
ALTER TABLE `customers_2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `line_items_1`
--
ALTER TABLE `line_items_1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `line_item_id` (`line_item_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `line_items_2`
--
ALTER TABLE `line_items_2`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `line_item_id` (`line_item_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `projects_1`
--
ALTER TABLE `projects_1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_id` (`project_id`);

--
-- Indexes for table `projects_2`
--
ALTER TABLE `projects_2`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_id` (`project_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `sub_line_items_1`
--
ALTER TABLE `sub_line_items_1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_line_item_id` (`sub_line_item_id`),
  ADD KEY `line_item_id` (`line_item_id`);

--
-- Indexes for table `sub_line_items_2`
--
ALTER TABLE `sub_line_items_2`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_line_item_id` (`sub_line_item_id`),
  ADD KEY `line_item_id` (`line_item_id`);

--
-- Indexes for table `totals_1`
--
ALTER TABLE `totals_1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `totals_2`
--
ALTER TABLE `totals_2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `vendors_1`
--
ALTER TABLE `vendors_1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `vendors_2`
--
ALTER TABLE `vendors_2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address_repository_1`
--
ALTER TABLE `address_repository_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `address_repository_2`
--
ALTER TABLE `address_repository_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts_1`
--
ALTER TABLE `contacts_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts_2`
--
ALTER TABLE `contacts_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers_1`
--
ALTER TABLE `customers_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers_2`
--
ALTER TABLE `customers_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `line_items_1`
--
ALTER TABLE `line_items_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `line_items_2`
--
ALTER TABLE `line_items_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects_1`
--
ALTER TABLE `projects_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects_2`
--
ALTER TABLE `projects_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_line_items_1`
--
ALTER TABLE `sub_line_items_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_line_items_2`
--
ALTER TABLE `sub_line_items_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `totals_1`
--
ALTER TABLE `totals_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `totals_2`
--
ALTER TABLE `totals_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors_1`
--
ALTER TABLE `vendors_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors_2`
--
ALTER TABLE `vendors_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address_repository_1`
--
ALTER TABLE `address_repository_1`
  ADD CONSTRAINT `address_repository_1_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_1` (`project_id`);

--
-- Constraints for table `address_repository_2`
--
ALTER TABLE `address_repository_2`
  ADD CONSTRAINT `address_repository_2_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_2` (`project_id`);

--
-- Constraints for table `contacts_1`
--
ALTER TABLE `contacts_1`
  ADD CONSTRAINT `contacts_1_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_1` (`project_id`);

--
-- Constraints for table `contacts_2`
--
ALTER TABLE `contacts_2`
  ADD CONSTRAINT `contacts_2_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_2` (`project_id`);

--
-- Constraints for table `customers_1`
--
ALTER TABLE `customers_1`
  ADD CONSTRAINT `customers_1_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_1` (`project_id`);

--
-- Constraints for table `customers_2`
--
ALTER TABLE `customers_2`
  ADD CONSTRAINT `customers_2_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_2` (`project_id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_2` (`project_id`);

--
-- Constraints for table `sub_line_items_1`
--
ALTER TABLE `sub_line_items_1`
  ADD CONSTRAINT `sub_line_items_1_ibfk_1` FOREIGN KEY (`line_item_id`) REFERENCES `line_items_1` (`line_item_id`);

--
-- Constraints for table `sub_line_items_2`
--
ALTER TABLE `sub_line_items_2`
  ADD CONSTRAINT `sub_line_items_2_ibfk_1` FOREIGN KEY (`line_item_id`) REFERENCES `line_items_2` (`line_item_id`);

--
-- Constraints for table `totals_1`
--
ALTER TABLE `totals_1`
  ADD CONSTRAINT `totals_1_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_1` (`project_id`);

--
-- Constraints for table `totals_2`
--
ALTER TABLE `totals_2`
  ADD CONSTRAINT `totals_2_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_2` (`project_id`);

--
-- Constraints for table `vendors_1`
--
ALTER TABLE `vendors_1`
  ADD CONSTRAINT `vendors_1_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_1` (`project_id`);

--
-- Constraints for table `vendors_2`
--
ALTER TABLE `vendors_2`
  ADD CONSTRAINT `vendors_2_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects_2` (`project_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
