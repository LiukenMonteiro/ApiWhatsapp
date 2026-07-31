<?php
/**
 * includes/helpers.php
 *
 * Funções auxiliares de renderização de HTML reutilizadas em múltiplas views.
 *
 * Funções disponíveis:
 *   - renderizarTabela(array $pacientes, string $origem): string
 *       Gera a tabela HTML de pacientes com botões de ação
 *       (enviar exame, pesquisa, editar, excluir).
 *       $origem indica de qual aba a ação foi disparada para redirecionar corretamente.
 *
 *   - renderizarFormulario(string $modo, ?array $p): string
 *       Gera o formulário de cadastro ou edição de paciente.
 *       $modo = 'cadastrar' → formulário em branco
 *       $modo = 'editar'    → formulário pré-preenchido com os dados de $p
 *
 * Incluído em: index.php (antes das views)
 */
function renderizarTabela(array $pacientes, string $origem): string {
  $html = '<table class="tabela-contatos"><thead><tr>
    <th>Nome</th><th>WhatsApp</th><th>Cidade</th><th>Observação</th><th>Ações</th>
  </tr></thead><tbody>';

  foreach ($pacientes as $p) {
    $id           = (int)$p['id'];
    $nome         = htmlspecialchars($p['nome_completo']);
    $nomeEscapado = htmlspecialchars($p['nome_completo'], ENT_QUOTES);

    $html .= "<tr>
      <td>{$nome}</td>
      <td>" . htmlspecialchars($p['whatsapp']) . "</td>
      <td>" . htmlspecialchars($p['cidade'] ?? '—') . "</td>
      <td>" . htmlspecialchars($p['observacao'] ?? '—') . "</td>
      <td class='acoes'>

        <!-- Exame pronto -->
        <form method='POST' class='form-acao'>
          <input type='hidden' name='acao' value='exame_pronto'>
          <input type='hidden' name='contato_id' value='{$id}'>
          <input type='hidden' name='origem_aba' value='{$origem}'>
          <select name='instance_id' class='select-instancia' title='Enviar via WhatsApp'>
            <option value='1'>W1</option>
            <option value='2'>W2</option>
          </select>
          <button type='submit' class='btn btn-verde'
            onclick=\"return confirm('Enviar exame pronto para {$nomeEscapado}?')\">
            ✅ Exame
          </button>
        </form>

        <!-- Pesquisa -->
        <form method='POST' class='form-acao'>
          <input type='hidden' name='acao' value='pesquisa_satisfacao'>
          <input type='hidden' name='contato_id' value='{$id}'>
          <input type='hidden' name='origem_aba' value='{$origem}'>
          <select name='instance_id' class='select-instancia' title='Enviar via WhatsApp'>
            <option value='1'>W1</option>
            <option value='2'>W2</option>
          </select>
          <button type='submit' class='btn btn-amarelo'
            onclick=\"return confirm('Enviar pesquisa para {$nomeEscapado}?')\">
            ⭐ Pesquisa
          </button>
        </form>

        <!-- Editar -->
        <a href='?aba=editar&id={$id}' class='btn btn-azul'>✏️ Editar</a>

        <!-- Excluir -->
        <form method='POST' style='display:inline'>
          <input type='hidden' name='acao' value='excluir'>
          <input type='hidden' name='contato_id' value='{$id}'>
          <input type='hidden' name='origem_aba' value='{$origem}'>
          <button type='submit' class='btn btn-vermelho'
            onclick=\"return confirm('Excluir {$nomeEscapado}? Esta ação não pode ser desfeita.')\">
            🗑️ Excluir
          </button>
        </form>

      </td>
    </tr>";
  }

  $html .= '</tbody></table>';
  return $html;
}

