<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/CoraApi

namespace ProtocolLive\CoraApi\Enums;

/**
 * @version 2024.01.18.00
 * Usado nos webhook recebidos do Cora
 */
enum WebhookRecurso2:string{
  case BoletoPago = 'invoice.paid';
}