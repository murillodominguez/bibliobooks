-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 12-Jul-2024 às 15:01
-- Versão do servidor: 8.0.31
-- versão do PHP: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dbprincipal`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `livros`
--

CREATE TABLE `livros` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `author` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `year` int NOT NULL,
  `editor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `owners` varchar(255) DEFAULT NULL,
  `quant` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `livros`
--

INSERT INTO `livros` (`id`, `title`, `author`, `year`, `editor`, `owners`, `quant`) VALUES
(19, 'Hamlet', 'William Shakespeare', 2020, 'RENOVA', '[]', 17),
(20, 'John Doe at the End of the World', 'Samuel Urbanetto', 2024, 'Trabalho Individual', '[{\"id\":\"32\"}]', 89),
(21, 'Mais esperto que o Diabo: O mistério revelado da liberdade e do sucesso', 'Napoleon Hill', 2020, 'Citadel', '[]', 196);

-- --------------------------------------------------------

--
-- Estrutura da tabela `relations`
--

CREATE TABLE `relations` (
  `id` int NOT NULL,
  `id_liv` int NOT NULL,
  `id_user` int NOT NULL,
  `dateupload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `relations`
--

INSERT INTO `relations` (`id`, `id_liv`, `id_user`, `dateupload`) VALUES
(80, 20, 32, '2024-07-12 14:56:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `adm` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `email`, `token`, `adm`) VALUES
(1, 'vargasmurillo', 'vargasmurillo03@gmail.com', '$2y$10$aK/B.110gkERBsxH9uEwgeMr7u56j8EKp7aHQED6xQOysJ./URPge', ''),
(4, 'ultimate echo echo', 'ultimate@echo.echo', 'echoecho', ''),
(6, 'ultimate big chill', 'ultimate@big.chill', 'bigchill', ''),
(10, 'Ultimate Humungossaur', 'Ultimate@humungos.saur', 'humungossaur', ''),
(12, 'Ultimate Cannonbolt', 'Ultimate@cannon.bolt', 'Cannonbolt', ''),
(14, 'Ultimate WayBig', 'Ultimate@way.big', 'Waybig', ''),
(16, 'Ultimate Wildmutt', 'Ultimate@wild.mutt', 'wildmutt', ''),
(17, 'Ultimate Swampfire', 'Ultimate@swamp.fire', 'swampfire', ''),
(18, 'novo', '12345@gmail.com', '$2y$10$6k51S0TEc3EYFQqf9u5URuPs8eXqTxsAsC4p23FHC2v7gbUncsHvm', ''),
(21, 'murillo', 'vargas@gmail.com', '$2y$10$eStRxERz1bwiWhLP9b9cJebQ9Q3G2GZlyM0LMnJJYCMANbLtbFM5C', ''),
(22, 'neww', 'varg@gmail.com', '$2y$10$KVrVMWalMRZtE.w1.DeRx.3pbjDG2.FE/oWHK3.oPHwV3cL4OeqOC', ''),
(23, 'varga', 'var@gmail.com', '$2y$10$BuCOnRsVFMaIjGyvl9.OM.5wMjlB5F7..6Z5m2u572n7n4OJNU2fm', ''),
(24, 'asd', 'asd@gmail.com', '$2y$10$uVLwpg1eyt.w6Y8fUwRSeuIwf52iZSxhgidPygca2hhvaes5NGpcK', ''),
(25, 'asd', 'asdd@gmail.com', '$2y$10$dt2BVJDJvigOPUV9lpk.d.rykL50ViQY.6Vx8cDhHmmmASGtoz5IK', ''),
(26, 'eqwe', 'asdf@gmail.com', '$2y$10$WgEBLXP0fgDNj4Lj4Z.mtu7K1bTkoiG2BjRD.vZo/ZE0u9pjj.Fte', ''),
(27, 'asddsa', 'adadssda@gmail.com', '$2y$10$Mq.5CWVgWdTYhaM1QqbIC.PwPgJF2BQ6bqqsNX.7eGDUM/QBV2YUG', ''),
(28, 'asddsa', 'asad@gmail.com', '$2y$10$xYEc0qzq28jQTVcdV.tUyOnXwYgvuNdujJGXU2hM0HncdydA3raom', ''),
(29, '214', 'as12d@gmail.com', '$2y$10$ZFfw7srZrrcKKsx1Yb7m6.JVibP9DILJziQMIBVVRzNGwOdAXEoP2', ''),
(30, 'asdsa', 'sadasdfsfa@gmail.com', '$2y$10$dJTFeEDTvBK7pFm5sb2D0.KJRlgc4dD2q8rK8pn3T1JuG9W9mYSFm', 'YES'),
(32, 'heigal', 'asd123@gmail.com', '$2y$10$J0gygpxDhnf9Rkpag96Te.ou4EdWHtH1tk9/u/W8D0YAdSHOFvAs6', 'YES'),
(33, 'usuario', 'usuario@hotmail', '$2y$10$kXbHAOPXd7vo03pWLfrouu4rYwnHViq.LzWTSNWmBkZwELgSzr79a', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `relations`
--
ALTER TABLE `relations`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `relations`
--
ALTER TABLE `relations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
