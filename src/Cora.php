<?php

namespace ProtocolLive\CoraApi;
use Exception;

/**
 * @version 2023.09.15.11
 */
final class Cora{
  private string|null $Token = null;
  private string|null $Url = null;

  public function __construct(
    private readonly string $ClientId,
    private readonly string $Certificado,
    private readonly string $Privkey,
    private readonly string $DirLogs = __DIR__,
    private readonly bool $Test = false
  ){
    if($Test):
      $this->Url = 'https://matls-clients.api.stage.cora.com.br';
    else:
      $this->Url = 'https://matls-clients.api.cora.com.br';
    endif;
  }

  public function Auth():bool{
    $post['grant_type'] = 'client_credentials';
    $post['client_id'] = $this->ClientId;
    $return = $this->Curl(
      '/token',
      Post: $post,
      JsonPost: false
    );
    if(isset($return['error'])):
      return false;
    endif;
    $this->Token = $return['access_token'];
    return true;
  }

  /**
   * @link https://developers.cora.com.br/reference/cancelamento-de-boleto
   * @throws Exception
   */
  public function BoletoDel(
    string $Id
  ):void{
    if($this->Token === null):
      throw new Exception('Você deve autenticar primeiro');
    endif;
    $this->Curl(
      '/invoices/' . $Id,
      HttpMethod: 'DELETE'
    );
  }

  /**
   * @param string $DataInicial Data início, no formato YYYY-MM-DD. Atenção: O intervalo de tempo da consulta estará relacionado à data de vencimento da fatura
   * @param string $DataFinal Data final, no formato YYYY-MM-DD
   * @param BoletoStatus $Status Descrição dos possíveis estados do boleto
   * @param string $Cpf CPF/CNPJ do destinatário
   * @param int $Pagina Número da página. Possui valor padrão 1
   * @param int $PorPagina Número de itens por página. Possui o valor padrão 20
   * @link https://developers.cora.com.br/reference/consultar-boletos
   * @throws Exception
   */
  public function BoletoGet(
    string $DataInicial = null,
    string $DataFinal = null,
    BoletoStatus $Status = null,
    string $Cpf = null,
    int $Pagina = null,
    int $PorPagina = null
  ):array{
    if($this->Token === null):
      throw new Exception('Você deve autenticar primeiro');
    endif;
    $get = [];
    if($DataInicial !== null):
      $get['start'] = $DataInicial;
    endif;
    if($DataFinal !== null):
      $get['end'] = $DataFinal;
    endif;
    if($Status !== null):
      $get['state'] = $Status->value;
    endif;
    if($Cpf !== null):
      $get['search'] = $Cpf;
    endif;
    if($Pagina !== null):
      $get['page'] = $Pagina;
    endif;
    if($PorPagina !== null):
      $get['perPage'] = $PorPagina;
    endif;

    return $this->Curl(
      '/invoices',
      $get
    );
  }

