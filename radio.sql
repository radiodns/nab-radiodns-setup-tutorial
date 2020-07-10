DROP TABLE IF EXISTS `stations`;

CREATE TABLE `stations` (
  `callsign` varchar(4) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL,
  `description` varchar(64) NOT NULL DEFAULT '',
  `genre` varchar(128) NOT NULL,
  `logo_url` varchar(255) NOT NULL DEFAULT '',
  `stream_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`callsign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `stations` WRITE;

INSERT INTO `stations` (`callsign`, `name`, `description`, `genre`, `logo_url`, `stream_url`)
VALUES
	('WBMX','104.3 Jams','Chicago\'s #1 For Throwbacks','urn:tva:metadata:cs:ContentCS:2004:3.6.4.5','../images/sdgdfg.png','https://17443.live.streamtheworld.com/WBMXFMAAC_SC'),
	('WHEB','1003. WHEB','The Rock Station: Portsmouth','urn:tva:metadata:cs:ContentCS:2004:3.6.4.14','../images/fhgdfg.png','http://stream.revma.ihrhls.com/zc1353'),
	('WKQX','101 WKQX','The New Home For Alternative','urn:tva:metadata:cs:ContentCS:2004:3.6.4.14.6','../images/fgdfds.png','http://17013.live.streamtheworld.com:80/WKQXFM_SC'),
	('WNOK','104.7 WNOK','Columbia\'s Hit Music Station','urn:tva:metadata:cs:ContentCS:2004:3.6.10','../images/fdgder.png','http://stream.revma.ihrhls.com/zc2081');

UNLOCK TABLES;
