/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 9.1.0 : Database - tra_monitoring
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tra_monitoring` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `tra_monitoring`;

/*Table structure for table `audit_observations` */

DROP TABLE IF EXISTS `audit_observations`;

CREATE TABLE `audit_observations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `audit_year` year NOT NULL,
  `total_observations` int NOT NULL,
  `resolved_observations` int NOT NULL,
  `resolution_ratio` decimal(5,2) DEFAULT NULL,
  `pending_details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `audit_observations` */

/*Table structure for table `budget_utilisation` */

DROP TABLE IF EXISTS `budget_utilisation`;

CREATE TABLE `budget_utilisation` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `financial_year` year NOT NULL,
  `quarter` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allocated_amount` decimal(15,2) NOT NULL,
  `utilised_amount` decimal(15,2) NOT NULL,
  `utilisation_percentage` decimal(5,2) DEFAULT NULL,
  `department` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `budget_utilisation` */

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `carbon_footprint_projects` */

DROP TABLE IF EXISTS `carbon_footprint_projects`;

CREATE TABLE `carbon_footprint_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carbon_reduction_tonnes` decimal(10,2) DEFAULT NULL,
  `measurement_methodology` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('ongoing','completed','planned') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `carbon_footprint_projects` */

/*Table structure for table `circular_economy_initiatives` */

DROP TABLE IF EXISTS `circular_economy_initiatives`;

