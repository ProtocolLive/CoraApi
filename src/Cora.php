<?php

namespace ProtocolLive\CoraApi;
use Exception;

/**
 * @version 2023.09.14.03
 */
final class Cora{
  private const Url = 'https://matls-clients.api.stage.cora.com.br';
  private string|null $Token = null;

  public function __construct(
    private readonly string $ClientId,
    private readonly string $Certificado,
    private readonly string $Privkey,
    private readonly string $DirLogs = __DIR__
  ){}

  public function Auth():bool{
    $post['grant_type'] = 'client_credentials';
    $post['client_id'] = $this->ClientId;
    $curl = curl_init(self::Url . '/token');
    curl_setopt($curl, CURLOPT_SSLCERT, $this->Certificado);
    curl_setopt($curl, CURLOPT_SSLKEY, $this->Privkey);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_VERBOSE, true);
    curl_setopt($curl, CURLOPT_STDERR, fopen($this->DirLogs . '/CoraApi.log', 'a'));
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      'Content-Type: application/x-www-form-urlencoded'
    ]);
    $return = json_decode(curl_exec($curl), true);
    if(isset($return['error'])):
      file_put_contents(
        DirLogs . '/CoraApi.log',
        'Auth error:' . $return['error']
      );
      return false;
    endif;
    $this->Token = $return['access_token'];
    return true;
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

    $url = self::Url . '/invoices/';
    file_put_contents(
      DirLogs . '/CoraApi.log',
      'Send ' . $url . PHP_EOL . json_encode($post, JSON_PRETTY_PRINT)
    );
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSLCERT, $this->Certificado);
    curl_setopt($curl, CURLOPT_SSLKEY, $this->Privkey);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($curl, CURLOPT_VERBOSE, true);
    curl_setopt($curl, CURLOPT_STDERR, fopen($this->DirLogs . '/CoraApi.log', 'a'));
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Idempotency-Key: ' . Uuid($Idempotency),
      'Authorization: Bearer ' . $this->Token
    ]);
    return json_decode(curl_exec($curl), true);
  }
}