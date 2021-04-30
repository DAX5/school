Sistema de agenda escolar


Instalação:

Obs.: Certifique-se de ter o MySQL 8, Composer e o PHP 7.3 ou superior instalados.

1- Renomeio o arquivo .env.example para .env e acrescente as informações do seu banco de dados.
2- Abra o terminal de sua preferência.
3- Execute o comando: composer install
4- Execute o comando: php artisan migrate
5- Execute o comando: php artisan passport:install
6- Execute o comando: php artisan db:seed --class=PermissionTableSeeder
7- Execute o comando: php artisan db:seed --class=RoleTableSeeder
8- Execute o comando: php artisan db:seed --class=TurmaTableSeeder
9- Execute o comando: php artisan db:seed --class=CreateAdminUserSeeder

Irá gerar o usuário Admin com as credenciais:
Email: admin@email.com
Senha: 12345678
