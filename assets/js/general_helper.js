setThemeColor();
$(window).load(function () {
    $('#pre-loader').delay(250).fadeOut(function () {
        $('#pre-loader').remove();
    });
});

$(document).ready(function () {


    $.ajaxSetup({cache: false});

    //expand or collapse sidebar menu 
    $("#sidebar-toggle-md").click(function () {
        $("#sidebar").toggleClass('collapsed');
        if ($("#sidebar").hasClass("collapsed")) {
            $(this).find(".fa").removeClass("fa-dedent");
            $(this).find(".fa").addClass("fa-indent");
        } else {
            $(this).find(".fa").addClass("fa-dedent");
            $(this).find(".fa").removeClass("fa-indent");
        }
    });

    $("#sidebar-collapse").click(function () {
        $("#sidebar").addClass('collapsed');
    });

    //expand or collaps sidebar menu items
    $("#sidebar-menu > .expand > a").click(function () {
        var $target = $(this).parent();
        if ($target.hasClass('main')) {
            if ($target.hasClass('open')) {
                $target.removeClass('open');
            } else {
                $("#sidebar-menu >.expand").removeClass('open');
                $target.addClass('open');
            }
            if (!$(this).closest(".collapsed").length) {
                return false;
            }
        }
    });


    $("#sidebar-toggle").click(function () {
        $("body").toggleClass("off-screen");
        $("#sidebar").removeClass("collapsed");
        $("#sidebar").toggleClass("w100p");
        $("#page-container").toggleClass("hide");
    });

    $(".change-theme").click(function () {
        if ($(this).attr("data-color")) {
            $(".custom-theme-color").remove();
            //set theme color
            setCookie("theme_color", $(this).attr("data-color"));
            setThemeColor();
        } else {
            //reset theme
            $(".custom-theme-color").remove();
            setCookie("theme_color", "");
        }

    });

    //set custom scrollbar
    setPageScrollable();
    setMenuScrollable();
    $(window).resize(function () {
        setPageScrollable();
        setMenuScrollable();
    });

    $('body').on('click', '.timeline-images a', function () {
        var $gallery = $(this).closest(".timeline-images");
        $gallery.magnificPopup({
            delegate: 'a',
            type: 'image',
            closeOnContentClick: false,
            closeBtnInside: false,
            mainClass: 'mfp-with-zoom mfp-img-mobile',
            gallery: {
                enabled: true
            },
            image: {
                titleSrc: 'data-title'
            },
            callbacks: {
                change: function (item) {

                    var itemData = $(item.el).data();
                    setTimeout(function () {
                        if (itemData && itemData.viewer === 'google') {
                            $(".mfp-content").addClass("full-width-mfp-content");
                        } else {
                            $(".mfp-content").removeClass("full-width-mfp-content");
                        }
                    });

                }
            }
        });
        $gallery.magnificPopup('open');
        return false;
    });


    //search datatable when clicks on the labels.

    $('body').on('click', '.label.clickable', function () {
        var value = $(this).text();

        $(this).closest(".dataTables_wrapper").find("input[type=search]").val(value).focus().select();
        $(this).closest(".dataTable").DataTable().search(value).draw();
        return false;
    });

    //add a hidden filed in form when clicking on delete file link
    $('body').on('click', '.delete-saved-file', function () {
        var fileName = $(this).attr("data-file_name");
        //add a hidden filed with the file name for delete
        $(this).closest(".saved-file-item-container").html("<input type='hidden' name=delete_file[] value='" + fileName + "' />");
        return false;
    });

    //apply summernote on textarea after click
    $('body').on('focus', 'textarea', function () {
        setSummernote($(this));
    });

    //show dropdowns of navbar like a collapse panel in mobile devices
    $("#personal-language-icon, #web-notification-icon, #message-notification-icon, #user-dropdown-icon, #project-timer-icon, #quick-add-icon").click(function () {
        if (isMobile()) {
            var $dropdown = $(this).closest("li").find('.dropdown-menu'),
                    handlerId = $(this).attr("id");

            $("#navbar").find('.dropdown-menu').addClass("hide");

            if ($("#navbar").find("[data-clone-id='" + handlerId + "']").attr("data-clone-id")) {
                //close dropdown
                $(this).closest("#navbar").find("[is-clone='1']").remove();
            } else {
                //open dropdown
                $(this).closest("#navbar").find("[is-clone='1']").remove(); //remove previously opened dropdown first
                appendDropdownClone($dropdown, handlerId);
            }

        }

    });

    //show push notification
    if (AppHelper.userId && AppHelper.settings.enablePushNotification && AppHelper.settings.userEnableWebNotification && AppHelper.settings.userDisablePushNotification !== "1" && AppHelper.settings.pusherKey && AppHelper.settings.pusherCluster) {

        var pusher = new Pusher(AppHelper.settings.pusherKey, {
            cluster: AppHelper.settings.pusherCluster,
            forceTLS: true
        });

        var channel = pusher.subscribe("user_" + AppHelper.userId + "_channel");

        channel.bind('rise-pusher-event', function (data) {

            if (data) {
                //show browser notification for https. otherwise show app notification
                if (AppHelper.https === "1") {
                    //browser notification
                    showBrowserNotification(data);
                } else {
                    //app notification
                    var appAlertText = data.title + " " + data.message;
                    if (data.url_attributes) {
                        var appAlertText = "<a class='color-white' " + data.url_attributes + ">" + appAlertText + "</a>";
                    }
                    appAlert.info(appAlertText, {duration: 10000});
                }

                //check web notifications
                notificationOptions.showPushNotification = true;
                checkNotifications(notificationOptions);

            }

        });

        document.addEventListener('DOMContentLoaded', function () {
            if (!Notification) {
                return;
            }

            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }
        });
    }

    //save the selected tab of ajax-tab list to cookie user-wise
    $('body').on('click', '[data-toggle="ajax-tab"] li a', function () {
        var tab = $(this).attr("data-target"),
                tabList = $(this).closest("ul").attr("id");

        setCookie("user_" + AppHelper.userId + "_" + tabList, tab);
    });

    //set keyboard condition
    document.onkeyup = function (e) {
        if (document.activeElement) {
            var activeElement = document.activeElement.tagName;

            if (activeElement) {
                activeElement = activeElement.toLowerCase();
            }

            //Shortcut isn't triggers when typing in rich text editor
            var isInTextEditor = $(document.activeElement).closest(".note-frame").length;
            if (activeElement !== "input" && activeElement !== "textarea" && !isInTextEditor && !$("#ajaxModal").hasClass('in') && !$("#confirmationModal").hasClass('in') && (!AppHelper.settings.disableKeyboardShortcuts || AppHelper.settings.disableKeyboardShortcuts === "0")) {
                var triggerBtn = keyboardShortcuts(e.which);
                $("body").find(triggerBtn).trigger("click");
            }
        }

    };

});

