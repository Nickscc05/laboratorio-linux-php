# LABORATÓRIO: Publicar uma Aplicação PHP em uma VM 
Essa é a documentação do exercício prático de infraestrutura Linux. Foi proposto como um desafio no primeiro mês que comecei como treinee.

## Objetivo 
O objetivo proposto era subir uma máquina virtual Linux, preparar um ambiente web, disponibilizar uma aplicação PHP escrita do zero e confirmar que a página esteja acessível no navegador. Ao decorrer do projeto foi praticado os conceitos de sistema operacional, instalação de serviços, as permissões, processamento de PHP e as requisições HTTP. 

## Ambiente utilizado 
| Item | Detalhe |
|---|---|
| Sistema operacional | Rocky Linux 9.8 (Blue Onyx) |
| Web server | Apache (httpd) 2.4.62 |
| Processador PHP | PHP-FPM |
| Firewall | firewalld |

## O que foi feito 
1. O primeiro passo realizado foi a **Conexão da VM através do SSH**, fazendo a verificação das suas configurações também através dos comandos abaixo

| Comando | Detalhe |
|---|---|
| cat /etc/os-release | Utilizado para confirmar se os pacotes e comandos usados seriam compativeis na nossa máquina |
| free -h | Utilizado para passar informações sobre a memória RAM do sistema |
| df -h | Utilizado para mostar o espaço em disco de cada partição montada |

![alt text](image-1.png)

2. O segundo passo foi realizar a instalação do Apache e do PHP através do comando **dnf install httpd php -y**, incluindo o PHP-FPM como um processador do PHP.

3. O terceiro passo foi fazer a ativação dos serviços (httpd e php-fpm) com os comandos abaixo, fazendo com que o **httpd e php-fpm** iniciem junto com o sistema.

| Comando | Detalhe |
|---|---|
| systemctl start | Utilizado para iniciar um serviço imediatamente |
| systemctl enable | Utilizado para ativar a função que fará o serviço iniciar automaticamente |

![alt text](<Captura de tela 2026-09-08 153021.png>)

O teste foi feito após a ativação e foi visto que realmente passou a ser iniciado automáticamente. 

![alt text](<Captura de tela 2026-09-08 153406.png>)

4. O quarto passo foi realizar a **criação do index.php**, onde deveriamos exibir de forma dinâmica: 
    - O ambiente Servidor, utilizando a função **php_uname()**.
    - O software do Servidor web **$_SERVER['SERVER_SOFTWARE']**.
    - A data e a hora geradas no servidor no momento que for feita a requisição **date()**.

![alt text](<Captura de tela 2026-09-09 135519.png>)

| Comando | Detalhe |
|---|---|
| php_uname() | Função usada para retornar as insformações sobre o sistema operacional onde o PHP está rodando |
| $_SERVER['SERVER_SOFTWARE'] | É uma variável especial do PHP chamada *superglobal*, que devolve informações sobre a requisição e o próprio servidor a chave 'SERVER_SOFTWARE' traz o nome e a versão do software que está servindo a página |
| date() | Utilizado para retornar a data e a hora de acordo com a forma que é informado |

5. O quinto passo foi o ajuste das permissões dos arquivos através dos comandos **chown apache:apache**, **chmod 755** para que o apache possa ler o conteúdo publicado.

| Comando | Detalhe |
|---|---|
| chown apache:apache | chown (*change owner*) muda o dono e o grupo de um arquivo ou pasta, nesse caso foi transferido a posse de /var/www/html para o usuário apache|
| chmod 755 | Define as permissões de leitura, escrita e execução para três grupos: dono, grupo e outros 7 (dono) 5 (grupo) 5 (outros) |

![alt text](image-2.png)

6. O sexto passo foi realizar a **liberação da porta HTTP (80)** no firewall com o comando **firewall-cmd --add-service=http**

| Comando | Detalhe |
|---|---|
| firewall-cmd --add-service=http | Esse comando libera a porta 80 (usada pelo protocolo HTTP) no firewall da VM, usando o perfil de serviço pré configurado chamado http (sem essa liberação mesmo com o Apache rodando corretamente, o firewall iria bloquear por padrão tudo o que não é permitido)|

![alt text](image-3.png)

7. No sétimo passo foi realizado a validação no navegador, confirmando a mensagem inserida e os dados dinâmicos.

## Caminho da Requisição 
**Navegador  →  Porta 80 (firewall)  →  Apache (httpd)  →  PHP-FPM  →  index.php  →  HTML gerado  →  Navegador**

