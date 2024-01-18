<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/CoraApi

namespace ProtocolLive\CoraApi\Enums;

/**
 * @version 2024.01.18.00
 */
enum BoletoStatus:string{
  /**
   * Boletos registrados, mas ainda não pagos
   */
  case Aberto = 'OPEN';
  /**
   * Boletos com pagamento em atraso, ou seja, após a data de vencimento
   */
  case Atrasado = 'LATE';
  /**
   * Boletos cancelados
   */
  case Cancelado = 'CANCELLED';
  /**
   * Boletos iniciados, aguardando a atualização dos próximos passos do fluxo
   */
  case Iniciado = 'INITIATED';
  /**
   * Boletos em processo de pagamento
   */
  case Pagando = 'IN_PAYMENT';
  /**
   * Boletos que foram pagos com sucesso
   */
  case Pago = 'PAID';
  /**
   * Boletos em rascunho, um estado intermediário entre criação e registro
   */
  case Rascunho = 'DRAFT';
  /**
   * Boletos criados com recorrência que nos quais o usuário não deu andamento à criação da cobrança
   */
  case RascunhoRecorrente = 'RECURRENCE_DRAFT';
}