=====================
设置Web服务器
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "/usr/local/apache/htdocs/ztchart/public"
   ServerName .local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "/usr/local/apache/htdocs/ztchart/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>


=====================
运行环境
=====================
Linux: 2.6.13以上内核版本

Apache: 2.2以上版本，需要的模块：mod_rewrite

PHP: 5.3以上版本，需要的额外模块：mbstring, pdo_mysql, pcntl, sysvshm, sysvsem, inotify, memcache, xcache

MySQL: 5.1以上版本，需要支持innodb

Infobright: 4.0以上版本

Memcache: 最新版本

Zend Framwork Library: 1.11.11


=====================
目录说明
=====================
application: 程序目录
bin: 数据导入脚本第一版（作废）
bin2: 数据导入脚本第二版
data: 数据文件目录，存放一些不会变化的公共数据
docs: 文档目录
draft: 草案目录
install: 安装文件目录
library: 运行库文件目录
logs: 日志文件目录，
public: 入口目录，存放入口文件以及静态文件
storage: 存储目录，存放程序运行产生的数据文件
tests: 测试文件目录
upload: 上传目录，存放用户上传的文件

=====================
设置.htaccess 文件
=====================
public目录下的.htaccess文件用来设置重写规则，默认为：

SetEnv APPLICATION_ENV development

RewriteEngine On
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
RewriteRule ^.*$ index.php [NC,L]

第一行设置环境类型，默认为development，表示开发环境，生产环境则设置为staging。


=====================
配置文件
=====================
目录: application/configs/
application.ini: Web程序的配置文件
console.ini: 数据导入脚本的配置文件
navigation.xml: 程序模块配置文件


=====================
添加超级管理员
=====================
把application.ini文件中的第18,19两行注释掉，然后进入系统管理中添加用户，默认就是超级管理员。


=====================
执行数据导入脚本
=====================
数据导入脚本在bin2目录下

realtime.php: 导入实时监控数据
参数 
	--logroot: 日志文件根目录

archive.php:  自动定时导入分类统计数据（每小时）
参数
	--logroot: 日志文件根目录
	--delay:   延迟执行时间
	
backup.php:   按时间段导入分类统计数据
	--logroot: 日志文件根目录
	--start:   开始时间
	

=====================

=====================