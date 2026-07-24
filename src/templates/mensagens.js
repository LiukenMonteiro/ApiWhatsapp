// ============================================================
// mensagens.js — Templates de mensagem centralizados
// ============================================================
// Todas as mensagens ficam aqui, em um só lugar.
// Para adicionar um novo tipo de mensagem no futuro,
// basta adicionar uma nova entrada neste objeto.
// Nenhuma outra parte do código precisa ser alterada.
// ============================================================

const templates = {

  // Mensagem enviada quando o exame do paciente fica pronto
  exame_pronto: (paciente) => {
    const primeiroNome = paciente.nome_completo.split(' ')[0];
    return `Ola, ${primeiroNome}! Seus exames estao prontos e ja podem ser retirados no laboratorio. Qualquer duvida, estamos a disposicao!`;
  },

  // Mensagem de pesquisa de satisfacao apos o atendimento
  pesquisa_satisfacao: (paciente) => {
    const primeiroNome = paciente.nome_completo.split(' ')[0];
    return `Ola, ${primeiroNome}! Gostaríamos de saber sua opiniao sobre nosso atendimento.\n\nNos de uma nota de 1 a 5:\n1 - Muito ruim\n2 - Ruim\n3 - Regular\n4 - Bom\n5 - Excelente\n\nObrigado!`;
  },

  // FUTURO: adicione novos templates aqui
  // resultado_alterado: (paciente) => { ... },
  // lembrete_jejum: (paciente) => { ... },
};

module.exports = templates;
