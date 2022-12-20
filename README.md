# symfony-deal-app

to run symfony use 
### symfony serve -d
to create database 
### symfony console doctrine:database:create
to run migration
### symfony console doctrine:migrations:migrate
to load fake data 
### symfony console :
doctrine:fixtures:load

#### Superadmin -> email: superadmin@test.test password: testsuperadmin 
#### Admin -> email: admin@test.test password: testadmin
#### Moderator -> email: mod@test.test password: testmod
#### User -> email: user@test.test password: testuser

Super admin can modify roles and delete users
admin can modify and delete deals and comments
moderator can modify deals and comments
