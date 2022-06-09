<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Send Email with Attachments</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="/css/emailsend.css" />
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
    crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
</head>
<body>
<section class="bg-white">
  <div class="container-fluid">
    <form id="frmEnquiry" action="" method="post" enctype='multipart/form-data'>
      <div id="mail-status"></div>
      <div>
          <input
              type="text" name="userName" id="userName"
              class="demoInputBox" placeholder="Name">
      </div>
      <div>
          <input type="text" name="userEmail" id="userEmail"
              class="demoInputBox" placeholder="Email">
      </div>
      <div>
          <input type="text" name="subject" id="subject"
              class="demoInputBox" placeholder="Subject">
      </div>
      <div>
          <textarea name="content" id="content" class="demoInputBox"
              cols="60" rows="6" placeholder="Content"></textarea>
      </div>
      <div>
          <label>Attachment</label><br /> <input type="file"
              name="attachment[]" class="demoInputBox" multiple="multiple">
      </div>
      <div>
          <input type="submit" value="Send" class="btnAction" />
      </div>
    </form>
    <div id="loader-icon" style="display: none;">
      <img src="/css/LoaderIcon.gif" />
    </div>
  </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="/js/emailsend.js"></script>
</body>

</html>
