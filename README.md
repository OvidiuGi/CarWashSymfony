# CarWashSymfony Web Application
## Installation

* Download Docker Desktop for Windows or Mac

* Open the repository in your favorite IDE
* Open a terminal and run the following commands:
    * `docker-compose up -d --build`
    * `docker-compose exec php81-service composer install`
    * `docker-compose exec php81-service php bin/console doctrine:migrations:migrate`

* After you build the containers, you don't need to build them again. Use the commands below.

* Commands for starting/closing the docker containers, after build.
    * `docker-compose up -d`
    * `docker-compose down`

* Verify if it works. Open your browser and go to `http://localhost:8080/`