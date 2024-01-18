<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/CoraApi

namespace ProtocolLive\CoraApi\Enums;

/**
 * @version 2024.01.18.00
 */
enum NotifyStatus:string{
  case Agendado = 'SCHEDULED';
  case Cancelado = 'CANCELED';
  case Enviado = 'SENT';
}