# Installing

Run build bash: `./build.sh`  
See credentials in `.env` file.  

Install NodeJs:

`curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -`  
`sudo apt-get install -y nodejs`

Install Sass:

`sudo gem install sass --no-user-install`

Install Gulp:
`npm install gulp-cli -g`  
`npm install gulp -D`  
`touch gulpfile.js`  
`gulp --help`

Run command `gulp`

Run in docker: `docker-compose up -d`

Run command `bash deploy/install.sh`

# Updating
Run command `bash deploy/update.sh`

# Load fixtures 
Run command `docker-compose exec php bash -c "php app/console doctrine:fixtures:load --no-interaction"`
  
Test login with TENANT role:
Login: peterparker@test.com
Pass: qweASD123
  
Test login with MANAGER role:
Login: tonystark@test.com
Pass: qweASD123
  
Test login with LANDLORD role:
Login: johndoe@test.com
Pass: qweASD123

Test login with ADMIN role:
Login: gatsby205@gmail.com
Pass: ErentP@y2015!
