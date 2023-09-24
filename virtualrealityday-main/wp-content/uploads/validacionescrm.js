/**
 * Mensajes de presentacion en vista y constantes de longitud
 * @type String
 v23
 */

const TIPO_CEDULA = 'CÉDULA DE IDENTIDAD';
const TIPO_PASAPORTE = 'PASAPORTE';
const MENSAJE_ERROR_VACIO = 'Por favor ingrese su cédula';
const MENSAJE_ERROR_CEDULA_INVALIDA = 'La cédula ingresada no es valida';
const MENSAJE_ERROR_IDENTIFICACION_NO_VALIDA = 'Tipo de Identificación no válida';
const TOTAL_DIGITOS_CEDULA = 10;
const MAX_FILE_SIZE = 2; // 1 = 1MB
const ACTIVE_DISTANCIA = true;
const ACTIVE_CONTINUA = true;

var minAgeRequired = 16;
var maxAgeRequired = 100;
var actualYear = "2020";
var validToSend = true;

var msgAgeNotCorrect = "Estimado usuario, usted no cumple con la edad requerida para llenar la solicitud.";
var msgAgeRequired = "Estimado usuario, por favor ingrese su fecha de nacimiento.";
var msgAgeValidDate = "Estimado usuario, por favor ingrese una fecha valida.";


$(document).ready(function() {
  var URLactual = window.location;
  var URLidaplicacion = "https://utpltest.elluciancrmrecruit.com/Apply/Application/Application?newTab=1&ApplicationId=";
  var idaplicacion = String(URLactual.href).substr(-36);
  var error = document.getElementsByClassName("text-center elcn-error-page-text").length;
  var enlaceError = String(URLidaplicacion).concat(idaplicacion);
  var elc_oprt_grado="https://utpltest.elluciancrmrecruit.com/Apply/Application/OpportunityCreateNew?type=elcn_solicituddegradopresenc";
  var elc_oprt_posgrado="https://utpltest.elluciancrmrecruit.com/Apply/Application/OpportunityCreateNew?type=elcn_solicituddepostgrado";
  
  /* Botones inicio/creacion de cuenta con facebooc y google */
/*Fabricio 08/01/2021*/


    if (URLactual =='https://utpltest.elluciancrmrecruit.com/Apply/Account/Login' ||
    URLactual =='https://utpltest.elluciancrmrecruit.com/Apply/Account/Create')
    {
        // Mover ubicación de boton "Crear cuenta"
        var createAccount = document.getElementById("createAccountLink");
        $( "div.crm-login").append( createAccount );
        //$( "#createAccountLink").addClass("createAccountbtn");
        // Ocultar controles originales de redes socuales
        $("div.external-login-provider > input").hide();
        // Crear nuevo botones
        var botones = $('<button/>').attr({ formmethod:'post',class:'btn btn-social btn-lg btn-block' });
        $("div.external-login-provider").append(botones);
        // Boton de facebook
        $( "div.external-login-provider > button").eq( 0 ).append( "<i class='fab fa-facebook-f'></i>" );
        $( "div.external-login-provider > button").eq( 0 ).append( "Conectar con <b>Facebook</b>" );
        $( "div.external-login-provider > button").eq( 0 ).attr( "formaction", "/Apply/Facebook/Login" );
        $( "div.external-login-provider > button").eq( 0 ).addClass("btn-faceook btn-primary");
        // Boton Google
        $( "div.external-login-provider > button").eq( 1 ).append( "<i class='fab fa-google'></i>" );
        $( "div.external-login-provider > button").eq( 1 ).append( "Conectar con <b>Google</b>" );
        $( "div.external-login-provider > button").eq( 1 ).attr( "formaction", "/Apply/Google/Login" );
        $( "div.external-login-provider > button").eq( 1 ).addClass("btn-google btn-danger");
        }



  if (error != 0) {
    console.log("el error");
    localStorage.setItem('error', 'error');
    console.log(enlaceError)
    window.location.href = enlaceError;

  }
  if (URLactual =='https://utpltest.elluciancrmrecruit.com/Apply/'){
  sessionStorage.removeItem("periodo");
  $('.card-box').click(function(e){
  var htmlString = $( this ).html();
  var el = document.createElement( 'html' );
  el.innerHTML =htmlString ;
  var valores=el.getElementsByClassName("ocultos");
  valores=valores[0].innerText;
  valores= valores.split(",");
  localStorage.setItem("nivel", valores[0]);
  localStorage.setItem("periodo", valores[1]);
  if(valores[0]==1){
	window.location.href='/Apply/Application/Apply?type=elcn_solicituddegradopresenc';
        localStorage.setItem("periodo", valores[1]);
  }else{
       window.location.href='/Apply/Application/Apply?type=elcn_solicituddepostgrado';  

   }
  })
  }

  if (URLactual == "https://utpltest.elluciancrmrecruit.com/Apply/Application/OpportunityCreateNew?type=elcn_solicituddepostgrado" || URLactual == "https://utpltest.elluciancrmrecruit.com/Apply/Application/Apply?type=elcn_solicituddepostgrado") {
    document.getElementById("elcn_academiclevelofinterestid").value = "1d641217-aeac-e911-80db-0e4c39aa5c6a";
    document.getElementById("elcn_anticipatedentrytermid").selectedIndex = "1";
  } else {

    document.getElementById("elcn_academiclevelofinterestid").selectedIndex = "1";
    var periodo=localStorage.getItem('periodo')
    if(periodo==1){
      document.getElementById("elcn_anticipatedentrytermid").selectedIndex = "1";
    }else{
      document.getElementById("elcn_anticipatedentrytermid").selectedIndex = "2";
    }
     
    $("#elcn_academicprogramofinterestid").click(function(){
       $("new_value").show();
       var selectobject = document.getElementById("elcn_academicprogramofinterestid");
      for (var i=0; i<selectobject.length; i++) {
        if (selectobject.options[i].text == '0 Ninguna'){
          selectobject.remove(i);
        }
     }
    });
     
  }
});


