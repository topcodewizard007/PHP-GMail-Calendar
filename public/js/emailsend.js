$(document).ready(function (e) {
  $("#frmEnquiry").on("submit", function (e) {
    e.preventDefault();
    $("#loader-icon").show();
    var valid;
    valid = validateContact();
    if (valid) {
      $.ajax({
        url: "/emailsend/sendEmailWithAttach",
        type: "POST",
        data: { data: new FormData(this) },
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
          $("#mail-status").html(data);
          $("#loader-icon").hide();
        },
        error: function () {},
      });
    }
  });

  function validateContact() {
    var valid = true;
    $(".demoInputBox").css("background-color", "");
    $(".info").html("");
    $("#userName").removeClass("invalid");
    $("#userEmail").removeClass("invalid");
    $("#subject").removeClass("invalid");
    $("#content").removeClass("invalid");

    if (!$("#userName").val()) {
      $("#userName").addClass("invalid");
      $("#userName").attr("title", "Required");
      valid = false;
    }
    if (!$("#userEmail").val()) {
      $("#userEmail").addClass("invalid");
      $("#userEmail").attr("title", "Required");
      valid = false;
    }
    if (
      !$("#userEmail")
        .val()
        .match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)
    ) {
      $("#userEmail").addClass("invalid");
      $("#userEmail").attr("title", "Invalid Email");
      valid = false;
    }
    if (!$("#subject").val()) {
      $("#subject").addClass("invalid");
      $("#subject").attr("title", "Required");
      valid = false;
    }
    if (!$("#content").val()) {
      $("#content").addClass("invalid");
      $("#content").attr("title", "Required");
      valid = false;
    }

    return valid;
  }
});
