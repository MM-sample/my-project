ARG NGINX_VERSION

FROM nginx:${NGINX_VERSION}
RUN apt-get update \
    && apt-get install -y apache2-utils

ARG PROJECT_PATH
RUN install -o nginx -g nginx -d ${PROJECT_PATH}