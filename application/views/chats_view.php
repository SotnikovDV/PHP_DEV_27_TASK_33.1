<?php
// обработка запроса на обновления
if (isset($_GET['act'])) {
  //require_once './application/core/class/gallery.class.php';
  $myid = $_GET['mid'];
  $uid = $_GET['uid'];
  $chats = new Chats($myid);
  $cnt = $chats->getMsgCount($uid);
  echo $cnt;
  return;
}

// обработка добавления сообщения в чат
if (isset($_POST['content'])) {
  $content = $_POST['content'];
  $from = $_POST['from'];
  $to = $_POST['to'];

  $chats = new Chats($user->user_id);
  $result = $chats->addChatMsg($from, $to, $content);

  if (!$result) {
    die('Ошибка добавления сообщения');
  }

  // обновим страницу чата
  header("Location: /chats?uid=" . $to);
}

// обработка поиска контакта
if (isset($_GET['find'])) {
  $find = $_GET['find'];
  // считываем список контактов
  $userList = $user->readUserList($find);
  //var_dump($userList);
  // автоматически выберем первый контакт
  if (!$userList) {
    $uid = $userList[0]['uid'];
  }  
} else {
  // считываем список контактов
  $userList = $user->readUserList('@');
}

// по умолчанию показываем первый чат в списке контактов
if (isset($_GET['uid'])) {
  $uid = $_GET['uid'];
} elseif ($userList) {
  $uid = $userList[0]['id'];
} else {
  $uid = null;
}

// считываем чаты
$chats = new Chats($user->user_id);
if ($uid) {
  $msgList = $chats->readList($uid);
} else {
  $msgList = null;
}

?>
<div class="chat_split">
  <div class="chat_left">
    <div class="chat_header">
      <h2>Список контактов</h2>
    </div>
    <div class="chat_contacts">
      <div class="contact">
        <div style="display: flex; flex-direction: row; align-items: center;">
          <input id="findContact" type="text" placeholder="поиск контактов" name="find" required class="chat-find-input" onclick="findContact();">
          <!-- <a href="onclick: findContact()"> --><img onclick="findContact();" src="images/pers_find.jpg" alt="Avatar" class="contact_img" style="height:30px"><!-- </a> -->
        </div>
        <!-- <div class="new_msg_send"> 
          <button class="new_msg_btn"><img src="/images/send.jpg" alt="send"></button>
        </div> -->
      </div>

      <?php
      if ($userList) {
        // цикл по контактам
        foreach ($userList as $key => $usr) {
          if (!$usr['photo_file']) {
            $userPhoto = '/images/empty_user_photo.png';
          } else {
            $userPhoto = '/images/face/' . $usr['photo_file'];
          }
          if (!$usr['nikname']) {
            $nikName = $usr['login'];
          } else {
            $nikName = $usr['nikname'];
          }
          $name = $usr['name'];
          $userID = $usr['id'];

          if ($usr['id'] == $uid) {
            $userPhotoNow = $userPhoto;
            $nikNameNow = $nikName;
            $nameNow = $name;
            $contactClass = 'contact_now';
            $itsNow = true;
          } else {
            $itsNow = false;
            $contactClass = 'contact_all';
          }

      ?>
          <div class="contact <?= $contactClass ?>">
            <a href="/chats?uid=<?= $userID ?>">
              <img src="<?= $userPhoto ?>" alt="Avatar" class="contact_img" style="width:60px">
              <div><span><?= $nikName ?></span><br><?= $name ?></div>
            </a>
          </div>
      <?php
        }
      }
      ?>


    </div>
  </div>

  <div class="chat_right">
    <div class="chat_header">
      <?php
      if ($uid) {
      ?>
        <img src="<?= $userPhotoNow ?>" alt="Avatar" style="height:60px; margin-right: 20px;">
        <h2><?= $nameNow ?></h2>
      <?php
      } else {
      ?>
        <h2>Сообщения</h2>
      <?php
      }
      ?>
    </div>

    <div class="chat_msg">
      <?php
      $msgCount = 0;
  // цикл по сообщения чата
      if ($msgList) {
        $msgCount = count($msgList);
        foreach ($msgList as $key => $msg) {
      ?>
          <div class="msg msg_<?= $msg['direction'] == 0 ? 'from' : 'to' ?>">
            <div class="msg_body msg_body_<?= $msg['direction'] == 0 ? 'from' : 'to' ?>">
              <p><?= $msg['content'] ?></p>
            </div>
          </div>
      <?php
        }
      }
      ?>
    </div>

    <!-- <div class="chat_bottom"> -->
    <form action="/chats" method="post" class="chat_bottom">
      <div class="new_msg">
        <input id="msgContent" type="text" placeholder="Ваше сообщение" name="content" required class="msg-input"> <!-- class="logon-frm-input" -->
        <input type="hidden" name="from" value="<?= $user->user_id ?>">
        <input type="hidden" name="to" value="<?= $uid ?>">
      </div>
      <div class="new_msg_send">
        <button type="submit" class="new_msg_btn"><img src="/images/send.jpg" alt="send"></button>
      </div>
    </form>
    <!-- </div> -->
  </div>
</div>

<script>
  function findContact() {
    let a = document.querySelector('#findContact');
    let find = a.value;
    location.href = '/chats?find=' + find;
  }

  function reLoad() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
      let x;
      if (this.readyState == 4 && this.status == 200) {
         x = Number(this.responseText);
         if (x > <?=$msgCount?>){
          location.href = '/chats?uid=<?= $uid ?>';  
          //alert('Обновляем');
         };
      }
    }
    xmlhttp.open("GET", "/chats?act=get&mid=<?= $user->user_id ?>&uid=<?= $uid ?>&load", true);
    xmlhttp.send();
    }
  
    findContact.key

  <?php
  if ($uid) {
  ?>
    setInterval(reLoad, 2500);
  <?php
  }
  ?>
</script>