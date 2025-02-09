/*node browser: true */ /*global $ */ /*global alert */
/*global updateContent */
window.addEventListener("DOMContentLoaded", function () {
    const formButton = document.getElementById("form-trigger");
    let popup = document.getElementById("popup");
    let backgroundOverlay = document.getElementById("background-overlay");

    formButton.addEventListener("click", function () {
        popup.style.display = "block";
        backgroundOverlay.classList.add("show");
        window.history.pushState("", "", "formPage.html");
    });
// Получаем элементы формы
    const userForm = document.getElementById("UserForm");
// Восстанавливаем значения из LocalStorage при загрузке страницы
    window.onload = function () {
        const savedName = localStorage.getItem("user_name");
        const savedEmail = localStorage.getItem("user_email");
        const savedMessage = localStorage.getItem("user_message");
        const savedOrganization = localStorage.getItem("user_organization");
        const savedPhone = localStorage.getItem("user_phone");
        if (savedPhone) {
            document.getElementsByName("user_phone")[0].value = savedPhone;
        }
        if (savedOrganization) {
            document.getElementsByName("user_organization")[0].value =
            savedOrganization;
        }
        if (savedName) {
            document.getElementsByName("user_name")[0].value = savedName;
        }
        if (savedEmail) {
            document.getElementsByName("user_email")[0].value = savedEmail;
        }
        if (savedMessage) {
            document.getElementsByName("user_message")[0].value = savedMessage;
        }
    };
    // Сохраняем значения в LocalStorage при каждом вводе
    userForm.addEventListener("input", function (event) {
        localStorage.setItem(event.target.name, event.target.value);
    });
    // Обработка событий навигации для контроля за поведением при переходах
    window.addEventListener("popstate", function (event) {
        if (popup.style.display === "block") {
            popup.style.display = "none";
            backgroundOverlay.classList.remove("show");
            window.history.replaceState("", "", "index.html");
        }
        updateContent(event.state.content);
    });

    $(function () {
        $(".ajaxForm").submit(function (e) {
            e.preventDefault();

            let emailField = document.getElementsByName("user_email");
            let nameField = document.getElementsByName("user_name");
            let phoneField = document.getElementsByName("user_phone");
            const consentCheckbox =
                  document.getElementsByName("data_consent")[0];
            let formcheck = true;

            if (!nameField[0].value) {
                formcheck = false;
            }
            if (!emailField[0].value) {
                formcheck = false;
            }
            if (!phoneField[0].value) {
                formcheck = false;
            }
            if (!consentCheckbox.checked) {
                formcheck = false;
            }
            if (formcheck) {
                $.ajax({
                    complete: function () {
                        userForm.reset();
                    },
                    contentType: false,
                    data: new FormData(this),
                    dataType: "json",
                    error: function (jqXHR) {
                        const errorInfo = jqXHR.responseJSON;
                        alert("Error: " + errorInfo.message);
                    },
                    processData: false,
                    success: function (response) {
                        if (response.status === "success") {
                            alert("Форма отправлена!");
                            userForm.reset();
                        } else {
                            alert("Ошибка");
                            userForm.reset();
                        }
                    },
                    type: "POST",
                    url: "https://formcarry.com/s/jMVltZAXvKN"
                });
            } else {
                alert("Заполните все поля формы");
            }
        });
    });
});
