=== VVerner - Getnet Gateway ===
Contributors: vverner
Tags: woocommerce, getnet, payment
Requires at least: 5.4
Tested up to: 5.4
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

= 1.0.0 - 28/08/2020

* Lançamento do Plugin