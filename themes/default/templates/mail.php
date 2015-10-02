<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="<?php echo $lang ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  
  <title><?php echo SERVICE ?></title>
  <link rel="stylesheet" type="text/css" href="<?php echo $domain ?>/themes/default/css/mail.css">
</head>
<body style="margin:0; padding:0;" bgcolor="#F0F0F0" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0">
  <tr>
    <td align="center" valign="top" bgcolor="#F0F0F0" style="background-color: #F0F0F0;">

      <br>

      <table border="0" width="600" cellpadding="0" cellspacing="0" class="container">
        <tr>
          <td class="container-padding header" align="left">
            <a href="<?php echo $domain ?>" title="<?php echo SERVICE ?>"><img src="<?php echo $domain ?>/themes/default/img/x3cms.png" alt="X3 CMS"></a>
          </td>
        </tr>
        <tr>
          <td class="container-padding content" align="left">
          
            <br>
            
            <div class="title"><?php echo $subject ?></div>
            <br>
            
            <div class="body-text">
              <?php echo $sub_subject ?>
              <br><br>
            </div>
            
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
            
            <strong><?php echo SERVICE ?></strong><br>
            
            <a href="mailto:<?php echo MAIL ?>"><?php echo MAIL ?></a><br>
            <a href="<?php echo $domain ?>"><?php echo $domain ?></a><br>
            <br><br>

          </td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>
</body>
</html>