function validarCedulaEC(cedula) {
  if (cedula.length === TOTAL_DIGITOS_CEDULA) {
    var cad = cedula;
    var total = 0;
    var longitud = cad.length;
    var longcheck = longitud - 1;

    if (cad !== "" && longitud === 10) {
      for (i = 0; i < longcheck; i++) {
        if (i % 2 === 0) {
          var aux = cad.charAt(i) * 2;
          if (aux > 9)
            aux -= 9;
          total += aux;
        } else {
          total += parseInt(cad.charAt(i));
        }
      }

      total = total % 10 ? 10 - total % 10 : 0;

      if (cad.charAt(longitud - 1) == total) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

function verificarCedula() {
  var cedula = $('input#datatel_sin').val().trim();
  var identificacion = '';

  try {
    identificacion = document.getElementById("utpl_tipodeidentificacion").selectedOptions[0].label;
  } catch (e) {
    identificacion = '';
  }

  if (cedula.length > 0) {
    switch (identificacion.toString().toUpperCase()) {
      case TIPO_CEDULA:
        if (!validarCedulaEC(cedula)) {
          document.getElementById("datatel_sin").value = "";
          alert(MENSAJE_ERROR_CEDULA_INVALIDA);
        }
        break;
      case TIPO_PASAPORTE:
        break;
      default:
        alert(MENSAJE_ERROR_IDENTIFICACION_NO_VALIDA);
        break;
    }
  } else {
    alert(MENSAJE_ERROR_VACIO);
  }
}

function verifyAge() {

  var idFieldBirthDate = 'input#datatel_birthdate';

  try {
    var birthDate = $(idFieldBirthDate).val().trim(); //11/12/2019
    var splitBirthDate = birthDate.split('/');

    if (splitBirthDate[2] != undefined) {
      var age = actualYear - splitBirthDate[2];
      if (age > 0 && age < minAgeRequired) {
        $(idFieldBirthDate).val("");
        alert(msgAgeNotCorrect);
      } else if (age > maxAgeRequired) {
        $(idFieldBirthDate).val("");
        alert(msgAgeNotCorrect);
      } else if (age <= 0) {
        $(idFieldBirthDate).val("");
        alert(msgAgeValidDate);
      } else {
        //console.log("Correct birthdate");
      }
    }
  } catch (e) {}
}

var msgValidateFields;

function validateFields() {
  msgValidateFields = "";
  var validPackage = 0;

  var fieldsToValidate = ["firstname", "lastname", "utpl_tipodeidentificacion",
    "datatel_sin", "emailaddress1", "datatel_emailaddress1_confirm",
    "address1_telephone2", "elcn_anticipatedentrytermid", "elcn_academiclevelofinterestid",
    "elcn_locationid", "elcn_academicprogramofinterestid", "utpl_site", "membership_password",
    "membership_confirmpassword"
  ];


  var pwd1 = "";
  var pwd2 = "";

  for (var i = 0; i < fieldsToValidate.length; i++) {
    var field = $("#" + fieldsToValidate[i]);
    if (field.val() != null && field.val() != undefined && field.val() != "") {
      validPackage++;
    } else {
      var elementLabelsFields = document.getElementsByTagName("label");
      for (var l = 0; l < elementLabelsFields.length; l++) {
        if (elementLabelsFields[l].getAttribute("for") == fieldsToValidate[i]) {
          msgValidateFields += "\nCampo " + elementLabelsFields[l].innerHTML + "";
          break;
        }
      }
    }

    if (fieldsToValidate[i] == "membership_password") {
      pwd1 = field.val();
    }

    if (fieldsToValidate[i] == "membership_confirmpassword") {
      pwd2 = field.val();
    }

    if (pwd1 != pwd2 && (pwd1 != "" && pwd2 != "")) {
      setTimeout(function() {
        $("#membership_password").val("");
        $("#membership_confirmpassword").val("");
      }, 500);
      msgValidateFields += "La contraseña no coincide, vuelva a ingresar";
      validPackage--;
    }

  }

  // If all validations are correct
  if (validPackage == fieldsToValidate.length) {
    // Active the button to send request make account
    return true;
  } else {
    return false;
  }
}

function addEventToCreateAccount() {
  try {
    var formulario = document.getElementById("submitCreateAccount");

    formulario.addEventListener("click", function(e) {
      /*Controlar espacios vacios al principio y final de campos: nombres e identificacion*/
      /*fabricio 25/01/2021 */
      $("#datatel_sin").val($.trim($("#datatel_sin").val()));
      $("#firstname").val($.trim($("#firstname").val()));
      $("#lastname").val($.trim($("#lastname").val()));
      $("#utpl_apellidomaterno").val($.trim($("#utpl_apellidomaterno").val()));
      $("#address1_composite").val($.trim($("#address1_composite").val()));

      //Validamos informaciÃ³n antes de enviar cuenta
      msgValidateFields = "";
      if (!validateFields()) {
        alert("Por favor, verificar la información ingresada:\n" + msgValidateFields);
        //super(preventDefault());
        try {
          var spinner = document.getElementsByClassName("recruit-spinner-wrapper")[0];
          spinner.style.display = "none";
        } catch (e) {}
        setTimeout(function() {
          formulario.disabled = false;
        }, 500);

        setTimeout(function() {

          var fields = ["firstname", "lastname", "utpl_tipodeidentificacion",
            "datatel_sin", "emailaddress1", "datatel_emailaddress1_confirm",
            "address1_telephone2", "elcn_anticipatedentrytermid", "elcn_academiclevelofinterestid",
            "elcn_locationid", "elcn_academicprogramofinterestid", "utpl_site", "membership_password",
            "membership_confirmpassword"
          ];

          for (var k = 0; k < fields.length; k++) {
            $("#" + fields[k]).removeAttr("readonly");
          }
        }, 500);

        e.preventDefault();
      }
    });
  } catch (err) {}
}

function addEventoToSubmitApplication() {
  try {
    var formulario = document.getElementById("submitApplication");
    formulario.onclick = function(e) {
      copyTermsConditions();
      if (validateDocumentsToSend()) {
        var okTermsCondition = document.getElementById("datatel_certify1_true").checked;
        if (okTermsCondition) {
          var opcion = confirm("Estimado aspirante, una vez enviada su solicitud no podrá ser editada, si sus datos son correctos, haga clic en Aceptar, si requiere verificar su información de clic en Cancelar");
          if (opcion == true) {
            alert("Estimado aspirante, su solicitud ha sido enviada.\nUna vez aprobada, recibirá una notificación vía correo electrónico.");
          } else {
            enabledButtonsToSend();
            e.preventDefault();
            return false;
          }
        } else {
          alert("Por favor, acepte los Términos y Condiciones");
          enabledButtonsToSend();
          e.preventDefault();
          return false;
        }
      } else {

        alert("Es necesario completar su registro antes de envíar su solicitud.\nPor favor adjunte los documentos faltantes en la sección de requisitos.");

        enabledButtonsToSend();

        // Send to view Requisites
        try {
          var btnRequisitos = $('a:contains("Requisitos")');
          btnRequisitos[0].click();
        } catch (e) {}
        e.preventDefault();
        return false;
      }
    }
  } catch (e) {}
}

function enabledButtonsToSend() {

  try {
    var spinner = document.getElementsByClassName("recruit-spinner-wrapper")[0];
    spinner.style.display = "none";
  } catch (e) {}

  setTimeout(function() {
    var dataInput = $("input");
    for (var i = 0; i < dataInput.length; i++) {
      var inputItem = dataInput[i];
      if (inputItem.id == "submitApplication" || inputItem.id == "save" || inputItem.id == "prev") {
        inputItem.disabled = false;
      }

      if (inputItem.defaultValue == 'Vista previa antes del enví­o') {
        inputItem.disabled = false;
      }

      inputItem.disabled = false;
    }
  }, 500);
}

function addReturnButton() {
  var contSizeElemntos = document.createElement("div");
  contSizeElemntos.id = "size-elements";
  $(contSizeElemntos).append("<label>Solo se aceptan archivos de tipo PDF (.pdf), el tamaño máximo de cada archivo es de " + MAX_FILE_SIZE + " MB</label>");

  var newBtnReturn = document.createElement("div"); //
  newBtnReturn.id = "returnScreen";

  var newBtnContinue = document.createElement("div"); //
  newBtnContinue.id = "continueScreen";

  var newLblBtnReturn = document.createElement("label"); //
  newLblBtnReturn.innerHTML = "Regresar";
  newBtnReturn.appendChild(newLblBtnReturn);

  var newLblBtnContinue = document.createElement("label"); //
  newLblBtnContinue.innerHTML = "Continuar";
  newBtnContinue.appendChild(newLblBtnContinue);



  var nodeToAddBtn = document.getElementById("supplemental-items-container");

/*Modificado por PTO para implementar funcionalidad de retorno al tab de Application*/
  newBtnReturn.addEventListener("click", function() {
    /*var navRequest = document.getElementsByTagName("a");
    for (var b = 0; b < navRequest.length; b++) {
      if (navRequest[b].innerHTML == "Solicitud") {
        //Obtain the component
        var btnRequest = navRequest[b];
        btnRequest.click();
        break;
      }
    }*/
    goTabApplication();
  });


  newBtnContinue.addEventListener("click", function() {
    if (validateDocumentsToSend()) {
      //console.log("Se puede enviar");
      //window.location.href = "/Apply/Application/Application?newTab=2&ApplicationId="+Ellucian.Recruit.Mvc.Application.ApplicationId;
      goTabTerms();
    } else {
      //console.log("No se puede enviar");
    }

  });

  var intervalAddBtns = setInterval(function() {
    var nodeToAddBtn = document.getElementById("supplemental-items-container");
    if (nodeToAddBtn) {
      if (!document.getElementById("returnScreen")) {
        nodeToAddBtn.appendChild(contSizeElemntos);
        nodeToAddBtn.appendChild(newBtnReturn);
        if (validateDocumentsToSend()) {
          var idPrint = document.getElementById("printOportunity");
          if (idPrint == undefined || idPrint == null) {
            nodeToAddBtn.appendChild(newBtnContinue);
          }
        }
        //nodeToAddBtn.insertAfter("section"); 
        clearInterval(intervalAddBtns);
      }
    }
  }, 500);
}

function validateProgram() {
  try {
    var program1 = document.getElementById("datatel_academicprogramid").selectedOptions[0].label;
    var program2 = document.getElementById("utpl_seleccionalasegundatitulacin").selectedOptions[0].label;

    if (program1 != "" && program2 != "") {
      if (program2 == program1) {
        alert("No puede seleccionar la misma carrera como segunda opción, por favor seleccione otra de su preferencia");
        document.getElementById("utpl_seleccionalasegundatitulacin").value = "0";
      }
    }
  } catch (e) {}
}

function validateSuplementalItems() {
  validToSend = true;
  setTimeout(function() {
    try {
      var trElements = document.getElementsByClassName("table elcn-data-table")[0].rows;
      var c = 0;
      for (let item of trElements) {
        var trElem = item;
        if (trElem.hasAttribute("data-submissionid")) {

          // Validamos si el documento es requerido
          var isRequired = trElem.childNodes[1].className;
          if (isRequired == "required") {
            var statusSend = trElem.childNodes[3].innerHTML;
            if (statusSend == "No recibido") {
              validToSend = false;
              c++;
            }
          }
        }
      }

    } catch (e) {}
  }, 2000);
}

function validateDocumentsToSend() {
  try {
    var trElements = document.getElementsByClassName("table elcn-data-table")[0].rows;
    for (let item of trElements) {
      var trElem = item;
      if (trElem.hasAttribute("data-submissionid")) {

        // Validamos si el documento es requerido
        var isRequired = trElem.childNodes[1].className;
        if (isRequired == "required") {
          var statusSend = trElem.childNodes[3].innerHTML;
          if (statusSend == "No recibido") {
            validToSend = false;
            return false;
          }
        }
      }
    }
  } catch (e) {}

  return true;
}

function validateRequisitWithCupo(nameElement, trElem) {

  var nombresRequisitos = ["Certificado Bachillerato Internacional",
    "Certificado de pertenecer al orden eclesiástico",
    "Certificado de ser abanderado del Pabellón Nacional",
    "Certificado de ser hijo de funcionario UTPL",
    "Certificado Discapacidad"
  ];

  var idCuposField = ["utpl_bachilleratointernacional_true",
    "utpl_pertenecealordeneclesistico_true",
    "utpl_esabanderadodelpabellnnacionaldesucolegi_true",
    "utpl_eshijodeunfuncionarioquelaboraatiempocom_true",
    "utpl_poseealgntipodediscapacidad_true"
  ];


  for (var i = 0; i < nombresRequisitos.length; i++) {

    if (nameElement == nombresRequisitos[i]) {
      var chkBachlInte = document.getElementById(idCuposField[i]).checked;
      if (chkBachlInte) {
        var statusSend = trElem.childNodes[3].innerHTML;
        if (statusSend == "No recibido") {
          validToSend = false;
          return true;
        }
      }
    }
  }
}

function copyTermsConditions() {
  try {
    document.getElementById("utpl_detalleterminosycondiciones").style.display = "none";
    var terms = document.getElementsByClassName("field-instructions")[0].innerHTML.replace(/<[^>]*>?/g, '');
    document.getElementById("utpl_detalleterminosycondiciones").value = terms;
  } catch (e) {}
}

function setTextFields() {
  try {
    var dataUniversidad = document.getElementsByTagName("h4");
    for (var a = 0; a < dataUniversidad.length; a++) {
      if (dataUniversidad[a].innerHTML == "Universidad 1") {
        dataUniversidad[a].innerHTML = "Institución de Educación Superior (IES)";
      }
    }
  } catch (e) {};

  try {
    var dataUniversidad = document.getElementsByTagName("h4");
    for (var a = 0; a < dataUniversidad.length; a++) {
      if (dataUniversidad[a].innerHTML == "Colegio 1") {
        dataUniversidad[a].innerHTML = "Datos del Colegio";
      }
    }
  } catch (e) {};

  try {
    var dataOrganizacion = document.getElementsByTagName("label");
    for (var a = 0; a < dataOrganizacion.length; a++) {
      if (dataOrganizacion[a].innerHTML == "Nombre de la organización (completo o parcial)") {
        $(dataOrganizacion[a]).remove();
        break;
      }
    }
  } catch (e) {};

  try {
    var dataOrganizacion = document.getElementsByTagName("label");
    for (var a = 0; a < dataOrganizacion.length; a++) {
      if (dataOrganizacion[a].innerHTML == "Institución") {
        dataOrganizacion[a].innerHTML = "Universidad";
        break;
      }
    }
  } catch (e) {};
}

function addEventToUploadDocuments() {
  var executeInterval = 0;

  var addBtn = setInterval(function() {
    try {
      var btnUpload = $('input[value="Cargar Todo"]');
      if (btnUpload) {
        btnUpload[0].onclick = function(e) {
          addReturnButton();
          validateSuplementalItems();
          addEventToDeleteDocument();
          setFormatUploadDocument();
        };
        clearInterval(addBtn);
      }
      executeInterval++;
      if (executeInterval == 10) {
        clearInterval(addBtn);
      }
    } catch (e) {}
  }, 500);
}

function addEventToDeleteDocument() {
  setTimeout(function() {
    try {
      var linkEliminarDocument = $("a[data-attachmentid]");
      for (var i = 0; i < linkEliminarDocument.length; i++) {
        var deleteDocument = linkEliminarDocument[i];
        deleteDocument.onclick = function(e) {
          addReturnButton();
          validateSuplementalItems();
          addEventToUploadDocuments();
          setFormatUploadDocument();
        };
      }
    } catch (e) {}
  }, 4000);
}

function addUpdateProfileMenu() {
  try {
    var updateProfileContainer = document.createElement("li");
    var updateProfileLink = document.createElement("a");
    updateProfileLink.innerHTML = "Actualizar Perfil";
    updateProfileLink.href = "/Apply/Account/UpdateProfile";
    updateProfileContainer.appendChild(updateProfileLink);

    var toMenu = document.getElementsByClassName("dropdown-menu");
    if (toMenu) {
      var menu = toMenu[1];
      menu.appendChild(updateProfileContainer);
    }
  } catch (e) {

  }
}

function hideMenuMatricula() {
  var dataMenu = document.getElementById("elcn-nav-main").children;
  for (var i = 0; i < dataMenu.length; i++) {
    var itemMenu = dataMenu[i];
    if (itemMenu.tagName == "LI") {
      if (itemMenu.innerHTML.includes("ApplicationDeadlines")) {
        itemMenu.style.display = "none";
        itemMenu.style.visibility = "hidden";
      }
    }
  }
}

function loadDataOpportunity() {

  var url = window.location;

  if (url.pathname.indexOf("Account/Create") != -1) {
    var periodo = document.getElementById("elcn_anticipatedentrytermid");


    if (periodo) {
      var sizeList = periodo.options.length;
      periodo.value = periodo.options[sizeList - 1].value;

      setTimeout(function() {
        var nivel = document.getElementById("elcn_academiclevelofinterestid");
        if (nivel) {
          var sizeList = nivel.options.length;
          nivel.value = nivel.options[sizeList - 1].value;
          nivel.selectedIndex = sizeList - 1;

          var modalidad = document.getElementById("elcn_locationid");
          if (modalidad) {
            var sizeList = modalidad.options.length;
            modalidad.value = modalidad.options[sizeList - 1].value;
            modalidad.selectedIndex = sizeList - 1;

            var programa = document.getElementById("elcn_academicprogramofinterestid");

            if (programa) {
              var sizeList = programa.options.length;
              programa.value = programa.options[sizeList - 1].value;
              programa.selectedIndex = sizeList - 1;

              var centro = document.getElementById("utpl_site");
              if (centro) {
                var sizeList = centro.options.length;
                for (var i = 0; i < sizeList; i++) {
                  if (centro.options[i].label == "LOJA") {
                    centro.value = (centro.options[i].value);
                    centro.selectedIndex = i;
                    break;
                  }
                }
              }
            }
          }
        }

      }, 1000);

    }
  } else {
    //console.log("NO ESTAMOS EN CREAR CEUNTA");
  }
  $('input#submitCreateAccount').prop('disabled', false);
}

/*function addMenuNewRequest() {

  var url = window.location;
  var menu = document.createElement('div');

  var menuDistancia = "";
  var menuContinua = "";

  if (ACTIVE_DISTANCIA) {
    menuDistancia = "<li><a href='/Apply/Application/Apply?type=elcn_solicituddegradomodalidadadistancia1'>Solicitud de Grado Distancia</a></li>";
  }

  if (ACTIVE_CONTINUA) {
    menuContinua = "<li><a href='/Apply/Application/Apply?type=elcn_solicitudeducontinua3'>Solicitud Educantión Continua</a></li>";
  }

  if (url.host.indexOf("utpltest") == -1) {
    menuDistancia = "";
    menuContinua = "";
  }

  $(menu).append(
    "<ul class='nav'>" +
    "<li><a href=''>Crear Nueva Solicitud</a>" +
    "<ul>" +
    "<li><a href='/Apply/Application/Apply?type=elcn_solicituddegradopresenc'>Solicitud de Grado Presencial</a></li>" +
    "<li><a href='/Apply/Application/Apply?type=elcn_solicituddepostgrado'>Solicitud de Posgrado</a></li>" +
    menuDistancia +
    menuContinua +
    "</ul>" +
    "</li>" +
    "</ul>");
  menu.id = 'header-menu-apply';

  var toDivN = document.getElementsByClassName('application-start-button');
  var toDivE = document.getElementsByClassName("app-list-new");

  if (toDivN) {
    $(menu).insertBefore(toDivN[0]);
  }
  if (toDivE) {
    $(menu).insertBefore(toDivE[0]);
  }
}*/

function redirectLoginFromActivatedAccount() {

  var url = window.location;

  if (url.pathname.indexOf("Account/AccountActivated") != -1) {
    var contenedor = document.getElementsByClassName("recruit-container");

    var divRedirect = document.createElement("div");
    divRedirect.id = "redirect";
    $(divRedirect).append("<div class='text-success'>Lo estamos redireccionando a la página principal para que pueda iniciar sesión y continuar con su proceso de admisión.<div id='contenedor-timer'><label id='timerChange'>10</label></div><div id='tmptimer'></div></div>");

    if (contenedor) {
      var toDiv = contenedor[0];
      $(toDiv).append(divRedirect);
    }

    var n = 10;
    var l = document.getElementById("timerChange");
    window.setInterval(function() {
      l.innerHTML = n;
      n--;
      if (n == 0) {
        window.location.href = "/Apply/Account/Login";
      }
    }, 1000);
  }
}

function changeUrlReturnOrpotunity() {

  var url = window.location;
  try {
    if (url.pathname.indexOf("Apply/Application/OpportunityCreateNew") != -1 ||
      url.pathname.indexOf("Apply/Application/Apply") != -1) {

      var inputBack = document.getElementsByTagName("input");
      for (var i = 0; i < inputBack.length; i++) {
        var nameInput = $(inputBack[i]).attr("name");
        if (nameInput == "back") {
          $(inputBack[i]).attr("formaction", "/Apply/");
          break;
        }
      }

    }
  } catch (e) {}
}

function setFormatUploadDocument() {

  var executeInterval = 0;
  var intervalUpload = setInterval(function() {

    var fieldUpload = document.getElementsByClassName("supplemental-upload");
    var acceptData = "application/pdf";

    if (fieldUpload.length >= 1) {
      for (var a = 0; a < fieldUpload.length; a++) {
        var field = fieldUpload[a];
        if ($(field).attr("type") == "file") {

          $(field).attr("accept", acceptData);

          fieldUpload[a].addEventListener("change", function(e) {
            var filePath = e.target.files[0].name;
            var allowedExtensions = /(.pdf)$/i; ///(.pdg|.jpeg|.png|.gif)$/i;
            if (!allowedExtensions.exec(filePath)) {
              alert('Solo se admiten archivos de tipo PDF (.pdf), vuelva a cargar.');
              this.value = '';
              return false;
            }

            var fileSize = e.target.files[0].size;
            if (fileSize > (1024 * 1024 * MAX_FILE_SIZE)) {
              alert('No puede cargar un archivo de más de ' + MAX_FILE_SIZE + 'MB, vuelva a cargar.');
              this.value = '';
              return false;
            }

          });
        }
      }
      clearInterval(intervalUpload);
    }
    executeInterval++;
    if (executeInterval == 10) {
      clearInterval(intervalUpload);
    }
  }, 500);
}

function redirectSaveAndNext() {

  var url = window.location;

  if (url.pathname.indexOf("Apply/Application/Application") != -1) {
    var tabActive = document.getElementsByClassName("active");

    for (var i = 0; i < tabActive.length; i++) {
      var item = tabActive[i];
      if (item.innerHTML.indexOf("2") != -1) {
        var btn = document.getElementById("next");
        //console.log("Cargando data a btn");
        btn.onclick = function(e) {
          var formIsValid = $("#applicationForm").valid();
          if (formIsValid) {
            //console.log("fire requitisot");
            var btnRequisitos = $('a:contains("Requisitos")');
            btnRequisitos[0].click();
            e.preventDefault();
            return false;
          } else {

          }
        }
        break;
      }

      if (item.innerHTML.indexOf("1") != -1) {
        var btn = document.getElementById("next");
        //console.log("Cargando data a btn");
        btn.onclick = function(e) {
          var formIsValid = $("#applicationForm").valid();
          if (formIsValid) {
            return true;
          } else {
            //Validar los campos requeridos
          }
        }
        break;
      }
    }
  }
}

function hideOportunityProfile() {
  var url = window.location;
  if (url.pathname.indexOf("Apply/Account/UpdateProfile") != -1) {
    var containerSection = document.getElementsByClassName("section");
    containerSection[5].style.display = "none";
  }
}

function redirectOportunityNew() {
  var url = window.location;

  if (url.href.indexOf("Application/Apply?type=elcn_solicituddegradopresenc") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddegradopresenc") != -1 ||
    url.href.indexOf("Application/Apply?type=elcn_solicituddepostgrado") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddepostgrado") != -1 ||
    url.href.indexOf("Apply?type=elcn_solicituddegradomodalidadadistancia1") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddegradomodalidadadistancia1") != -1 ||
    url.href.indexOf("Apply?type=elcn_solicitudeducontinua3") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicitudeducontinua3") != -1) {
    var inputSection = document.getElementsByTagName("input");

    var ok = 0;
    var btnCreateNew;
    for (var i = 0; i < inputSection.length; i++) {
      if (inputSection[i].type == "submit") {
        if (inputSection[i].getAttribute('formaction').indexOf("OpportunityCreateNew") != -1) {
          ok++;
          btnCreateNew = inputSection[i];
        }

        if (inputSection[i].getAttribute('formaction').indexOf("OpportunitySelectExisting") != -1) {
          ok++;
        }
      }
    }



    var isGradeOrPosgrade = "";
    var urlToNewOportunity = "";
    if (url.href.indexOf("Apply?type=elcn_solicituddegradopresenc") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddegradopresenc") != -1) {
      isGradeOrPosgrade = "GRADO";
      urlToNewOportunity = "?type=elcn_solicituddegradopresenc";
    } else if (url.href.indexOf("Apply?type=elcn_solicituddepostgrado") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddepostgrado") != -1) {
      isGradeOrPosgrade = "POSGRADO";
      urlToNewOportunity = "?type=elcn_solicituddepostgrado";
    } else if (url.href.indexOf("Apply?type=elcn_solicituddegradomodalidadadistancia1") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddegradomodalidadadistancia1") != -1) {
      isGradeOrPosgrade = "GRADO";
      urlToNewOportunity = "?type=elcn_solicituddegradomodalidadadistancia1";
    } else if (url.href.indexOf("Apply?type=elcn_solicitudeducontinua3") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicitudeducontinua3") != -1) {
      isGradeOrPosgrade = "EDUCACIÓN CONTINUA";
      urlToNewOportunity = "?type=elcn_solicitudeducontinua3";
    }

    if (ok == 2) {
      document.getElementsByClassName("opportunity-content")[0].style.display = "none";
      var urlFormAction = btnCreateNew.getAttribute("formaction");
      $(btnCreateNew).attr("formaction", urlFormAction.replace(urlFormAction, urlFormAction + urlToNewOportunity));
      btnCreateNew.click();
    }

    if (isGradeOrPosgrade != "") {
      document.getElementById("elcn_anticipatedentrytermid").onchange = function(e) {
        document.getElementById("elcn_academiclevelofinterestid").disabled = false;
        if (this.options.selectedIndex > 0) {
          var isGradeOrPosgrade = "";
          if (url.href.indexOf("Apply?type=elcn_solicituddegradopresenc") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddegradopresenc") != -1) {
            isGradeOrPosgrade = "GRADO";
          } else if (url.href.indexOf("Apply?type=elcn_solicituddepostgrado") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddepostgrado") != -1) {
            isGradeOrPosgrade = "POSGRADO";
          } else if (url.href.indexOf("Apply?type=elcn_solicituddegradomodalidadadistancia1") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddegradomodalidadadistancia1") != -1) {
            isGradeOrPosgrade = "GRADO";
          } else if (url.href.indexOf("Apply?type=elcn_solicitudeducontinua3") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicitudeducontinua3") != -1) {
            isGradeOrPosgrade = "EDUCACIÓN CONTINUA";
          }

          //console.log("EJECUTANDO change");
          setTimeout(function(e) {
            var nivelPrograma = document.getElementById("elcn_academiclevelofinterestid");
            //nivelPrograma.disabled = false;
            //console.log(nivelPrograma);

            if (nivelPrograma) {
              var sizeList = nivelPrograma.options.length;
              var notFound = true;
              for (var i = 0; i < sizeList; i++) {
                //console.log("NIVEL: "+nivelPrograma.options[i].label);
                if (nivelPrograma.options[i].label.toString().toUpperCase() == isGradeOrPosgrade) {
                  //nivelPrograma.value = (nivelPrograma.options[i].value);
                  //nivelPrograma.selectedIndex = i;
                  //nivelPrograma.disabled = true;
                  notFound = false;
                  break;
                }
              }
              if (notFound) {
                //console.log("Periodo no encontrado");
                alert("Este periodo no Aplica para su solicitud de " + isGradeOrPosgrade);
                document.getElementById("elcn_anticipatedentrytermid").value = "";
              }
            }
          }, 250);
        }
      };
      document.getElementById("elcn_academiclevelofinterestid").onchange = function(e) {
        if (this.options.selectedIndex > 0) {
          var isGradeOrPosgrade = "";
          if (url.href.indexOf("Apply?type=elcn_solicituddegradopresenc") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddegradopresenc") != -1) {
            isGradeOrPosgrade = "GRADO";
          } else if (url.href.indexOf("Apply?type=elcn_solicituddepostgrado") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddepostgrado") != -1) {
            isGradeOrPosgrade = "POSGRADO";
          } else if (url.href.indexOf("Apply?type=elcn_solicituddegradomodalidadadistancia1") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicituddegradomodalidadadistancia1") != -1) {
            isGradeOrPosgrade = "GRADO";
          } else if (url.href.indexOf("Apply?type=elcn_solicitudeducontinua3") != -1 || url.href.indexOf("OpportunityCreateNew?type=elcn_solicitudeducontinua3") != -1) {
            isGradeOrPosgrade = "EDUCACIÓN CONTINUA";
          }

          var nivelPrograma = document.getElementById("elcn_academiclevelofinterestid");
          var levelSelected = document.getElementById("elcn_academiclevelofinterestid").options.selectedIndex;
          var nameSelected = nivelPrograma.options[levelSelected].label;

          if (nameSelected != isGradeOrPosgrade) {
            alert("El nivel seleccionado no aplica para su tipo de solicitud " + isGradeOrPosgrade);
            document.getElementById("elcn_academiclevelofinterestid").value = "";
            document.getElementById("elcn_locationid").value = "";
            document.getElementById("elcn_academicprogramofinterestid").value = "";
            document.getElementById("utpl_site").value = "";
          } else {
            document.getElementById("elcn_locationid").value = "";
            document.getElementById("elcn_academicprogramofinterestid").value = "";
            document.getElementById("utpl_site").value = "";
          }

        } else {
          document.getElementById("elcn_academiclevelofinterestid").value = "";
          document.getElementById("elcn_locationid").value = "";
          document.getElementById("elcn_academicprogramofinterestid").value = "";
          document.getElementById("utpl_site").value = "";
        }
      };
    }
  }

}

document.addEventListener("DOMContentLoaded", function(event) {

  hideSaveButtonOnStep3();

  addReturnButton();

  setTimeout(function() {
    addReturnButton();
    addEventToUploadDocuments();
    validateSuplementalItems();
    addEventToDeleteDocument();
  }, 3000);

  setTimeout(function() {
    try {
      var btnRequisitos = $('a:contains("Requisitos")');
      btnRequisitos[0].addEventListener("click", function() {
        addReturnButton();
        validateSuplementalItems();
        addEventToUploadDocuments();
        addEventToDeleteDocument();
      });
    } catch (e) {}
  }, 1000);

  setTimeout(function() {
    addEventToUploadDocuments();
  }, 1000);

  setTimeout(function() {
    copyTermsConditions();
  }, 500);

  addEventoToSubmitApplication();

  addEventToDeleteDocument();

  addReturnButton();

  copyTermsConditions();

  setTextFields();

  validateSuplementalItems();

  verifyAge();

  addUpdateProfileMenu();

  hideMenuMatricula();

  loadDataOpportunity();

  addEventToCreateAccount();

 // addMenuNewRequest();

  redirectLoginFromActivatedAccount();

  changeUrlReturnOrpotunity();

  setFormatUploadDocument();

  redirectSaveAndNext();

  hideOportunityProfile();

  redirectOportunityNew();

  redirectTabs();

  addEventValidationToSendOnPreview();

  enabledPrint();

  $('input#datatel_sin').blur(function() {
    verificarCedula();
  });

  $(document).ready(function() {
    $('#utpl_tipodeidentificacion').on('change', function() {
      var cedula = $('input#datatel_sin').val().trim();
      (cedula.length > 0) ? verificarCedula(): null;
    });
  });

  $('input#datatel_birthdate').change(function() {
    verifyAge();
  });

  $('input#membership_password').blur(function() {
    validateFields();
  });

  $('#utpl_seleccionalasegundatitulacin').change(function() {
    validateProgram();
  });

  $('input#membership_confirmpassword').blur(function() {
    validateFields();
  });

  try {
    validarCedulaEC($('input#datatel_sin').val().trim());
  } catch (e) {}

});

function redirectTabs() {

  var navTab = document.getElementsByClassName("nav-tabs")[0];
  if (navTab) {
    var childTabs = navTab.childNodes;
    if (childTabs) {
      for (var i = 0; i < childTabs.length; i++) {

        // If exist button un tab
        var inputButton = childTabs[i].childNodes[0];
        if (inputButton.nodeName == "INPUT") {
          if (inputButton.defaultValue.indexOf("3.") != -1) {
            //console.log("Se encontro tab 3 Reidrecionando el mismo a Requisitos");
            inputButton.onclick = function(e) {
              //console.log("Click btn");
              var btnRequisitos = $('a:contains("Requisitos")');
              btnRequisitos[0].click();
              e.preventDefault();
              return false;
            };
          }
        }

      }
    }
  }

}

function goTabTerms() {

  var newBtnContinue = document.createElement("input"); //
  newBtnContinue.id = "nextTermsScreen";
  newBtnContinue.value = "Continnuar";

  $(newBtnContinue).attr("formaction", "/Apply/Application/Application?newTab=2&ApplicationId=" + Ellucian.Recruit.Mvc.Application.ApplicationId);


  $(newBtnContinue).attr("formmethod", "post");
  $(newBtnContinue).attr("type", "submit");
  newBtnContinue.style.display = 'none';
  newBtnContinue.style.visibility = 'hidden';


  var navTab = document.getElementsByClassName("nav-tabs")[0];

  navTab.appendChild(newBtnContinue);
  $(newBtnContinue).click();

}

/*Funcion incorporada por PTO para implementar funcionalidad de botón de retorno*/
function goTabApplication() {
   var newBtnBack = document.createElement("input");
   newBtnBack.id = "backApplicationScreen";
   newBtnBack.value = "Regresar";
   
   $(newBtnBack).attr("formaction", "/Apply/Application/Application?newTab=0&ApplicationId=" + Ellucian.Recruit.Mvc.Application.ApplicationId);
   
   $(newBtnBack).attr("formmethod", "post");
   $(newBtnBack).attr("type", "submit");
   newBtnBack.style.display = 'none';
   newBtnBack.style.visibility = 'hidden';

   var navTab = document.getElementsByClassName("nav-tabs")[0];

   navTab.appendChild(newBtnBack);
   $(newBtnBack).click();
}

function addEventValidationToSendOnPreview() {
  var preview = document.getElementsByClassName("print")[0];
  if (preview) {
    var buttonGroup = document.getElementsByClassName("application-button-group")[0];
    if (buttonGroup) {
      var elements = buttonGroup.childNodes;
      for (var i = 0; i < elements.length; i++) {
        var valueElement = elements[i].value;
        if (valueElement != undefined && valueElement.indexOf("Enviar solicitud")) {
          //console.log("Se encontro elemento para redireccionar");
          document.getElementsByClassName("application-button-group")[0].childNodes[3].onclick = function(e) {
            //elements[i].addEventListener("click", function(){
            //elements[i].onclick = function(e){
            copyTermsConditions();
            if (validateDocumentsToSend()) {

              var opcion = confirm("Estimado aspirante, una vez enviada su solicitud no podrá ser editada, si sus datos son correctos, haga clic en Aceptar, si requiere verificar su información de clic en Cancelar");
              if (opcion == true) {
                alert("Estimado aspirante, su solicitud ha sido enviada.\nUna vez aprobada, recibirá una notificación vía correo electrónico.");
              } else {
                enabledButtonsToSend();
                e.preventDefault();
                return false;
              }

            } else {

              alert("Es necesario completar su registro antes de envíar su solicitud.\nPor favor adjunte los documentos faltantes en la sección de requisitos.");

              enabledButtonsToSend();

              // Send to view Requisites
              try {
                var btnRequisitos = $('a:contains("Requisitos")');
                btnRequisitos[0].click();
              } catch (e) {}
              e.preventDefault();
              return false;
            }
          };
        }
      }
    }
  }
}

function enabledPrint() {
  var classPrint = document.getElementsByClassName("print crm-form")[0];

  if (classPrint) {
    var glyphicon = document.getElementsByClassName("glyphicon glyphicon-print")[0];
    if (glyphicon) {
      var printButton = document.createElement("div");
      printButton.id = "printOportunity";
      var iconPrint = document.createElement("span");
      iconPrint.class = "glyphicon glyphicon-print";
      var linkPrint = document.createElement("a");
      linkPrint.href = "/Apply/Application/Print?applicationId=" + Ellucian.Recruit.Mvc.Application.ApplicationId;
      linkPrint.innerHTML = "Imprimir";
      linkPrint.target = "_blank";
      printButton.appendChild(iconPrint);
      printButton.appendChild(linkPrint);
      $(glyphicon).after(printButton);
    }
  }

}

/**
  * Hide Save Button on Step 3 of Application
  * @return {none}
  */
function hideSaveButtonOnStep3() {

  var navTab = document.getElementsByClassName("nav-tabs")[0];
  if (navTab) {
    var childTabs = navTab.childNodes;
    if (childTabs) {
      for (var i = 0; i < childTabs.length; i++) {
        // If exist button un tab
        var inputButton = childTabs[i];
        if (inputButton.nodeName.toUpperCase() == "LI" && inputButton.className.trim() == "active") {
          if (inputButton.innerText.indexOf("3.") != -1) {
            $("#save").css("display", "none");
            $("#save").css("visibility", "hidden");
          }
        }

      }
    }
  }

}

/*Codigo que agrega boton de Eliminar en la lista de solicitudes del sitio prinicipal WFE - PTO*/
//alert('boton eliminar: ' + window.location);
var existBanner;
const HttpIdCaducidad = new XMLHttpRequest();
const HttpEjecutarCaducidad = new XMLHttpRequest();
var mainURL = window.location;
var retiroURL = location.protocol + "//" + location.host + location.pathname;
//var urlCaducidad = "https://srv-si-001.utpl.edu.ec/esbapi/1.0/utpl-proceso-caducidad/solicitud-admision?id=" + bannerId + "&periodo=" + apiPeriodo + "&nivel=" + apiNivel + "&modalidad=" + apiModalidad + "&tipo-alumno=N&regla-tiempo=N&activo=Y&tipo-ejecucion=EJECUTAR";
var urlCaducidad = "https://srv-si-001.utpl.edu.ec/esbapi/1.0/utpl-proceso-caducidad/solicitud-admision";
var elemSpinner = document.getElementById("elcn-full-page-spinner");
var apiPeriodo = "";
var apiNivel = "";
var apiModalidad = "";
var bannerId = "";
const apiKeyCaducidad = "eyJ4NXQiOiJOVGRtWmpNNFpEazNOalkwWXpjNU1tWm1PRGd3TVRFM01XWXdOREU1TVdSbFpEZzROemM0WkE9PSIsImtpZCI6ImdhdGV3YXlfY2VydGlmaWNhdGVfYWxpYXMiLCJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJVVFBMLkVEVS5FQ1wvanNjYWxkZXJvbkBjYXJib24uc3VwZXIiLCJhcHBsaWNhdGlvbiI6eyJvd25lciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidGllclF1b3RhVHlwZSI6bnVsbCwidGllciI6IlVubGltaXRlZCIsIm5hbWUiOiJEZWZhdWx0QXBwbGljYXRpb24iLCJpZCI6MSwidXVpZCI6IjhkYTZkYzA4LTc2YzYtNGJlYy1iNTViLTg2NjY2MDA5YWRmZSJ9LCJpc3MiOiJodHRwczpcL1wvc3J2LXNpLTAwMS51dHBsLmVkdS5lYzo0NDNcL29hdXRoMlwvdG9rZW4iLCJ0aWVySW5mbyI6eyJCcm9uemUiOnsidGllclF1b3RhVHlwZSI6InJlcXVlc3RDb3VudCIsImdyYXBoUUxNYXhDb21wbGV4aXR5IjowLCJncmFwaFFMTWF4RGVwdGgiOjAsInN0b3BPblF1b3RhUmVhY2giOnRydWUsInNwaWtlQXJyZXN0TGltaXQiOjAsInNwaWtlQXJyZXN0VW5pdCI6bnVsbH19LCJrZXl0eXBlIjoiU0FOREJPWCIsInN1YnNjcmliZWRBUElzIjpbeyJzdWJzY3JpYmVyVGVuYW50RG9tYWluIjoiY2FyYm9uLnN1cGVyIiwibmFtZSI6IkVTQkFwaSIsImNvbnRleHQiOiJcL2VzYmFwaVwvMS4wIiwicHVibGlzaGVyIjoiVVRQTC5FRFUuRUNcL2pzY2FsZGVyb24iLCJ2ZXJzaW9uIjoiMS4wIiwic3Vic2NyaXB0aW9uVGllciI6IkJyb256ZSJ9LHsic3Vic2NyaWJlclRlbmFudERvbWFpbiI6ImNhcmJvbi5zdXBlciIsIm5hbWUiOiJTZWd1cmlkYWRBcGkiLCJjb250ZXh0IjoiXC9hcGltXC9zZWd1cmlkYWRcLzEuMCIsInB1Ymxpc2hlciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidmVyc2lvbiI6IjEuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJCcm9uemUifSx7InN1YnNjcmliZXJUZW5hbnREb21haW4iOiJjYXJib24uc3VwZXIiLCJuYW1lIjoiVVRQTFBheUFwaSIsImNvbnRleHQiOiJcL2FwaW1cL3V0cGxwYXlcLzEuMCIsInB1Ymxpc2hlciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidmVyc2lvbiI6IjEuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJCcm9uemUifSx7InN1YnNjcmliZXJUZW5hbnREb21haW4iOiJjYXJib24uc3VwZXIiLCJuYW1lIjoiUmVnaXN0cm9DaXZpbEFwaSIsImNvbnRleHQiOiJcL2VzYmFwaVwvcmVnY2l2aWxcLzEuMCIsInB1Ymxpc2hlciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidmVyc2lvbiI6IjEuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJCcm9uemUifV0sImlhdCI6MTYwODA1MTA0OSwianRpIjoiZGRmOWRhYTktODA0OS00MGE0LTk4ZjMtNmI3ZGI2MThjZWRmIn0=.lbsJQTVAtSglz0rvxhtkOCKMOGqc6jJntDlQDSaTxrDn3YH1POUaDVk9lYuB1B0mlXjr36mpGIUpeQWNofWDc_8E7MmqQYAFU3t-A7pJ9-crhb1YOOIc3gmV5i6BNgnP4t9n5jSciqQrwl7Bzj_tTYE-cj70aaoiiF3O210-jJ0JPhV3iD9ZNNIATnJDnFLVucPG6DH4KoKqPiir13mrcjNjaaW4VIPUdh2teIBQaCMXwXpc4wFyHECncNoVJfp0apDuWnHgttjtBcS_4DFie5wMC4-AUEYrHRuzg2JZYOQcvUN3er_w5Bj5rSqRAMnw8PuAlSCHl2hiNN6BUuaIbQ==";

function apiEjecutaCaducidad(){
	//alert("bid: " + bannerId + " per: " + apiPeriodo + " niv: " + apiNivel + " mod: " + apiModalidad); 
	var elemSpinner = document.getElementById("elcn-full-page-spinner");
	$("#ModalCaducidad").modal("hide");
	elemSpinner.style = "display:block";
	if(bannerId != "" && apiPeriodo != "" && apiNivel != "" && apiModalidad != ""){
		HttpEjecutarCaducidad.open("POST", urlCaducidad);
		HttpEjecutarCaducidad.setRequestHeader("apikey", apiKeyCaducidad);
		HttpEjecutarCaducidad.setRequestHeader("Content-Type", "application/json");
   		HttpEjecutarCaducidad.send(JSON.stringify({"id": bannerId, "periodo": apiPeriodo, "nivel": apiNivel, "modalidad": apiModalidad, "tipo-alumno": "N", "regla-tiempo": "N", "activo": "Y", "tipo-ejecucion": "EJECUTAR"}));
		HttpEjecutarCaducidad.onreadystatechange=(ejeapi)=>{
			var objJsonEjecutarCaducidad = JSON.parse(HttpEjecutarCaducidad.responseText);
			if(objJsonEjecutarCaducidad.Status == 0){
				elemSpinner.style = "display:none";
				window.location.href = "/Apply/";
			}
			else{
				alert("no se ejecut� el proceso de caducidad");
			}
		}
	}
	else{
		alert("par�metros de retiro no correctos");
	}
}


/*DEFINICION DE BOTONES DEL MODAL DE CADUCIDAD*/
$(function () {
	/*$("#btnShowPopup").click(function () {
	});
	$("#btnRegresaCaducidad").click(function () {
		$("#ModalCaducidad").modal("hide");
	});*/
	$("#btnAtras").click(function () {
		window.location.href = "/Apply/";
	});
});

/*FINALIZA DEFINICION DE BOTONES DEL MODAL DE CADUCIDAD*/

/*Funcion del event listener de submit del form de retiro*/
function retiroSubmit(event){
	$("#ModalCaducidad").modal("hide");
}

function cierreModal(){
	$("#ModalCaducidad").modal("hide");
}

function apiRetiro(idSolicitud){
	const apiKeyRetiro = "eyJ4NXQiOiJOVGRtWmpNNFpEazNOalkwWXpjNU1tWm1PRGd3TVRFM01XWXdOREU1TVdSbFpEZzROemM0WkE9PSIsImtpZCI6ImdhdGV3YXlfY2VydGlmaWNhdGVfYWxpYXMiLCJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJVVFBMLkVEVS5FQ1wvanNjYWxkZXJvbkBjYXJib24uc3VwZXIiLCJhcHBsaWNhdGlvbiI6eyJvd25lciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidGllclF1b3RhVHlwZSI6bnVsbCwidGllciI6IlVubGltaXRlZCIsIm5hbWUiOiJEZWZhdWx0QXBwbGljYXRpb24iLCJpZCI6MSwidXVpZCI6IjhkYTZkYzA4LTc2YzYtNGJlYy1iNTViLTg2NjY2MDA5YWRmZSJ9LCJpc3MiOiJodHRwczpcL1wvc3J2LXNpLTAwMS51dHBsLmVkdS5lYzo0NDNcL29hdXRoMlwvdG9rZW4iLCJ0aWVySW5mbyI6eyJCcm9uemUiOnsidGllclF1b3RhVHlwZSI6InJlcXVlc3RDb3VudCIsImdyYXBoUUxNYXhDb21wbGV4aXR5IjowLCJncmFwaFFMTWF4RGVwdGgiOjAsInN0b3BPblF1b3RhUmVhY2giOnRydWUsInNwaWtlQXJyZXN0TGltaXQiOjAsInNwaWtlQXJyZXN0VW5pdCI6bnVsbH19LCJrZXl0eXBlIjoiU0FOREJPWCIsInN1YnNjcmliZWRBUElzIjpbeyJzdWJzY3JpYmVyVGVuYW50RG9tYWluIjoiY2FyYm9uLnN1cGVyIiwibmFtZSI6IkVTQkFwaSIsImNvbnRleHQiOiJcL2VzYmFwaVwvMS4wIiwicHVibGlzaGVyIjoiVVRQTC5FRFUuRUNcL2pzY2FsZGVyb24iLCJ2ZXJzaW9uIjoiMS4wIiwic3Vic2NyaXB0aW9uVGllciI6IkJyb256ZSJ9LHsic3Vic2NyaWJlclRlbmFudERvbWFpbiI6ImNhcmJvbi5zdXBlciIsIm5hbWUiOiJTZWd1cmlkYWRBcGkiLCJjb250ZXh0IjoiXC9hcGltXC9zZWd1cmlkYWRcLzEuMCIsInB1Ymxpc2hlciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidmVyc2lvbiI6IjEuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJCcm9uemUifSx7InN1YnNjcmliZXJUZW5hbnREb21haW4iOiJjYXJib24uc3VwZXIiLCJuYW1lIjoiVVRQTFBheUFwaSIsImNvbnRleHQiOiJcL2FwaW1cL3V0cGxwYXlcLzEuMCIsInB1Ymxpc2hlciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidmVyc2lvbiI6IjEuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJCcm9uemUifSx7InN1YnNjcmliZXJUZW5hbnREb21haW4iOiJjYXJib24uc3VwZXIiLCJuYW1lIjoiTmV0Q29yZUFwaSIsImNvbnRleHQiOiJcL2FwaW1cL25ldGNvcmVcLzEuMCIsInB1Ymxpc2hlciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidmVyc2lvbiI6IjEuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJCcm9uemUifSx7InN1YnNjcmliZXJUZW5hbnREb21haW4iOiJjYXJib24uc3VwZXIiLCJuYW1lIjoiUmVnaXN0cm9DaXZpbEFwaSIsImNvbnRleHQiOiJcL2VzYmFwaVwvcmVnY2l2aWxcLzEuMCIsInB1Ymxpc2hlciI6IlVUUEwuRURVLkVDXC9qc2NhbGRlcm9uIiwidmVyc2lvbiI6IjEuMCIsInN1YnNjcmlwdGlvblRpZXIiOiJCcm9uemUifV0sImlhdCI6MTYxMDM4MjE4NSwianRpIjoiNmU1YzYxN2EtM2YwMS00MGYwLTkyNjktYjIyZTUxZGQ3N2IwIn0=.QjlEqbJnPKs2SBlYuhqhk_oTo1wYpn5QVFojkrgJNpGUVLBc3ODE1RhQacD20ZO9B1Ak5OuCjtnLE3tikEJGeDYRkAXTh7e3LFAAmSdnrgvIrO6bKa-N4HMnBN_O-yzrnIMKNOka-VsGoBwsln8xBvG9dsTqwFAGaonBQjKzZfpoduo3zDuMGsmWiUC713qChqBrbIfPrEZ4qpYCqd3qbqeHcBLFhcyv6hba9cqvnSVtvgfZ1rfKg1hZZLaeNEIoZut0FLuWNcs0ny2n4VJpm1oh-w6xZWpshCheV1jqa64hgi1LhFxXIa7HaQU2KO7Qoex5vRymSgHSwX3PJAFs8A==";
	const HttpIdRetiro = new XMLHttpRequest();
	var urlRetiro="https://srv-si-001.utpl.edu.ec/esbapi/1.0/crm_recruit/consultar?resource=datatel_applications&$filter=datatel_applicationid eq " + idSolicitud;
	var elemSpinner = document.getElementById("elcn-full-page-spinner");
	var modalCont = document.getElementById("modal-dialog");
	//var modNuevo = document.getElementById('ModalCaducidad');
	//alert(modNuevo);
	
	HttpIdRetiro.open("GET", urlRetiro);
	HttpIdRetiro.setRequestHeader("apikey", apiKeyRetiro);
	HttpIdRetiro.send();
	HttpIdRetiro.onreadystatechange=(ee)=>{
		var objJsonRetiro = JSON.parse(HttpIdRetiro.responseText);
		var msgRetiro = objJsonRetiro.Message;
		var btnConfCad = document.getElementById("btnConfirmaCaducidad");
		var btnRegrCad = document.getElementById("btnRegresaCaducidad");
		var btnCancelar =  document.getElementById("btnCancelar");
		
		existBanner = objJsonRetiro["Data"]["existBanner"];
		if(msgRetiro == "Se ha procesado Correctamente"){
			apiPeriodo = objJsonRetiro["Data"]["termCode"];
                        apiNivel = objJsonRetiro["Data"]["levelCode"];
                        apiModalidad = objJsonRetiro["Data"]["campusCode"];
                        bannerId = objJsonRetiro["Data"]["bannerID"];
                      	//alert(bannerId + " " + apiModalidad + " " + apiNivel + " " + apiPeriodo);
						
			HttpIdCaducidad.open("POST", urlCaducidad);
			HttpIdCaducidad.setRequestHeader("apikey", apiKeyCaducidad);
			HttpIdCaducidad.setRequestHeader("Content-Type", "application/json");
   			HttpIdCaducidad.send(JSON.stringify({"id": bannerId, "periodo": apiPeriodo, "nivel": apiNivel, "modalidad": apiModalidad, "tipo-alumno": "N", "regla-tiempo": "N", "activo": "Y", "tipo-ejecucion": "VALIDAR"}));
                        
			HttpIdCaducidad.onreadystatechange=(eapi)=>{
                                var objJsonCaducidad = JSON.parse(HttpIdCaducidad.responseText);
				if(objJsonCaducidad.Status == 0){ /*Indica que si se envi� a Banner y se puede caducar*/
					elemSpinner.style = "display:none";
					btnCancelar.style = "display:none";
					modalCont.style.width = "500px";
					btnConfCad.style = "display:inline";
					btnConfCad.style.width = "80px";
					btnConfCad.style.marginRight = "30px";
					btnRegrCad.style = "display:inline";
					btnRegrCad.style.width = "80px";
					btnRegrCad.style.marginRight = "150px";
					$("#ModalCaducidad .modal-title").html("Confirmar acci&oacute;n de caducidad");
			   		$("#ModalCaducidad .modal-body").html("La solicitud se ha enviado al Sistema Banner. Se eliminar&aacute;n los cargos de admisi&oacute;n y la deuda al banco. Deseas continuar?");
  					$("#ModalCaducidad").modal("show");					
				}
				else{ /*no se ha enviado a Banner, se env�a a la p�gina para selecci�n del motivo del retiro*/
					elemSpinner.style = "display:none";
					//window.location.href = "/Apply/Application/Withdraw?applicationId=" + idSolicitud;
					const HttpWithDraw = new XMLHttpRequest();
					//HttpWithDraw.open("GET", "https://utpltest.elluciancrmrecruit.com/Apply/Application/Withdraw?applicationId=6595df85-c461-eb11-a97e-e3ca8ec8f7e3");
					HttpWithDraw.open("GET", "https://utpltest.elluciancrmrecruit.com/Apply/Application/Withdraw?applicationId=" + idSolicitud);
					HttpWithDraw.send();
					HttpWithDraw.onreadystatechange=(eewd)=>{
						var resultFormRetiro = HttpWithDraw.responseText;
						if(resultFormRetiro.length > 0){
							//var indexFormRazonRetiro = resultFormRetiro.indexOf('<form action="/Apply/Application');
							var indexFormRazonRetiro = resultFormRetiro.indexOf('action="/Apply/Application');
							if(indexFormRazonRetiro > 0){
								var formRazonRetiro = resultFormRetiro.substring(indexFormRazonRetiro, resultFormRetiro.length);
								indexFormRazonRetiro = formRazonRetiro.indexOf('</form') - 1;
								formRazonRetiro = formRazonRetiro.substring(0, indexFormRazonRetiro);
								formRazonRetiro = '<form id="formRetiro" ' + formRazonRetiro + '</form>';
								modalCont.style.width = "800px";
								/*Oculta botones del modal*/
								btnCancelar.style = "display:inline";
								btnCancelar.style.width = "100px";
								btnCancelar.style.marginRight = "330px";
								btnConfCad.style = "display:none";
								btnRegrCad.style = "display:none";
								//$("#ModalCaducidad .modal-title").html('<input type="button" value="x" style="width: 35px; float: right;" onclick="cierreModal()">');
								$("#ModalCaducidad .modal-body").html(formRazonRetiro);
  								$("#ModalCaducidad").modal("show");
								var containerCad = document.getElementsByClassName("col-md-8 center");
								containerCad[0].style.marginLeft = "0px";
								var formRetiro = document.getElementById("formRetiro");
								formRetiro.addEventListener('submit', retiroSubmit);
							}
						}
					};

				}
                        }
		}
		else{
			alert('Error al procesar API en modo auditar: ' + msgRetiro);
		}
	}
}

/*Funci�n para habilitar bot�n Retirar*/
function EnableWithdrawButton(){
	var control = document.getElementById('withdrawBtn');
	control.disabled = false;
	control.removeAttribute("aria-disabled");
}


/*Agrega botones de eliminar y editar en la pantalla principal*/
if(mainURL == 'https://utpltest.elluciancrmrecruit.com/Apply/' || mainURL == 'https://utpltest.elluciancrmrecruit.com/Apply')
{
	window.onload = function(){
		var mainDiv = document.getElementById('home-applications');
		//alert("div1 " + mainDiv);
		var divCad1 = document.createElement('div');
		divCad1.id = "ModalCaducidad";
		divCad1.className = "modal fade";
		divCad1.role = "dialog";
		document.body.appendChild(divCad1);

		divCad1 = document.getElementById("ModalCaducidad");
		var divCad2 = document.createElement('div');
		divCad2.id = "modal-dialog";
		divCad2.className = "modal-dialog";
		divCad2.className += " modal-dialog-cad-center"; //incluir el estilo
		divCad2.role = "document";
		divCad1.appendChild(divCad2);

		divCad2 = document.getElementById("modal-dialog");
		var divCad3 = document.createElement('div');
		divCad3.id = "modal-content";
		divCad3.className = "modal-content";
		divCad2.appendChild(divCad3);

		divCad3 = document.getElementById("modal-content");
		var divCad4 = document.createElement('div');
		divCad4.id = "modal-header";
		divCad4.className = "modal-header";
		divCad3.appendChild(divCad4);

		divCad4 = document.getElementById("modal-header");
		var divCad5 = document.createElement('h4');
		divCad5.className = "modal-title";
		divCad5.value = "Titulo del modal";
		divCad4.appendChild(divCad5);

		var divCad8 = document.createElement('div');
		divCad8.className = "modal-body";
		divCad8.id = "modal-body";
		divCad3.appendChild(divCad8);

		var divCad6 = document.createElement('div');
		divCad6.id = "modal-footer";
		divCad6.className = "modal-footer";
		divCad3.appendChild(divCad6);

		divCad6 = document.getElementById("modal-footer");
		var divCad7 = document.createElement('input');
		divCad7.type = "button";
		divCad7.id = "btnConfirmaCaducidad";
		divCad7.value = "SI";
		divCad7.onclick = function(){
			apiEjecutaCaducidad();
		};
		divCad6.appendChild(divCad7);

		var divCad9 = document.createElement('input');
		divCad9.type = "button";
		divCad9.id = "btnRegresaCaducidad";
		divCad9.value = "NO";
		divCad9.onclick = function(){
			$("#ModalCaducidad").modal("hide");
		};
		divCad6.appendChild(divCad9);

		var divCad10 = document.createElement('input');
		divCad10.type = "button";
		divCad10.id = "btnCancelar";
		divCad10.value = "Cancelar";
		divCad10.onclick = function(){
			$("#ModalCaducidad").modal("hide");
		};
		divCad6.appendChild(divCad10);
/*FIN DECLARACION MODAL*/


        var solicitudes = document.getElementsByClassName('col-xs-8');
	var divPadre = document.getElementsByClassName('row row-no-gutters');
	var elemSpinner = document.getElementById("elcn-full-page-spinner");
	for (var i = 2; i < solicitudes.length; i+=3) {
  		for (var j = 0; j < solicitudes[i].childNodes.length; j++){
    			if(solicitudes[i].childNodes[j].nodeName=="A"){
      				var enlace = solicitudes[i].childNodes[1].href;
				solicitudes[i].childNodes[j].style.display = "none";
      				enlace = enlace.substr(enlace.indexOf("=")+1, enlace.length);
      				var newButtonDelete = document.createElement("input");
      				newButtonDelete.type = "Image";
      				newButtonDelete.src = "http://recursos.utpl.edu.ec/crm/pprd/imagen/bote-de-basura-32px.png";
				newButtonDelete.alt = "Eliminar"
      				newButtonDelete.id = enlace;
				newButtonDelete.style.verticalAlign = "top";
      				newButtonDelete.onclick = function(){
        				//alert(this.id);
					elemSpinner.style = "display:block";
					apiRetiro(this.id);
      				};
				var newButtonEdit = document.createElement("input");
      				newButtonEdit.type = "Image";
				newButtonEdit.src= "http://recursos.utpl.edu.ec/crm/pprd/imagen/editar32px.png";
      				newButtonEdit.alt = "Editar";
      				newButtonEdit.id = enlace;
				newButtonEdit.style.verticalAlign = "top";
      				newButtonEdit.onclick = function(){
					elemSpinner.style = "display:block";
					window.location.href = "/Apply/Application/Apply?ApplicationId="+this.id;
      				};
				
				solicitudes[i].appendChild(newButtonDelete);
				solicitudes[i].appendChild(newButtonEdit);
				
				solicitudes[i].style.cssText = "border-right: 0px solid #FFFFFF !important;";
				solicitudes[i].style.background = "none #FFFFFF";
				solicitudes[i].style.padding = "0.0em";
				solicitudes[i].style.margin = "0px 0px 0px 0px";
				solicitudes[i].style.textAlign = "left";
				//solicitudes[i].style.display = "none";
				for(var k=0; k < solicitudes[i-1].childNodes.length; k++){
					if(solicitudes[i-1].childNodes[k].nodeName == "SPAN" && solicitudes[i-1].childNodes[k].innerHTML == "Retirado"){
						newButtonDelete.disabled = true;
						newButtonDelete.src = "http://utpl.edu.ec/sites/default/files/crm/recursos/eliminar.png";
					}
				}
    			}
  		}
	}
	};
}