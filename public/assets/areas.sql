-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Dec 10, 2020 at 09:36 AM
-- Server version: 5.7.30
-- PHP Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `runner`
--

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `district_id` bigint(20) UNSIGNED NOT NULL COMMENT 'district id',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'check, if active or inactive',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'area name',
  `bn_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'area bengali name'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `district_id`, `status`, `name`, `bn_name`) VALUES
(1, 1, 'active', 'Debidwar', 'দেবিদ্বার'),
(2, 1, 'active', 'Barura', 'বরুড়া'),
(3, 1, 'active', 'Brahmanpara', 'ব্রাহ্মণপাড়া'),
(4, 1, 'active', 'Chandina', 'চান্দিনা'),
(5, 1, 'active', 'Chauddagram', 'চৌদ্দগ্রাম'),
(6, 1, 'active', 'Daudkandi', 'দাউদকান্দি'),
(7, 1, 'active', 'Homna', 'হোমনা'),
(8, 1, 'active', 'Laksam', 'লাকসাম'),
(9, 1, 'active', 'Muradnagar', 'মুরাদনগর'),
(10, 1, 'active', 'Nangalkot', 'নাঙ্গলকোট'),
(11, 1, 'active', 'Comilla Sadar', 'কুমিল্লা সদর'),
(12, 1, 'active', 'Meghna', 'মেঘনা'),
(13, 1, 'active', 'Monohargonj', 'মনোহরগঞ্জ'),
(14, 1, 'active', 'Sadarsouth', 'সদর দক্ষিণ'),
(15, 1, 'active', 'Titas', 'তিতাস'),
(16, 1, 'active', 'Burichang', 'বুড়িচং'),
(17, 1, 'active', 'Lalmai', 'লালমাই'),
(18, 2, 'active', 'Chhagalnaiya', 'ছাগলনাইয়া'),
(19, 2, 'active', 'Feni Sadar', 'ফেনী সদর'),
(20, 2, 'active', 'Sonagazi', 'সোনাগাজী'),
(21, 2, 'active', 'Fulgazi', 'ফুলগাজী'),
(22, 2, 'active', 'Parshuram', 'পরশুরাম'),
(23, 2, 'active', 'Daganbhuiyan', 'দাগনভূঞা'),
(24, 3, 'active', 'Brahmanbaria Sadar', 'ব্রাহ্মণবাড়িয়া সদর'),
(25, 3, 'active', 'Kasba', 'কসবা'),
(26, 3, 'active', 'Nasirnagar', 'নাসিরনগর'),
(27, 3, 'active', 'Sarail', 'সরাইল'),
(28, 3, 'active', 'Ashuganj', 'আশুগঞ্জ'),
(29, 3, 'active', 'Akhaura', 'আখাউড়া'),
(30, 3, 'active', 'Nabinagar', 'নবীনগর'),
(31, 3, 'active', 'Bancharampur', 'বাঞ্ছারামপুর'),
(32, 3, 'active', 'Bijoynagar', 'বিজয়নগর'),
(33, 4, 'active', 'Rangamati Sadar', 'রাঙ্গামাটি সদর'),
(34, 4, 'active', 'Kaptai', 'কাপ্তাই'),
(35, 4, 'active', 'Kawkhali', 'কাউখালী'),
(36, 4, 'active', 'Baghaichari', 'বাঘাইছড়ি'),
(37, 4, 'active', 'Barkal', 'বরকল'),
(38, 4, 'active', 'Langadu', 'লংগদু'),
(39, 4, 'active', 'Rajasthali', 'রাজস্থলী'),
(40, 4, 'active', 'Belaichari', 'বিলাইছড়ি'),
(41, 4, 'active', 'Juraichari', 'জুরাছড়ি'),
(42, 4, 'active', 'Naniarchar', 'নানিয়ারচর'),
(43, 5, 'active', 'Noakhali Sadar', 'নোয়াখালী সদর'),
(44, 5, 'active', 'Companiganj', 'কোম্পানীগঞ্জ'),
(45, 5, 'active', 'Begumganj', 'বেগমগঞ্জ'),
(46, 5, 'active', 'Hatia', 'হাতিয়া'),
(47, 5, 'active', 'Subarnachar', 'সুবর্ণচর'),
(48, 5, 'active', 'Kabirhat', 'কবিরহাট'),
(49, 5, 'active', 'Senbug', 'সেনবাগ'),
(50, 5, 'active', 'Chatkhil', 'চাটখিল'),
(51, 5, 'active', 'Sonaimori', 'সোনাইমুড়ী'),
(52, 6, 'active', 'Haimchar', 'হাইমচর'),
(53, 6, 'active', 'Kachua', 'কচুয়া'),
(54, 6, 'active', 'Shahrasti', 'শাহরাস্তি	'),
(55, 6, 'active', 'Chandpur Sadar', 'চাঁদপুর সদর'),
(56, 6, 'active', 'Matlab South', 'মতলব দক্ষিণ'),
(57, 6, 'active', 'Hajiganj', 'হাজীগঞ্জ'),
(58, 6, 'active', 'Matlab North', 'মতলব উত্তর'),
(59, 6, 'active', 'Faridgonj', 'ফরিদগঞ্জ'),
(60, 7, 'active', 'Lakshmipur Sadar', 'লক্ষ্মীপুর সদর'),
(61, 7, 'active', 'Kamalnagar', 'কমলনগর'),
(62, 7, 'active', 'Raipur', 'রায়পুর'),
(63, 7, 'active', 'Ramgati', 'রামগতি'),
(64, 7, 'active', 'Ramganj', 'রামগঞ্জ'),
(65, 8, 'active', 'Rangunia', 'রাঙ্গুনিয়া'),
(66, 8, 'active', 'Sitakunda', 'সীতাকুন্ড'),
(67, 8, 'active', 'Mirsharai', 'মীরসরাই'),
(68, 8, 'active', 'Patiya', 'পটিয়া'),
(69, 8, 'active', 'Sandwip', 'সন্দ্বীপ'),
(70, 8, 'active', 'Banshkhali', 'বাঁশখালী'),
(71, 8, 'active', 'Boalkhali', 'বোয়ালখালী'),
(72, 8, 'active', 'Anwara', 'আনোয়ারা'),
(73, 8, 'active', 'Chandanaish', 'চন্দনাইশ'),
(74, 8, 'active', 'Satkania', 'সাতকানিয়া'),
(75, 8, 'active', 'Lohagara', 'লোহাগাড়া'),
(76, 8, 'active', 'Hathazari', 'হাটহাজারী'),
(77, 8, 'active', 'Fatikchhari', 'ফটিকছড়ি'),
(78, 8, 'active', 'Raozan', 'রাউজান'),
(79, 8, 'active', 'Karnafuli', 'কর্ণফুলী'),
(80, 9, 'active', 'Coxsbazar Sadar', 'কক্সবাজার সদর'),
(81, 9, 'active', 'Chakaria', 'চকরিয়া'),
(82, 9, 'active', 'Kutubdia', 'কুতুবদিয়া'),
(83, 9, 'active', 'Ukhiya', 'উখিয়া'),
(84, 9, 'active', 'Moheshkhali', 'মহেশখালী'),
(85, 9, 'active', 'Pekua', 'পেকুয়া'),
(86, 9, 'active', 'Ramu', 'রামু'),
(87, 9, 'active', 'Teknaf', 'টেকনাফ'),
(88, 10, 'active', 'Khagrachhari Sadar', 'খাগড়াছড়ি সদর'),
(89, 10, 'active', 'Dighinala', 'দিঘীনালা'),
(90, 10, 'active', 'Panchari', 'পানছড়ি'),
(91, 10, 'active', 'Laxmichhari', 'লক্ষীছড়ি'),
(92, 10, 'active', 'Mohalchari', 'মহালছড়ি'),
(93, 10, 'active', 'Manikchari', 'মানিকছড়ি'),
(94, 10, 'active', 'Ramgarh', 'রামগড়'),
(95, 10, 'active', 'Matiranga', 'মাটিরাঙ্গা'),
(96, 10, 'active', 'Guimara', 'গুইমারা'),
(97, 11, 'active', 'Bandarban Sadar', 'বান্দরবান সদর'),
(98, 11, 'active', 'Alikadam', 'আলীকদম'),
(99, 11, 'active', 'Naikhongchhari', 'নাইক্ষ্যংছড়ি'),
(100, 11, 'active', 'Rowangchhari', 'রোয়াংছড়ি'),
(101, 11, 'active', 'Lama', 'লামা'),
(102, 11, 'active', 'Ruma', 'রুমা'),
(103, 11, 'active', 'Thanchi', 'থানচি'),
(104, 12, 'active', 'Belkuchi', 'বেলকুচি'),
(105, 12, 'active', 'Chauhali', 'চৌহালি'),
(106, 12, 'active', 'Kamarkhand', 'কামারখন্দ'),
(107, 12, 'active', 'Kazipur', 'কাজীপুর'),
(108, 12, 'active', 'Raigonj', 'রায়গঞ্জ'),
(109, 12, 'active', 'Shahjadpur', 'শাহজাদপুর'),
(110, 12, 'active', 'Sirajganj Sadar', 'সিরাজগঞ্জ সদর'),
(111, 12, 'active', 'Tarash', 'তাড়াশ'),
(112, 12, 'active', 'Ullapara', 'উল্লাপাড়া'),
(113, 13, 'active', 'Sujanagar', 'সুজানগর'),
(114, 13, 'active', 'Ishurdi', 'ঈশ্বরদী'),
(115, 13, 'active', 'Bhangura', 'ভাঙ্গুড়া'),
(116, 13, 'active', 'Pabna Sadar', 'পাবনা সদর'),
(117, 13, 'active', 'Bera', 'বেড়া'),
(118, 13, 'active', 'Atghoria', 'আটঘরিয়া'),
(119, 13, 'active', 'Chatmohar', 'চাটমোহর'),
(120, 13, 'active', 'Santhia', 'সাঁথিয়া'),
(121, 13, 'active', 'Faridpur', 'ফরিদপুর'),
(122, 14, 'active', 'Kahaloo', 'কাহালু'),
(123, 14, 'active', 'Bogra Sadar', 'বগুড়া সদর'),
(124, 14, 'active', 'Shariakandi', 'সারিয়াকান্দি'),
(125, 14, 'active', 'Shajahanpur', 'শাজাহানপুর'),
(126, 14, 'active', 'Dupchanchia', 'দুপচাচিঁয়া'),
(127, 14, 'active', 'Adamdighi', 'আদমদিঘি'),
(128, 14, 'active', 'Nondigram', 'নন্দিগ্রাম'),
(129, 14, 'active', 'Sonatala', 'সোনাতলা'),
(130, 14, 'active', 'Dhunot', 'ধুনট'),
(131, 14, 'active', 'Gabtali', 'গাবতলী'),
(132, 14, 'active', 'Sherpur', 'শেরপুর'),
(133, 14, 'active', 'Shibganj', 'শিবগঞ্জ'),
(134, 15, 'active', 'Paba', 'পবা'),
(135, 15, 'active', 'Durgapur', 'দুর্গাপুর'),
(136, 15, 'active', 'Mohonpur', 'মোহনপুর'),
(137, 15, 'active', 'Charghat', 'চারঘাট'),
(138, 15, 'active', 'Puthia', 'পুঠিয়া'),
(139, 15, 'active', 'Bagha', 'বাঘা'),
(140, 15, 'active', 'Godagari', 'গোদাগাড়ী'),
(141, 15, 'active', 'Tanore', 'তানোর'),
(142, 15, 'active', 'Bagmara', 'বাগমারা'),
(143, 16, 'active', 'Natore Sadar', 'নাটোর সদর'),
(144, 16, 'active', 'Singra', 'সিংড়া'),
(145, 16, 'active', 'Baraigram', 'বড়াইগ্রাম'),
(146, 16, 'active', 'Bagatipara', 'বাগাতিপাড়া'),
(147, 16, 'active', 'Lalpur', 'লালপুর'),
(148, 16, 'active', 'Gurudaspur', 'গুরুদাসপুর'),
(149, 16, 'active', 'Naldanga', 'নলডাঙ্গা'),
(150, 17, 'active', 'Akkelpur', 'আক্কেলপুর'),
(151, 17, 'active', 'Kalai', 'কালাই'),
(152, 17, 'active', 'Khetlal', 'ক্ষেতলাল'),
(153, 17, 'active', 'Panchbibi', 'পাঁচবিবি'),
(154, 17, 'active', 'Joypurhat Sadar', 'জয়পুরহাট সদর'),
(155, 18, 'active', 'Chapainawabganj Sadar', 'চাঁপাইনবাবগঞ্জ সদর'),
(156, 18, 'active', 'Gomostapur', 'গোমস্তাপুর'),
(157, 18, 'active', 'Nachol', 'নাচোল'),
(158, 18, 'active', 'Bholahat', 'ভোলাহাট'),
(159, 18, 'active', 'Shibganj', 'শিবগঞ্জ'),
(160, 19, 'active', 'Mohadevpur', 'মহাদেবপুর'),
(161, 19, 'active', 'Badalgachi', 'বদলগাছী'),
(162, 19, 'active', 'Patnitala', 'পত্নিতলা'),
(163, 19, 'active', 'Dhamoirhat', 'ধামইরহাট'),
(164, 19, 'active', 'Niamatpur', 'নিয়ামতপুর'),
(165, 19, 'active', 'Manda', 'মান্দা'),
(166, 19, 'active', 'Atrai', 'আত্রাই'),
(167, 19, 'active', 'Raninagar', 'রাণীনগর'),
(168, 19, 'active', 'Naogaon Sadar', 'নওগাঁ সদর'),
(169, 19, 'active', 'Porsha', 'পোরশা'),
(170, 19, 'active', 'Sapahar', 'সাপাহার'),
(171, 20, 'active', 'Manirampur', 'মণিরামপুর'),
(172, 20, 'active', 'Abhaynagar', 'অভয়নগর'),
(173, 20, 'active', 'Bagherpara', 'বাঘারপাড়া'),
(174, 20, 'active', 'Chougachha', 'চৌগাছা'),
(175, 20, 'active', 'Jhikargacha', 'ঝিকরগাছা'),
(176, 20, 'active', 'Keshabpur', 'কেশবপুর'),
(177, 20, 'active', 'Jessore Sadar', 'যশোর সদর'),
(178, 20, 'active', 'Sharsha', 'শার্শা'),
(179, 21, 'active', 'Assasuni', 'আশাশুনি'),
(180, 21, 'active', 'Debhata', 'দেবহাটা'),
(181, 21, 'active', 'Kalaroa', 'কলারোয়া'),
(182, 21, 'active', 'Satkhira Sadar', 'সাতক্ষীরা সদর'),
(183, 21, 'active', 'Shyamnagar', 'শ্যামনগর'),
(184, 21, 'active', 'Tala', 'তালা'),
(185, 21, 'active', 'Kaliganj', 'কালিগঞ্জ'),
(186, 22, 'active', 'Mujibnagar', 'মুজিবনগর'),
(187, 22, 'active', 'Meherpur Sadar', 'মেহেরপুর সদর'),
(188, 22, 'active', 'Gangni', 'গাংনী'),
(189, 23, 'active', 'Narail Sadar', 'নড়াইল সদর'),
(190, 23, 'active', 'Lohagara', 'লোহাগড়া'),
(191, 23, 'active', 'Kalia', 'কালিয়া'),
(192, 24, 'active', 'Chuadanga Sadar', 'চুয়াডাঙ্গা সদর'),
(193, 24, 'active', 'Alamdanga', 'আলমডাঙ্গা'),
(194, 24, 'active', 'Damurhuda', 'দামুড়হুদা'),
(195, 24, 'active', 'Jibannagar', 'জীবননগর'),
(196, 25, 'active', 'Kushtia Sadar', 'কুষ্টিয়া সদর'),
(197, 25, 'active', 'Kumarkhali', 'কুমারখালী'),
(198, 25, 'active', 'Khoksa', 'খোকসা'),
(199, 25, 'active', 'Mirpur', 'মিরপুর'),
(200, 25, 'active', 'Daulatpur', 'দৌলতপুর'),
(201, 25, 'active', 'Bheramara', 'ভেড়ামারা'),
(202, 26, 'active', 'Shalikha', 'শালিখা'),
(203, 26, 'active', 'Sreepur', 'শ্রীপুর'),
(204, 26, 'active', 'Magura Sadar', 'মাগুরা সদর'),
(205, 26, 'active', 'Mohammadpur', 'মহম্মদপুর'),
(206, 27, 'active', 'Paikgasa', 'পাইকগাছা'),
(207, 27, 'active', 'Fultola', 'ফুলতলা'),
(208, 27, 'active', 'Digholia', 'দিঘলিয়া'),
(209, 27, 'active', 'Rupsha', 'রূপসা'),
(210, 27, 'active', 'Terokhada', 'তেরখাদা'),
(211, 27, 'active', 'Dumuria', 'ডুমুরিয়া'),
(212, 27, 'active', 'Botiaghata', 'বটিয়াঘাটা'),
(213, 27, 'active', 'Dakop', 'দাকোপ'),
(214, 27, 'active', 'Koyra', 'কয়রা'),
(215, 28, 'active', 'Fakirhat', 'ফকিরহাট'),
(216, 28, 'active', 'Bagerhat Sadar', 'বাগেরহাট সদর'),
(217, 28, 'active', 'Mollahat', 'মোল্লাহাট'),
(218, 28, 'active', 'Sarankhola', 'শরণখোলা'),
(219, 28, 'active', 'Rampal', 'রামপাল'),
(220, 28, 'active', 'Morrelganj', 'মোড়েলগঞ্জ'),
(221, 28, 'active', 'Kachua', 'কচুয়া'),
(222, 28, 'active', 'Mongla', 'মোংলা'),
(223, 28, 'active', 'Chitalmari', 'চিতলমারী'),
(224, 29, 'active', 'Jhenaidah Sadar', 'ঝিনাইদহ সদর'),
(225, 29, 'active', 'Shailkupa', 'শৈলকুপা'),
(226, 29, 'active', 'Harinakundu', 'হরিণাকুন্ডু'),
(227, 29, 'active', 'Kaliganj', 'কালীগঞ্জ'),
(228, 29, 'active', 'Kotchandpur', 'কোটচাঁদপুর'),
(229, 29, 'active', 'Moheshpur', 'মহেশপুর'),
(230, 30, 'active', 'Jhalakathi Sadar', 'ঝালকাঠি সদর'),
(231, 30, 'active', 'Kathalia', 'কাঠালিয়া'),
(232, 30, 'active', 'Nalchity', 'নলছিটি'),
(233, 30, 'active', 'Rajapur', 'রাজাপুর'),
(234, 31, 'active', 'Bauphal', 'বাউফল'),
(235, 31, 'active', 'Patuakhali Sadar', 'পটুয়াখালী সদর'),
(236, 31, 'active', 'Dumki', 'দুমকি'),
(237, 31, 'active', 'Dashmina', 'দশমিনা'),
(238, 31, 'active', 'Kalapara', 'কলাপাড়া'),
(239, 31, 'active', 'Mirzaganj', 'মির্জাগঞ্জ'),
(240, 31, 'active', 'Galachipa', 'গলাচিপা'),
(241, 31, 'active', 'Rangabali', 'রাঙ্গাবালী'),
(242, 32, 'active', 'Pirojpur Sadar', 'পিরোজপুর সদর'),
(243, 32, 'active', 'Nazirpur', 'নাজিরপুর'),
(244, 32, 'active', 'Kawkhali', 'কাউখালী'),
(245, 32, 'active', 'Zianagar', 'জিয়ানগর'),
(246, 32, 'active', 'Bhandaria', 'ভান্ডারিয়া'),
(247, 32, 'active', 'Mathbaria', 'মঠবাড়ীয়া'),
(248, 32, 'active', 'Nesarabad', 'নেছারাবাদ'),
(249, 33, 'active', 'Barisal Sadar', 'বরিশাল সদর'),
(250, 33, 'active', 'Bakerganj', 'বাকেরগঞ্জ'),
(251, 33, 'active', 'Babuganj', 'বাবুগঞ্জ'),
(252, 33, 'active', 'Wazirpur', 'উজিরপুর'),
(253, 33, 'active', 'Banaripara', 'বানারীপাড়া'),
(254, 33, 'active', 'Gournadi', 'গৌরনদী'),
(255, 33, 'active', 'Agailjhara', 'আগৈলঝাড়া'),
(256, 33, 'active', 'Mehendiganj', 'মেহেন্দিগঞ্জ'),
(257, 33, 'active', 'Muladi', 'মুলাদী'),
(258, 33, 'active', 'Hizla', 'হিজলা'),
(259, 34, 'active', 'Bhola Sadar', 'ভোলা সদর'),
(260, 34, 'active', 'Borhan Sddin', 'বোরহান উদ্দিন'),
(261, 34, 'active', 'Charfesson', 'চরফ্যাশন'),
(262, 34, 'active', 'Doulatkhan', 'দৌলতখান'),
(263, 34, 'active', 'Monpura', 'মনপুরা'),
(264, 34, 'active', 'Tazumuddin', 'তজুমদ্দিন'),
(265, 34, 'active', 'Lalmohan', 'লালমোহন'),
(266, 35, 'active', 'Amtali', 'আমতলী'),
(267, 35, 'active', 'Barguna Sadar', 'বরগুনা সদর'),
(268, 35, 'active', 'Betagi', 'বেতাগী'),
(269, 35, 'active', 'Bamna', 'বামনা'),
(270, 35, 'active', 'Pathorghata', 'পাথরঘাটা'),
(271, 35, 'active', 'Taltali', 'তালতলি'),
(272, 36, 'active', 'Balaganj', 'বালাগঞ্জ'),
(273, 36, 'active', 'Beanibazar', 'বিয়ানীবাজার'),
(274, 36, 'active', 'Bishwanath', 'বিশ্বনাথ'),
(275, 36, 'active', 'Companiganj', 'কোম্পানীগঞ্জ'),
(276, 36, 'active', 'Fenchuganj', 'ফেঞ্চুগঞ্জ'),
(277, 36, 'active', 'Golapganj', 'গোলাপগঞ্জ'),
(278, 36, 'active', 'Gowainghat', 'গোয়াইনঘাট'),
(279, 36, 'active', 'Jaintiapur', 'জৈন্তাপুর'),
(280, 36, 'active', 'Kanaighat', 'কানাইঘাট'),
(281, 36, 'active', 'Sylhet Sadar', 'সিলেট সদর'),
(282, 36, 'active', 'Zakiganj', 'জকিগঞ্জ'),
(283, 36, 'active', 'Dakshinsurma', 'দক্ষিণ সুরমা'),
(284, 36, 'active', 'Osmaninagar', 'ওসমানী নগর'),
(285, 37, 'active', 'Barlekha', 'বড়লেখা'),
(286, 37, 'active', 'Kamolganj', 'কমলগঞ্জ'),
(287, 37, 'active', 'Kulaura', 'কুলাউড়া'),
(288, 37, 'active', 'Moulvibazar Sadar', 'মৌলভীবাজার সদর'),
(289, 37, 'active', 'Rajnagar', 'রাজনগর'),
(290, 37, 'active', 'Sreemangal', 'শ্রীমঙ্গল'),
(291, 37, 'active', 'Juri', 'জুড়ী'),
(292, 38, 'active', 'Nabiganj', 'নবীগঞ্জ'),
(293, 38, 'active', 'Bahubal', 'বাহুবল'),
(294, 38, 'active', 'Ajmiriganj', 'আজমিরীগঞ্জ'),
(295, 38, 'active', 'Baniachong', 'বানিয়াচং'),
(296, 38, 'active', 'Lakhai', 'লাখাই'),
(297, 38, 'active', 'Chunarughat', 'চুনারুঘাট'),
(298, 38, 'active', 'Habiganj Sadar', 'হবিগঞ্জ সদর'),
(299, 38, 'active', 'Madhabpur', 'মাধবপুর'),
(300, 39, 'active', 'Sunamganj Sadar', 'সুনামগঞ্জ সদর'),
(301, 39, 'active', 'South Sunamganj', 'দক্ষিণ সুনামগঞ্জ'),
(302, 39, 'active', 'Bishwambarpur', 'বিশ্বম্ভরপুর'),
(303, 39, 'active', 'Chhatak', 'ছাতক'),
(304, 39, 'active', 'Jagannathpur', 'জগন্নাথপুর'),
(305, 39, 'active', 'Dowarabazar', 'দোয়ারাবাজার'),
(306, 39, 'active', 'Tahirpur', 'তাহিরপুর'),
(307, 39, 'active', 'Dharmapasha', 'ধর্মপাশা'),
(308, 39, 'active', 'Jamalganj', 'জামালগঞ্জ'),
(309, 39, 'active', 'Shalla', 'শাল্লা'),
(310, 39, 'active', 'Derai', 'দিরাই'),
(311, 40, 'active', 'Belabo', 'বেলাবো'),
(312, 40, 'active', 'Monohardi', 'মনোহরদী'),
(313, 40, 'active', 'Narsingdi Sadar', 'নরসিংদী সদর'),
(314, 40, 'active', 'Palash', 'পলাশ'),
(315, 40, 'active', 'Raipura', 'রায়পুরা'),
(316, 40, 'active', 'Shibpur', 'শিবপুর'),
(317, 41, 'active', 'Kaliganj', 'কালীগঞ্জ'),
(318, 41, 'active', 'Kaliakair', 'কালিয়াকৈর'),
(319, 41, 'active', 'Kapasia', 'কাপাসিয়া'),
(320, 41, 'active', 'Gazipur Sadar', 'গাজীপুর সদর'),
(321, 41, 'active', 'Sreepur', 'শ্রীপুর'),
(322, 42, 'active', 'Shariatpur Sadar', 'শরিয়তপুর সদর'),
(323, 42, 'active', 'Naria', 'নড়িয়া'),
(324, 42, 'active', 'Zajira', 'জাজিরা'),
(325, 42, 'active', 'Gosairhat', 'গোসাইরহাট'),
(326, 42, 'active', 'Bhedarganj', 'ভেদরগঞ্জ'),
(327, 42, 'active', 'Damudya', 'ডামুড্যা'),
(328, 43, 'active', 'Araihazar', 'আড়াইহাজার'),
(329, 43, 'active', 'Bandar', 'বন্দর'),
(330, 43, 'active', 'Narayanganj Sadar', 'নারায়নগঞ্জ সদর'),
(331, 43, 'active', 'Rupganj', 'রূপগঞ্জ'),
(332, 43, 'active', 'Sonargaon', 'সোনারগাঁ'),
(333, 44, 'active', 'Basail', 'বাসাইল'),
(334, 44, 'active', 'Bhuapur', 'ভুয়াপুর'),
(335, 44, 'active', 'Delduar', 'দেলদুয়ার'),
(336, 44, 'active', 'Ghatail', 'ঘাটাইল'),
(337, 44, 'active', 'Gopalpur', 'গোপালপুর'),
(338, 44, 'active', 'Madhupur', 'মধুপুর'),
(339, 44, 'active', 'Mirzapur', 'মির্জাপুর'),
(340, 44, 'active', 'Nagarpur', 'নাগরপুর'),
(341, 44, 'active', 'Sakhipur', 'সখিপুর'),
(342, 44, 'active', 'Tangail Sadar', 'টাঙ্গাইল সদর'),
(343, 44, 'active', 'Kalihati', 'কালিহাতী'),
(344, 44, 'active', 'Dhanbari', 'ধনবাড়ী'),
(345, 45, 'active', 'Itna', 'ইটনা'),
(346, 45, 'active', 'Katiadi', 'কটিয়াদী'),
(347, 45, 'active', 'Bhairab', 'ভৈরব'),
(348, 45, 'active', 'Tarail', 'তাড়াইল'),
(349, 45, 'active', 'Hossainpur', 'হোসেনপুর'),
(350, 45, 'active', 'Pakundia', 'পাকুন্দিয়া'),
(351, 45, 'active', 'Kuliarchar', 'কুলিয়ারচর'),
(352, 45, 'active', 'Kishoreganj Sadar', 'কিশোরগঞ্জ সদর'),
(353, 45, 'active', 'Karimgonj', 'করিমগঞ্জ'),
(354, 45, 'active', 'Bajitpur', 'বাজিতপুর'),
(355, 45, 'active', 'Austagram', 'অষ্টগ্রাম'),
(356, 45, 'active', 'Mithamoin', 'মিঠামইন'),
(357, 45, 'active', 'Nikli', 'নিকলী'),
(358, 46, 'active', 'Harirampur', 'হরিরামপুর'),
(359, 46, 'active', 'Saturia', 'সাটুরিয়া'),
(360, 46, 'active', 'Manikganj Sadar', 'মানিকগঞ্জ সদর'),
(361, 46, 'active', 'Gior', 'ঘিওর'),
(362, 46, 'active', 'Shibaloy', 'শিবালয়'),
(363, 46, 'active', 'Doulatpur', 'দৌলতপুর'),
(364, 46, 'active', 'Singiar', 'সিংগাইর'),
(365, 47, 'active', 'Savar', 'সাভার'),
(366, 47, 'active', 'Dhamrai', 'ধামরাই'),
(367, 47, 'active', 'Keraniganj', 'কেরাণীগঞ্জ'),
(368, 47, 'active', 'Nawabganj', 'নবাবগঞ্জ'),
(369, 47, 'active', 'Dohar', 'দোহার'),
(370, 48, 'active', 'Munshiganj Sadar', 'মুন্সিগঞ্জ সদর'),
(371, 48, 'active', 'Sreenagar', 'শ্রীনগর'),
(372, 48, 'active', 'Sirajdikhan', 'সিরাজদিখান'),
(373, 48, 'active', 'Louhajanj', 'লৌহজং'),
(374, 48, 'active', 'Gajaria', 'গজারিয়া'),
(375, 48, 'active', 'Tongibari', 'টংগীবাড়ি'),
(376, 49, 'active', 'Rajbari Sadar', 'রাজবাড়ী সদর'),
(377, 49, 'active', 'Goalanda', 'গোয়ালন্দ'),
(378, 49, 'active', 'Pangsa', 'পাংশা'),
(379, 49, 'active', 'Baliakandi', 'বালিয়াকান্দি'),
(380, 49, 'active', 'Kalukhali', 'কালুখালী'),
(381, 50, 'active', 'Madaripur Sadar', 'মাদারীপুর সদর'),
(382, 50, 'active', 'Shibchar', 'শিবচর'),
(383, 50, 'active', 'Kalkini', 'কালকিনি'),
(384, 50, 'active', 'Rajoir', 'রাজৈর'),
(385, 51, 'active', 'Gopalganj Sadar', 'গোপালগঞ্জ সদর'),
(386, 51, 'active', 'Kashiani', 'কাশিয়ানী'),
(387, 51, 'active', 'Tungipara', 'টুংগীপাড়া'),
(388, 51, 'active', 'Kotalipara', 'কোটালীপাড়া'),
(389, 51, 'active', 'Muksudpur', 'মুকসুদপুর'),
(390, 52, 'active', 'Faridpur Sadar', 'ফরিদপুর সদর'),
(391, 52, 'active', 'Alfadanga', 'আলফাডাঙ্গা'),
(392, 52, 'active', 'Boalmari', 'বোয়ালমারী'),
(393, 52, 'active', 'Sadarpur', 'সদরপুর'),
(394, 52, 'active', 'Nagarkanda', 'নগরকান্দা'),
(395, 52, 'active', 'Bhanga', 'ভাঙ্গা'),
(396, 52, 'active', 'Charbhadrasan', 'চরভদ্রাসন'),
(397, 52, 'active', 'Madhukhali', 'মধুখালী'),
(398, 52, 'active', 'Saltha', 'সালথা'),
(399, 53, 'active', 'Panchagarh Sadar', 'পঞ্চগড় সদর'),
(400, 53, 'active', 'Debiganj', 'দেবীগঞ্জ'),
(401, 53, 'active', 'Boda', 'বোদা'),
(402, 53, 'active', 'Atwari', 'আটোয়ারী'),
(403, 53, 'active', 'Tetulia', 'তেতুলিয়া'),
(404, 54, 'active', 'Nawabganj', 'নবাবগঞ্জ'),
(405, 54, 'active', 'Birganj', 'বীরগঞ্জ'),
(406, 54, 'active', 'Ghoraghat', 'ঘোড়াঘাট'),
(407, 54, 'active', 'Birampur', 'বিরামপুর'),
(408, 54, 'active', 'Parbatipur', 'পার্বতীপুর'),
(409, 54, 'active', 'Bochaganj', 'বোচাগঞ্জ'),
(410, 54, 'active', 'Kaharol', 'কাহারোল'),
(411, 54, 'active', 'Fulbari', 'ফুলবাড়ী'),
(412, 54, 'active', 'Dinajpur Sadar', 'দিনাজপুর সদর'),
(413, 54, 'active', 'Hakimpur', 'হাকিমপুর'),
(414, 54, 'active', 'Khansama', 'খানসামা'),
(415, 54, 'active', 'Birol', 'বিরল'),
(416, 54, 'active', 'Chirirbandar', 'চিরিরবন্দর'),
(417, 55, 'active', 'Lalmonirhat Sadar', 'লালমনিরহাট সদর'),
(418, 55, 'active', 'Kaliganj', 'কালীগঞ্জ'),
(419, 55, 'active', 'Hatibandha', 'হাতীবান্ধা'),
(420, 55, 'active', 'Patgram', 'পাটগ্রাম'),
(421, 55, 'active', 'Aditmari', 'আদিতমারী'),
(422, 56, 'active', 'Syedpur', 'সৈয়দপুর'),
(423, 56, 'active', 'Domar', 'ডোমার'),
(424, 56, 'active', 'Dimla', 'ডিমলা'),
(425, 56, 'active', 'Jaldhaka', 'জলঢাকা'),
(426, 56, 'active', 'Kishorganj', 'কিশোরগঞ্জ'),
(427, 56, 'active', 'Nilphamari Sadar', 'নীলফামারী সদর'),
(428, 57, 'active', 'Sadullapur', 'সাদুল্লাপুর'),
(429, 57, 'active', 'Gaibandha Sadar', 'গাইবান্ধা সদর'),
(430, 57, 'active', 'Palashbari', 'পলাশবাড়ী'),
(431, 57, 'active', 'Saghata', 'সাঘাটা'),
(432, 57, 'active', 'Gobindaganj', 'গোবিন্দগঞ্জ'),
(433, 57, 'active', 'Sundarganj', 'সুন্দরগঞ্জ'),
(434, 57, 'active', 'Phulchari', 'ফুলছড়ি'),
(435, 58, 'active', 'Thakurgaon Sadar', 'ঠাকুরগাঁও সদর'),
(436, 58, 'active', 'Pirganj', 'পীরগঞ্জ'),
(437, 58, 'active', 'Ranisankail', 'রাণীশংকৈল'),
(438, 58, 'active', 'Haripur', 'হরিপুর'),
(439, 58, 'active', 'Baliadangi', 'বালিয়াডাঙ্গী'),
(440, 59, 'active', 'Rangpur Sadar', 'রংপুর সদর'),
(441, 59, 'active', 'Gangachara', 'গংগাচড়া'),
(442, 59, 'active', 'Taragonj', 'তারাগঞ্জ'),
(443, 59, 'active', 'Badargonj', 'বদরগঞ্জ'),
(444, 59, 'active', 'Mithapukur', 'মিঠাপুকুর'),
(445, 59, 'active', 'Pirgonj', 'পীরগঞ্জ'),
(446, 59, 'active', 'Kaunia', 'কাউনিয়া'),
(447, 59, 'active', 'Pirgacha', 'পীরগাছা'),
(448, 60, 'active', 'Kurigram Sadar', 'কুড়িগ্রাম সদর'),
(449, 60, 'active', 'Nageshwari', 'নাগেশ্বরী'),
(450, 60, 'active', 'Bhurungamari', 'ভুরুঙ্গামারী'),
(451, 60, 'active', 'Phulbari', 'ফুলবাড়ী'),
(452, 60, 'active', 'Rajarhat', 'রাজারহাট'),
(453, 60, 'active', 'Ulipur', 'উলিপুর'),
(454, 60, 'active', 'Chilmari', 'চিলমারী'),
(455, 60, 'active', 'Rowmari', 'রৌমারী'),
(456, 60, 'active', 'Charrajibpur', 'চর রাজিবপুর'),
(457, 61, 'active', 'Sherpur Sadar', 'শেরপুর সদর'),
(458, 61, 'active', 'Nalitabari', 'নালিতাবাড়ী'),
(459, 61, 'active', 'Sreebordi', 'শ্রীবরদী'),
(460, 61, 'active', 'Nokla', 'নকলা'),
(461, 61, 'active', 'Jhenaigati', 'ঝিনাইগাতী'),
(462, 62, 'active', 'Fulbaria', 'ফুলবাড়ীয়া'),
(463, 62, 'active', 'Trishal', 'ত্রিশাল'),
(464, 62, 'active', 'Bhaluka', 'ভালুকা'),
(465, 62, 'active', 'Muktagacha', 'মুক্তাগাছা'),
(466, 62, 'active', 'Mymensingh Sadar', 'ময়মনসিংহ সদর'),
(467, 62, 'active', 'Dhobaura', 'ধোবাউড়া'),
(468, 62, 'active', 'Phulpur', 'ফুলপুর'),
(469, 62, 'active', 'Haluaghat', 'হালুয়াঘাট'),
(470, 62, 'active', 'Gouripur', 'গৌরীপুর'),
(471, 62, 'active', 'Gafargaon', 'গফরগাঁও'),
(472, 62, 'active', 'Iswarganj', 'ঈশ্বরগঞ্জ'),
(473, 62, 'active', 'Nandail', 'নান্দাইল'),
(474, 62, 'active', 'Tarakanda', 'তারাকান্দা'),
(475, 63, 'active', 'Jamalpur Sadar', 'জামালপুর সদর'),
(476, 63, 'active', 'Melandah', 'মেলান্দহ'),
(477, 63, 'active', 'Islampur', 'ইসলামপুর'),
(478, 63, 'active', 'Dewangonj', 'দেওয়ানগঞ্জ'),
(479, 63, 'active', 'Sarishabari', 'সরিষাবাড়ী'),
(480, 63, 'active', 'Madarganj', 'মাদারগঞ্জ'),
(481, 63, 'active', 'Bokshiganj', 'বকশীগঞ্জ'),
(482, 64, 'active', 'Barhatta', 'বারহাট্টা'),
(483, 64, 'active', 'Durgapur', 'দুর্গাপুর'),
(484, 64, 'active', 'Kendua', 'কেন্দুয়া'),
(485, 64, 'active', 'Atpara', 'আটপাড়া'),
(486, 64, 'active', 'Madan', 'মদন'),
(487, 64, 'active', 'Khaliajuri', 'খালিয়াজুরী'),
(488, 64, 'active', 'Kalmakanda', 'কলমাকান্দা'),
(489, 64, 'active', 'Mohongonj', 'মোহনগঞ্জ'),
(490, 64, 'active', 'Purbadhala', 'পূর্বধলা'),
(491, 64, 'active', 'Netrokona Sadar', 'নেত্রকোণা সদর'),
(492, 47, 'active', 'Adabar', 'আদাবর'),
(493, 47, 'active', 'Airport', 'এয়ারপোর্ট'),
(494, 47, 'active', 'Badda', 'বাড্ডা'),
(495, 47, 'active', 'Cantonment', 'ক্যান্টনমেন্ট'),
(496, 47, 'active', 'Demra', 'ডেমরা'),
(497, 47, 'active', 'Dhanmondi', 'ধানমন্ডি'),
(498, 47, 'active', 'Gulshan', 'গুলশান'),
(499, 47, 'active', 'Hajaribag', 'হাজারিবাগ'),
(500, 47, 'active', 'Kafrul', 'কাফরুল'),
(501, 47, 'active', 'Kamrangirchar', 'কামরাঙ্গীরচর'),
(502, 47, 'active', 'Khilgaon', 'খিলগাঁও'),
(503, 47, 'active', 'Kotwali', 'কোতোয়ালী'),
(504, 47, 'active', 'Lalbagh', 'লালবাগ'),
(505, 47, 'active', 'Mirpur', 'মিরপুর'),
(506, 47, 'active', 'Mohammodpur', 'মোহাম্মদপুর'),
(507, 47, 'active', 'Motijheel', 'মতিঝিল'),
(508, 47, 'active', 'New Market', 'নিউমার্কেট'),
(509, 47, 'active', 'Pallabi', 'পল্লবি'),
(510, 47, 'active', 'Paltan', 'পল্টন'),
(511, 47, 'active', 'Ramna', 'রমনা'),
(512, 47, 'active', 'Shah Ali', 'শাহ আলী'),
(513, 47, 'active', 'Shympur', 'শ্যামপুর'),
(514, 47, 'active', 'Sobujbag', 'সবুজবাগ'),
(515, 47, 'active', 'Sutrapur', 'সূত্রাপুর'),
(516, 47, 'active', 'Tejgaon', 'তেজগাঁও'),
(517, 47, 'active', 'Turag', 'তুরাগ'),
(518, 47, 'active', 'Uttara', 'উত্তরা'),
(519, 47, 'active', 'Sher e bangla nagar', 'শেরেবাংলা নগর'),
(520, 47, 'active', 'Darus Salam', 'দারুস সালাম'),
(521, 47, 'active', 'Rupnagar', 'রূপনগর'),
(522, 47, 'active', 'Vasantek', 'ভাষানটেক');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `areas_district_id_foreign` (`district_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=523;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `areas`
--
ALTER TABLE `areas`
  ADD CONSTRAINT `areas_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