function renderizarFormulario(string $modo, ?array $p): string {
  $isEdicao = ($modo === 'editar' && $p !== null);
  $acao     = $isEdicao ? 'atualizar' : 'cadastrar';
  $action   = $isEdicao ? "?aba=editar&id={$p['id']}" : '?aba=cadastrar';

  $v = fn(string $campo) => htmlspecialchars($p[$campo] ?? '');

  $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS',
          'MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

  $opcoesUF = '<option value="">—</option>';
  foreach ($ufs as $uf) {
    $sel = (($p['estado'] ?? '') === $uf) ? ' selected' : '';
    $opcoesUF .= "<option value='{$uf}'{$sel}>{$uf}</option>";
  }

  return "
  <form method='POST' action='{$action}'>
    <input type='hidden' name='acao' value='{$acao}'>
    " . ($isEdicao ? "<input type='hidden' name='id' value='{$p['id']}'>" : '') . "

    <p class='secao-titulo'>Dados pessoais</p>
    <div class='grade-2'>
      <div class='campo'>
        <label for='nome_completo'>Nome completo *</label>
        <input type='text' id='nome_completo' name='nome_completo'
               placeholder='Ex: Frederico Viana Santos'
               value='{$v('nome_completo')}' required>
      </div>
      <div class='campo'>
        <label for='cpf'>CPF</label>
        <input type='text' id='cpf' name='cpf'
               placeholder='000.000.000-00' maxlength='14'
               value='{$v('cpf')}'>
      </div>
      <div class='campo'>
        <label for='data_nascimento'>Data de nascimento</label>
        <input type='date' id='data_nascimento' name='data_nascimento'
               value='{$v('data_nascimento')}'>
      </div>
    </div>

    <p class='secao-titulo'>Contato</p>
    <div class='grade-2'>
      <div class='campo'>
        <label for='whatsapp'>WhatsApp * (somente números)</label>
        <input type='text' id='whatsapp' name='whatsapp'
               placeholder='5511999990001' required
               value='{$v('whatsapp')}'>
      </div>
      <div class='campo'>
        <label for='telefone_fixo'>Telefone fixo</label>
        <input type='text' id='telefone_fixo' name='telefone_fixo'
               placeholder='551133330000'
               value='{$v('telefone_fixo')}'>
      </div>
      <div class='campo'>
        <label for='email'>E-mail</label>
        <input type='email' id='email' name='email'
               placeholder='paciente@email.com'
               value='{$v('email')}'>
      </div>
    </div>

    <p class='secao-titulo'>Endereço</p>
    <div class='grade-2'>
      <div class='campo campo-largo'>
        <label for='logradouro'>Rua / Avenida</label>
        <input type='text' id='logradouro' name='logradouro'
               placeholder='Rua das Flores'
               value='{$v('logradouro')}'>
      </div>
      <div class='campo campo-curto'>
        <label for='numero'>Número</label>
        <input type='text' id='numero' name='numero' placeholder='123'
               value='{$v('numero')}'>
      </div>
      <div class='campo'>
        <label for='complemento'>Complemento</label>
        <input type='text' id='complemento' name='complemento'
               placeholder='Apto 42, Bloco B'
               value='{$v('complemento')}'>
      </div>
      <div class='campo'>
        <label for='bairro'>Bairro</label>
        <input type='text' id='bairro' name='bairro' placeholder='Centro'
               value='{$v('bairro')}'>
      </div>
      <div class='campo'>
        <label for='cidade'>Cidade</label>
        <input type='text' id='cidade' name='cidade' placeholder='São Paulo'
               value='{$v('cidade')}'>
      </div>
      <div class='campo campo-curto'>
        <label for='estado'>UF</label>
        <select id='estado' name='estado'>{$opcoesUF}</select>
      </div>
    </div>

    <p class='secao-titulo'>Observações</p>
    <div class='campo'>
      <label for='observacao'>Observação / tipo de exame</label>
      <input type='text' id='observacao' name='observacao'
             placeholder='Ex: Hemograma completo'
             value='{$v('observacao')}'>
    </div>

    <div class='rodape-form'>
      <span class='legenda-obrigatorio'>* campos obrigatórios</span>
      <div style='display:flex;gap:10px'>
        " . ($isEdicao ? "<a href='?aba=todos' class='btn btn-secundario'>Cancelar</a>" : '') . "
        <button type='submit' class='btn btn-primario'>
          " . ($isEdicao ? '💾 Salvar Alterações' : 'Cadastrar Paciente') . "
        </button>
      </div>
    </div>
  </form>";
}