keyboardShortcuts = function (keyupCode) {
    var shortcuts = {
        "84": "#js-quick-add-task",
        "77": "#js-quick-add-multiple-task",
        "73": "#js-quick-add-project-time",
        "69": "#js-quick-add-event",
        "78": "#js-quick-add-note",
        "68": "#js-quick-add-to-do",
        "83": "#js-quick-add-ticket",
        "191": "#global-search-btn"
    };

    return shortcuts[keyupCode];
};

//apply summernote to all textarea, if those have any values
setSummernoteToAll = function (notFocus) {
    $("textarea").each(function () {
        var $instance = $(this);
        if ($instance.val()) {
            setTimeout(function () {
                setSummernote($instance, notFocus);
            }, 100);
        }
    });
};


//apply scrollbar on modal
setModalScrollbar = function () {
    var $scroll = $("#ajaxModalContent").find(".modal-body"),
            height = $scroll.height(),
            maxHeight = $(window).height() - 200;

    if (isMobile()) {
        //show full screen in mobile devices
        maxHeight = $(window).height() - 123;
    }

    if (height > maxHeight) {
        height = maxHeight;
        initScrollbar($scroll, {setHeight: height});
    }
};

//show browser notification
showBrowserNotification = function (data) {
    if (Notification.permission !== "granted") {
        Notification.requestPermission();
    } else {
        var notification = new Notification(data.title, {
            icon: data.icon,
            body: data.message,
            tag: data.notification_id //to prevent multiple notifications for multiple tab
        });

        setTimeout(notification.close.bind(notification), 10000); //show notification for 10 seconds

        notification.onclick = function () {
            if (data.url_attributes && data.notification_id) {
                //create notification url
                var link = "<a id='push-notification-link-" + data.notification_id + "' " + data.url_attributes + "></a>";
                $("#default-navbar").append(link);

                var $linkId = $("#push-notification-link-" + data.notification_id);

                //mark the notification as read
                $.ajax({
                    url: AppHelper.settings.pushNotficationMarkAsReadUrl + '/' + data.notification_id
                });

                if ($linkId.attr("data-act")) {
                    //if the link is modal
                    $linkId.trigger("click");
                } else if ($linkId.attr("href")) {
                    //if the link is not a modal
                    window.location.href = $linkId.attr("href");
                }

                //remove link
                $linkId.remove();

                //select the specific tab
                window.focus();
            }

            //remove notification
            notification.close();
        };

    }
};

