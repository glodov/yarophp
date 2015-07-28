CREATE TABLE `areas` (
	`id` int(255) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `index_name` (`name`) USING BTREE
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE `results` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`area_id` int(11) NOT NULL,
	`risk_id` int(11) NOT NULL,
	`probability` int(11) NOT NULL,
	`impact` int(11) NOT NULL,
	`severity` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE `risks` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`parent_id` varchar(255) CHARACTER SET utf8 NOT NULL,
	`name` varchar(255) CHARACTER SET utf8 NOT NULL,
	PRIMARY KEY (`id`),
	KEY `Index_1` (`parent_id`) USING BTREE
) ENGINE=InnoDB CHARSET=utf8;

create table tokens (
	`id` char(40) not null,
	`object` char(40) not null,
	`object_id` int not null,
	primary key (`id`)
) ENGINE=InnoDB CHARSET=utf8;

create table users (
	`id` int not null AUTO_INCREMENT,
	`login` varchar(50) not null,
	`password` varchar(50) not null,
	primary key (`id`),
	unique (`login`)
) ENGINE=InnoDB CHARSET=utf8;