  /**
   * @param string $Idempotency UUID aleatório para evitar duplicações (https://meajuda.cora.com.br/hc/pt-br/articles/19643328936467-O-que-%C3%A9-idempotency-key-e-pra-qu%C3%AA-ele-serve-)
   * @param string $Codigo Código definido por você, pode ser um id do recurso no seu sistema. Nós iremos retornar esse código sempre que você consultar uma fatura.
   * @param string $Nome Nome do seu cliente (máximo 60 caracteres)
   * @param string $Email E-mail do seu cliente (máximo 60 caracteres)
   * @param string $CpfCnpj CPF/CNPJ do seu cliente
   * @param string $Rua Nome da rua do seu cliente.
   * @param string $Numero Número da rua do seu cliente.
   * @param string $Bairro Bairro do seu cliente.
   * @param string $Cidade Cidade do seu cliente.
   * @param string $Estado Estado do seu cliente no formato AA. Exemplos: SP, AC, GO, RJ.
   * @param string $Complemento Complemento do endereço do seu cliente.
   * @param string $Pais País do seu cliente.
   * @param string $Cep CEP do seu cliente. Formatos possíveis: 00111222 e 00111-222.
   * @param string $Titulo Nome do serviço prestado.
   * @param string $Descricao Descrição do serviço prestado. Máximo de 150 caracteres.
   * @param int $Valor Valor do serviço prestado.
   * @param string $Vencimento
   * @param int $MultaPorcento Valor percentual da multa a ser cobrada. Atenção: Esse parâmetro tem menor prioridade em relação ao parâmetro amount. Portanto, só será considerado caso o valor amount seja nulo. Valores possíveis: de 0 a 100 (com duas casas decimais).
   * @param int $MultaValor Valor em centavos da multa a ser cobrada. Atenção: O parâmetro amount tem precedência sobre o parâmetro rate. Portanto, se for informado os dois parâmetros no objeto fine, apenas o atributo amount será considerado.
   * @param string $MultaData Data a partir da qual será aplicado os juros diários. Essa data é facultativa, caso não informada, o padrão é data de vencimento +1.
   * @param int $Juros Taxa de juros a ser cobrada. Valores possíveis: de 0 a 100 (com duas casas decimais).
   * @param string $Desconto Valor do desconto a ser aplicado. Se o valor for float, será entendido como porcentagem. Apesar do campo ser float, caso o tipo seja inteiro o valor decimal será truncado, mantendo o padrão de envio de valores fixos com centavos. Ex: R$ 20,50 é representado como 2050.
   * @param EmailNotify[] $Notificacao Lista de Strings que representam as regras das notificações, ou seja, quando elas serão disparadas. As possíveis Strings estão detalhadas no Enum de Tipos de Notificação
   * @link https://developers.cora.com.br/reference/emiss%C3%A3o-de-boleto-registrado
   * @throws Exception
   */
  public function BoletoNew(
    string $Idempotency,
    string $Codigo,
    string $Nome,
    string $Email,
    string $CpfCnpj,
    string $Rua,
    string $Numero,
    string $Bairro,
    string $Cidade,
    string $Estado,
    string|null $Complemento = null,
    string $Pais,
    string $Cep,
    string $Titulo,
    string $Descricao,
    int $Valor,
    string $Vencimento,
    int $MultaPorcento = null,
    int $MultaValor = null,
    int $MultaData = null,
    int $Juros = null,
    string $Desconto = null,
    array $Notificacao = null,
    bool $Pix = true
  ):array{
    if($this->Token === null):
      throw new Exception('Você deve autenticar primeiro');
    endif;
    if(strlen($Nome) > 60):
      throw new Exception('O nome deve ter no máximo 60 caracteres');
    endif;
    if(strlen($Email) > 60):
      throw new Exception('O email deve ter no máximo 60 caracteres');
    endif;
    if(strlen($Descricao) > 150):
      throw new Exception('A descrição deve ter no máximo 150 caracteres');
    endif;
    if($MultaPorcento > 100):
      throw new Exception('A porcentagem de multa não pode ser maior que 100%');
    endif;
    if($Juros > 100):
      throw new Exception('A porcentagem de juros não pode ser maior que 100%');
    endif;
    $post = [
      'code' => $Codigo,
      'customer' => [
        'name' => $Nome,
        'email' => $Email,
        'document' => [
          'identity' => $CpfCnpj,
          'type' => strlen($CpfCnpj) === 11 ? 'CPF' : 'CNPJ'
        ],
        'address' => [
          'street' => $Rua,
          'number' => $Numero,
          'district' => $Bairro,
          'city' => $Cidade,
          'state' => $Estado,
          'country' => $Pais,
          'complement' => $Complemento,
          'zip_code' => $Cep
        ]
      ],
      'payment_terms' => [
        'due_date' => $Vencimento,
      ],
      'services' => [[
        'name' => $Titulo,
        'description' => $Descricao,
        'amount' => $Valor
      ]],
      'payment_forms' => ['BANK_SLIP']
    ];
    if($MultaPorcento !== null):
      $post['payment_terms']['fine']['rate'] = $MultaPorcento;
    endif;
    if($MultaValor !== null):
      $post['payment_terms']['fine']['amount'] = $MultaValor;
    endif;
    if($MultaData !== null):
      $post['payment_terms']['fine']['date'] = $MultaData;
    endif;
    if($Juros !== null):
      $post['payment_terms']['interest']['rate'] = $Juros;
    endif;
    if($Desconto !== null):
      $post['payment_terms']['discount']['type'] = is_int($Desconto) ? 'FIXED' : 'PERCENT';
    endif;
    if($Notificacao):
      $post['notifications'] = [
        'channels' => ['EMAIL'],
        'destination' => [
          'name' => $Nome,
          'email' => $Email
        ],
        'rules' => []
      ];
      foreach($Notificacao as $valor):
        $post['notifications']['rules'][] = $valor->value;
      endforeach;
    endif;
    if($Pix):
      $post['payment_forms'][] = 'PIX';
    endif;

    return $this->Curl(
      '/invoices/',
      Post: $post,
      JsonPost: true,
      Idempotency: $Idempotency
    );
  }

  /**
   * @throws Exception
   */
  private function Curl(
    string $Url,
    array $Get = null,
    array $Post = null,
    bool $JsonPost = true,
    string $Idempotency = null,
    string $HttpMethod = null
  ):array|null{
    $Url = $this->Url . $Url;
    $header = [
      'Accept: application/json',
      'Authorization: Bearer ' . $this->Token
    ];
    if($Get !== null):
      $Url .= '?' . http_build_query($Get);
    endif;
    if($Idempotency !== null):
      $header[] = 'Idempotency-Key: ' . $Idempotency;
    endif;
    $curl = curl_init($Url);
    if($Post === null):
      curl_setopt($curl, CURLOPT_POST, false);
    else:
      curl_setopt($curl, CURLOPT_POST, true);
      if($JsonPost):
        $header[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($Post));
      else:
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($Post));
      endif;
    endif;
    if($HttpMethod !== null):
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $HttpMethod);
    endif;
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSLCERT, $this->Certificado);
    curl_setopt($curl, CURLOPT_SSLKEY, $this->Privkey);
    curl_setopt($curl, CURLOPT_VERBOSE, true);
    curl_setopt($curl, CURLOPT_STDERR, fopen($this->DirLogs . '/CoraApi.log', 'a'));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    file_put_contents(
      $this->DirLogs . '/CoraApi.log',
      PHP_EOL . 'Send ' . PHP_EOL . json_encode($Post, JSON_PRETTY_PRINT) . PHP_EOL,
      FILE_APPEND
    );
    $return = json_decode(curl_exec($curl), true);
    file_put_contents(
      $this->DirLogs . '/CoraApi.log',
      PHP_EOL . 'Receive:' . PHP_EOL . json_encode($return, JSON_PRETTY_PRINT) . PHP_EOL,
      FILE_APPEND
    );
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if($code >= 400):
      throw new Exception('HTTP error code ' . $code, $code);
    endif;
    return $return;
  }
}