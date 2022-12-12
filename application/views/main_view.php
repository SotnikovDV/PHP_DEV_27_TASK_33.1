<?php
    // если пользователь вошел
    if ($user->loged) {
        // переходим на главную
        header("Location: /chats");
    }
?>
<div class="tab-dialog">
<h1>Интернет-болталка</h1><hr>
<p>Что бы вступить в сообщество <a href="/logon">войдите</a> или <a href="/logon">зарегистрируйтесь</a></p>

</div>