//initialize summernote
setSummernote = function ($instance, notFocus) {
    if (AppHelper.settings.enableRichTextEditor === "1" && $instance.attr("data-rich-text-editor")) {

        var focus = true;
        if (notFocus) {
            focus = false;
        }

        $instance.fadeOut(100, function () {
            var settings = {
                height: 150,
                focus: focus,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['hr']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                disableDragAndDrop: true
            };

            $instance.summernote(settings);
        });
    }
};

//append dropdown clone to the topbar
function appendDropdownClone($dropdown, handlerId) {
    var $dropdownClone = $dropdown.clone();
    $dropdownClone.attr({"is-clone": "1", "data-clone-id": handlerId}); //add attributes to grab later
    $dropdownClone.css({"display": "block", "width": "100%", "min-width": "100%", "margin-top": "0"});
    $dropdownClone.removeClass("hide");
    $("#navbar").append($dropdownClone);
}

//set scrollbar on page
setPageScrollable = function () {

    if ($(window).width() <= 640) {
        $('html').css({"overflow": "initial"});
        $('body').css({"overflow": "initial"});
    } else {
        //set less height scrollbar when there has footer
        if ($("body").find("nav.public-navbar").length) {
            initScrollbar('.scrollable-page', {
                setHeight: $(window).height() - 98
            });
        } else if ($("body").find("div.footer").length) {
            initScrollbar('.scrollable-page', {
                setHeight: $(window).height() - 48
            });
        } else {
            initScrollbar('.scrollable-page', {
                setHeight: $(window).height() - 45
            });
        }
    }

};

//set scrollbar on left menu
setMenuScrollable = function () {
    initScrollbar('#sidebar-scroll', {
        setHeight: $(window).height() - 45
    });
};

initScrollbar = function (selector, options) {
    if (!options) {
        options = {};
    }

    if (!$(selector).length)
        return false;

    if (selector && selector.selector) {
        //it's a jquery element
        //add a id with the elment and then apply scrollbar
        var id = getRandomAlphabet(8);
        selector.attr("id", id)
        selector = "#" + id;
    }

    var defaults = {
        wheelPropagation: true
    },
    settings = $.extend({}, defaults, options);


    if (options.setHeight) {
        $(selector).css({"height": settings.setHeight + "px", position: "relative"})
    }

    if (AppHelper.settings.scrollbar == "native") {
        $(selector).css({"overflow-y": "scroll"});
    } else {
        var ps = new PerfectScrollbar(selector);
    }

};

// generate reandom string 
getRndomString = function (length) {
    var result = '',
            chars = '!-().0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for (var i = length; i > 0; --i)
        result += chars[Math.round(Math.random() * (chars.length - 1))];
    return result;
};


