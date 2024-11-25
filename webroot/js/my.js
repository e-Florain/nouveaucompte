// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
    'use strict'
  
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')
  
    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
  
        form.classList.add('was-validated')
      }, false)
    })
  })()

  function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }
  
  function isPhone(phone) {
    if (phone == "") {
      console.log("test");
      return false;
    }
    var regex=/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
    return regex.test(phone);
  
  }
  
  function isIBAN(iban) {
    var regex = /^FR\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[A-Z0-9]\s*[\d]\s*[\d]\s*$/;
    return regex.test(iban);
  }
  
  function waiting() {
    console.log("waiting");
    $('.container').fadeTo(500, 0.2);
    $('.spinner-border').removeClass('invisible');
    //$('.spinner-border').addClass('visible');
  }
  
  $( "#email" ).change(function() {
    var email = $("#email").val();
    if (!isEmail(email)) {
      console.log("not email");
      $("#email").removeClass("is-valid");
      $("#email").addClass("is-invalid");
    } else {
      $("#email").removeClass("is-invalid");
      $("#email").addClass("is-valid");
    }
  });
  
  $( "#iban" ).change(function() {
    var iban = $("#iban").val();
    if (!isIBAN(iban)) {
      console.log("not iban");
      $("#iban").removeClass("is-valid");
      $("#iban").addClass("is-invalid");
      $("#iban-feedback").html('IBAN non valide');
    } else {
      $("#iban").removeClass("is-invalid");
      $("#iban").addClass("is-valid");
    }
  });
  

  $( "#phone" ).change(function() {
    var phone = $("#phone").val();
    if (!isPhone(phone)) {
      $("#phone").removeClass("is-valid");
      $("#phone").addClass("is-invalid");
    } else {
      $("#phone").removeClass("is-invalid");
      $("#phone").addClass("is-valid");
    }
  });
  
  $( "#montant" ).change(function() {
    var montant = $("#montant").val();
    console.log("change montant "+montant+" "+parseInt(montant));
    //if (!Number.isInteger(montant)) {
    if (montant == parseInt(montant, 10)) {
      console.log("valid");
      $("#montant").removeClass("is-invalid");
      $("#montant").addClass("is-valid");
    } else {
      console.log("invalid");
      $("#montant").removeClass("is-valid");
      $("#montant").addClass("is-invalid");
      $("#montant-feedback").html('Nombre entier requis');
      return ;
    }
    if (parseInt(montant) < 20) {
      $("#montant").removeClass("is-valid");
      $("#montant").addClass("is-invalid");
      $("#montant-feedback").html('Le montant doit être au minimum de 20 florains');
    } else {
      $("#montant").removeClass("is-invalid");
      $("#montant").addClass("is-valid");
    }
  });
  
  /* Adhesion montant mensuel */
  $( "#montantmensuel" ).change(function() {
    var montant = $("#montantmensuel").val();
    console.log("adhesion montantmensuel "+montant+" "+parseInt(montant));
    if (montant == parseInt(montant, 10)) {
      if (parseInt(montant) >= 6) {
        console.log("valid");
        $("#montantmensuel").removeClass("is-invalid");
        $("#montantmensuel").addClass("is-valid");
      } else {
        console.log("invalid");
        $("#montantmensuel-feedback").html('Montant trop faible');
        $("#montantmensuel").removeClass("is-valid");
        $("#montantmensuel").addClass("is-invalid");
        return ;
      }
    } else {
      console.log("invalid");
      $("#montantmensuel-feedback").html('Nombre entier requis');
      $("#montantmensuel").removeClass("is-valid");
      $("#montantmensuel").addClass("is-invalid");
      return ;
    }
  });
  
  /* Adhesion montant annuel */
  $( "#montantannuel" ).change(function() {
    var montant = $("#montantannuel").val();
    console.log("adhesion montantannuel "+montant+" "+parseInt(montant));
    //if (!Number.isInteger(montant)) {
    if (montant == parseInt(montant, 10)) {
      if (parseInt(montant) > 5) {
        console.log("valid");
        $("#montantannuel").removeClass("is-invalid");
        $("#montantannuel").addClass("is-valid");
      } else {
        console.log("invalid");
        $("#montantannuel-feedback").html('Montant trop faible');
        $("#montantannuel").addClass("is-invalid");
        return ;
      }
    } else {
      console.log("invalid");
      $("#montantannuel-feedback").html('Nombre entier requis');
      $("#montantannuel").addClass("is-invalid");
      return ;
    }
  });
  
  $('input[type=radio][name=nbflorains]').change(function() {
      var radiostr = $('input[name=nbflorains]:checked').val();
      if (radiostr == "other") {
        console.log(radiostr);
        //$('#montant').prop('type') == 'text';
        $("#montant").prop("type", "text");
        $("#divmontant").show();
        $("#montant").prop('required',true);
      } else {
        $("#montant").prop("type", "hidden");
        $("#divmontant").hide();
        $("#montant").prop('required',false);
      }
  });
  
  $('input[type=radio][name=nbeurosadhmensuel]').change(function() {
    var radiostr = $('input[name=nbeurosadhmensuel]:checked').val();
    if (radiostr == "other") {
      console.log(radiostr);
      //$('#montant').prop('type') == 'text';
      $("#montantmensuel").prop("type", "text");
      $("#divmontantmensuel").show();
      $("#montantmensuel").prop('required',true);
    } else {
      $("#montantmensuel").prop("type", "hidden");
      $("#divmontantmensuel").hide();
      $("#montantmensuel").prop('required',false);
    }
  });
  
  $('input[type=radio][name=nbeurosadhannuel]').change(function() {
    var radiostr = $('input[name=nbeurosadhannuel]:checked').val();
    if (radiostr == "other") {
      console.log(radiostr);
      //$('#montant').prop('type') == 'text';
      $("#montantannuel").prop("type", "text");
      $("#divmontantannuel").show();
      $("#montantannuel").prop('required',true);
    } else {
      $("#montantannuel").prop("type", "hidden");
      $("#divmontantannuel").hide();
      $("#montantannuel").prop('required',false);
    }
  });
  
  $( "#adhchoice" ).change(function() {
    var choice = $("#adhchoice").val();
    console.log("adhchoice "+choice);
    if (choice == "annuel") {
      $(".nbeurosadhmensuel").prop('disabled', 'disabled');
      $("#montantmensuel").prop('disabled', true);
      $(".adhmensuel").fadeTo(500, 0.2);
      $(".adhannuel").fadeTo(500, 1.0);
      $(".nbeurosadhannuel").prop('disabled', false);
      $("#montantannuel").prop('disabled', false);
      $("#adhchoice").removeClass("is-invalid");
    }
    if (choice == "mensuel") {
      $(".nbeurosadhannuel").prop('disabled', 'disabled');
      $("#montantannuel").prop('disabled', true);
      $(".adhannuel").fadeTo(500, 0.2);
      $(".adhmensuel").fadeTo(500, 1.0);
      $(".nbeurosadhmensuel").prop('disabled', false);
      $("#montantmensuel").prop('disabled', false);
      $("#adhchoice").removeClass("is-invalid");
    }
  });
  
  $( "#adhchoicemoncompte" ).change(function() {
    var choice = $("#adhchoicemoncompte").val();
    var montantadh = $("#montantadh").val();
    console.log("montantadh "+montantadh);
    console.log("adhchoicemoncompte "+choice);
    if (choice == "annuel") {
      if (montantadh == parseInt(montantadh, 10)) {
        if (parseInt(montantadh) < 6) {
          $("#montantadh").addClass("is-invalid");
          $("#liveAlertAdh").html('<div class="alert alert-primary" role="alert">Les cotisations des adhérent·e·s sont la principale ressource et la garantie de l’indépendance de notre association !</div>');
        }
        else if (parseInt(montantadh) < 24) {
          $("#montantadh").removeClass("is-invalid");
          $("#liveAlertAdh").html('<div class="alert alert-primary" role="alert">Les cotisations des adhérent·e·s sont la principale ressource et la garantie de l’indépendance de notre association !</div>');
        } else {
          $("#liveAlertAdh").html('');
        }
      }
    }
    if (choice == "mensuel") {
      if (montantadh == parseInt(montantadh, 10)) {
        if (parseInt(montantadh) < 6) {
          $("#montantadh").addClass("is-invalid");
        } else {
          $("#montantadh").removeClass("is-invalid");
        }
      }
    }
  });

  $( "#montantadh" ).change(function() {
    var montantadh = $("#montantadh").val();
    console.log("adhesion  "+montantadh+" "+parseInt(montantadh));
    var choice = $("#adhchoicemoncompte").val();
    console.log("adhchoicemoncompte "+choice);
    if (choice == "annuel") {
      if (montantadh == parseInt(montantadh, 10)) {
        if (parseInt(montantadh) < 6) {
          $("#montantadh").addClass("is-invalid");
          $("#liveAlertAdh").html('<div class="alert alert-primary" role="alert">Les cotisations des adhérent·e·s sont la principale ressource et la garantie de l’indépendance de notre association !</div>');
        }
        else if (parseInt(montantadh) < 24) {
          $("#montantadh").removeClass("is-invalid");
          $("#liveAlertAdh").html('<div class="alert alert-primary" role="alert">Les cotisations des adhérent·e·s sont la principale ressource et la garantie de l’indépendance de notre association !</div>');
        } else {
          $("#liveAlertAdh").html('');
          //$("#montantadh").removeClass("is-invalid");
        }
      }
    }
    if (choice == "mensuel") {
      if (montantadh == parseInt(montantadh, 10)) {
        if (parseInt(montantadh) < 6) {
          $("#montantadh").addClass("is-invalid");
        } else {
          $("#montantadh").removeClass("is-invalid");
        }
      }
    }
  });

  //testFormAdh
  function testFormAdh() {
    console.log("testFormAdh");
    var myVar = $(".container").find('.is-invalid');
    if (myVar.length > 0) {
      console.log(false);
      return false;
    } else {
      console.log(true);
      return true;
    }
  }

  //testFormAdh
  function testForm() {
    console.log("testForm");
    var myVar = $(".container").find('.is-invalid');
    if (myVar.length > 0) {
      return false;
    } else {
      waiting();
      return true;
    }
  }

  function testFormChange() {
    console.log("testFormChange");
    var myVar = $(".container").find('.is-invalid');
    if (myVar.length > 0) {
      return false;
    } else {
      return true;
    }
  }
  
  function testFormIBAN() {
    console.log("testFormIBAN");
    var myVar = $(".container").find('.is-invalid');
    if (myVar.length > 0) {
      return false;
    } else {
      return true;
    }
  }

  $( "#amount" ).change(function() {
    var amount = $("#amount").val();
    console.log("amount "+amount);
    if (amount ==  "500") {
      console.log("500");
      $(".alertcb").addClass("alert-warning");
      $(".alertcb").html("Attention le change par CB engendre des frais conséquents pour l'association")
    } else {
      $(".alertcb").removeClass("alert-warning");
      $(".alertcb").html("");
    }
  });

  function delete_customer() {
    let text = "Etes-vous sur de vouloir effacer ce client ?";
    if (confirm(text) == true) {
      return true;
    } else {
      return false;
    }
  }

  function delete_sub() {
    let text = "Etes-vous sur de vouloir effacer ce prélèvement ?";
    if (confirm(text) == true) {
      return true;
    } else {
      return false;
    }
  }

  $( '#table-customers' ).searchable();
  $( '#table-mandates' ).searchable();
  $( '#table-payments' ).searchable();
  $( '#table-subscriptions' ).searchable();

  $(function () {
    $('#datepicker1').datepicker(
      {
        format: 'yyyy-mm-dd'
      }
    );
});