<?php

namespace ProtocolLive\CoraApi;

/**
 * @version 2023.09.15.00
 * @link https://developers.cora.com.br/reference/cria%C3%A7%C3%A3o-de-endpoints#enum-de-recursos
 */
enum WebhookRecurso:string{
  /**
   * Recurso de boletos. Poderá ser utilizado na atualização do status de boletos criados através de API, tornando possível, por exemplo, notificações de pagamento em tempo real
   */
  case Boleto = 'invoice';
  /**
   * Recurso de pagamentos. Este recurso poderá ser utilizado no monitoramento de erros em pagamentos e em atualizações de status em tempo real
   */
  case Pagamento = 'payment';
  /**
   * Recurso de pré-cadastro. Poderá ser utilizado para identificar quais indicações realizadas através da API de pré-cadastro foram concluídas com sucesso, ou seja, quantos clientes indicados efetivamente abriram uma conta Cora
   */
  case Registro = 'register';
  /**
   * Recurso de transferências. Pode ajudar no monitoramento de erros em transferências e em atualizações de status em tempo real
   */
  case Transferencia = 'transfer';
}