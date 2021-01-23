

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";




CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cep` varchar(50) NOT NULL,
  `street` varchar(50) NOT NULL,
  `number` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `neighbourhood` varchar(50) NOT NULL,
  `uf` char(2) NOT NULL,
  `complement` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `carts` (
  `hash` varchar(127) NOT NULL,
  `seq` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `departaments` (
  `id` int(11) NOT NULL,
  `name_departament` varchar(50) DEFAULT NULL,
  `observation_departament` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



INSERT INTO `departaments` (`id`, `name_departament`, `observation_departament`) VALUES
(1, 'Processadores', 'Núcleo'),
(2, 'Placas de Vídeo', 'Memória de Vídeo'),
(3, 'Memórias RAM', 'Tipo de Memória'),
(4, 'Discos Rígidos - HD', 'Capacidade'),
(5, 'Solid State Drives - SSD', 'Capacidade'),
(6, 'Placas Mãe', 'Socket'),
(7, 'Fonte de Alimentação', 'Potência (W)'),
(8, 'Gabinetes', 'Padrão'),
(9, 'CPU Coolers', 'Socket'),
(10, 'Case Mods', 'Tamanho Fan (MM)'),
(11, 'Placas de Som', 'Número de canais'),
(12, 'Drivers Óticos', 'Tipo de Mídia'),
(13, 'Teclados', 'Número de teclas'),
(14, 'Mouses', 'DPI'),
(15, 'Fones e Headsets', 'Tipo conexão'),
(16, 'Caixas de Som', 'Potência (W RMS)'),
(17, 'Web Cams', 'Resolução'),
(18, 'Monitores', 'Tamanho');


CREATE TABLE `fiscal_notes` (
  `id` int(11) NOT NULL,
  `date_begin` date DEFAULT NULL,
  `date_cancel` date DEFAULT NULL,
  `reason_cancel` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `freights` (
  `id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `value_freight` double NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `send_mode` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `item_notes` (
  `value_unit` double DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `item_requests` (
  `request_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `value_unit` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `payments` (
  `request_id` int(11) NOT NULL,
  `data_payment` datetime NOT NULL,
  `type_payment` varchar(10) NOT NULL,
  `number_validation` varchar(50) NOT NULL,
  `identification` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` double NOT NULL,
  `stock` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `departament_id` int(11) DEFAULT NULL,
  `img1` varchar(50) NOT NULL,
  `img2` varchar(50) NOT NULL,
  `img3` varchar(50) NOT NULL,
  `description` varchar(10000) NOT NULL,
  `filter` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_begin` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_end` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `reviews` (
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(50) NOT NULL,
  `note` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `cnpj` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



INSERT INTO `suppliers` (`id`, `name`, `cnpj`) VALUES
(1, 'Intel', '05487441000183'),
(2, 'AMD', '31897981000145'),
(3, 'Razer', '17839072000120'),
(4, 'Nvidia', '35581823000132'),
(5, 'Corsair', '14523232000139'),
(6, 'Kingston', '84389872000183'),
(7, 'Seagate', '93729128'),
(8, 'Western Digital', '932729128'),
(9, 'Asus', '87329819'),
(10, 'Gigabyte', '873292819');



CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(128) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `carts`
  ADD PRIMARY KEY (`hash`,`seq`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `departaments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `fiscal_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `request_id` (`request_id`);

ALTER TABLE `freights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `address_id` (`address_id`);

ALTER TABLE `item_notes`
  ADD PRIMARY KEY (`product_id`,`note_id`),
  ADD KEY `note_id` (`note_id`);

ALTER TABLE `item_requests`
  ADD PRIMARY KEY (`product_id`,`request_id`),
  ADD KEY `request_id` (`request_id`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`request_id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departament_id` (`departament_id`),
  ADD KEY `supplier_id` (`supplier_id`);

ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`product_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);


ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `rg` (`rg`);


ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `carts`
  MODIFY `seq` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `departaments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `fiscal_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `freights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
