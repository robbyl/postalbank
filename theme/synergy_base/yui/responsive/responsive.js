YUI.add('moodle-theme_synergy_base-responsive', function(Y) {
    var ModulenameNAME = 'synergy_base-responsive';
 
    M.theme_synergy_base = M.theme_synergy_base || {}; // This line use existing name path if it exists, otherwise create a new one. 
                                                 // This is to avoid to overwrite previously loaded module with same name.
    M.theme_synergy_base.responsive = {
        init: function() {
            Y.all('table:not(.gradestable)').each(function (node) {
                var headcount = 0;
                for (var i=0; i<100; i++) {
                    if (Y.one('tbody td.c'+headcount) == null) {
                        break;
                    }
                    if (Y.one('thead') == null) {
                        var headnode = 'tbody tr.heading th.c'+i;
                    } else {
                        var headnode = 'thead th.c'+i;
                    }
                    if (node.one(headnode) != null) {
                        var headerhtml = node.one(headnode)._node.innerHTML;
                        var regex = /(<span.+?<\/span>)/g;
                        var headername = headerhtml.replace(regex, '');
                        regex = /<(?:.|\n)*?>/gm;
                        headername = headername.replace(regex, '');
                        node.all('tbody td.c'+headcount).each(function (node) {
                            node.setAttribute('data-headername', headername);
                        });
                        headcount++;
                        if (node.one(headnode)._node.colSpan == '2') {
                            headcount++;
                        }
                        if (node.one(headnode)._node.colSpan == '3') {
                            headcount++;
                            headcount++;
                        }
                    }
                }
            });
        }
    }

  }, '@VERSION@', {
      requires:['base','node']
  });