<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/CoraApi

namespace ProtocolLive\CoraApi\Enums;

/**
 * @version 2024.01.18.00
 */
enum EmailNotify:string{
  /**
   * Notifica quinze dias antes da data de vencimento.
   */
  case Antes15 = 'NOTIFY_FIFTEEN_DAYS_BEFORE_DUE_DATE';
  /**
   * Notifica dez dias antes da data de vencimento.
   */
  case Antes10 = 'NOTIFY_TEN_DAYS_BEFORE_DUE_DATE';
  /**
   * Notifica sete dias antes da data de vencimento.
   */
  case Antes7 = 'NOTIFY_SEVEN_DAYS_BEFORE_DUE_DATE';
  /**
   * Notifica cinco dias antes da data de vencimento.
   */
  case Antes5 = 'NOTIFY_FIVE_DAYS_BEFORE_DUE_DATE';
  /**
   * Notifica dois dias antes da data de vencimento.
   */
  case Antes2 = 'NOTIFY_TWO_DAYS_BEFORE_DUE_DATE';
  /**
   * Notifica dois dias depois da data de vencimento.
   */
  case Depois2 = 'NOTIFY_TWO_DAYS_AFTER_DUE_DATE';
  /**
   * Notifica cinco dias depois da data de vencimento.
   */
  case Depois5 = 'NOTIFY_FIVE_DAYS_AFTER_DUE_DATE';
  /**
   * Notifica sete dias depois da data de vencimento.
   */
  case Depois7 = 'NOTIFY_SEVEN_DAYS_AFTER_DUE_DATE';
  /**
   * Notifica dez dias depois da data de vencimento.
   */
  case Depois10 = 'NOTIFY_TEN_DAYS_AFTER_DUE_DATE';
  /**
   * Notifica quinze dias depois da data de vencimento.
   */
  case Depois15 = 'NOTIFY_FIFTEEN_DAYS_AFTER_DUE_DATE';
  /**
   * Notifica quando o boleto é pago.
   */
  case QuandoPago = 'NOTIFY_WHEN_PAID';
  /**
   * Notifica no dia do vencimento.
   */
  case NoDia = 'NOTIFY_ON_DUE_DATE';
}