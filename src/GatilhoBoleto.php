<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/CoraApi

namespace ProtocolLive\CoraApi;

/**
 * @version 2023.09.15.00
 * @link https://developers.cora.com.br/reference/cria%C3%A7%C3%A3o-de-endpoints#enum-de-triggers
 */
enum GatilhoBoleto:string{
  /**
   * Fatura em aberto, não está paga, nem vencida e nem cancelada. Possuem o status OPEN
   */
  case Aberto = 'created';
  /**
   * Fatura atrasada com status LATE (passou da data de vencimento). É possível que faturas mudem de LATE para PAID quando há atraso no recebimento de arquivos pelo banco ou quando a data de vencimento cai em um dia que não há recebimento de arquivos do banco. É possível também que faturas mudem de LATE para CANCELED.
   */
  case Atrasado = 'overdue';
  /**
   * Fatura cancelada, possui status CANCELED
   */
  case Cancelado = 'canceled';
  /**
   * Fatura paga, possui status PAID
   */
  case Pago = 'paid';
  /**
   * Status intermediário da fatura quando ela está sendo criada. As faturas ficam no status DRAFT por um tempo muito pequeno
   */
  case Rascunho = 'drafted';
  case Todos = '*';
}