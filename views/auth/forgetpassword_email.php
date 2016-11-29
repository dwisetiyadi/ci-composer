<!DOCTYPE html>
<html style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;">
<head>
<meta name="viewport" content="width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Travelnego.com - Forget Password</title>
<link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
</head>
<body bgcolor="#e6e7e7" style="color:#565a5c;background:#f7f7f7;font-size:14px;font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; -webkit-font-smoothing: antialiased; height: 100%; -webkit-text-size-adjust: none; width: 100% !important; margin: 0; padding: 0;">
<table width="95%" style="height:100%;padding:0px;border:none;border-collapse: collapse;" align="center">	
	<tr>
		<td style="padding:20px;text-align:center;padding-bottom:0px;"><?php echo img(array('src'=>base_url().'public/img/logo-for-email2.png', 'width'=>'300')); ?></td>
	</tr>	
	<tr>
		<td style="padding:10px;font-size:14px;">Hi <?php echo $name; ?>,</td>		
	</tr>
	<tr>
		<td style="padding:10px;font-size:14px;">Kami menerima request untuk mereset password anda. Jika anda tidak merasa membuat request tersebut, abaikan email ini. Jika benar, anda dapat mereset password anda dengan link dibawah ini.</td>		
	</tr>
	<tr>
		<td style="padding:20px;text-align:center;">
			<a href="<?php echo base_url()."auth/resetpassword/".$verifikasi_id; ?>" style="font-size:14px;text-decoration:none;background:#ffa200;color:white;text-align:center;padding:15px;width:50%;margin:auto;display:inline-block;border-radius:5px;">Klik disini untuk reset password</a>
		</td>
	</tr>
	<tr>
		<td style="padding:10px;font-size:14px;">Terimakasih, <br/>Travelnego Team</td>		
	</tr>
	<tr>
		<td style="text-align:center;background-color:#125d8c;"><?php echo img(array('src'=>base_url().'public/img/footer-email-new.jpg')); ?></td>
	</tr>
	<tr style="background:#f7f7f7;">
		<td style="color:#636262;font-weight:bold;text-align:center;font-size:12px;padding:10px;">	&copy;<?php echo date('Y'); ?> Travelnego All Rights Reserved</td>
	</tr>
</table>
</body>
</html>