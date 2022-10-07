<?=$render('header', ['loggedUser' => $loggedUser])?>
<section class="container main">
    <?=$render('sidebar', ['activeMenu' => 'search', 'loggedUser'=>$loggedUser])?>

    <section class="feed mt-10">

    <div class="row">
           
        <div class="column pr-5">
            <h1>Configuracões</h1>
            <div>
                <form action="<?=$base?>/config/save" method="post" enctype="multipart/form-data">
                    <?php if(!empty($flash)):?>
                        <div class="flash"><?php echo $flash;?></div>
                    <?php endif;?>
                    
                    <br>
                    <label for="avatar">
                        Novo Avatar:
                    </label>
                    <br>
                    <br>
                    <input type="file" name="avatar" class='input-file'>
                    <br>
                    <br>
                    <label for="avatar">
                        Nova Capa:
                    </label>
                    <br>
                    <br>
                    <input type="file" name="cover" class='input-file'>
                    <br>
                    <br>
                    <hr>
                    <br>
                    <label for="name">Nome Completo:</label>
                    <br>
                    <input type="text" name='name' value="<?=$user->name?>">
                    <br>
                    <br>
                    <label for="birthdate">Data de Nascimento:</label>
                    <br>
                    <input value='<?=date('d/m/Y', strtotime($user->birthdate))?>' class="input" type="text" name="birthdate" id="birthdate" />
                    <br>
                    <br>                    
                    <label for="email">E-mail:</label>
                    <br>
                    <input type="text" name='email' value="<?=$user->email?>">
                    <br>
                    <br>                    
                    <label for="city">Cidade:</label>
                    <br>
                    <input type="text" name='city' value="<?=$user->city?>">
                    <br>
                    <br>
                    <label for="work">Trabalho:</label>
                    <br>
                    <input type="text" name='work' value="<?=$user->work?>">
                    <br>
                    <br>
                    <hr>
                    <br>
                    <label for="password">Nova Senha:</label>
                    <br>
                    <input type="password" name='password'>
                    <br>
                    <br>
                    <label for="passwordConfirm">Confirmar Nova Senha:</label>
                    <br>
                    <input type="password" name='passwordConfirm'>
                    <br>
                    <br>
                    <input type="submit" value="Salvar" class="button">

                </form>
            </div>
        </div>

    </div> 
    </section>
</section>
<?=$render('footer');?>

<script src="https://unpkg.com/imask"></script>
<script>
    IMask(
        document.getElementById('birthdate'),
        {
            mask:'00/00/0000'
        }
    )
</script>