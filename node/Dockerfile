FROM node:18.12

WORKDIR /var/www/project

COPY ./frontend .

RUN npm install -g npm@9.3.1

RUN npm install

CMD [ "npm", "start" ]