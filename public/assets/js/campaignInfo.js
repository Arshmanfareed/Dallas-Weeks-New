$(document).ready(function () {
    localStorage.removeItem("elements_array");
    localStorage.removeItem("elements_data_array");
    
    var settings = JSON.parse(localStorage.getItem("settings"));

    if (settings) {
        $(".linkedin_setting_switch").each(function (e) {
            var name = $(this).attr("name");
            if (settings[name] === "no") {
                $(this).attr("value", "no");
                $(this).prop("checked", false);
                console.log($(this)[0]);
            } else {
                $(this).attr("value", "yes");
                $(this).attr("checked", true);
            }
        });
    } else {
        settings = {};
        var inputs = $(".linkedin_setting_switch");
        inputs.each(function () {
            $(this).prop("checked", false);
            input_name = $(this).attr("name");
            input_value = "no";
            settings[input_name] = input_value;
        });
        localStorage.setItem("settings", JSON.stringify(settings));
    }

    var form = $("#settings");
    form.append(
        $("<input>")
            .attr("type", "hidden")
            .attr("name", "campaign_type")
            .val(campaign_details["campaign_type"])
    );
    form.append(
        $("<input>")
            .attr("type", "hidden")
            .attr("name", "campaign_name")
            .val(campaign_details["campaign_name"])
    );
    form.append(
        $("<input>")
            .attr("type", "hidden")
            .attr("name", "campaign_url")
            .val(campaign_details["campaign_url"])
    );

    if (campaign_details["connections"] != undefined) {
        form.append(
            $("<input>")
                .attr("type", "hidden")
                .attr("name", "connections")
                .val(campaign_details["connections"])
        );
    }

    /* Before submitting make every checkbox having valing */
    $("#create_sequence").on("click", function (e) {
        e.preventDefault();
        var form = $("#settings");
        var inputs = form.find(".linkedin_setting_switch");
        inputs.each(function () {
            if ($(this).is(":checked")) {
                $(this).attr("value", "yes");
                input_name = $(this).attr("name");
                input_value = "yes";
            } else {
                $(this).prop("checked", true);
                $(this).attr("value", "no");
                input_name = $(this).attr("name");
                input_value = "no";
            }
            settings[input_name] = input_value;
        });
        localStorage.setItem("settings", JSON.stringify(settings));
        form.submit();
    });
    $(".next_tab").on("click", function (e) {
        $(this)
            .closest(".comp_tabs")
            .find(".nav-tabs .nav-link.active")
            .next()
            .click();
    });
    $(".prev_tab").on("click", function (e) {
        $(this)
            .closest(".comp_tabs")
            .find(".nav-tabs .nav-link.active")
            .prev()
            .click();
    });
    /* Changing tabs among settings */
    $(".schedule-btn").on("click", function (e) {
        e.preventDefault();
        var targetTab = $("#" + $(this).data("tab"));
        var parent = $(this).parent().parent();
        $(parent).find(".schedule-content.active").removeClass("active");
        $(targetTab).addClass("active");
        $(parent).find(".schedule-btn.active").removeClass("active");
        $(this).addClass("active");
    });
    $(".schedule_days").on("change", function (e) {
        var day = $(this).val();
        if ($(this).prop("checked")) {
            $("#" + day + "_start_time").val("09:00:00");
            $("#" + day + "_end_time").val("17:00:00");
        } else {
            $("#" + day + "_start_time").val("");
            $("#" + day + "_end_time").val("");
        }
    });
    $(".add_schedule").on("click", function (e) {
        e.preventDefault();
        var form = $(".schedule_form");
        var scheduleDays = form.find('input[type="checkbox"]');
        scheduleDays.each(function () {
            if ($(this).is(":checked")) {
                $(this).attr("value", "true");
            } else {
                $(this).prop("checked", true);
                $(this).attr("value", "false");
            }
        });
        $.ajax({
            url: createSchedulePath,
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
            data: form.serialize(),
            success: function (response) {
                if (response.success) {
                    $("#schedule_modal").modal("hide");
                    schedules = response.schedules;
                    html = ``;
                    for (var i = 0; i < schedules.length; i++) {
                        schedule = schedules[i];
                        html += `<li><div class="row schedule_list_item"><div class="col-lg-1 schedule_item">`;
                        html += `<input type="radio" name="email_settings_schedule_id" class="schedule_id"`;
                        if (schedule["user_id"] == 0) {
                            html += `checked `;
                        }
                        html += `value=` + schedule["id"] + `></div>`;
                        html += `<div class="col-lg-1 schedule_avatar">S</div>`;
                        html += `<div class="col-lg-3 schedule_name"><i class="fa-solid fa-circle-check"`;
                        html += `style="color: #4bcea6;"></i>`;
                        html +=
                            `<span>` +
                            schedule["schedule_name"] +
                            `</span></div>`;
                        html += `<div class="col-lg-6 schedule_days">`;
                        var schedule_days = schedule["Days"];
                        html += `<ul class="schedule_day_list">`;
                        for (var j = 0; j < schedule_days.length; j++) {
                            html += `<li `;
                            html += `class="schedule_day `;
                            day = schedule_days[j];
                            if (day["is_active"] == "1") {
                                html += `selected_day`;
                            }
                            html += `">`;
                            html += day["schedule_day"].toUpperCase() + `</li>`;
                        }
                        html += `<li class="schedule_time"><button href="javascript:;"`;
                        html += `type="button" class="btn" data-bs-toggle="modal"`;
                        html += `data-bs-target="#time_modal"><i class="fa-solid fa-globe"`;
                        html += `style="color: #16adcb;"></i></button></li></ul>`;
                        html += `</div><div class="col-lg-1 schedule_menu_btn">`;
                        html += `<i class="fa-solid fa-ellipsis-vertical" style="color: #ffffff;"></i>`;
                        html += `</div></div></li>`;
                    }
                    $("#schedule_list_1").html(html);
                    $("#schedule_list_2").html(
                        html.replace(
                            "email_settings_schedule_id",
                            "global_settings_schedule_id"
                        )
                    );
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    });
    $(".search_schedule").on("input", function (e) {
        var schedule_input = $(this);
        var search = $(this).val();
        if (search === "") {
            search = "null";
        }
        $.ajax({
            url: filterSchedulePath.replace(":search", search),
            method: "GET",
            success: function (response) {
                if (response.success) {
                    schedules = response.schedules;
                    var schedule_list = $(schedule_input)
                        .parent()
                        .next(".schedule_list");
                    html = ``;
                    for (var i = 0; i < schedules.length; i++) {
                        schedule = schedules[i];
                        html += `<li><div class="row schedule_list_item"><div class="col-lg-1 schedule_item">`;
                        html += `<input type="radio" name="email_settings_schedule_id" class="schedule_id"`;
                        if (schedule["user_id"] == 0 || i == 0) {
                            html += `checked `;
                        }
                        html += `value=` + schedule["id"] + `></div>`;
                        html += `<div class="col-lg-1 schedule_avatar">S</div>`;
                        html += `<div class="col-lg-3 schedule_name"><i class="fa-solid fa-circle-check"`;
                        html += `style="color: #4bcea6;"></i>`;
                        html +=
                            `<span>` +
                            schedule["schedule_name"] +
                            `</span></div>`;
                        html += `<div class="col-lg-6 schedule_days">`;
                        var schedule_days = schedule["Days"];
                        html += `<ul class="schedule_day_list">`;
                        for (var j = 0; j < schedule_days.length; j++) {
                            html += `<li `;
                            html += `class="schedule_day `;
                            day = schedule_days[j];
                            if (day["is_active"] == "1") {
                                html += `selected_day`;
                            }
                            html += `">`;
                            html += day["schedule_day"].toUpperCase() + `</li>`;
                        }
                        html += `<li class="schedule_time"><button href="javascript:;"`;
                        html += `type="button" class="btn" data-bs-toggle="modal"`;
                        html += `data-bs-target="#time_modal"><i class="fa-solid fa-globe"`;
                        html += `style="color: #16adcb;"></i></button></li></ul>`;
                        html += `</div><div class="col-lg-1 schedule_menu_btn">`;
                        html += `<i class="fa-solid fa-ellipsis-vertical" style="color: #ffffff;"></i>`;
                        html += `</div></div></li>`;
                    }
                    if (schedule_list.attr("id") == "schedule_list_2") {
                        html = html.replace(
                            "email_settings_schedule_id",
                            "global_settings_schedule_id"
                        );
                        $("#schedule_list_2").html(html);
                    } else {
                        $("#schedule_list_1").html(html);
                    }
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status == 404) {
                    var schedule_list = $(schedule_input)
                        .parent()
                        .next(".schedule_list");
                    html = ``;
                    html += `<li><div class="text-center text-danger">Not Found!</div></li>`;
                    if (schedule_list.attr("id") == "schedule_list_2") {
                        $("#schedule_list_2").html(html);
                    } else {
                        $("#schedule_list_1").html(html);
                    }
                }
            },
        });
    });
});
