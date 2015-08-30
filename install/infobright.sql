CREATE TABLE `flserver` (
  `flserver_datetime` datetime NOT NULL,
  `flserver_uid` int(11) NOT NULL,
  `flserver_mac` char(12) DEFAULT NULL,
  `flserver_clientip` bigint(20) DEFAULT NULL,
  `flserver_gameid` int(10) DEFAULT NULL,
  `flserver_gametype` mediumint(8) DEFAULT NULL,
  `flserver_gameserver` mediumint(8) DEFAULT NULL,
  `flserver_hash` char(32) DEFAULT NULL
) ENGINE=BRIGHTHOUSE DEFAULT CHARSET=utf8;

CREATE TABLE `flserver_area` (
  `flserver_datetime` datetime DEFAULT NULL,
  `flserver_gametype` mediumint(9) DEFAULT NULL,
  `flserver_area` tinyint(4) DEFAULT NULL,
  `flserver_count` int(11) DEFAULT NULL,
  `flserver_hash` char(32) DEFAULT NULL
) ENGINE=BRIGHTHOUSE DEFAULT CHARSET=utf8;

CREATE TABLE `flserver_ip` (
  `flserver_datetime` datetime DEFAULT NULL,
  `flserver_gametype` mediumint(9) DEFAULT NULL,
  `flserver_clientip` bigint(11) DEFAULT NULL,
  `flserver_count` int(11) DEFAULT NULL,
  `flserver_hash` char(32) DEFAULT NULL
) ENGINE=BRIGHTHOUSE DEFAULT CHARSET=utf8;

CREATE TABLE `usertrade` (
  `usertrade_datetime` datetime DEFAULT NULL,
  `usertrade_account` varchar(48) DEFAULT NULL,
  `usertrade_optype` tinyint(4) DEFAULT NULL,
  `usertrade_gametype` mediumint(9) DEFAULT NULL,
  `usertrade_point` int(11) DEFAULT NULL,
  `usertrade_netbank` tinyint(4) DEFAULT NULL,
  `usertrade_clientip` bigint(20) DEFAULT NULL,
  `usertrade_hash` char(32) DEFAULT NULL
) ENGINE=BRIGHTHOUSE DEFAULT CHARSET=utf8;