// getnerat random small alphabet 
getRandomAlphabet = function (length) {
    var result = '',
            chars = 'abcdefghijklmnopqrstuvwxyz';
    for (var i = length; i > 0; --i)
        result += chars[Math.round(Math.random() * (chars.length - 1))];
    return result;
};


attachDropzoneWithForm = function (dropzoneTarget, uploadUrl, validationUrl, options) {
    var $dropzonePreviewArea = $(dropzoneTarget),
            $dropzonePreviewScrollbar = $dropzonePreviewArea.find(".post-file-dropzone-scrollbar"),
            $previews = $dropzonePreviewArea.find(".post-file-previews"),
            $postFileUploadRow = $dropzonePreviewArea.find(".post-file-upload-row"),
            $uploadFileButton = $dropzonePreviewArea.find(".upload-file-button"),
            $submitButton = $dropzonePreviewArea.find("button[type=submit]"),
            previewsContainer = getRandomAlphabet(15),
            postFileUploadRowId = getRandomAlphabet(15),
            uploadFileButtonId = getRandomAlphabet(15);

    //set random id with the previws 
    $previews.attr("id", previewsContainer);
    $postFileUploadRow.attr("id", postFileUploadRowId);
    $uploadFileButton.attr("id", uploadFileButtonId);


    //get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#" + postFileUploadRowId);
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    if (!options)
        options = {};

    var postFilesDropzone = new Dropzone(dropzoneTarget, {
        url: uploadUrl,
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        maxFilesize: 3000,
        previewTemplate: previewTemplate,
        dictDefaultMessage: AppLanugage.fileUploadInstruction,
        autoQueue: true,
        previewsContainer: "#" + previewsContainer,
        clickable: "#" + uploadFileButtonId,
        maxFiles: options.maxFiles ? options.maxFiles : 1000,
        sending: function (file, xhr, formData) {
            formData.append(AppHelper.csrfTokenName, AppHelper.csrfHash);
        },
        init: function () {
            this.on("maxfilesexceeded", function (file) {
                this.removeAllFiles();
                this.addFile(file);
            });
        },
        accept: function (file, done) {
            if (file.name.length > 200) {
                done(AppLanugage.fileNameTooLong);
            }

            $dropzonePreviewScrollbar.removeClass("hide");
            initScrollbar($dropzonePreviewScrollbar, {setHeight: 90});

            $dropzonePreviewScrollbar.parent().removeClass("hide");
            $dropzonePreviewArea.find("textarea").focus();

            var postData = {file_name: file.name, file_size: file.size};

            //validate the file
            $.ajax({
                url: validationUrl,
                data: postData,
                cache: false,
                type: 'POST',
                dataType: "json",
                success: function (response) {
                    if (response.success) {

                        $(file.previewTemplate).append("<input type='hidden' name='file_names[]' value='" + file.name + "' />\n\
                                 <input type='hidden' name='file_sizes[]' value='" + file.size + "' />");
                        done();
                    } else {
                        appAlert.error(response.message);
                        $(file.previewTemplate).find("input").remove();
                        done(response.message);

                    }
                }
            });
        },
        processing: function () {
            $submitButton.prop("disabled", true);
            appLoader.show();
        },
        queuecomplete: function () {
            $submitButton.prop("disabled", false);
            appLoader.hide();
        },
        reset: function (file) {
            $dropzonePreviewScrollbar.addClass("hide");
        },
        fallback: function () {
            //add custom fallback;
            $("body").addClass("dropzone-disabled");

            $uploadFileButton.click(function () {
                //fallback for old browser
                $(this).html("<i class='fa fa-camera'></i> Add more");

                $dropzonePreviewScrollbar.removeClass("hide");
                initScrollbar($dropzonePreviewScrollbar, {setHeight: 90});

                $dropzonePreviewScrollbar.parent().removeClass("hide");
                $previews.prepend("<div class='clearfix p5 file-row'><button type='button' class='btn btn-xs btn-danger pull-left mr10 remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>");

            });
            $previews.on("click", ".remove-file", function () {
                $(this).parent().remove();
            });
        },
        success: function (file) {
            setTimeout(function () {
                $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
            }, 1000);
        }
    });

    return postFilesDropzone;
};

