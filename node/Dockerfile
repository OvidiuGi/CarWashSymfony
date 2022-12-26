FROM node:18.12

WORKDIR /var/www/project

COPY ./frontend .

RUN chmod 777 /var/www/project/node_modules
RUN chmod -R 777 /var/www/project/node_modules/*

CMD [ "npm", "start" ]