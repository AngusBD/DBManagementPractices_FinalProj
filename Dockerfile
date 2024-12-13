FROM php:8.2-apache

# 安裝 mysqli 擴展
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# 如果需要，安裝其他擴展（例如 pdo_mysql）
# RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql

# 複製網站文件到容器
COPY ./html /var/www/html

# 設置工作目錄
WORKDIR /var/www/html