teamAndMemberSelect2Format = function (option) {
    if (option.type === "team") {
        return "<i class='fa fa-users info'></i> " + option.text;
    } else {
        return "<i class='fa fa-user'></i> " + option.text;
    }
};

setDatePicker = function (element, options) {
    if (!options) {
        options = {};
    }
    var dateFormat = getJsDateFormat();
    var settings = $.extend({}, {
        autoclose: true,
        language: "custom",
        todayHighlight: true,
        weekStart: AppHelper.settings.firstDayOfWeek,
        format: dateFormat
    }, options);


    //set dateformat
    $.each(element.split(","), function (index, el) {
        $(el).attr("data-convert-date-format", "1");
        if (isMobile()) {
            $(el).attr("readonly", "true"); //make fields read only for mobile devices
        }

        var value = $(el).val();

        if (value) {
            var dateArray = value.split("-"),
                    year = dateArray[0],
                    month = dateArray[1],
                    day = dateArray[2];

            if (year && month && day) {
                value = dateFormat.replace("yyyy", year).replace("mm", month).replace("dd", day);
                $(el).val(value);
            }
        }
        if (!$(el).attr("placeholder") || $(el).attr("placeholder") === "YYYY-MM-DD") {
            $(el).attr("placeholder", dateFormat.toUpperCase());
        }

    });


    $(element).datepicker(settings);


};


getJsDateFormat = function () {
    var formats = {
        "d-m-Y": "dd-mm-yyyy",
        "m-d-Y": "mm-dd-yyyy",
        "Y-m-d": "yyyy-mm-dd",
        "d/m/Y": "dd/mm/yyyy",
        "m/d/Y": "mm/dd/yyyy",
        "Y/m/d": "yyyy/mm/dd",
        "d.m.Y": "dd.mm.yyyy",
        "m.d.Y": "mm.dd.yyyy",
        "Y.m.d": "yyyy.mm.dd"
    };

    return formats[AppHelper.settings.dateFormat] || "yyyy-mm-dd";
};

setTimePicker = function (element, options) {
    if (!options) {
        options = {};
    }

    var showMeridian = AppHelper.settings.timeFormat == "24_hours" ? false : true;

    var settings = $.extend({}, {
        minuteStep: 5,
        defaultTime: "",
        appendWidgetTo: "#ajaxModal",
        showMeridian: showMeridian
    }, options);

    $(element).timepicker(settings);
};


initWYSIWYGEditor = function (element, options) {
    if (!options) {
        options = {};
    }

    var settings = $.extend({}, {
        height: 250,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['hr']],
            ['view', ['fullscreen', 'codeview']]
        ],
        disableDragAndDrop: true
    }, options);

    $(element).summernote(settings);
};

getWYSIWYGEditorHTML = function (element) {
    return $(element).summernote('code');
};

combineCustomFieldsColumns = function (defaultFields, customFieldString) {
    if (defaultFields && customFieldString) {

        var startAfter = defaultFields.slice(-1)[0];
        //count no of custom fields
        var noOfCustomFields = customFieldString.split(',').length - 1;
        if (noOfCustomFields) {
            for (var i = 1; i <= noOfCustomFields; i++) {
                defaultFields.push(i + startAfter);
            }
        }
    }
    return defaultFields;
};


function setCookie(cname, cvalue, exdays) {
    if (exdays)
        exdays = 1000;

    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function setThemeColor() {
    var color = getCookie("theme_color") || AppHelper.settings.defaultThemeColor;
    if (color && color !== "1d2632") {
        var href = AppHelper.assetsDirectory + "/css/color/" + color + ".css";
        $('head').append('<link id="custom-theme-color" class="custom-theme-color" rel="stylesheet" href="' + href + '" type="text/css" />');
    }
}

function isMobile() {
    return window.outerWidth < 800 ? true : false;
}
