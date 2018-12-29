1、安装redis拓展 #要根据phpinfo里的版本选择下载不同的版本扩展
http://windows.php.net/downloads/pecl/releases/redis/         php_redis.dll
http://windows.php.net/downloads/pecl/releases/igbinary/      php_igbinary.dll

php.ini 里添加 #注意一定要按照这个顺序
extension=php_igbinary.dll
extension=php_redis.dll

2、redis常用命令
  https://www.cnblogs.com/kevinws/p/6281395.html
