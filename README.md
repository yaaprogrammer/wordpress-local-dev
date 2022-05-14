# wordpress-local-dev

已经上线的Wordpress网站本地开发方案。使用`Docker`搭建LNMP环境，模拟线上的域名，站点目录及数据库。

1. 域名配置

    [mkcert](https://github.com/FiloSottile/mkcert)快速生成证书

    ```bash
    mkcert -install  #信任CA
    mkcert example.com #生成证书
    ```

    将生成的两个`example.com.pem`和`example.com-key.pem`放在`./config/nginx/ssl`下，修改`./config/nginx/sites-available/www.conf`配置文件中出现的域名

    注: 本nginx配置为[nginxconfig.io](https://nginxconfig.io)生成后简化所得。

    证书路径配置:

    ```conf
    # SSL
    ssl_certificate     /etc/nginx/ssl/example.com.pem;
    ssl_certificate_key /etc/nginx/ssl/example.com-key.pem;
    ```

    监听443端口的`server_name`配置( http重定向和子域名重定向视具体情况修改和开启 ):

    ```conf
    server_name         example.com;
    ```

    修改root为线上的Wordpress站点对应目录

    ```conf
    root                /www/wwwroot/example.com
    ```

    将php文件转发给php容器

    ```conf
    location ~ \.php$ {
        fastcgi_pass php_cgi_container:9000;
        include      nginxconfig.io/php_fastcgi.conf;
    }
    ```

2. 修改docker挂载目录

    修改`docker-compose.yml`里`web`和`cgi`两个service中的volumes

    ```yaml
    volumes:
      - "./www:/www/wwwroot/example.com"
    ```

    将`/www/wwwroot/example.com`改为Wordpress站点对应的目录

3. [SwitchHosts](https://github.com/oldj/SwitchHosts)管理Hosts文件

   添加新的Hosts文件，写入内容并激活

   ```hosts
   127.0.0.1    example.com
   ```

4. 导出站点`wordpress`数据库为sql文件，放在`./sql`文件夹中，构建`mysql`镜像时会自动导入

    配置`docker-compose.yml`中mysql的root密码。

    ```yaml
    environment:
      - MYSQL_ROOT_PASSWORD=admin
    ```

5. 为PHP镜像安装扩展，参考PHP官方说明

    [how-to-install-more-php-extensions](https://github.com/docker-library/docs/tree/master/php#how-to-install-more-php-extensions)

    我采用的方案是[docker-php-extension-installer](https://github.com/mlocati/docker-php-extension-installer)，已经安装`mysqli`和`xdebug`。 添加更多扩展请在`./docker/php/Dockerfile`中最后一行加入，用空格隔开。

6. 导入Wordpress站点文件

    `./www`目录即为站点的根目录。

    推荐使用Git对线上站点进行版本控制。`.gitignore.example`是线上站点的`.gitingore`模板，根据情况修改。

7. 配置`wp-config.php`

    复制一份站点的`wp-config.php`文件到本地。
    将`DB_host`修改为`mysql_db_container`。

    ```php
    define( 'DB_HOST', "mysql_db_container" );
    ```

    访问`test.php`即可测试mysql连接

8. `docker-compose up -d`