A requisição segue sempre um caminho fixo para termos o resultado esperado, primeiro o navegador deve acessar **http://< IP-da-VM >/index.php**, após isso o Apache vai identificar o arquívo PHP e ira encaminhar a execução para o gerenciador de processos (PHP-FPM), responsável por executar/interpretar o código PHP. O PHP-FPM vai processar o código e gerar o HTML puro como resultado e devolve ao Apache, que por fim vai enviar a resposta ao navegador. 

![alt text](image.png)

## Conceitos Praticados 

Foi escolhido o **Rocky Linux** é uma distribuição da RHEL (Red Hat Enterprise Linux), possui código aberto mas mantém sua compatibilidade com a RHEL, foi instruído pelos gestores e adotei ela para proseeguir com a VM. 

O **Apache** foi escolhido pois é um pouco mais simples e possui bastante documentação o que ajuda caso surjam dúvidas, foi mais simples para realizar o processamento do PHP pois a integração entre o servidor web e o PHP-FPM vem configurada automaticamente ao instalar o pacote php no Rocky.


## Configuração de HTTPS com certificado autoassinado

Foi instruído fazer um teste de configuração de HTTPS no Apache, usando um certificado autoassinado (self-signed).

### Conceito

HTTP transmite dados em texto puro, sem criptografia. HTTPS adiciona uma camada de criptografia (TLS/SSL) sobre o HTTP, exigindo um certificado digital no servidor. Um certificado autoassinado é gerado e "assinado" pela própria máquina, sem depender de uma Autoridade Certificadora (CA) externa — funciona perfeitamente para criptografar o tráfego, mas o navegador exibe um aviso de "conexão não segura", já que não reconhece a autoridade que assinou o certificado. É o padrão utilizado em ambientes de laboratório, teste e desenvolvimento interno.

### O que foi feito

1. **Instalação do módulo SSL do Apache e do OpenSSL**
```bash
   sudo dnf install mod_ssl openssl -y
```
   O suporte a HTTPS não vem por padrão no Apache — é necessário o módulo `mod_ssl`. O `openssl` é a ferramenta usada para gerar o certificado.

2. **Geração da chave privada e do certificado autoassinado**
```bash
   sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
     -keyout /etc/pki/tls/private/app-php.key \
     -out /etc/pki/tls/certs/app-php.crt
```

| Comando | Detalhe |
|---|---|
| -x509 | gera diretamente um certificado autoassinado|
| -nodes | não protege a chave privada com senha (necessário para o Apache iniciar sem intervenção manual) |
| -days 365 | validade de 1 ano |
| -newkey rsa:2048 | gera uma chave nova, usando RSA de 2048 bits |  

   - No campo **Common Name**, foi informado o IP da VM, pois é esse valor que o navegador compara com o endereço acessado

3. **Configuração do Apache para usar o certificado**

   Editado o arquivo `/etc/httpd/conf.d/ssl.conf`, apontando as diretivas para os arquivos gerados:
  - **SSLCertificateFile /etc/pki/tls/certs/app-php.crt**
  - **SSLCertificateKeyFile /etc/pki/tls/private/app-php.key**

   
4. **Liberação da porta HTTPS (443) no firewall**
```bash
   sudo firewall-cmd --permanent --add-service=https  // ele libera exatamente essa porta, sem você precisar especificar o número manualmente.
   sudo firewall-cmd --reload
```

5. **Confirmação do contexto do SELinux**
- O SELinux não trabalha só com permissões tradicionais (dono/grupo/leitura-escrita) — ele adiciona uma camada extra chamada contexto de segurança, que é uma espécie de "etiqueta" atribuída a cada arquivo, dizendo qual tipo de processo pode acessá-lo.

```bash
   sudo restorecon -Rv /etc/pki/tls/
```

6. **Reinício do Apache**
```bash
   sudo systemctl restart httpd
```
   Confirmado nos logs que o serviço passou a escutar em duas portas: `port 443, port 80`.

7. **Validação no navegador**

   Acesso via `https://IP DA MÁQUINA/index.php`, apresentando o aviso esperado de certificado não confiável (por ser autoassinado). Após aceitar o aviso, a página carregou normalmente, agora com a conexão criptografada.

### Resultado

![alt text](<Captura de tela 2026-09-10 161220.png>)

A aplicação passou a responder tanto em HTTP (porta 80) quanto em HTTPS (porta 443), com o tráfego da versão HTTPS sendo criptografado através do certificado autoassinado gerado.