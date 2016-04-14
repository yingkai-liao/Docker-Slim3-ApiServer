FROM debian:jessie

MAINTAINER "yingkai" <yingkai.liao@madhead.com>

WORKDIR /tmp

RUN apt-get update -y && \
	apt-get install php5-cli -y && \
    apt-get install -y curl git php5-mcrypt php5-gd && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer self-update && \
    apt-get clean

RUN mkdir -p /data/www
VOLUME ["/data"]
WORKDIR /data/www

ENTRYPOINT ["composer"]
CMD ["--help"]
