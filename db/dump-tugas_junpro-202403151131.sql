-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: tugas_junpro
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `author` varchar(100) DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article`
--

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
INSERT INTO `article` VALUES (107,'dsdsdsdsdsd','dsdsdsdsdsdsdd','Rijky Sallaffudin Abdul Haque','2024-03-09',NULL,'../../uploads/photo_2024-03-06_16-20-32.jpg',1),(108,'ddsdsdsdsdsdsd','dsdsdsdsdsdsdsdsdsd','Rijky Sallaffudin Abdul Haque','2024-03-09',NULL,'../../uploads/0af1ddfe-7568-496f-81fd-61364b740edd.png',1),(109,'sdsdsdsdsd','dsdsdsdsdsd','Rijky Sallaffudin Abdul Haque','2024-03-09',NULL,'../../uploads/3333.png',3),(111,'dddddddddddddddd','ccvcvcvcvcvcvcv','fakhir Alhak','2024-03-05',NULL,'../../uploads/11.jpg',2),(112,'weweweewwewewe','sdsdsdsddsdsds','fakhir Alhak','2024-03-04',NULL,'../../uploads/22.jpg',2),(113,'Cara Mengatasi Pencemaran Tanah dan Panduannya','Penting sekali untuk mengetahui cara mengatasi pencemaran tanah di era modern. Ini mengingat kondisi lingkungan kita yang semakin tercemar, terancam oleh pencemaran tanah, yang mencapai tingkat memprihatinkan. Data terbaru menunjukkan bahwa sebagian besar wilayah di seluruh dunia menghadapi masalah serius akibat pencemaran tanah.\r\n\r\nPencemaran tanah sendiri dapat menimbulkan gangguan terhadap kesehatan manusia, bahkan di tingkat yang sangat berbahaya. Beberapa dampak pencemaran tanah bisa menimbulkan beberapa penyakit jangka pendek sampai panjang. Selain pada kesehatan manusia, pencemaran tanah juga berdampak pada ekosistem, baik flora maupun fauna di sekitar kita. Jadi jelas sekali kalau kita harus mengetahui cara mengatasi pencemaran tanah yang tepat untuk mengurangi kerusakan alam ini.\r\n\r\nBeberapa dampak pencemaran tanah terhadap ekosistem mulai dari perubahan dalam struktur dan kandungan tanah hingga memutus rantai makanan. Selanjutnya, mari kita simak beberapa cara mengatasi pencemaran tanah akibat pestisida dan kontaminan lainnya.\r\n\r\n','fakhir Alhak','2024-03-21',NULL,'../../uploads/contoh-artikel-ilmiah.jpg',2),(114,'Cara Mengatasi Pencemaran Tanah dan Panduannya','Reading merupakan salah satu aspek yang dinilai dalam tes kemahiran bahasa Inggris seperti pada TOEFL atau pun IELTS. Nah, kalau berbicara tentang reading pasti tak lepas dari yang namanya kalimat dan teks bahasa Inggris.\r\n\r\nPada umumnya, macam-macam teks yang akan kamu pelajari di artikel ini nggak jauh berbeda dengan jenis teks dalam bahasa Indonesia. Masing-masing teks tersebut memiliki tujuan/fungsi sosial masing-masing. Jadi, yuk kita cari tahu definisi, fungsi, struktur, dan contoh paragraf dari setiap teks-nya!\r\n\r\nPengertian Teks\r\n\r\nTeks adalah sebuah tulisan yang disusun dengan kalimat yang memiliki konteks. Kalau dalam teori sastra, teks adalah segala benda yang dapat “dibaca”, baik benda tersebut berupa karya sastra, tanda jalan, atau gaya pakaian.\r\n\r\nTapi, dalam hal ini, cakupannya hanya akan seputar “tulisan” saja ya, guys. Maka dari itu, setiap teks bahasa Inggris memiliki struktur dan kaidah kebahasaan (language feature) dalam penulisannya.\r\n\r\nJenis-jenis Teks Bahasa Inggris\r\n\r\n1. Descriptive Text \r\n\r\nDescriptive text bertujuan untuk menggambarkan/menjelaskan kepada pembaca mengenai seseorang, tempat, benda, hewan, dan hal lainnya secara detail. Pada teks ini, suatu objek akan dipaparkan secara rinci. Fungsinya supaya pembaca bisa membayangkan bagaimana bentuk, suasana, atau wujud dari suatu objek. Struktur dari descriptive text adalah identification dan description.\r\n\r\n2. Explanation Text\r\n\r\nSederhananya, descriptive text itu berisi mengenai penjelasan yang menjawab pertanyaan “what” atau “apa”. Nah, sementara, explanation text adalah jenis teks untuk menjawab pertanyaan “how”, alias bagaimana. Jadi, teks eksplanasi berfungsi untuk menjelaskan bagaimana suatu hal bisa terjadi, sifatnya logis dan mendetail. \r\n\r\nUmumnya explanation text digunakan banyak orang untuk memaparkan fenomena alam, sosial, dan juga budaya. Supaya pembaca bisa semakin mudah untuk memahami isi teks, biasanya sang penulis akan melengkapi teks dengan gambar yang relevan. Struktur dari explanation text adalah general statement, explanation, dan ada juga yang menambahkannya dengan conclusion.\r\n\r\n3. Recount Text \r\n\r\nKamu hobi mengabadikan pengalaman melalui tulisan? Nah, berarti recount text bisa jadi salah satu teks yang cocok untuk kamu tulis. Jadi, recount adalah teks yang menjelaskan cerita/pengalaman dari kejadian lampau, misal cerita traveling, mengikuti lomba, dan lain-lain.  Struktur teks recount adalah orientation, series of event, kemudian diakhiri dengan reorientation.','fakhir Alhak','2024-03-26','2024-03-14','../../uploads/photo_2024-03-06_16-20-32.jpg',1);
/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'IT Support','It Support'),(2,'IT SKill','IT SKill'),(3,'Sport','Sport');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `users_status` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (21,'Rijky Sallaffudin Abdul Haque','rijky@yopmail.com','$2y$10$imQ8aigf.PszopnJIOu3oO.vvVEoIs4Tbf.IvK6hsj8BH8Kzaegne','2024-03-05 02:22:27','2024-03-05 02:22:27',0,'rijky@ajasssdsdsd',1),(22,'joko nugroho','joko@yopmail.com','$2y$10$XOtXCrGwZY25RKyI1B2TlewLMooc6CtvYVZEHz4UaTQHhY6ARp4re','2024-03-06 02:25:39','2024-03-06 02:25:39',1,'joko@nugroho',1),(23,'ikram zakaria','ikram@yopmail.com','$2y$10$aOhU7y5uxHF9mkdSCEovb.ffKSoxV4Zp/tAwTQ1usfo6/eXziVGay','2024-03-08 10:02:10','2024-03-08 10:02:10',1,'ikram@zakaria',1),(24,'fakhir Alhak','0','$2y$10$s5i1O6X/ZOUeX/fN0n4l0eELNLDPTtFcYdlrlAi83sSq0nsEnjM12','2024-03-12 03:22:30','2024-03-12 03:22:30',1,'fakhir@alhaq',2);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_status`
--

DROP TABLE IF EXISTS `users_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_status` (
  `users_status_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`users_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_status`
--

LOCK TABLES `users_status` WRITE;
/*!40000 ALTER TABLE `users_status` DISABLE KEYS */;
INSERT INTO `users_status` VALUES (1,'active','Aktif','active'),(2,'Inactive','Sudah tidak aktif','Inactive'),(3,'Locked','Locked','Locked');
/*!40000 ALTER TABLE `users_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tugas_junpro'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-15 11:31:31
