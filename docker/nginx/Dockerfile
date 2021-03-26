FROM nginx:latest

RUN apt-get update

# ssl-sertificate
RUN yes "" | openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/nginx-selfsigned.key \
    -out /etc/ssl/certs/nginx-selfsigned.crt

ARG UID
ARG NGINX_PHP_USER
ARG IP

COPY nginx.conf /etc/nginx/nginx.conf
COPY mysite.conf /etc/nginx/conf.d/default.conf
RUN addgroup --gid $UID --system $NGINX_PHP_USER \
  && adduser --uid $UID --system --disabled-login --disabled-password --gid $UID $NGINX_PHP_USER \
  && sed -i -r "s/%REPLACE_USERNAME%/$NGINX_PHP_USER/g" /etc/nginx/nginx.conf \
  && sed -i -r "s/%REPLACE_IP%/$IP/g" /etc/nginx/conf.d/default.conf

CMD ["nginx", "-g", "daemon off;"]