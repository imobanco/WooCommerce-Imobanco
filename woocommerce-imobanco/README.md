# Plugin de Integração Imopay para Woocommerce

Plugin de integração Imopay para Woocommerce

## Dependências

[Brazilian Market on WooCommerce](https://br.wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/)

## Instalação

1 - Baixe o [ZIP](https://github.com/ibrunodev/woocommerce-imobanco-integracao/archive/master.zip) e descompacte em `wp-content/plugins` ou use o GIT

```
$ cd wp-content/plugins
$ git clone https://github.com/ibrunodev/woocommerce-imobanco-integracao.git
```
2 - Acesse o wp-admin, menu plugins e habilite o plugin.

3 - Acesse as opções de pagamento do woocommerce no admin e habilite. Formas de pagamento suportadas: Cartão de Crédito, Boleto.

## Configurações

Constantes definidas em `wp-config.php`:

```
<?php
/** API KEY fornecida pelo Imopay */
define( 'WOO_IMOPAY_API_KEY', '---' );

/** Seller ID fornecido pelo Imopay */
define( 'WOO_IMOPAY_SELLER_ID', '---' );

/** Ambiente */
define( 'WOO_IMOPAY_ENVIRONMENT', 'test' );

```

Outras constantes disponíveis para definição são:

- `WOO_IMOPAY_API_URL`: URL da API do Imopay. Isso ignora a configuração WOO_IMOPAY_ENVIRONMENT
- `WOO_IMOPAY_CREDITCARD_ORDER_DESCRIPTION`: Descrição do pedido na API do Imopay. Padrão:  <Título do Site> - Pedido no cartão de crédito
- `WOO_IMOPAY_BILLET_ORDER_DESCRIPTION`: Descrição do pedido na API do Imopay. Padrão:  <Título do Site> - Pedido no boleto
- `WOO_IMOPAY_EXPIRATION_DATE_INCREMENT`: Incremento de expiração do boleto. Deve ser passado no padrão aceito pela função `strtotime` do PHP. **Valor Padrão: '+ 3 days'**
- `WOO_IMOPAY_LIMIT_DATE_INCREMENT`: Incremento da data limite do boleto. Deve ser passado no padrão aceito pela função `strtotime` do PHP. **Valor Padrão: '+ 3 days'**