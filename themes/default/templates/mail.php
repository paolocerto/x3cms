<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="<?php echo $lang ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">

    <title><?php echo $site->title ?></title>

    <style>
<?php
echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/themes/default/css/mail.css');
?>
    </style>

</head>
<body style="margin:0; padding:0;" bgcolor="#0ea5e9" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#0ea5e9">
  <tr>
    <td align="center" valign="top" bgcolor="#0ea5e9" style="background-color: #0ea5e9;">

      <br>

      <table border="0" width="600" cellpadding="0" cellspacing="0" class="container">
        <tr bgcolor="#0ea5e9" style="background-color: #0ea5e9;">
          <td class="container-padding header" align="left">
            <a href="<?php echo $site->domain ?>" title="<?php echo $site->title ?>"><img src="<?php echo $site->domain ?>/themes/default/img/chebeo_long.png" alt="<?php echo $site->title ?>"></a>
          </td>
        </tr>
        <tr>
          <td class="container-padding content" align="left">

            <br>

            <div class="title"><?php echo $subject ?></div>
            <br>

            <div class="hr">&nbsp;</div>

            <div class="body-text">
<?php
// check html
if (substr($message, 0, 1) == '<')
{
	echo $message;
}
else
{
	echo '<p>'.$message.'</p>';
}
?>
            <br><br>
            </div>

            <br>
          </td>
        </tr>
        <tr>
          <td class="container-padding footer-text" align="left">
            <br><br>

            <strong><?php echo $site->title ?></strong><br>

            <a href="mailto:<?php echo $site->replyto ?>"><?php echo $site->replyto ?></a><br>
            <a href="<?php echo $site->domain ?>"><?php echo $site->domain ?></a><br>
            <br><br>

          </td>
        </tr>
      </table>

    </td>
  </tr>
</table>
</body>
</html>
