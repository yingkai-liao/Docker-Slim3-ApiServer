FROM debian:jessie

MAINTAINER "yingkai" <yingkai.liao@madhead.com>

# Install Nginx
RUN apt-get update -y && \
    apt-get install -y nginx

# Apply Nginx configuration
ADD config/nginx.conf /etc/nginx/nginx.conf
ADD config/.htpasswd /etc/nginx/.htpasswd
ADD config/nginx_php /etc/nginx/sites-available/nginx_php
RUN ln -s /etc/nginx/sites-available/nginx_php /etc/nginx/sites-enabled/nginx_php && \
    rm /etc/nginx/sites-enabled/default

RUN mkdir -p /data
VOLUME ["/data"]

# PORTS
EXPOSE 80
EXPOSE 443

ENTRYPOINT ["/usr/sbin/nginx"]