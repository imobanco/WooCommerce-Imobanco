<div id="custom_input">

    <div class="form-row form-row-wide">
        <label for="holder_name">Nome impresso no cartão</label>
        <input type="text" class="holder_name" name="holder_name">
    </div>
    <div class="form-row form-row-wide">
        <label for="card_number">Número do cartão (somente dígitos)</label>
        <input type="text" class="" name="card_number" id="card_number" placeholder="" value="" onkeyup="this.value=this.value.replace(/\D/ig, '')">
    </div>
    <div class="form-row form-row-first">
        <label for="expiration_month">Mês validade</label>
        <input type="number" class="expiration_month" name="expiration_month" placeholder="Ex: 04">
    </div>
    <div class="form-row form-row-last">
        <label for="expiration_year">Ano validade</label>
        <input type="number" class="expiration_year" name="expiration_year" placeholder="Ex: 2021" min="2020">
    </div>
    <div class="form-row form-row-wide">
        <label for="security_code" class="security_code">Código de segurança (CVV)</label>
        <input type="number" class="security_code" name="security_code">
    </div>

</div>