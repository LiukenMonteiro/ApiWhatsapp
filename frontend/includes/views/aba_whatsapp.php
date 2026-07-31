<section class="card">
  <h2>Conexões WhatsApp</h2>
  <p style="color:#666;font-size:0.88rem;margin-bottom:20px">
    Gerencie as duas instâncias WhatsApp. Conecte via QR Code.
  </p>

  <div class="whatsapp-grid">
    <?php foreach (['1', '2'] as $wid): ?>
    <div class="wa-card" id="wa-card-<?= $wid ?>">
      <div class="wa-card-header">
        <span class="wa-titulo">📱 WhatsApp <?= $wid ?></span>
        <span class="wa-badge" id="wa-badge-<?= $wid ?>">carregando...</span>
      </div>

      <div class="wa-acoes" id="wa-acoes-<?= $wid ?>">
        <div style="color:#999;font-size:0.85rem">Verificando status...</div>
      </div>

      <div class="wa-qr-area" id="wa-qr-<?= $wid ?>" style="display:none">
        <p class="wa-qr-instrucao">Abra o WhatsApp no celular → Dispositivos conectados → Conectar dispositivo</p>
        <img id="wa-qr-img-<?= $wid ?>" src="" alt="QR Code" class="wa-qr-img">
        <p class="wa-qr-aviso">O QR Code expira em 60 segundos. Esta página atualiza automaticamente.</p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
