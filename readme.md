=== VVerner - Getnet Gateway ===
Contributors: vverner
Tags: woocommerce, getnet, payment
Requires at least: 5.4
Tested up to: 5.8.1
Requires PHP: 7.1
Stable tag: trunk
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.txt


Inclui a Getnet como método de pagamento no WooCommerce. Você precisará das chaves de API da Getnet para que a integração funcione. Consiga-as com seu gerente.

== Description ==

### Inclui a Getnet como método de pagamento no WooCommerce. ###

Este plugin adiciona a Getnet como método de pagamento no WooCommerce. 

Lembre-se que os plugins [WooCommerce](http://wordpress.org/plugins/woocommerce/) e [Brazilian Market on WooCommerce](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) devem estar instalados e ativados para que o plugin funcione corretamente.

Toda a integração foi desenvolvida utilizado e documentação de API disponível na [documentação](https://developers.getnet.com.br/).

O processamento dos dados do cartão, tokenização, cobrança e etc. são realizados pela Getnet.

= Requisitos =

* Chaves de API da Getnet (ver seção "Integração")

* WooCommerce instalado e ativado.

* Brazilian Market on WooCommerce instado e ativado.

= Integração =

A integração com a Getnet pode ser realizada de duas maneiras

* Sandbox: Para testes, você consegue as chaves de API facilmente neste [link](https://developers.getnet.com.br/login)

* Produção: Neste caso você deve solicitar ao seu gerente de conta as chaves de API a serem usadas com sua conta.

= Colaborar =

Você pode contribuir com código-fonte em nossa página no [GitHub](https://github.com/v-verner/woo-getnet).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de FAQ.
* Utilizando o nosso [fórum no Github](https://github.com/v-verner/woo-getnet).
* Criando um tópico no fórum de ajuda do WordPress.

== Installation ==

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.
* Navegue para WooCommerce -> Configurações -> Pagamentos, escolha Getnet e preencha as informações necessárias

== Frequently Asked Questions ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= Quais são os meios de pagamento que o plugin aceita? =

Cartão de Crédito

= O pedido foi pago e ficou com o status de "processando" e não como "concluído", isto esta certo? =

Sim, esta certo e significa que o plugin esta trabalhando como deveria.

Todo gateway de pagamentos no WooCommerce deve mudar o status do pedido para "processando" no momento que é confirmado o pagamento e nunca deve ser alterado sozinho para "concluído", pois o pedido deve ir apenas para o status "concluído" após ele ter sido entregue.

Para produtos baixáveis a configuração padrão do WooCommerce é permitir o acesso apenas quando o pedido tem o status "concluído", entretanto nas configurações do WooCommerce na aba *Produtos* é possível ativar a opção **"Conceder acesso para download do produto após o pagamento"** e assim liberar o download quando o status do pedido esta como "processando".

= Funciona com o Lightbox da Getnet? =

Não, apenas checkout transparente nesta versão

= Posso estornar (chargeback) pedidos pelo wp-admin? =

Por enquanto não, estamos trabalhando nesta funcionalidade

== Changelog ==

= 1.3.1 - 11/11/2021 =

* Correção do método "get_shipping_email" que estava sendo usado incorretamente na criação do pedido

= 1.3 - 01/11/2021 =

* Correção que no caso de o produto ser "virtual" o endereço usado no anti-fraude não estava funcionando
* Testes com as versões mais recentes do WooCommerce e WordPress
* Teste de compatibilidade com PHP 8
* Método de pagamento por boleto bancário!
* IMPORTANTE: Por enquanto, pagamento via boleto não confirmam o pagamento do pedido no WooCommerce. Você deve aprovar os pagamentos manualmente

= 1.2.3 - 06/08/2021 =

* Correção dos preços dos pedidos que estavam indo sem centavos. Obrigado a @cleberasl1234 que identificou o problema

= 1.2.2 - 11/06/2021 =

* Corrigido tipagem dos dados para envio da APIs
* Inicio do desenvolvimento do método de pagamento via débito, falta trabalhar no recebimento das notificações de pagamento antes de liberar para uso

= 1.2.1 - 24/05/2021 =

* Corrigido algumas notices que estavam carregando no log do WP
* Atualizada versões mínimas e testadas do WordPress e WooCommerce

= 1.2 - 14/05/2021 =

* Assets minificados e operacionais
* Integração com o modo "anti-fraude" da api
* Tradução adicionada com sucesso
* Erros do checkout agora são mais descritivos e exibem o retorno da API
* Atualizado o nome do arquivo inicial do plugin para ficar igual ao diretório do WordPress

= 1.1.2 - 11/01/2021 =

* A versão de produção do arquivo .js do carrinho estava com erro, a modificação foi revertida.

= 1.1.1 - 06/01/2020 =

* Em alguns casos os campos de número de cartão e data de validade não tinham formatação.
* A mensagem de erro ao validar o cartão não era exibida corretamente no checkout.

= 1.1 - 11/09/2020 =

* O processo de pagamento esta mais robusto e seguro
* Foram atualizadas chamadas da API
* Traduções atualizadas. Obrigado para Deise Dilkin que contribuiu com o projeto
* Testes iniciais para pagamento via boleto! Acreditamos que na próxima atualização essa função estará disponível
* Testes iniciais para estorno de pagamento pelo painel admin! Acreditamos que na próxima atualização essa função estará disponível

= 1.0.1 - 18/08/2020 =

* Inseridos textos em inglês para futura tradução
* Ativada integração com log nativo do WooCommerce

= 1.0.0 - 28/08/2020 =

* Lançamento do Plugin