<form class="nyroModal" method="post" action="/admin/users/subscribeDo">

Тема<br />
<?=in_text('message_subject', $post['message_subject'], array('width' => '900px'));?><br /><br />

Сообщение<br />
<?=in_textarea('message_body', $post['message_body'], array('width' => '900px', 'height' => '350px'));?><br /><br />

<?=in_submit('submit_send', 'Отправить');?>

</form>