CREATE TABLE `circular_economy_initiatives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `initiative_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('recycling','upcycling','waste_utilisation','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `launch_date` date NOT NULL,
  `impact_metrics` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `circular_economy_initiatives` */

/*Table structure for table `cluster_outreach` */

DROP TABLE IF EXISTS `cluster_outreach`;

CREATE TABLE `cluster_outreach` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cluster_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `smes_supported` int NOT NULL,
  `intervention_type` enum('technical','financial','training','consultancy') COLLATE utf8mb4_unicode_ci NOT NULL,
  `intervention_date` date NOT NULL,
  `outcomes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cluster_outreach` */

/*Table structure for table `commercialization_revenues` */

DROP TABLE IF EXISTS `commercialization_revenues`;

CREATE TABLE `commercialization_revenues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `technology_transfer_id` bigint unsigned DEFAULT NULL,
  `revenue_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revenue_type` enum('Licensing Fee','Royalty','Consultancy','Testing Services','Training Programs','Patent Sale','Joint Development','Technology Sale','Prototype Sale','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `revenue_source` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_date` date NOT NULL,
  `financial_year` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `amount_inr` decimal(15,2) NOT NULL COMMENT 'Converted to INR',
  `payment_status` enum('Expected','Received','Partially Received','Overdue','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Expected',
  `expected_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revenue_category` enum('Recurring','One-time') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'One-time',
  `related_patent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_publication` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commercialization_revenues_revenue_code_unique` (`revenue_code`),
  KEY `commercialization_revenues_tra_id_foreign` (`tra_id`),
  KEY `commercialization_revenues_project_id_foreign` (`project_id`),
  KEY `commercialization_revenues_technology_transfer_id_foreign` (`technology_transfer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `commercialization_revenues` */

/*Table structure for table `compliance_reports` */

DROP TABLE IF EXISTS `compliance_reports`;

CREATE TABLE `compliance_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` date NOT NULL,
  `submission_date` date DEFAULT NULL,
  `submitted_on_time` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','submitted','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `ministry_department` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `compliance_reports` */

/*Table structure for table `digitization_index` */

DROP TABLE IF EXISTS `digitization_index`;

CREATE TABLE `digitization_index` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` year NOT NULL,
  `system_type` enum('MIS','HR','Lab') COLLATE utf8mb4_unicode_ci NOT NULL,
  `automation_percentage` decimal(5,2) NOT NULL,
  `modules_automated` text COLLATE utf8mb4_unicode_ci,
  `pending_modules` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `digitization_index` */

/*Table structure for table `eco_friendly_technologies` */

DROP TABLE IF EXISTS `eco_friendly_technologies`;

CREATE TABLE `eco_friendly_technologies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `technology_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('process','product') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sustainability_contribution` text COLLATE utf8mb4_unicode_ci,
  `energy_reduction_percentage` decimal(5,2) DEFAULT NULL,
  `water_reduction_percentage` decimal(5,2) DEFAULT NULL,
  `chemical_reduction_percentage` decimal(5,2) DEFAULT NULL,
  `development_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `eco_friendly_technologies` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `financial_records` */

DROP TABLE IF EXISTS `financial_records`;

CREATE TABLE `financial_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_type` enum('Release','Expenditure','Adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_head` enum('Manpower','Equipment','Consumables','Travel','Overhead','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `voucher_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_records_project_id_foreign` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `financial_records` */

insert  into `financial_records`(`id`,`project_id`,`transaction_date`,`transaction_type`,`expense_head`,`amount`,`voucher_number`,`description`,`approved_by`,`created_at`,`updated_at`) values 
(1,1,'2023-01-15','Release','Other',4500000.00,'INIT-AMRA-2023-001','Initial fund release for project setup and equipment procurement','Dr. S. Ramanathan','2025-11-28 07:46:39','2025-11-28 07:46:39'),
(2,1,'2023-02-15','Expenditure','Equipment',3525000.00,'EQP-AMRA-2023-001-001','Procurement of laboratory equipment and instruments','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(3,1,'2023-02-15','Expenditure','Manpower',1175000.00,'MP-AMRA-2023-001-001','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(4,1,'2023-03-15','Expenditure','Manpower',1175000.00,'MP-AMRA-2023-001-002','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(5,1,'2023-04-15','Expenditure','Manpower',1175000.00,'MP-AMRA-2023-001-003','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(6,1,'2023-05-15','Expenditure','Manpower',1175000.00,'MP-AMRA-2023-001-004','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(7,1,'2023-06-15','Expenditure','Manpower',1175000.00,'MP-AMRA-2023-001-005','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(8,1,'2023-07-15','Expenditure','Manpower',1175000.00,'MP-AMRA-2023-001-006','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(9,1,'2023-04-15','Expenditure','Consumables',1880000.00,'CONS-AMRA-2023-001-001','Purchase of laboratory consumables and chemicals','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(10,1,'2023-05-15','Expenditure','Travel',940000.00,'TRV-AMRA-2023-001-001','Travel for field work and conference participation','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(11,1,'2023-07-15','Release','Other',6000000.00,'REL-AMRA-2023-001-002','Second installment release for ongoing activities','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(12,1,'2023-06-15','Expenditure','Overhead',705000.00,'OH-AMRA-2023-001-001','Infrastructure and administrative overhead costs','Finance Department','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(13,2,'2023-03-01','Release','Other',3600000.00,'INIT-BIC-2023-002','Initial fund release for project setup and equipment procurement','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(14,2,'2023-04-01','Expenditure','Equipment',2475000.00,'EQP-BIC-2023-002-001','Procurement of laboratory equipment and instruments','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(15,2,'2023-04-01','Expenditure','Manpower',825000.00,'MP-BIC-2023-002-001','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(16,2,'2023-05-01','Expenditure','Manpower',825000.00,'MP-BIC-2023-002-002','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(17,2,'2023-06-01','Expenditure','Manpower',825000.00,'MP-BIC-2023-002-003','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(18,2,'2023-07-01','Expenditure','Manpower',825000.00,'MP-BIC-2023-002-004','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(19,2,'2023-08-01','Expenditure','Manpower',825000.00,'MP-BIC-2023-002-005','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(20,2,'2023-09-01','Expenditure','Manpower',825000.00,'MP-BIC-2023-002-006','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(21,2,'2023-06-01','Expenditure','Consumables',1320000.00,'CONS-BIC-2023-002-001','Purchase of laboratory consumables and chemicals','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(22,2,'2023-07-01','Expenditure','Travel',660000.00,'TRV-BIC-2023-002-001','Travel for field work and conference participation','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(23,2,'2023-09-01','Release','Other',4800000.00,'REL-BIC-2023-002-002','Second installment release for ongoing activities','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(24,2,'2023-08-01','Expenditure','Overhead',495000.00,'OH-BIC-2023-002-001','Infrastructure and administrative overhead costs','Finance Department','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(25,3,'2023-02-10','Release','Other',5400000.00,'INIT-CERF-2023-003','Initial fund release for project setup and equipment procurement','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(26,3,'2023-03-10','Expenditure','Equipment',4425000.00,'EQP-CERF-2023-003-001','Procurement of laboratory equipment and instruments','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(27,3,'2023-03-10','Expenditure','Manpower',1475000.00,'MP-CERF-2023-003-001','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(28,3,'2023-04-10','Expenditure','Manpower',1475000.00,'MP-CERF-2023-003-002','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(29,3,'2023-05-10','Expenditure','Manpower',1475000.00,'MP-CERF-2023-003-003','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(30,3,'2023-06-10','Expenditure','Manpower',1475000.00,'MP-CERF-2023-003-004','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(31,3,'2023-07-10','Expenditure','Manpower',1475000.00,'MP-CERF-2023-003-005','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(32,3,'2023-08-10','Expenditure','Manpower',1475000.00,'MP-CERF-2023-003-006','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(33,3,'2023-05-10','Expenditure','Consumables',2360000.00,'CONS-CERF-2023-003-001','Purchase of laboratory consumables and chemicals','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(34,3,'2023-06-10','Expenditure','Travel',1180000.00,'TRV-CERF-2023-003-001','Travel for field work and conference participation','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(35,3,'2023-08-10','Release','Other',7200000.00,'REL-CERF-2023-003-002','Second installment release for ongoing activities','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(36,3,'2023-07-10','Expenditure','Overhead',885000.00,'OH-CERF-2023-003-001','Infrastructure and administrative overhead costs','Finance Department','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(37,4,'2023-04-01','Release','Other',3000000.00,'INIT-DTRG-2023-004','Initial fund release for project setup and equipment procurement','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(38,4,'2023-05-01','Expenditure','Equipment',2025000.00,'EQP-DTRG-2023-004-001','Procurement of laboratory equipment and instruments','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(39,4,'2023-05-01','Expenditure','Manpower',675000.00,'MP-DTRG-2023-004-001','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(40,4,'2023-06-01','Expenditure','Manpower',675000.00,'MP-DTRG-2023-004-002','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(41,4,'2023-07-01','Expenditure','Manpower',675000.00,'MP-DTRG-2023-004-003','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(42,4,'2023-08-01','Expenditure','Manpower',675000.00,'MP-DTRG-2023-004-004','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(43,4,'2023-09-01','Expenditure','Manpower',675000.00,'MP-DTRG-2023-004-005','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(44,4,'2023-10-01','Expenditure','Manpower',675000.00,'MP-DTRG-2023-004-006','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(45,4,'2023-07-01','Expenditure','Consumables',1080000.00,'CONS-DTRG-2023-004-001','Purchase of laboratory consumables and chemicals','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(46,4,'2023-08-01','Expenditure','Travel',540000.00,'TRV-DTRG-2023-004-001','Travel for field work and conference participation','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(47,4,'2023-10-01','Release','Other',4000000.00,'REL-DTRG-2023-004-002','Second installment release for ongoing activities','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(48,4,'2023-09-01','Expenditure','Overhead',405000.00,'OH-DTRG-2023-004-001','Infrastructure and administrative overhead costs','Finance Department','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(49,5,'2023-05-15','Release','Other',2100000.00,'INIT-HIL-2023-005','Initial fund release for project setup and equipment procurement','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(50,5,'2023-06-15','Expenditure','Equipment',1650000.00,'EQP-HIL-2023-005-001','Procurement of laboratory equipment and instruments','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(51,5,'2023-06-15','Expenditure','Manpower',550000.00,'MP-HIL-2023-005-001','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(52,5,'2023-07-15','Expenditure','Manpower',550000.00,'MP-HIL-2023-005-002','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(53,5,'2023-08-15','Expenditure','Manpower',550000.00,'MP-HIL-2023-005-003','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(54,5,'2023-09-15','Expenditure','Manpower',550000.00,'MP-HIL-2023-005-004','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(55,5,'2023-10-15','Expenditure','Manpower',550000.00,'MP-HIL-2023-005-005','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(56,5,'2023-11-15','Expenditure','Manpower',550000.00,'MP-HIL-2023-005-006','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(57,5,'2023-08-15','Expenditure','Consumables',880000.00,'CONS-HIL-2023-005-001','Purchase of laboratory consumables and chemicals','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(58,5,'2023-09-15','Expenditure','Travel',440000.00,'TRV-HIL-2023-005-001','Travel for field work and conference participation','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(59,5,'2023-11-15','Release','Other',2800000.00,'REL-HIL-2023-005-002','Second installment release for ongoing activities','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(60,5,'2023-10-15','Expenditure','Overhead',330000.00,'OH-HIL-2023-005-001','Infrastructure and administrative overhead costs','Finance Department','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(61,6,'2022-01-10','Release','Other',5250000.00,'INIT-AMRA-2022-001','Initial fund release for project setup and equipment procurement','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(62,6,'2022-02-10','Expenditure','Equipment',2625000.00,'EQP-AMRA-2022-001-001','Procurement of laboratory equipment and instruments','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(63,6,'2022-02-10','Expenditure','Manpower',875000.00,'MP-AMRA-2022-001-001','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(64,6,'2022-03-10','Expenditure','Manpower',875000.00,'MP-AMRA-2022-001-002','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(65,6,'2022-04-10','Expenditure','Manpower',875000.00,'MP-AMRA-2022-001-003','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(66,6,'2022-05-10','Expenditure','Manpower',875000.00,'MP-AMRA-2022-001-004','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(67,6,'2022-06-10','Expenditure','Manpower',875000.00,'MP-AMRA-2022-001-005','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(68,6,'2022-07-10','Expenditure','Manpower',875000.00,'MP-AMRA-2022-001-006','Monthly manpower costs including salaries and stipends','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(69,6,'2022-04-10','Expenditure','Consumables',1400000.00,'CONS-AMRA-2022-001-001','Purchase of laboratory consumables and chemicals','Dr. S. Ramanathan','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(70,6,'2022-05-10','Expenditure','Travel',700000.00,'TRV-AMRA-2022-001-001','Travel for field work and conference participation','Project Coordinator','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(71,6,'2022-06-10','Expenditure','Overhead',525000.00,'OH-AMRA-2022-001-001','Infrastructure and administrative overhead costs','Finance Department','2025-11-28 07:46:40','2025-11-28 07:46:40');

/*Table structure for table `gem_procurement` */

DROP TABLE IF EXISTS `gem_procurement`;

CREATE TABLE `gem_procurement` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tra_id` bigint unsigned NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `type` enum('goods','service') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gem_procurement_tra_id_foreign` (`tra_id`),
  KEY `gem_procurement_user_id_month_year_index` (`user_id`,`month`,`year`),
  KEY `gem_procurement_type_month_year_index` (`type`,`month`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `gem_procurement` */

/*Table structure for table `global_participations` */

DROP TABLE IF EXISTS `global_participations`;

CREATE TABLE `global_participations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('exhibition','conference') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_date` date NOT NULL,
  `representatives_count` int NOT NULL,
  `outcomes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `global_participations` */

/*Table structure for table `industry_collaborations` */

DROP TABLE IF EXISTS `industry_collaborations`;

CREATE TABLE `industry_collaborations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `collaboration_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objectives` text COLLATE utf8mb4_unicode_ci,
  `collaboration_type` enum('MoU','Sponsored Research','Consultancy Project','Joint R&D','Testing Services','Training Program','Technology Development','Incubation Support','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `industry_partner_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partner_type` enum('MSME','Large Enterprise','Startup','PSU','MNC','Cooperative','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MSME',
  `industry_sector` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partner_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signing_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `duration_months` int NOT NULL,
  `agreement_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Active','Completed','Extended','Terminated','On Hold') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `funding_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount_received` decimal(15,2) NOT NULL DEFAULT '0.00',
  `funding_type` enum('Paid','In-kind','Mixed','No Cost') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Paid',
  `deliverables` json DEFAULT NULL,
  `outcomes_achieved` text COLLATE utf8mb4_unicode_ci,
  `publications_generated` int NOT NULL DEFAULT '0',
  `patents_filed` int NOT NULL DEFAULT '0',
  `prototypes_developed` int NOT NULL DEFAULT '0',
  `tra_coordinator` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tra_team_members` json DEFAULT NULL,
  `tra_manpower_deployed` int NOT NULL DEFAULT '0',
  `technology_impact` text COLLATE utf8mb4_unicode_ci,
  `social_impact` text COLLATE utf8mb4_unicode_ci,
  `satisfaction_score` decimal(3,2) DEFAULT NULL COMMENT 'Out of 5',
  `agreement_document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completion_report_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `industry_collaborations_collaboration_code_unique` (`collaboration_code`),
  KEY `industry_collaborations_tra_id_foreign` (`tra_id`),
  KEY `industry_collaborations_project_id_foreign` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `industry_collaborations` */

/*Table structure for table `industry_feedback` */

DROP TABLE IF EXISTS `industry_feedback`;

CREATE TABLE `industry_feedback` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `technology_transfer_id` bigint unsigned DEFAULT NULL,
  `industry_collaboration_id` bigint unsigned DEFAULT NULL,
  `feedback_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `industry_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_designation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feedback_date` date NOT NULL,
  `feedback_type` enum('Project Delivery','Technology Transfer','Consultancy','Testing Service','Training','General') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Project Delivery',
  `quality_of_deliverables` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `technical_expertise` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `timeliness` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `cost_effectiveness` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `innovation_level` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `problem_solving_ability` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `communication` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `scalability_of_solution` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `commercial_viability` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `after_support` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `overall_satisfaction` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT 'Out of 5',
  `strengths` text COLLATE utf8mb4_unicode_ci,
  `areas_of_improvement` text COLLATE utf8mb4_unicode_ci,
  `suggestions` text COLLATE utf8mb4_unicode_ci,
  `additional_comments` text COLLATE utf8mb4_unicode_ci,
  `business_impact` enum('Very High','High','Moderate','Low','No Impact') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `impact_description` text COLLATE utf8mb4_unicode_ci,
  `cost_savings_achieved` decimal(15,2) DEFAULT NULL,
  `revenue_increase_attributed` decimal(15,2) DEFAULT NULL,
  `likelihood_to_recommend` enum('Very Likely','Likely','Neutral','Unlikely','Very Unlikely') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Neutral',
  `willing_for_future_collaboration` tinyint(1) NOT NULL DEFAULT '1',
  `future_requirements` text COLLATE utf8mb4_unicode_ci,
  `verification_status` enum('Pending','Verified','Disputed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `verified_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `industry_feedback_feedback_code_unique` (`feedback_code`),
  KEY `industry_feedback_tra_id_foreign` (`tra_id`),
  KEY `industry_feedback_project_id_foreign` (`project_id`),
  KEY `industry_feedback_technology_transfer_id_foreign` (`technology_transfer_id`),
  KEY `industry_feedback_industry_collaboration_id_foreign` (`industry_collaboration_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `industry_feedback` */

/*Table structure for table `instruments` */

DROP TABLE IF EXISTS `instruments`;

CREATE TABLE `instruments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `make` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_purchase` date NOT NULL,
  `test_parameters` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `test_standard` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `working_status` enum('Working','Under Maintenance','Out of Order','Calibration Due') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tra_id` bigint unsigned NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `instruments_tra_id_status_index` (`tra_id`,`status`),
  KEY `instruments_working_status_index` (`working_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `instruments` */

/*Table structure for table `international_collaborations` */

DROP TABLE IF EXISTS `international_collaborations`;

CREATE TABLE `international_collaborations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partner_institute` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_agency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','completed','planned') COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_amount` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `international_collaborations` */

/*Table structure for table `international_exposures` */

DROP TABLE IF EXISTS `international_exposures`;

CREATE TABLE `international_exposures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `scientist_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('training_abroad','conference','exchange_visit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `institution_or_event` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `international_exposures` */

/*Table structure for table `job_batches` */

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `job_batches` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jobs` */

/*Table structure for table `kpi_records` */

DROP TABLE IF EXISTS `kpi_records`;

CREATE TABLE `kpi_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `reporting_date` date NOT NULL,
  `kpi_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_value` decimal(10,2) NOT NULL,
  `achieved_value` decimal(10,2) NOT NULL,
  `variance` decimal(10,2) NOT NULL,
  `trend` enum('Improving','Stable','Declining') COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kpi_records_project_id_foreign` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kpi_records` */

/*Table structure for table `manpower_utilisation` */

DROP TABLE IF EXISTS `manpower_utilisation`;

CREATE TABLE `manpower_utilisation` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tra_id` bigint unsigned NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `technical_manpower` int NOT NULL DEFAULT '0',
  `working_day` int NOT NULL DEFAULT '0',
  `extra_man_day` int NOT NULL DEFAULT '0',
  `addtional_working` int NOT NULL DEFAULT '0',
  `special_work` int NOT NULL DEFAULT '0',
  `deputation` int NOT NULL DEFAULT '0',
  `leave_day` int NOT NULL DEFAULT '0',
  `total_s` int NOT NULL DEFAULT '0',
  `total_p` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manpower_utilisation_user_id_tra_id_month_year_unique` (`user_id`,`tra_id`,`month`,`year`),
  KEY `manpower_utilisation_tra_id_foreign` (`tra_id`),
  KEY `manpower_utilisation_user_id_month_year_index` (`user_id`,`month`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `manpower_utilisation` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2025_10_22_043032_create_tras_table',1),
(5,'2025_10_22_043101_create_projects_table',1),
(6,'2025_10_22_043155_create_milestones_table',1),
(7,'2025_10_22_043211_create_financial_records_table',1),
(8,'2025_10_22_043224_create_publications_table',1),
(9,'2025_10_22_043239_create_patents_table',1),
(10,'2025_10_22_043253_create_kpi_records_table',1),
(11,'2025_10_22_043310_create_industry_collaborations_table',1),
(12,'2025_10_27_062126_create_technology_transfers_table',1),
(13,'2025_10_27_062209_create_commercialization_revenues_table',1),
(14,'2025_10_27_062303_create_startup_spinoffs_table',1),
(15,'2025_10_27_062322_create_industry_feedback_table',1),
(16,'2025_10_31_090233_create_labms_tables',1),
(17,'2025_11_10_121704_create_training_programmes_table',1),
(18,'2025_11_10_121716_create_international_exposures_table',1),
(19,'2025_11_10_121723_create_staff_retention_table',1),
(20,'2025_11_10_121805_create_budget_utilisation_table',1),
(21,'2025_11_10_121812_create_audit_observations_table',1),
(22,'2025_11_10_121829_create_compliance_reports_table',1),
(23,'2025_11_10_121834_create_digitization_index_table',1),
(24,'2025_11_10_121842_create_eco_friendly_technologies_table',1),
(25,'2025_11_10_121848_create_circular_economy_initiatives_table',1),
(26,'2025_11_10_121854_create_carbon_footprint_projects_table',1),
(27,'2025_11_10_121859_create_cluster_outreach_table',1),
(28,'2025_11_10_121905_create_seminars_workshops_table',1),
(29,'2025_11_10_121913_create_international_collaborations_table',1),
(30,'2025_11_10_121926_create_visibility_metrics_table',1),
(31,'2025_11_11_044539_create_global_participations_table',1),
(32,'2025_11_20_044504_create_tra_scores_table',2),
(33,'2025_11_20_044507_create_tra_targets_table',2),
(34,'2025_11_20_044515_create_tra_kpi_mappings_table',2),
(35,'2025_11_22_061412_add_metrics_to_tras_table',2),
(36,'2025_11_27_113337_instruments',2);

/*Table structure for table `milestones` */

DROP TABLE IF EXISTS `milestones`;

CREATE TABLE `milestones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `planned_date` date NOT NULL,
  `actual_date` date DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Delayed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `weightage` int NOT NULL DEFAULT '10',
  `deliverables` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `milestones_project_id_foreign` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `milestones` */

insert  into `milestones`(`id`,`project_id`,`title`,`description`,`planned_date`,`actual_date`,`status`,`weightage`,`deliverables`,`remarks`,`created_at`,`updated_at`) values 
(1,1,'Project Kick-off and Team Formation','Initial project setup and team allocation','2023-02-28','2023-02-25','Completed',5,'Project charter, Team allocation document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(2,1,'Literature Review Completion','Comprehensive review of existing research and technologies','2023-04-30','2023-05-10','Completed',10,'Literature review report, Technology gap analysis',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(3,1,'Experimental Setup and Methodology Finalization','Establishment of experimental facilities and methodology validation','2023-07-31','2023-07-20','Completed',15,'Experimental setup, Methodology document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(4,1,'Initial Prototype Development','Development and testing of first prototype iteration','2023-10-31',NULL,'In Progress',25,'First prototype, Initial test results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(5,1,'Performance Optimization','Iterative improvement and optimization based on initial results','2024-02-29',NULL,'Pending',20,'Optimized prototype, Performance metrics',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(6,1,'Field Testing and Validation','Real-world testing and validation of optimized solution','2024-06-30',NULL,'Pending',15,'Field test report, Validation results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(7,1,'Final Documentation and Reporting','Preparation of final project documentation and reports','2024-09-30',NULL,'Pending',10,'Final project report, Technical documentation',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(8,2,'Project Kick-off and Team Formation','Initial project setup and team allocation','2023-02-28','2023-02-25','Completed',5,'Project charter, Team allocation document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(9,2,'Literature Review Completion','Comprehensive review of existing research and technologies','2023-04-30','2023-05-10','Completed',10,'Literature review report, Technology gap analysis',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(10,2,'Experimental Setup and Methodology Finalization','Establishment of experimental facilities and methodology validation','2023-07-31','2023-07-20','Completed',15,'Experimental setup, Methodology document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(11,2,'Initial Prototype Development','Development and testing of first prototype iteration','2023-10-31',NULL,'In Progress',25,'First prototype, Initial test results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(12,2,'Performance Optimization','Iterative improvement and optimization based on initial results','2024-02-29',NULL,'Pending',20,'Optimized prototype, Performance metrics',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(13,2,'Field Testing and Validation','Real-world testing and validation of optimized solution','2024-06-30',NULL,'Pending',15,'Field test report, Validation results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(14,2,'Final Documentation and Reporting','Preparation of final project documentation and reports','2024-09-30',NULL,'Pending',10,'Final project report, Technical documentation',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(15,3,'Project Kick-off and Team Formation','Initial project setup and team allocation','2023-02-28','2023-02-25','Completed',5,'Project charter, Team allocation document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(16,3,'Literature Review Completion','Comprehensive review of existing research and technologies','2023-04-30','2023-05-10','Completed',10,'Literature review report, Technology gap analysis',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(17,3,'Experimental Setup and Methodology Finalization','Establishment of experimental facilities and methodology validation','2023-07-31','2023-07-20','Completed',15,'Experimental setup, Methodology document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(18,3,'Initial Prototype Development','Development and testing of first prototype iteration','2023-10-31',NULL,'In Progress',25,'First prototype, Initial test results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(19,3,'Performance Optimization','Iterative improvement and optimization based on initial results','2024-02-29',NULL,'Pending',20,'Optimized prototype, Performance metrics',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(20,3,'Field Testing and Validation','Real-world testing and validation of optimized solution','2024-06-30',NULL,'Pending',15,'Field test report, Validation results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(21,3,'Final Documentation and Reporting','Preparation of final project documentation and reports','2024-09-30',NULL,'Pending',10,'Final project report, Technical documentation',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(22,4,'Project Kick-off and Team Formation','Initial project setup and team allocation','2023-02-28','2023-02-25','Completed',5,'Project charter, Team allocation document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(23,4,'Literature Review Completion','Comprehensive review of existing research and technologies','2023-04-30','2023-05-10','Completed',10,'Literature review report, Technology gap analysis',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(24,4,'Experimental Setup and Methodology Finalization','Establishment of experimental facilities and methodology validation','2023-07-31','2023-07-20','Completed',15,'Experimental setup, Methodology document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(25,4,'Initial Prototype Development','Development and testing of first prototype iteration','2023-10-31',NULL,'In Progress',25,'First prototype, Initial test results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(26,4,'Performance Optimization','Iterative improvement and optimization based on initial results','2024-02-29',NULL,'Pending',20,'Optimized prototype, Performance metrics',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(27,4,'Field Testing and Validation','Real-world testing and validation of optimized solution','2024-06-30',NULL,'Pending',15,'Field test report, Validation results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(28,4,'Final Documentation and Reporting','Preparation of final project documentation and reports','2024-09-30',NULL,'Pending',10,'Final project report, Technical documentation',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(29,5,'Project Kick-off and Team Formation','Initial project setup and team allocation','2023-02-28','2023-02-25','Completed',5,'Project charter, Team allocation document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(30,5,'Literature Review Completion','Comprehensive review of existing research and technologies','2023-04-30','2023-05-10','Completed',10,'Literature review report, Technology gap analysis',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(31,5,'Experimental Setup and Methodology Finalization','Establishment of experimental facilities and methodology validation','2023-07-31','2023-07-20','Completed',15,'Experimental setup, Methodology document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(32,5,'Initial Prototype Development','Development and testing of first prototype iteration','2023-10-31',NULL,'In Progress',25,'First prototype, Initial test results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(33,5,'Performance Optimization','Iterative improvement and optimization based on initial results','2024-02-29',NULL,'Pending',20,'Optimized prototype, Performance metrics',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(34,5,'Field Testing and Validation','Real-world testing and validation of optimized solution','2024-06-30',NULL,'Pending',15,'Field test report, Validation results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(35,5,'Final Documentation and Reporting','Preparation of final project documentation and reports','2024-09-30',NULL,'Pending',10,'Final project report, Technical documentation',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(36,6,'Literature Review and Technology Assessment','Comprehensive review of existing technologies and identification of research gaps','2022-03-31','2022-03-28','Completed',10,'Technology assessment report, Literature review document',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(37,6,'Material Synthesis and Characterization','Development of synthesis protocols and characterization of base materials','2022-06-30','2022-07-15','Completed',20,'Material synthesis protocols, Characterization reports',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(38,6,'Prototype Development','Fabrication of first-generation prototype and initial testing','2022-12-31','2023-01-20','Completed',30,'Working prototype, Initial test results',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(39,6,'Performance Validation','Rigorous testing and validation of prototype performance','2023-06-30','2023-06-15','Completed',25,'Validation report, Performance metrics',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39'),
(40,6,'Technology Transfer and Final Reporting','Preparation of final documentation and technology transfer to industry partners','2023-11-30','2023-11-20','Completed',15,'Final project report, Technology transfer documents',NULL,'2025-11-28 07:46:39','2025-11-28 07:46:39');

/*Table structure for table `months` */

DROP TABLE IF EXISTS `months`;

CREATE TABLE `months` (
  `month_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `month_title` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `month_number` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`month_id`),
  UNIQUE KEY `months_month_number_unique` (`month_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `months` */

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `patents` */

DROP TABLE IF EXISTS `patents`;

CREATE TABLE `patents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `tra_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inventors` json NOT NULL,
  `filing_date` date NOT NULL,
  `publication_date` date DEFAULT NULL,
  `grant_date` date DEFAULT NULL,
  `patent_office` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `application_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patent_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Filed','Published','Granted','Rejected','Abandoned') COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Product','Process','Design','Utility') COLLATE utf8mb4_unicode_ci NOT NULL,
  `abstract` text COLLATE utf8mb4_unicode_ci,
  `commercialized` tinyint(1) NOT NULL DEFAULT '0',
  `revenue_generated` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patents_project_id_foreign` (`project_id`),
  KEY `patents_tra_id_foreign` (`tra_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `patents` */

insert  into `patents`(`id`,`project_id`,`tra_id`,`title`,`inventors`,`filing_date`,`publication_date`,`grant_date`,`patent_office`,`application_number`,`patent_number`,`status`,`type`,`abstract`,`commercialized`,`revenue_generated`,`created_at`,`updated_at`) values 
(1,1,1,'Novel Graphene-Polymer Composite Material and Method of Preparation Thereof','\"[\\\"Dr. Ramesh Iyer\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','2023-06-15',NULL,NULL,'Indian Patent Office','2023764647',NULL,'Filed','Product','The present invention relates to a novel graphene-based composite material exhibiting exceptional mechanical strength and thermal stability, suitable for aerospace applications.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(2,1,1,'Method for Synthesizing Graphene Composites','\"[\\\"Dr. Ramesh Iyer\\\",\\\"Dr. Process Engineer\\\",\\\"Dr. Quality Manager\\\"]\"','2023-09-01',NULL,NULL,'European Patent Office','EP45117714',NULL,'Filed','Process','A manufacturing process for producing graphene-Polymer Composite Material with improved yield, reduced production costs, and enhanced product quality compared to conventional methods.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(3,2,2,'Novel Plastic-Degrading Enzyme Composition and Method of Preparation Thereof','\"[\\\"Dr. Ananya Reddy\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','2023-06-15',NULL,NULL,'Indian Patent Office','2023594130',NULL,'Filed','Product','The present invention relates to a novel enzymatic composition capable of efficiently degrading plastic waste under ambient conditions, addressing environmental pollution concerns.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(4,3,3,'Novel Thermal Energy Storage Material and Method of Preparation Thereof','\"[\\\"Dr. Vikram Singh\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','2023-06-15',NULL,NULL,'Indian Patent Office','2023531978',NULL,'Filed','Product','The present invention relates to an advanced thermal energy storage system utilizing phase change materials for efficient solar energy utilization in power generation applications.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(5,3,3,'Method for Manufacturing Thermal Storage Units','\"[\\\"Dr. Vikram Singh\\\",\\\"Dr. Process Engineer\\\",\\\"Dr. Quality Manager\\\"]\"','2023-09-01',NULL,NULL,'European Patent Office','EP90295240',NULL,'Filed','Process','A manufacturing process for producing thermal Energy Storage Material with improved yield, reduced production costs, and enhanced product quality compared to conventional methods.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(6,4,4,'Novel Predictive Maintenance Algorithm and Method of Preparation Thereof','\"[\\\"Dr. Sameer Deshpande\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','2023-06-15',NULL,NULL,'Indian Patent Office','2023548121',NULL,'Filed','Product','The present invention relates to a machine learning-based predictive maintenance system that accurately forecasts equipment failures in industrial IoT environments.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(7,5,5,'Novel Portable Diagnostic Device and Method of Preparation Thereof','\"[\\\"Dr. Nisha Verma\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','2023-06-15',NULL,NULL,'Indian Patent Office','2023431393',NULL,'Filed','Product','The present invention relates to a low-cost, portable diagnostic device for comprehensive health screening in resource-limited settings.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(8,6,1,'Novel Anti-Corrosion Nanocomposite Coating and Method of Preparation Thereof','\"[\\\"Dr. Sanjay Mehta\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','2023-06-15',NULL,NULL,'Indian Patent Office','2023750924',NULL,'Filed','Product','The present invention relates to an innovative technological solution addressing current challenges in the field with improved efficiency and performance characteristics.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(9,6,1,'Advanced Coating Application System for Enhanced Performance','\"[\\\"Dr. Sanjay Mehta\\\",\\\"Dr. Senior Researcher\\\",\\\"Dr. Technical Expert\\\"]\"','2022-03-20','2022-09-15','2023-05-10','Indian Patent Office','2022276652','IN450025','Granted','Process','The present invention relates to an innovative technological solution addressing current challenges in the field with improved efficiency and performance characteristics. The invention demonstrates significant improvements over existing technologies in terms of efficiency, cost-effectiveness, and environmental sustainability.',1,647813.00,'2025-11-28 07:46:40','2025-11-28 07:46:40'),
(10,6,1,'Improved Coating Formulation with Superior Characteristics','\"[\\\"Dr. Research Associate\\\",\\\"Dr. Sanjay Mehta\\\",\\\"Dr. Material Scientist\\\"]\"','2022-08-10','2023-02-28',NULL,'USPTO','US90892563',NULL,'Published','Product','A system and method for advanced Manufacturing Process comprising novel features that enhance performance, reliability, and operational efficiency in practical applications.',0,0.00,'2025-11-28 07:46:40','2025-11-28 07:46:40');

/*Table structure for table `projects` */

DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_id` bigint unsigned NOT NULL,
  `project_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objectives` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `actual_completion_date` date DEFAULT NULL,
  `funding_source` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_budget` decimal(15,2) NOT NULL,
  `sanctioned_budget` decimal(15,2) NOT NULL,
  `funds_released` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_expenditure` decimal(15,2) NOT NULL DEFAULT '0.00',
  `lead_investigator` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `co_investigators` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Proposed','Active','Completed','Delayed','On Hold','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `priority` enum('High','Medium','Low') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Medium',
  `physical_progress` decimal(5,2) NOT NULL DEFAULT '0.00',
  `financial_progress` decimal(5,2) NOT NULL DEFAULT '0.00',
  `target_publications` int NOT NULL DEFAULT '0',
  `achieved_publications` int NOT NULL DEFAULT '0',
  `target_patents` int NOT NULL DEFAULT '0',
  `achieved_patents` int NOT NULL DEFAULT '0',
  `target_prototypes` int NOT NULL DEFAULT '0',
  `achieved_prototypes` int NOT NULL DEFAULT '0',
  `target_industry_collaborations` int NOT NULL DEFAULT '0',
  `achieved_industry_collaborations` int NOT NULL DEFAULT '0',
  `phd_scholars` int NOT NULL DEFAULT '0',
  `mtech_students` int NOT NULL DEFAULT '0',
  `research_staff` int NOT NULL DEFAULT '0',
  `current_trl` int NOT NULL DEFAULT '1',
  `target_trl` int NOT NULL DEFAULT '9',
  `technology_transfer_potential` decimal(5,2) NOT NULL DEFAULT '0.00',
  `commercial_viability_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `social_impact_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projects_project_code_unique` (`project_code`),
  KEY `projects_tra_id_foreign` (`tra_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `projects` */

insert  into `projects`(`id`,`tra_id`,`project_code`,`title`,`objectives`,`description`,`start_date`,`end_date`,`actual_completion_date`,`funding_source`,`approved_budget`,`sanctioned_budget`,`funds_released`,`total_expenditure`,`lead_investigator`,`co_investigators`,`status`,`priority`,`physical_progress`,`financial_progress`,`target_publications`,`achieved_publications`,`target_patents`,`achieved_patents`,`target_prototypes`,`achieved_prototypes`,`target_industry_collaborations`,`achieved_industry_collaborations`,`phd_scholars`,`mtech_students`,`research_staff`,`current_trl`,`target_trl`,`technology_transfer_potential`,`commercial_viability_score`,`social_impact_score`,`created_at`,`updated_at`,`deleted_at`) values 
(1,1,'AMRA-2023-001','Development of High-Performance Graphene-Based Composite Materials for Aerospace Applications','Develop lightweight, high-strength graphene composites for aerospace structural components with 40% weight reduction and improved thermal stability.','This project focuses on synthesizing graphene-reinforced polymer composites for use in aircraft interiors and structural components.','2023-01-15','2025-12-31',NULL,'Department of Science and Technology',25000000.00,23500000.00,15000000.00,12850000.00,'Dr. Ramesh Iyer','Dr. Meena Patel, Dr. Sameer Joshi','Active','High',65.50,54.70,8,5,3,1,2,1,2,1,4,6,8,5,7,85.00,78.50,65.00,'2025-11-28 07:46:39','2025-11-28 07:46:39',NULL),
(2,2,'BIC-2023-002','Novel Enzyme Discovery for Plastic Waste Degradation','Identify and characterize novel microbial enzymes capable of degrading PET plastics with 80% efficiency within 30 days.','Screening of extremophilic microorganisms for plastic-degrading enzymes and optimization of degradation processes.','2023-03-01','2024-12-31',NULL,'Ministry of Environment',18000000.00,16500000.00,12000000.00,9850000.00,'Dr. Ananya Reddy','Dr. Karthik Menon, Dr. Sunita Rao','Active','High',52.30,59.70,6,3,2,1,1,0,3,2,3,4,6,4,6,92.00,88.50,95.00,'2025-11-28 07:46:39','2025-11-28 07:46:39',NULL),
(3,3,'CERF-2023-003','Advanced Solar Thermal Energy Storage System','Develop phase change material-based thermal energy storage with 85% efficiency and 12-hour storage capacity.','Research on novel phase change materials and heat exchanger designs for concentrated solar power applications.','2023-02-10','2025-06-30',NULL,'Ministry of New and Renewable Energy',32000000.00,29500000.00,18000000.00,14200000.00,'Dr. Vikram Singh','Dr. Neha Gupta, Dr. Rajiv Malhotra','Active','Medium',45.80,48.10,10,6,4,2,3,1,2,1,5,8,10,4,7,78.00,82.50,88.00,'2025-11-28 07:46:39','2025-11-28 07:46:39',NULL),
(4,4,'DTRG-2023-004','AI-Powered Predictive Maintenance for Industrial IoT','Develop machine learning algorithms for predictive maintenance with 95% accuracy in failure prediction 48 hours in advance.','Integration of sensor data with deep learning models for real-time equipment health monitoring in manufacturing plants.','2023-04-01','2024-09-30',NULL,'Industry Collaboration',15000000.00,13500000.00,10000000.00,7850000.00,'Dr. Sameer Deshpande','Dr. Priya Nair, Dr. Amit Sharma','Active','High',80.50,58.10,5,4,2,1,1,1,4,3,2,5,7,6,8,90.00,92.50,75.00,'2025-11-28 07:46:39','2025-12-04 06:22:40',NULL),
(5,5,'HIL-2023-005','Low-Cost Portable Diagnostic Device for Rural Healthcare','Develop affordable multi-parameter diagnostic device for basic health screening in remote areas.','Integration of microfluidics, sensors, and mobile technology for point-of-care diagnostics.','2023-05-15','2024-11-30',NULL,'Bill & Melinda Gates Foundation',12000000.00,11000000.00,7000000.00,5200000.00,'Dr. Nisha Verma','Dr. Arjun Kapoor, Dr. Sneha Iyer','Active','Medium',48.20,47.30,4,2,3,1,2,1,2,1,2,3,5,5,7,85.00,70.00,95.00,'2025-11-28 07:46:39','2025-11-28 07:46:39',NULL),
(6,1,'AMRA-2022-001','Nanocomposite Coatings for Corrosion Protection','Develop self-healing nanocomposite coatings with 10-year corrosion protection in marine environments.','Completed project focusing on nanoceramic coatings for offshore structures and marine applications.','2022-01-10','2023-12-15','2023-11-30','Naval Research Board',18500000.00,17500000.00,17500000.00,16800000.00,'Dr. Sanjay Mehta','Dr. Anjali Joshi, Dr. Ravi Kumar','Completed','High',100.00,96.00,7,8,2,3,1,1,2,3,3,5,6,8,8,88.00,85.00,70.00,'2025-11-28 07:46:39','2025-11-28 07:46:39',NULL);

/*Table structure for table `publications` */

DROP TABLE IF EXISTS `publications`;

CREATE TABLE `publications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `tra_id` bigint unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authors` json NOT NULL,
  `journal_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conference_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('Journal','Conference','Book Chapter','Technical Report','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  `doi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `impact_factor` decimal(5,2) DEFAULT NULL,
  `indexing` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quartile` enum('Q1','Q2','Q3','Q4','NA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `citations` int NOT NULL DEFAULT '0',
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `publications_project_id_foreign` (`project_id`),
  KEY `publications_tra_id_foreign` (`tra_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `publications` */

insert  into `publications`(`id`,`project_id`,`tra_id`,`title`,`authors`,`journal_name`,`conference_name`,`type`,`year`,`doi`,`impact_factor`,`indexing`,`quartile`,`citations`,`url`,`created_at`,`updated_at`) values 
(1,1,1,'Advanced Graphene Composites for Aerospace Applications','\"[\\\"Dr. Ramesh Iyer\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','Journal of Advanced Materials',NULL,'Journal',2023,'10.1016/j.jam.8151',8.20,'SCI, Scopus','Q1',24,'https://doi.org/10.1016/j.jam.3597','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(2,1,1,'Novel Approach to Composite Material Synthesis','\"[\\\"Dr. Ramesh Iyer\\\",\\\"Student Researcher\\\",\\\"Dr. Senior Researcher\\\"]\"',NULL,'International Conference on Advanced Technologies','Conference',2023,'10.1109/ICAT.3298',NULL,'IEEE Xplore',NULL,12,'https://doi.org/10.1109/ICAT.2094','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(3,2,2,'Advanced Microbial Enzymes for Environmental Remediation','\"[\\\"Dr. Ananya Reddy\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','Journal of Advanced Materials',NULL,'Journal',2023,'10.1016/j.jam.2626',8.20,'SCI, Scopus','Q1',25,'https://doi.org/10.1016/j.jam.3382','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(4,2,2,'Novel Approach to Biocatalytic Processes','\"[\\\"Dr. Ananya Reddy\\\",\\\"Student Researcher\\\",\\\"Dr. Senior Researcher\\\"]\"',NULL,'International Conference on Advanced Technologies','Conference',2023,'10.1109/ICAT.5224',NULL,'IEEE Xplore',NULL,12,'https://doi.org/10.1109/ICAT.7331','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(5,3,3,'Advanced Phase Change Materials for Renewable Energy Systems','\"[\\\"Dr. Vikram Singh\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','Journal of Advanced Materials',NULL,'Journal',2023,'10.1016/j.jam.1879',8.20,'SCI, Scopus','Q1',8,'https://doi.org/10.1016/j.jam.9667','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(6,3,3,'Novel Approach to Thermal Energy Storage','\"[\\\"Dr. Vikram Singh\\\",\\\"Student Researcher\\\",\\\"Dr. Senior Researcher\\\"]\"',NULL,'International Conference on Advanced Technologies','Conference',2023,'10.1109/ICAT.5182',NULL,'IEEE Xplore',NULL,8,'https://doi.org/10.1109/ICAT.8754','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(7,4,4,'Advanced Machine Learning Algorithms for Industrial Automation','\"[\\\"Dr. Sameer Deshpande\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','Journal of Advanced Materials',NULL,'Journal',2023,'10.1016/j.jam.3503',8.20,'SCI, Scopus','Q1',5,'https://doi.org/10.1016/j.jam.4198','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(8,4,4,'Novel Approach to Predictive Analytics','\"[\\\"Dr. Sameer Deshpande\\\",\\\"Student Researcher\\\",\\\"Dr. Senior Researcher\\\"]\"',NULL,'International Conference on Advanced Technologies','Conference',2023,'10.1109/ICAT.6582',NULL,'IEEE Xplore',NULL,12,'https://doi.org/10.1109/ICAT.9120','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(9,5,5,'Advanced Biosensing Materials for Medical Diagnostics','\"[\\\"Dr. Nisha Verma\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','Journal of Advanced Materials',NULL,'Journal',2023,'10.1016/j.jam.4464',8.20,'SCI, Scopus','Q1',7,'https://doi.org/10.1016/j.jam.1705','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(10,5,5,'Novel Approach to Point-of-Care Testing','\"[\\\"Dr. Nisha Verma\\\",\\\"Student Researcher\\\",\\\"Dr. Senior Researcher\\\"]\"',NULL,'International Conference on Advanced Technologies','Conference',2023,'10.1109/ICAT.4464',NULL,'IEEE Xplore',NULL,7,'https://doi.org/10.1109/ICAT.6683','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(11,6,1,'Advanced Advanced Materials for Industrial Applications','\"[\\\"Dr. Sanjay Mehta\\\",\\\"Dr. Research Scholar\\\",\\\"Dr. Co-Investigator\\\"]\"','Journal of Advanced Materials',NULL,'Journal',2023,'10.1016/j.jam.1216',8.20,'SCI, Scopus','Q1',6,'https://doi.org/10.1016/j.jam.4476','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(12,6,1,'Novel Approach to Advanced Technology Development','\"[\\\"Dr. Sanjay Mehta\\\",\\\"Student Researcher\\\",\\\"Dr. Senior Researcher\\\"]\"',NULL,'International Conference on Advanced Technologies','Conference',2023,'10.1109/ICAT.5022',NULL,'IEEE Xplore',NULL,5,'https://doi.org/10.1109/ICAT.3338','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(13,6,1,'Comprehensive Analysis of Protective Coatings','\"[\\\"Dr. Sanjay Mehta\\\",\\\"Dr. Research Associate\\\",\\\"Dr. Domain Expert\\\"]\"','Materials Science and Engineering',NULL,'Journal',2022,'10.1016/j.mse.8493',6.80,'SCI, Scopus','Q1',25,'https://doi.org/10.1016/j.mse.2681','2025-11-28 07:46:40','2025-11-28 07:46:40'),
(14,6,1,'Performance Evaluation of Developed Technologies','\"[\\\"Dr. Research Scholar\\\",\\\"Dr. Sanjay Mehta\\\",\\\"Dr. Technical Expert\\\"]\"','Applied Physics Letters',NULL,'Journal',2023,'10.1063/1.540213',3.80,'SCI, Scopus','Q2',7,'https://doi.org/10.1063/1.593716','2025-11-28 07:46:40','2025-11-28 07:46:40');

/*Table structure for table `revenue_transactions` */

DROP TABLE IF EXISTS `revenue_transactions`;

CREATE TABLE `revenue_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tra_id` bigint unsigned NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `rev_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `rev_tested` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `revenue_transactions_user_id_tra_id_month_year_unique` (`user_id`,`tra_id`,`month`,`year`),
  KEY `revenue_transactions_tra_id_foreign` (`tra_id`),
  KEY `revenue_transactions_user_id_month_year_index` (`user_id`,`month`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `revenue_transactions` */

/*Table structure for table `sample_transactions` */

DROP TABLE IF EXISTS `sample_transactions`;

CREATE TABLE `sample_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tra_id` bigint unsigned NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `total` int NOT NULL DEFAULT '0',
  `tested` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sample_transactions_user_id_tra_id_month_year_unique` (`user_id`,`tra_id`,`month`,`year`),
  KEY `sample_transactions_tra_id_foreign` (`tra_id`),
  KEY `sample_transactions_user_id_month_year_index` (`user_id`,`month`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sample_transactions` */

/*Table structure for table `seminars_workshops` */

DROP TABLE IF EXISTS `seminars_workshops`;

CREATE TABLE `seminars_workshops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('seminar','workshop') COLLATE utf8mb4_unicode_ci NOT NULL,
  `audience` enum('industry','academia','both') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `participants_count` int NOT NULL DEFAULT '0',
  `venue` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topics_covered` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `seminars_workshops` */

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sessions` */

insert  into `sessions`(`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) values 
('ecCKZ3l8I887ixA7xedxDsHGASVwRUT1TaMJZDvZ',NULL,'172.16.1.241','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTTAyblgzMHVHdkwzeTVOekNVVjRBakM1aHFZUWFUdVBaQWVPR0h1UiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly8xNzIuMTYuMS4yNDYvdHJhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1OiJsb2dpbiI7czo5OiJkYXNoYm9hcmQiO30=',1765259513),
('J1fRurKdRudtDbxQZc5FmDAIX6RBgNOuAe3IK2Fp',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFh2RlB2bGpXaVhHdFFFUHdmbTRaRFBDUmp1dW9BbERSdXdVRnVXRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly9sb2NhbGhvc3QvdHJhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1765254899),
('WpGVVVtWFajhBotycpx5ghFygmlgucDi2vjCvXOc',NULL,'172.16.1.80','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoibHdYaW9SOVplUVh2WjJRYmlBc2VNU2w1Yk51Mjh1TXJDeFhXZmVpbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly8xNzIuMTYuMS4yNDYvdHJhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1OiJsb2dpbiI7czo5OiJkYXNoYm9hcmQiO30=',1765279139),
('IXgRq0nGlJjI2WJQKDICIVl20R6417bLhXvEH4H4',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYThhQnNGdTVzaTdJeGd5TjlabTA0RG16Qnd1NkNGRFZ2Tzk1MXVCTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly9sb2NhbGhvc3QvdHJhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1765266688),
('ecvq2i4RY0mYkeZ5RNuqjoARyK5IMvJ8b9biZhwB',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFdpZXJHZ2dpTmF3Nnh3Q0V5MWJuak9obG9JWGtaejNUd3VNWmdyUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9sb2NhbGhvc3QvdHJhL2luZHVzdHJ5LWtwaS9yZXBvcnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1765280610);

/*Table structure for table `staff_retention` */

DROP TABLE IF EXISTS `staff_retention`;

CREATE TABLE `staff_retention` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` year NOT NULL,
  `total_staff_beginning` int NOT NULL,
  `staff_retained` int NOT NULL,
  `staff_left` int NOT NULL,
  `retention_rate` decimal(5,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `staff_retention` */

/*Table structure for table `startup_spinoffs` */

DROP TABLE IF EXISTS `startup_spinoffs`;

CREATE TABLE `startup_spinoffs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `technology_transfer_id` bigint unsigned DEFAULT NULL,
  `company_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` enum('Startup','Spinoff','MSE','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Startup',
  `registration_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incorporation_date` date DEFAULT NULL,
  `cin_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `industry_sector` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_service_offered` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `technology_base` text COLLATE utf8mb4_unicode_ci,
  `development_stage` enum('Idea','Prototype','Pre-revenue','Revenue','Scaling','Exit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Idea',
  `founders` json NOT NULL COMMENT 'Array of founder names',
  `ceo_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `office_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_type` enum('Incubation','Technology Transfer','Mentorship','Funding Support','Infrastructure','Testing Facilities','Co-working Space','Market Linkage','Multiple') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Incubation',
  `support_start_date` date NOT NULL,
  `support_end_date` date DEFAULT NULL,
  `support_duration_months` int NOT NULL DEFAULT '0',
  `current_status` enum('Active','Graduated','Exited','Closed','Dormant') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `seed_funding_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `infrastructure_support_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_support_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `annual_revenue` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_funding_raised` decimal(15,2) NOT NULL DEFAULT '0.00',
  `funding_sources` json DEFAULT NULL,
  `current_employees` int NOT NULL DEFAULT '0',
  `jobs_created` int NOT NULL DEFAULT '0',
  `valuation` decimal(15,2) DEFAULT NULL,
  `products_launched` int NOT NULL DEFAULT '0',
  `patents_filed` int NOT NULL DEFAULT '0',
  `patents_granted` int NOT NULL DEFAULT '0',
  `customers_acquired` int NOT NULL DEFAULT '0',
  `awards_received` json DEFAULT NULL,
  `certifications` json DEFAULT NULL,
  `dpiit_recognized` tinyint(1) NOT NULL DEFAULT '0',
  `tra_equity_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `returns_to_tra` decimal(15,2) NOT NULL DEFAULT '0.00',
  `exit_details` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `startup_spinoffs_company_code_unique` (`company_code`),
  KEY `startup_spinoffs_tra_id_foreign` (`tra_id`),
  KEY `startup_spinoffs_project_id_foreign` (`project_id`),
  KEY `startup_spinoffs_technology_transfer_id_foreign` (`technology_transfer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `startup_spinoffs` */

/*Table structure for table `technology_transfers` */

DROP TABLE IF EXISTS `technology_transfers`;

CREATE TABLE `technology_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `technology_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `technology_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `technology_type` enum('Product','Process','Service','Software','Design','Know-how') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Product',
  `trl_at_transfer` int NOT NULL COMMENT 'Technology Readiness Level 1-9',
  `industry_partner_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `industry_partner_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `industry_sector` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_date` date NOT NULL,
  `agreement_date` date DEFAULT NULL,
  `agreement_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_type` enum('Licensing','Joint Development','Know-how Transfer','Patent Assignment','Outright Sale') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Licensing',
  `status` enum('Negotiation','Agreement Signed','Implementation','Commercialized','Terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Negotiation',
  `upfront_fee` decimal(15,2) NOT NULL DEFAULT '0.00',
  `royalty_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `minimum_royalty_annual` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_revenue_generated` decimal(15,2) NOT NULL DEFAULT '0.00',
  `agreement_duration_years` int NOT NULL DEFAULT '0',
  `jobs_created` int NOT NULL DEFAULT '0',
  `units_produced` int NOT NULL DEFAULT '0',
  `market_size_potential` decimal(15,2) NOT NULL DEFAULT '0.00',
  `social_impact` text COLLATE utf8mb4_unicode_ci,
  `agreement_document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `technology_document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `technology_transfers_technology_code_unique` (`technology_code`),
  KEY `technology_transfers_tra_id_foreign` (`tra_id`),
  KEY `technology_transfers_project_id_foreign` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `technology_transfers` */

/*Table structure for table `tra_kpi_mappings` */

DROP TABLE IF EXISTS `tra_kpi_mappings`;

CREATE TABLE `tra_kpi_mappings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tra_category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kpi_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kpi_table` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weightage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `calculation_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tra_kpi_mappings_tra_category_is_active_index` (`tra_category`,`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tra_kpi_mappings` */

/*Table structure for table `tra_scores` */

DROP TABLE IF EXISTS `tra_scores`;

CREATE TABLE `tra_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assessment_year` year NOT NULL,
  `tra_code` json DEFAULT NULL,
  `period` enum('Annual','Q1','Q2','Q3','Q4') COLLATE utf8mb4_unicode_ci NOT NULL,
  `research_development` decimal(5,2) NOT NULL DEFAULT '0.00',
  `technology_transfer` decimal(5,2) NOT NULL DEFAULT '0.00',
  `testing_standardisation` decimal(5,2) NOT NULL DEFAULT '0.00',
  `self_sustainability` decimal(5,2) NOT NULL DEFAULT '0.00',
  `human_resources` decimal(5,2) NOT NULL DEFAULT '0.00',
  `institutional_governance` decimal(5,2) NOT NULL DEFAULT '0.00',
  `sustainability_impact` decimal(5,2) NOT NULL DEFAULT '0.00',
  `outreach_collaboration` decimal(5,2) NOT NULL DEFAULT '0.00',
  `overall_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `assessor_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tra_scores_assessment_year_period_unique` (`assessment_year`,`period`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tra_scores` */

/*Table structure for table `tra_targets` */

DROP TABLE IF EXISTS `tra_targets`;

CREATE TABLE `tra_targets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `target_year` year NOT NULL,
  `tra_code` json DEFAULT NULL,
  `research_development` decimal(5,2) NOT NULL DEFAULT '0.00',
  `technology_transfer` decimal(5,2) NOT NULL DEFAULT '0.00',
  `testing_standardisation` decimal(5,2) NOT NULL DEFAULT '0.00',
  `self_sustainability` decimal(5,2) NOT NULL DEFAULT '0.00',
  `human_resources` decimal(5,2) NOT NULL DEFAULT '0.00',
  `institutional_governance` decimal(5,2) NOT NULL DEFAULT '0.00',
  `sustainability_impact` decimal(5,2) NOT NULL DEFAULT '0.00',
  `outreach_collaboration` decimal(5,2) NOT NULL DEFAULT '0.00',
  `overall_target` decimal(5,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tra_targets_target_year_unique` (`target_year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tra_targets` */

/*Table structure for table `training_programmes` */

DROP TABLE IF EXISTS `training_programmes`;

CREATE TABLE `training_programmes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `programme_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `programme_type` enum('skill_development','awareness') COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `participants_count` int NOT NULL DEFAULT '0',
  `participant_type` enum('industry_students','artisans','weavers','mixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `training_programmes` */

/*Table structure for table `tras` */

DROP TABLE IF EXISTS `tras`;

CREATE TABLE `tras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `established` year NOT NULL,
  `status` enum('Active','Inactive','Under Review') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `staff_count` int NOT NULL DEFAULT '0',
  `address` text COLLATE utf8mb4_unicode_ci,
  `director_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tras_code_unique` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tras` */

insert  into `tras`(`id`,`name`,`code`,`location`,`established`,`status`,`staff_count`,`address`,`director_name`,`email`,`phone`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'Advanced Materials Research Association','AMRA','Bangalore',2010,'Active',45,'Science Park, Whitefield, Bangalore - 560066','Dr. Rajesh Kumar','director@amra.org','+91-80-25678901','2025-11-28 07:46:39','2025-11-28 07:46:39',NULL),
(2,'Biotechnology Innovation Center','BIC','Hyderabad',2015,'Active',32,'Genome Valley, Turkapally, Hyderabad - 500078','Dr. Priya Sharma','priya.sharma@bic.org','+91-40-27894561','2025-11-28 07:46:39','2025-11-28 07:46:39',NULL),
(3,'Clean Energy Research Foundation','CERF','Chennai',2012,'Active',28,'IT Corridor, Taramani, Chennai - 600113','Dr. Arun Patel','arun.patel@cerf.org','+91-44-26547890','2025-11-28 07:46:39','2025-11-28 07:46:39',NULL),
(4,'Digital Technology Research Group','DTRG','Pune',2018,'Active',38,'Hinjewadi IT Park, Phase 1, Pune - 411057','Dr. Sanjay Mehta','sanjay.mehta@dtrg.org','+91-20-27894567','2025-11-28 07:46:39','2025-11-28 07:46:39',NULL),
(5,'Healthcare Innovation Lab','HIL','Delhi',2016,'Under Review',25,'Okhla Industrial Area, Phase 3, Delhi - 110020','Dr. Anjali Singh','anjali.singh@hil.org','+91-11-26547890','2025-11-28 07:46:39','2025-11-28 07:46:39',NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

/*Table structure for table `visibility_metrics` */

DROP TABLE IF EXISTS `visibility_metrics`;

CREATE TABLE `visibility_metrics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `metric_date` date NOT NULL,
  `media_mentions` int NOT NULL DEFAULT '0',
  `website_traffic` int NOT NULL DEFAULT '0',
  `social_media_engagement` int NOT NULL DEFAULT '0',
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notable_mentions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `visibility_metrics` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
