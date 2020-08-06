<h3>Boleto</h3>

<p>
    <strong>Descrição: </strong> <?= $billet->description ?> <br>
    <strong>Código: </strong> <?= $billet->barcode ?> <a style="color:#999;text-decoration:none;cursor:pointer" onclick="navigator.clipboard && (() => { navigator.clipboard.writeText('<?= $billet->barcode ?>'); this.innerHTML = '<span style=color:green>copiado</span>'; setTimeout(()=> {this.innerHTML='&#128458; &nbsp; copiar'}, 1000) })() ">&#128458; &nbsp;copiar</a><br>
    <strong>Expira em:</strong> <?= date('d/m/Y',strtotime($billet->expiration_date)) ?><br>
    <a href="<?= $billet->zoop_url ?>" target="_blank">Acessar boleto</a>
</p>

<p>
    <div id="imopay-billet-iframe-wrap">
        <iframe id="imopay-billet-iframe" src="<?= $billet->zoop_url ?>" frameborder="0" style="width: 100%"></iframe>
    </div>
</p>

<style>

#imopay-billet-iframe-wrap {
  width: 125%;
  padding: 0;
  overflow: hidden;
}

#imopay-billet-iframe {
  width: 100%;
  min-height: 800px;
  border: 0px;
}

#imopay-billet-iframe {
  zoom: 0.75;
  -moz-transform: scale(0.75);
  -moz-transform-origin: 0 0;
  -o-transform: scale(0.75);
  -o-transform-origin: 0 0;
  -webkit-transform: scale(0.75);
  -webkit-transform-origin: 0 0;
}

</style>