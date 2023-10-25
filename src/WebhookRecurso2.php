<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/CoraApi

namespace ProtocolLive\CoraApi;

/**
 * @version 2023.10.25.00
 * Usado nos webhook recebidos do Cora
 */
enum WebhookRecurso2:string{
  case BoletoPago = 'invoice.paid';
}