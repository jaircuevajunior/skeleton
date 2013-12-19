<div id="top_container">
  <h2>Cadastro de Usuário</h2>
  <p>&nbsp;</p>
</div>

{$CADASTRO_ALERTS}

<div class="button_bar clearfix">
  <button type="button" class="link" href="/adm/usuario/lista/">
    <img height="24" width="24" alt="Voltar para a lista" src="/adm/images/list.png">
    <span>Voltar para a lista</span>
  </button>
  <button type="button" class="link boxradios" href="/adm/usuario/cadastro/">
    <img height="24" width="24" alt="Voltar para a lista" src="/adm/images/create_write.png">
    <span>Novo Registro</span>
  </button>
</div>

<div class="box">

  <form class="validate_form" method="post" id="main_form">

    <fieldset class="label_side top">
      <label>Ativo ?</label>
      <div>
        <input class="uniform" type="checkbox" name="ativo" id="ativo" <?php if ( @$data['table']['ativo'] == '1' ): ?>checked="checked"<?php endif; ?> />
      </div>
    </fieldset>

    <fieldset class="label_side top">
      <label for="required_field">Nome</label>
      <div>
        <input name="nome" type="text" class="" value="<?php echo @$data['table']['nome'] ?>">
      </div>
    </fieldset>

    <fieldset class="label_side top">
      <label for="required_field">E-mail</label>
      <div>
        <input name="email" type="text" class="" value="<?php echo @$data['table']['email'] ?>">
      </div>
    </fieldset>

    <fieldset class="label_side top">
      <label for="required_field">Senha</label>
      <div>
        <input name="senha" type="password">
      </div>
    </fieldset>

    <fieldset class="label_side top">
      <label for="required_field">Avatar</label>
      <div>
        <?php echo @$data['table']['avatar'] ?>
      </div>
    </fieldset>

    {$CADASTRO_SUBMIT}

  </form>

</div>