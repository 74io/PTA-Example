# SQL Manager 2007 for MySQL 4.4.2.1
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : schema_0_9_1


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE `schema_0_9_1`
    CHARACTER SET 'utf8'
    COLLATE 'utf8_general_ci';

USE `schema_0_9_1`;

#
# Structure for the `cohort` table : 
#

CREATE TABLE `cohort` (
  `id` varchar(10) NOT NULL,
  `term_start_date` date DEFAULT NULL,
  `term_end_date` date DEFAULT NULL,
  `default` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Structure for the `datacache` table : 
#

CREATE TABLE `datacache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `checksum` int(11) unsigned DEFAULT NULL,
  `data` longtext,
  `cohort_id` varchar(10) DEFAULT NULL,
  `key_stage` tinyint(1) DEFAULT NULL,
  `category` varchar(30) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Idx_cohort_ks` (`cohort_id`,`key_stage`),
  KEY `idx_check_cohort_ks_cat` (`checksum`,`cohort_id`,`key_stage`,`category`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

#
# Structure for the `eventlog` table : 
#

CREATE TABLE `eventlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `level` varchar(20) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `message` text,
  `object_id` int(11) DEFAULT NULL,
  `key_stage` int(1) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `level` (`level`),
  KEY `key_stage` (`key_stage`)
) ENGINE=InnoDB AUTO_INCREMENT=448 DEFAULT CHARSET=utf8;

#
# Structure for the `excludedpupils` table : 
#

CREATE TABLE `excludedpupils` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subjectmapping_id` int(11) DEFAULT NULL,
  `pupil_id` varchar(20) DEFAULT NULL,
  `set_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subjmap_pupil` (`subjectmapping_id`,`pupil_id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8;

#
# Structure for the `excludedsets` table : 
#

CREATE TABLE `excludedsets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subjectmapping_id` int(11) DEFAULT NULL,
  `set_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subjmap_setcode` (`subjectmapping_id`,`set_code`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

#
# Structure for the `fieldmapping` table : 
#

CREATE TABLE `fieldmapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cohort_id` varchar(10) DEFAULT NULL,
  `mapped_field` varchar(20) DEFAULT NULL,
  `mapped_alias` varchar(50) DEFAULT NULL,
  `year_group` tinyint(2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `default` tinyint(1) DEFAULT NULL,
  `last_built` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cohort_id` (`cohort_id`),
  KEY `cohort_id_year_group_type` (`cohort_id`,`year_group`,`type`),
  KEY `type_default` (`type`,`default`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

#
# Structure for the `ks4meta` table : 
#

CREATE TABLE `ks4meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cohort_id` varchar(10) DEFAULT NULL,
  `subjectmapping_id` int(11) DEFAULT NULL,
  `pupil_id` varchar(20) DEFAULT NULL,
  `fieldmapping_id` int(11) DEFAULT NULL,
  `astar_a` decimal(2,1) DEFAULT NULL,
  `astar_c` decimal(2,1) DEFAULT NULL,
  `astar_g` decimal(2,1) DEFAULT NULL,
  `a_a` decimal(2,1) DEFAULT NULL,
  `a_c` decimal(2,1) DEFAULT NULL,
  `a_g` decimal(2,1) DEFAULT NULL,
  `standardised_points` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_field_pupil` (`fieldmapping_id`,`pupil_id`),
  KEY `idx_cohort_pupil` (`cohort_id`,`pupil_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67958 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=292;

#
# Structure for the `pupil` table : 
#

CREATE TABLE `pupil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pupil_id` varchar(20) DEFAULT NULL,
  `cohort_id` varchar(10) DEFAULT NULL,
  `surname` varchar(20) DEFAULT NULL,
  `forename` varchar(20) DEFAULT NULL,
  `year` tinyint(2) DEFAULT NULL,
  `form` varchar(20) DEFAULT NULL,
  `dob` varchar(20) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `ethnicity` varchar(20) DEFAULT NULL,
  `sen_code` varchar(20) DEFAULT NULL,
  `fsm` varchar(20) DEFAULT NULL,
  `gifted` varchar(20) DEFAULT NULL,
  `cla` varchar(20) DEFAULT NULL,
  `eal` varchar(20) DEFAULT NULL,
  `ks2_english` char(1) DEFAULT '',
  `ks2_maths` char(1) DEFAULT '',
  `ks2_science` char(1) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `cohort_id` (`cohort_id`),
  KEY `year` (`year`),
  KEY `idx_surname_forename` (`surname`,`forename`),
  KEY `idx_cohort_pupil` (`cohort_id`,`pupil_id`),
  KEY `pupil_id` (`pupil_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5111 DEFAULT CHARSET=utf8;

#
# Structure for the `setdata` table : 
#

CREATE TABLE `setdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cohort_id` varchar(20) DEFAULT NULL,
  `pupil_id` varchar(20) DEFAULT NULL,
  `mapped_subject` varchar(20) DEFAULT NULL,
  `set_code` varchar(20) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cohort_id_pupil_mapsubj` (`cohort_id`,`pupil_id`,`mapped_subject`),
  KEY `cohort_id` (`cohort_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46548 DEFAULT CHARSET=utf8;

#
# Structure for the `settings` table : 
#

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(64) NOT NULL DEFAULT 'system',
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_key` (`category`,`key`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

#
# Structure for the `subjectdata` table : 
#

CREATE TABLE `subjectdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cohort_id` varchar(10) DEFAULT NULL,
  `subjectmapping_id` int(11) DEFAULT NULL,
  `pupil_id` varchar(20) DEFAULT NULL,
  `fieldmapping_id` int(11) DEFAULT NULL,
  `result` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fieldmapping_id` (`fieldmapping_id`),
  KEY `subjectmapping_id` (`subjectmapping_id`),
  KEY `result` (`result`),
  KEY `idx_cohort_pupil` (`cohort_id`,`pupil_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39769 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=92;

#
# Structure for the `subjectmapping` table : 
#

CREATE TABLE `subjectmapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cohort_id` varchar(10) DEFAULT NULL,
  `key_stage` tinyint(1) DEFAULT NULL,
  `mapped_subject` varchar(20) NOT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `volume` decimal(2,1) DEFAULT NULL,
  `equivalent` tinyint(1) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `include` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cohort_id` (`cohort_id`),
  KEY `qualification` (`qualification`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

#
# Structure for the `user` table : 
#

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salt` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `activation_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recovery_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#
# Data for the `user` table  (LIMIT 0,500)
#

INSERT INTO `user` (`id`, `username`, `password`, `role`, `salt`, `email`, `active`, `activation_id`, `recovery_id`, `account_created`) VALUES 
  (1,'roneill','1384cb4fb62e16549fa0ccf1f5c59867','super','50226ad7168c25.23290186','roneill@pupiltracking.com',1,NULL,NULL,NULL);
COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;