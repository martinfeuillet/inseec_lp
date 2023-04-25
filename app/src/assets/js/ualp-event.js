(function ($) {
    $(document).ready(function () {
        const part_one = $('.part1')
        const part_two = $('.part2')
        const goToPartTwo = $('.go_to_part_two')
        const omnesEvent = $('.omnes_form_event')
        const selectCampus = $('select[name="of_city"]')
        const selectDay = $('select[name="of_day"]')
        const msg_error = $('.msg_error')
        const submit = $('.submit_omnes_form')
        goToPartTwo.on('click', function (e) {
            e.preventDefault()
            if (selectCampus.val() === null || selectDay.val() === null || !omnesEvent.hasClass('active')) {
                msg_error.text("veuillez selectionner une ville, une date et un événement")
            } else {
                part_one.hide(500)
                part_two.show(500)
                msg_error.text("")
                let language = navigator.language;
                language = language.split("-")[0];
                let inputPhone = $("#champ_mobilePhoneNumber")[0]
                var superInput = window.intlTelInput(inputPhone, {
                    initialCountry: language,
                    // separateDialCode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    autoPlaceholder: "aggressive",
                    customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
                        return selectedCountryPlaceholder;
                    },
                    preferredCountries: ['fr', 'be', 'bg', 'hr', 'cy', 'cz', 'dk', 'ee', 'fi', 'de', 'gr', 'hu', 'ie', 'it', 'lv', 'lt', 'lu', 'mt', 'nl', 'at', 'pl', 'pt', 'ro', 'sk', 'si', 'es', 'se', 'gb', 'is', 'li', 'no', 'ch', 'mk', 'al', 'ad', 'ba', 'by', 'fo', 'ge', 'gi', 'im', 'je', 'md', 'me', 'rs', 'sm', 'ua', 'va', 'rs', 'tr', 'az', 'am', 'ge', 'kz', 'kg', 'md', 'tj', 'tm', 'uz', 'ru', 'ua']
                });
                inputPhone.addEventListener("blur", function () {
                    if (superInput.isValidNumber()) {
                        inputPhone.value = superInput.getNumber(intlTelInputUtils.numberFormat.E164)
                    } else {
                        console.log("error")
                    }
                })
            }
        })

        //     everytime we click on the event, we change the value on the two selects
        omnesEvent.on('click', function () {
            $(this).addClass('active').siblings().removeClass('active')
            let campus = $(this).data('campus')

            let day = $(this).data('day')
            // foreach options of selectcampus, if an option have the same text as the campus of the event, we select it
            selectCampus.find('option').each(function () {
                if ($(this).text() === campus) {
                    $(this).attr('selected', 'selected')
                }
            })

            selectDay.val(day)
        })

        //     everytime we change the value of the select, we change the value on the event
        selectCampus.on('change', function () {
            let campus = $(this).find('option:selected').text()
            let days = []
            omnesEvent.each(function () {
                $(this).show()
                if ($(this).data('campus') === campus || $(this).data('campus') === "En ligne") {
                    days.push($(this).data('day'))
                } else {
                    $(this).hide()
                }
            })
            selectDay.empty()
            days.forEach(function (day) {
                let formattedDay = day.split('-')
                formattedDay = formattedDay[2] + '/' + formattedDay[1]
                selectDay.append('<option value="' + day + '">' + formattedDay + '</option>')
            })
        })


    // error message if the user doesn't fill the first part of the form
        goToPartTwo.on('click', function () {
            if (selectCampus.val() === null || selectDay.val() === null || !omnesEvent.hasClass('active')) {
                msg_error.text("veuillez selectionner une ville, une date et un événement")
            }
        })

    //     counter for the next event
        let day = parseInt($('.daycounter').text())
        let hour = parseInt($('.hourcounter').text())
        let minute = parseInt($('.minutecounter').text())
        let second = parseInt($('.secondcounter').text())

        setInterval(function() {
            // Decrement the second
            second--;

            // If the second becomes negative, decrement the minute
            if (second < 0) {
                second = 59;
                minute--;
            }

            // If the minute becomes negative, decrement the hour
            if (minute < 0) {
                minute = 59;
                hour--;
            }

            // If the hour becomes negative, decrement the day
            if (hour < 0) {
                hour = 23;
                day--;
            }

            // Update the HTML elements with the new values
            $('.daycounter').text(day);
            $('.hourcounter').text(hour);
            $('.minutecounter').text(minute);
            $('.secondcounter').text(second);

        }, 1000);


    // validation of the formulary
        submit.on('click', function (e) {
            e.preventDefault()
            let form = $("#omnes_form")
            let lastname = $('input[name="champ_lastName"]')
            let firstname = $('input[name="champ_firstName"]')
            let phone = $('input[name="champ_mobilePhoneNumber"]')
            let email = $('input[name="champ_email"]')
            let backToSchool = $('select[name="champ_backToSchool"]')
            let admissionLevel = $('select[name="champ_admissionLevel"]')
            let champEducationLevel = $('select[name="champ_educationLevel"]')
            let consent = $('input[name="champ_consent"]')
            let omnes_form_event_active = $('.omnes_form_event.active').data('id')
            const emailRegex = /^([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})(\.[a-zA-Z]{2,})?$/;
            if (lastname.val() === "" || firstname.val() === "") {
                msg_error.text("veuillez remplir les champs nom et prénom")
            } else if (phone.val().charAt(0) !== "+") {
                msg_error.text("veuillez entrer un numéro de téléphone valide")
            } else if (!emailRegex.test(email.val())) {
                msg_error.text("veuillez entrer une adresse email valide")
            } else if (backToSchool.val() === null) {
                msg_error.text("veuillez selectionner votre rentrée")
            } else if (admissionLevel.val() === null) {
                msg_error.text("veuillez selectionner votre niveau d'admission")
            } else if (champEducationLevel.val() === null) {
                msg_error.text("veuillez selectionner votre niveau d'étude")
            } else if (!consent.is(':checked')) {
                msg_error.text("veuillez accepter les conditions d'utilisation")
            } else {
                // do an ajax request to send the form
                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: {
                        "lastName": lastname.val(),
                        "firstName": firstname.val(),
                        "mobilePhoneNumber": phone.val(),
                        "email": email.val(),
                        "backToSchool": backToSchool.val(),
                        "campus": selectCampus.val(),
                        "admissionLevel": admissionLevel.val(),
                        "educationLevel": champEducationLevel.val(),
                        "consent": consent.val(),
                        "event": omnes_form_event_active,
                        "source": "omnes_form"
                    },
                    success: function (data) {
                        console.log(data)
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
            }
        })

        simpleslider.getSlider({
            container: document.querySelector('.slider-container'),
            delay: 4,
            duration: 1,
        });
    });
})(jQuery)