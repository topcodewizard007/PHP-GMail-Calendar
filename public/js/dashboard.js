// Display Snackbar
let contactlist = new Array();
function displaySnackbar(message, type, next) {
  // Get the snackbar DIV
  var snackbar = document.getElementById("snackbar");
  switch (type) {
    case "success":
      snackbar.style.backgroundColor = "#59983b";
      break;
    case "error":
      snackbar.style.backgroundColor = "#dc3545";
      break;

    default:
      break;
  }
  // Add the "show" class to DIV
  snackbar.className = "show";
  snackbar.innerHTML = message;
  // After 3 seconds, remove the show class from DIV
  setTimeout(function () {
    snackbar.className = snackbar.className.replace("show", "");
    next();
  }, 5000);
}
// Send Ajax Request to create new calendar
$(".save-calendar").on("click", function () {
  $(this).addClass("loading");
  var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
    error = 0,
    parameters;

  $(".input-error").removeClass("input-error");

  if (!blank_reg_exp.test($("#calendar_summary").val())) {
    $("#calendar_summary").addClass("input-error");
    error = 1;
  }
  if (!blank_reg_exp.test($("#calendar_description").val())) {
    $("#calendar_description").addClass("input-error");
    error = 1;
  }
  if (error == 1) return false;
  parameters = {
    summary: $("#calendar_summary").val(),
    description: $("#calendar_description").val(),
    operation: $(this).attr("data-operation"),
  };
  $.ajax({
    type: "POST",
    url: "dashboard/create",
    data: { data: parameters },
    dataType: "json",
    success: function (response) {
      console.log("create success", response);
      // close the modal if successfull
      $(this).removeClass("loading");
      $("#createModal").modal("hide");
      displaySnackbar(
        "Calendar was created successfully",
        "success",
        location.reload()
      );
    },
    error: function (response) {
      // close the modal if an error occur then display the messages to the user
      console.log("create error", response);
      $(this).removeClass("loading");
      $("#createModal").modal("hide");
      displaySnackbar(response.responseText, "error", location.reload());
      // alert(response.responseJSON.message);
    },
  });
});
// Send Ajax Request to Delete Event
$(".delete-calendar-btn").on("click", function (e) {
  $(this).addClass("loading");
  // Event details
  var parameters = {
    calendar_id: $(this).attr("data-calendar-id"),
  };
  console.log("parameters", parameters);
  $.ajax({
    type: "POST",
    url: "dashboard/delete",
    data: { data: parameters },
    dataType: "json",
    success: function (response) {
      console.log("success delete", response);
      $(this).removeClass("loading");
      displaySnackbar(
        "Calendar was deleted successfully",
        "success",
        location.reload()
      );
    },
    error: function (response) {
      console.log("failed delete", response);
      $(this).removeClass("loading");
      alert(response.responseJSON.message);
    },
  });
});
// Send Ajax Request to get Contacts
$(".extract_contacts_gmail").on("click", function () {
  $(this).addClass("loading");
  $.ajax({
    type: "GET",
    url: "dashboard/getContacts",
    // data: { data: "" },
    dataType: "json",
    success: function (response) {
      console.log("success extracted", response);
      $(this).removeClass("loading");
      displaySnackbar(
        "Contacts was extracted successfully",
        "success",
        function () {}
      );
      viewEmailList(response);
    },
    error: function (response) {
      console.log("failed delete", response);
      $(this).removeClass("loading");
      alert(response.responseJSON.message);
    },
  });
});
$(".save_contacts_db").on("click", function () {
  if (contactlist.length === 0) {
    $("#emails").append(
      "<span>Please Extract email addresses from GMail</span>"
    );
    return;
  }
  $(this).addClass("loading");
  $.ajax({
    type: "POST",
    url: "contact/insertData",
    data: { data: contactlist },
    dataType: "json",
    success: function (response) {
      console.log("success saved", response);
      $(this).removeClass("loading");
      displaySnackbar(
        "Contacts was saved in DB successfully",
        "success",
        function () {}
      );
      viewEmailList(response);
    },
    error: function (response) {
      console.log("failed saved", response);
      $(this).removeClass("loading");
      alert(response.responseJSON.message);
    },
  });
});

// view email addresses from GMail
function viewEmailList(data) {
  let arrData = data["contacts"];

  // console.log(data["contacts"][0].gd$etag);
  $.each(arrData, function (index) {
    let contact = {
      name: "",
      email: "",
    };
    contact.name =
      arrData[index].gd$name == undefined
        ? arrData[index].gd$email[0].address
        : arrData[index].gd$name.gd$fullName.$t;
    contact.email = arrData[index].gd$email[0].address;
    $("#emails").append(
      "<li>" + contact.name + " -> " + contact.email + "</li>"
    );
    len = contactlist.push(contact);
    console.log("contact count", contactlist);
  });
}
