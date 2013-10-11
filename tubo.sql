-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 11, 2013 at 06:17 PM
-- Server version: 5.5.32-0ubuntu0.12.04.1
-- PHP Version: 5.5.4-1+debphp.org~precise+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tubo`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '类别名称',
  `count` int(11) NOT NULL COMMENT '所属新闻记录数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL COMMENT '标题',
  `category_id` int(11) NOT NULL COMMENT '分类ID',
  `summary` text NOT NULL COMMENT '内容简要',
  `thumbnail` varchar(200) NOT NULL COMMENT '内容缩略图',
  `url` varchar(200) NOT NULL COMMENT '新闻真实地址',
  `source` int(11) NOT NULL COMMENT '新闻来源',
  `click_count` int(11) NOT NULL COMMENT '点击数',
  `digg_count` int(11) NOT NULL COMMENT '好评的人数',
  `bury_count` int(11) NOT NULL COMMENT '差评的人数',
  `fav_count` int(11) NOT NULL COMMENT '收藏的人数',
  `dateline` datetime NOT NULL COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
