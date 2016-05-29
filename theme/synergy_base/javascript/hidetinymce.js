YUI().use('node', 'event', function (Y) {
    "use strict";
    var inportrait;
    inportrait = false;

    function set_one_tinymce(el, enable) {
        var area, tinymce, content, tinymceinner, iframe, doc;
        area = el.one('textarea');
        tinymce = el.one('span.mceEditor');

        if (!area) {
            return;
        }

        if (!tinymce) {
            // TinyMCE has not yet loaded - try again until it has.
            Y.config.win.setTimeout(function () {
                set_one_tinymce(el, enable);
            }, 500);
            return;
        }
        if (enable) {
            area.setStyle('display', 'none');
            tinymce.setStyle('display', 'block');
            Y.Event.detach('keyup', undefined, area);

        } else {
            area.setStyle('display', 'block');
            tinymce.setStyle('display', 'none');

            // Copy the content from the textarea inside tinymce
            iframe = tinymce.one('iframe').getDOMNode();
            doc = (iframe.contentWindow || iframe.contentDocument);
            if (doc.document) {
                doc = doc.document;
            }
            tinymceinner = Y.one(doc).one('body');
            area.on('keyup', function () {
                content = area.get('value');
                tinymceinner.setContent(content);
            });
        }
    }

    function set_all_tinymce(enable) {
        Y.all('form .felement').each(function (el) {
            set_one_tinymce(el, enable);
        });
        Y.all('.form-item .form-setting .form-htmlarea').each(function (el) {
            set_one_tinymce(el, enable);
        });
        Y.all('#theform > div.generalbox').each(function (el) {
            set_one_tinymce(el, enable);
        });
        Y.all('#tempform div.generalbox tr').each(function (el) {
            set_one_tinymce(el, enable);
        });
    }

    function check_into_portrait(forceportrait, forcehide) {
        var win;
        win = Y.config.win;
        if (win.innerWidth > 768) {
            if (inportrait) {
                inportrait = false;
                set_all_tinymce(true);
            }
            return; // Too big to be a mobile screen.
        }
        if (forceportrait === true || win.innerWidth < win.innerHeight) {
            if (!inportrait || forcehide === true) {
                inportrait = true;
                set_all_tinymce(false);
            }
        } else if (inportrait) {
            inportrait = false;
            set_all_tinymce(true);
        }
    }

    Y.one(Y.config.win).on('orientationchange', function (e) {
        if (this.orientation === 0) {
            check_into_portrait(true);
        }
    });
    Y.one(Y.config.win).on('resize', function (e) {
        check_into_portrait();
    });

    Y.on('domready', function () {
        check_into_portrait(false, true);
    });
});