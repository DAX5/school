Sistema de agenda escolar


Instalação:

Obs.: Certifique-se de ter o MySQL 8, Composer e o PHP 7.3 ou superior instalados.

1- Renomeio o arquivo .env.example para .env e acrescente as informações do seu banco de dados.<br>
2- Abra o terminal de sua preferência.<br>
3- Execute o comando: composer install<br>
4- Execute o comando: php artisan migrate<br>
5- Execute o comando: php artisan passport:install<br>
6- Execute o comando: php artisan db:seed --class=PermissionTableSeeder<br>
7- Execute o comando: php artisan db:seed --class=RoleTableSeeder<br>
8- Execute o comando: php artisan db:seed --class=TurmaTableSeeder<br>
9- Execute o comando: php artisan db:seed --class=CreateAdminUserSeeder<br>

Irá gerar o usuário Admin com as credenciais:<br>
Email: admin@email.com<br>
Senha: 12345678<br>
