FROM nginx:1.12

ARG APP_ENV=dev

RUN mkdir /etc/nginx/sites-enabled
RUN rm /etc/nginx/conf.d/default.conf
RUN rm -rf /var/lib/apt/lists/*

ADD nginx.conf /etc/nginx/nginx.conf
ADD sites-enabled.dist/$APP_ENV/default.conf /etc/nginx/sites-enabled/default.conf
COPY certs.dist/$APP_ENV/* /etc/nginx/certs/

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log

CMD ["nginx", "-g", "daemon off;"]
