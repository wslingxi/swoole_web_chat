# swoole_web_chat
swoole网页聊天室，基于jwt登录
php安装swoole扩展
命令行切换到项目目录，执行php server.php
网页访问项目client_login.html即可开启聊天

数据表uid_fid
CREATE TABLE `uid_fid` (

  `id` int(11) NOT NULL AUTO_INCREMENT,
  
  `uid` int(11) NOT NULL,
  
  `fd` int(11) NOT NULL,
  
  PRIMARY KEY (`id`),
  
  UNIQUE KEY `uid_fd` (`uid`,`fd`)
  
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;




数据表user

CREATE TABLE `user` (

  `id` int(11) NOT NULL AUTO_INCREMENT,
  
  `name` varchar(60) NOT NULL,
  
  `password` varchar(255) NOT NULL,
  
  PRIMARY KEY (`id`),
  
  UNIQUE KEY `name` (`name`)
  
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


INSERT INTO `user` VALUES ('1', 'Tom', 'e10adc3949ba59abbe56e057f20f883e'), ('2', 'Lucy', 'e10adc3949ba59abbe56e057f20f883e'), ('3', 'Join', 'e10adc3949ba59abbe56e057f20f883e');
