FROM alpine:edge
MAINTAINER Anh Ngo <anhnq996@gmail.com>
# Define workdir
WORKDIR /var/www/html
# Define environments
ARG ENV=production
ENV TZ=Asia/Ho_Chi_Minh
# Set timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
# Add testing repositories
RUN echo 'https://dl-cdn.alpinelinux.org/alpine/edge/testing' >> /etc/apk/repositories

# Installing packages
RUN if [ "$ENV" = "production" ] ; \
      then apk add --no-cache curl bash shadow sudo supervisor htop; \
      else apk add --no-cache curl bash shadow sudo supervisor nodejs npm htop; \
    fi ; \
    sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd
# Installing PHP 8
RUN apk add --no-cache php81 php81-common php81-opcache php81-gd php81-pdo php81-pdo_mysql php81-zip php81-phar php81-iconv \
    php81-curl php81-ctype php81-openssl php81-mbstring php81-tokenizer php81-fileinfo php81-json php81-pcntl php81-posix \
    php81-xml php81-xmlreader php81-xmlwriter php81-simplexml php81-dom php81-pecl-redis php81-pecl-swoole ; \
    ln -s /usr/bin/php81 /usr/bin/php
# Configure php service
COPY .docker/php.ini /etc/php81/conf.d/99-wsrc.ini
# Installing composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php ; \
    php composer-setup.php --install-dir=/usr/bin --filename=composer ; \
    rm -rf composer-setup.php
# Configure supervisor & shell script
COPY .docker/start-container /usr/bin/start-container
COPY .docker/supervisord.conf /etc/supervisord.conf
RUN chmod +x /usr/bin/start-container ; \
    mkdir -p /run/supervisor.d
# Install crontab
RUN crontab -l > /tmp/crontab.tmp ; \
    printf "*\t*\t*\t*\t*\tsu suser -c \"/usr/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1\"\n\n" >> /tmp/crontab.tmp ; \
    crontab /tmp/crontab.tmp ; \
    rm -rf /tmp/crontab.tmp
# Copy source code
COPY . .
RUN composer install ; \
    rm -rf .docker .kubernetes ; \
    php artisan storage:link
# Create user/group suser:sgroup
RUN addgroup -g 5000 sgroup ; \
    adduser -S -G sgroup -u 5000 -s /bin/bash suser -h /var/www/html ; \
    printf "suser ALL=(root) NOPASSWD:/bin/chown -R suser.sgroup /var/www/html\n" > /etc/sudoers.d/suser-chown-workspace ; \
    chown -R suser.sgroup /var/www/html /run/supervisor.d
# Expose HTTP port
EXPOSE 8080
# Switch user
USER suser
# Start services
ENTRYPOINT ["start-container"]
