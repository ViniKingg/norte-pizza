NORTE PIZZA - SISTEMA ACADÊMICO
================================

O projeto contém as telas de Visão geral, Cadastro de Clientes e Registro de Pedidos.
Foi desenvolvido com PHP, HTML, CSS e MySQL, sem JavaScript.

REQUISITOS
- XAMPP instalado (Apache e MySQL).
- Navegador atualizado.

COMO EXECUTAR
1. Copie a pasta norte_pizza para C:\xampp\htdocs\
2. Abra o XAMPP e inicie Apache e MySQL.
3. Acesse http://localhost/phpmyadmin
4. Importe o arquivo database.sql na aba Importar.
5. Confira usuário e senha do MySQL no arquivo config.php.
6. Acesse http://localhost/norte_pizza/

TELAS
- Visão geral: resumo dos cadastros e pedidos.
- Clientes: cadastro, consulta, edição e exclusão.
- Pedidos: seleção do cliente e de até três produtos. O total é calculado no PHP.

OBSERVAÇÕES
- Os produtos de exemplo são inseridos pelo database.sql.
- Cadastre clientes antes de registrar pedidos.
- Esta é uma versão acadêmica inicial. Não possui login de usuários.
- Para uso real, implemente autenticação, permissões, proteção CSRF,
  política de backup e HTTPS, além de testes adicionais.
