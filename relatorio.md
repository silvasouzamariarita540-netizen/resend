# RELATÓRIO — Integração PHP com a API Resend

## 1. Como foi o desenvolvimento do projeto?

O projeto foi desenvolvido utilizando HTML, CSS e PHP, com o objetivo de criar um formulário de contato integrado à API do Resend. O formulário permite que o visitante informe seu nome, e-mail e mensagem. Após o envio, o sistema encaminha o conteúdo para o e-mail configurado e também envia uma confirmação ao visitante. Os arquivos foram organizados para separar o formulário, o processamento e as configurações. Também foi implementado um registro das tentativas de envio.

## 2. Quais dificuldades foram encontradas e como foram resolvidas?

Uma das principais dificuldades foi configurar corretamente a chave da API e entender as respostas retornadas pelo Resend. Durante os testes, foram simulados erros de autenticação, destinatário não autorizado, dados inválidos e falta de conexão com a internet. A partir desses testes, foram ajustadas as mensagens exibidas ao usuário para cada situação, sem mostrar informações técnicas desnecessárias.

## 3. O que foi aprendido utilizando a API do Resend?

Com a atividade, foi possível aprender como integrar um projeto PHP a um serviço externo para realizar o envio de e-mails. Também foram trabalhados conceitos de requisições HTTP, cURL, autenticação por API, tratamento de erros, registro de logs e proteção de informações de acesso. O projeto mostrou a importância de organizar o código e tratar corretamente as falhas que podem acontecer durante uma comunicação com uma API.