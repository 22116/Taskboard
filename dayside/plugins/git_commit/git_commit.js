(function ($,ui) {

dayside.plugins.git_commit = ui.Control.extend({
    init: function (options) {
        this._super(options);
        var me = this;   
        this.Class.instance = me;     
        dayside.ready(function(){
            dayside.editor.filePanel.bind("contextMenu",function(b,e){
                if (e.node.data("folder") && e.node.find(">ul>li[rel$='/.git']").length) {
                    var menuItem = {
                        label: 'Git commit',
                        action: function () {
                            me.openTab(e.path);
                        }
                    }
                    e.menu = e.inject(e.menu,'openGitCommit',menuItem,function (pk,pv,nk,nv) { return pk=="link"; });
                }
            });
            
            dayside.editor.bind("editorOptions",function(b,e){
                var path = e.tab.options.file;
                if (path.indexOf("git_commit://")==0) e.options.readOnly = true;
            });    
            
            dayside.editor.bind("codeTabCreated",function(b,tab){
                var path = tab.options.file;
                if (path.indexOf("git_commit://")==0) {
                    var parts = path.split("/");
                    var ref = parts[2];
                    tab.options.label += ' (' + ref + ')';
                }
            });
        });
        
        var old_file = FileApi.file;
        FileApi.file =  function (path,callback) {
            if (path.indexOf("git_commit://")==0) {
                var parts = path.split("/");
                var ref = parts[2];
                var root = decodeURIComponent(parts[3]);
                var rel = parts.slice(4).join("/");
                
                FileApi.request('git_commit',{action:'show_file',path:root,file:rel,ref:ref},false,function(answer){
                    callback(FileApi.cache[path] = answer.data);
                });
            } else {
                return old_file.apply(this,arguments);
            }
        }
    },
    
    openTab: function (path) {
        var tab = dayside.plugins.git_commit.projectTab.hash[path];
        if (!tab || !tab.tabPanel) {
            tab = new dayside.plugins.git_commit.projectTab({path:path});
            dayside.editor.mainPanel.addTab(tab,tab.id,"center");
        }
        tab.tabPanel.selectTab(tab);
        return tab;
    }
});
    
dayside.plugins.git_commit.projectTab = teacss.ui.panel.extend("dayside.plugins.git_commit.projectTab",{
    serialize: function (tab) {
        var view_type = tab.element.find(".view_type").data("value");
        return {path:tab.options.path,view_type:view_type};
    },
    deserialize: function (data) {
        return new this({path:data.path,view_type:data.view_type});
    },
    hash: {}
},{
    init: function (o) {

        var path = o.path;
        var rel = path.substring(FileApi.root.length);
        if (rel[0]=='/') rel = rel.substring(1);
        
        this._super($.extend({label:"Commit: /" + rel,closable:true},o));
        
        this.element.addClass('git-commit-tab');
        this.path = path;
        
        this.initTabHandlers();

        var e = {
            tab:this,
            initialState: {action:this.options.view_type || 'working_tree'}
        };
        dayside.plugins.git_commit.instance.trigger("tabCreated",e);
        if (e.initialState) this.reloadTab(e.initialState);
        
        this.id = 'git_commit_'+path.replace(/[^0-9a-zA-Z]/g, "__");
        this.Class.hash[path] = this;

        
    },
    
    reloadTab: function(data,resType,cb) {
        var tab = this;
        if($.isPlainObject(data)){   
            $.extend(data,{
                status_hash:tab.element.find("#status_hash").val(),
                path: this.path
            });
        }
        if (resType!='json') tab.element.empty();
        
        FileApi.request('git_commit',data,resType=='json',function(answer) {
            var res = answer.data;
            if (resType!='json') tab.element.html(res);
            if (resType=='json' && res.status_hash) {
                tab.element.find("[name=status_hash]").val(res.status_hash);
            }
            if (cb) cb(res);
        });
    },
    
    initTabHandlers: function (tab) { 
        var tab = this;
        var reloadTab = function (data,resType,cb) { tab.reloadTab(data,resType,cb); }
        
        function reloadCodeTab(one_tab) {
            FileApi.file(one_tab.options.file,function (answer){
                if (!one_tab.editor) return;
                if(one_tab.editor.getValue()==FileApi.cache[one_tab.options.file]) return;

                var stateData = dayside.storage.get("codeTabState") || {};
                var tab_state = stateData[one_tab.options.file];

                one_tab.editor.setValue(FileApi.cache[one_tab.options.file]); 

                stateData[one_tab.options.file] = tab_state;
                dayside.storage.set("codeTabState",stateData);

                one_tab.restoreState();
                one_tab.editorChange();
            });
        }        
        
        function reloadFileTab() {
            var tree = window.dayside.editor.filePanel.tree;
            var node = tree.find("li[rel]").each(function(){
                if ($(this).attr("rel")==tab.path) tree.jstree('refresh',$(this));
            });
        }
        
        function showError(text) {
            tab.element.find(".ui-state-error").text(text);
            var $diff_scroll_wrap =  $(tab.element).find(".diff_scroll_wrap");
            $diff_scroll_wrap.animate({ scrollTop: 0 }, 600);
        }

        function tpl(one_status) {
            return $("<tr class='file ui-widget-content ui-state-default'>").attr("data-file", one_status.file).attr("data-status", JSON.stringify(one_status)).append(
                $("<td class='checkbox'>").append(
                    $("<input class='checkbox' type='checkbox'>").attr("checked", one_status.staged ? true : false).addClass(one_status.partial ? 'partial' : '')
                ),
                $("<td class='state'>").text(one_status.state),
                $("<td class='filename'>").text(one_status.old_file ? one_status.old_file+" -> "+one_status.file : one_status.file),
                $("<td class='checkout'>").append(
                    $("<button class='checkout_file ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close'>").append(
                        $("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'>")
                    )
                ),
                $("<td class='empty'>")
            ).add(
                $("<tr>").append(
                    $("<td class='diff_html ui-state-default' colspan='5'>").html(one_status.diff)
                )                
            );
        }
        
        // клик по любой кнопке нужно отследить, чтоб учесть как параметр action при отправке формы
        $(tab.element).on("click","form",function(e){
            $(this).data("clicked",$(e.target).parents("[value]").eq(0));
        });
        
        // перехватываем отправку формы и отправляем ее ajax-ом
        $(tab.element).on("submit","form",function(e){            
            e.preventDefault();
            var action = 'commit';
            var clicked = $(this).data("clicked");
            if (clicked && clicked.val()) action = clicked.val();
            $(this).data("clicked",false);
            
            var data = {action:action};
            $.each($(this).serializeArray(),function(){
                data[this.name] = this.value;
            });
            reloadTab(data);
        });
        
        // только в working tree при клике на строку в diff-е переход и фокусировка на эту же строку в оригинальном файле 
        $(tab.element).on("click",".diff_line .insert,.diff_line .delete,.diff_line .context",function(e){

            if (getSelection().toString()) return;
            
            var $hunk = $(this).parents('.hunk').eq(0);
            var filename = $(this).parents('.delta').data('filename');
            var part,line,new_tab;
            
            if ($(this).hasClass('delete')) {
                part = $hunk.data("parts")[0];
                line = $hunk.find('.number_del > div').eq($(this).index()).text();
            } else {
                part = $hunk.data("parts")[1];
                line = $hunk.find('.number_add > div').eq($(this).index()).text();
            }
            
            if (part=='WT') {
                new_tab = dayside.editor.selectFile(tab.path+"/"+filename);
            } else {
                new_tab = dayside.editor.selectFile("git_commit://"+part+"/"+encodeURIComponent(tab.path)+"/"+filename);
            }

            function positionCursor() {
                var position = {lineNumber: parseInt(line), column: 1};
                new_tab.editor.focus();
                new_tab.editor.setPosition(position);
                new_tab.editor.revealLineInCenter(position.lineNumber);
            }

            if (new_tab.editor) {
                positionCursor();
            } else {
                new_tab.bind("editorCreated",function(){
                    positionCursor();
                });
            } 

        });      
        
        // изменение состояния файла - staged/unstaged
        $(tab.element).on("click","input.checkbox",function(e){

            var chbox = this;
            var tr_file = $(chbox).parents("tr.file");
            var td_diff_html = tr_file.next().children("td.diff_html");

            reloadTab(
                {
                    ajax_action: 'change_staged',
                    stage_file: chbox.checked ? 1 : 0,
                    file: tr_file.data("file"),
                    need_diff: (td_diff_html.is(":visible")) ? true : false
                },
                'json',
                function(data) {  
                    if (data.error) {
                        chbox.checked = !chbox.checked;
                        showError(data.error);
                        return;
                    }
                    $(chbox).removeClass("partial");
                    $(chbox).parents("tr.file").attr("data-status",JSON.stringify(data.status));

                    if(data.extra_status && data.extra_status.length>1) {                   
                        tr_file.next().after(tpl(data.extra_status[1]));
                        tr_file.next().andSelf().replaceWith(tpl(data.extra_status[0]));
                    } 
                    else if(data.extra_status && data.extra_status.length==1) {
                        $("tr.file").each(function(){
                            var filename = $(this).data("file");
                            if (filename==data.extra_status[0].file){
                                $(this).next().andSelf().remove();
                            }
                            if (filename==data.extra_status[0].old_file){
                                $(this).next().andSelf().replaceWith(tpl(data.extra_status[0]));
                            }
                        });
                    }
                    else {
                        tr_file.find('td.state').text(data.state);
                        if(data.diff_html){                            
                            td_diff_html.children(".delta").replaceWith(data.diff_html);
                        }
                    }
                }
            );
        });
        
        // откат изменений(checkout) файла
        $(tab.element).on("click",".checkout_file",function(e){
            var $tr = $(this).parents("tr.file");
            var file_status = $tr.data("status");
            e.preventDefault();
            reloadTab(
                {
                    ajax_action: 'checkout_file',
                    file: file_status.file
                },
                'json',
                function(data) { 
                    if(data.error){
                        showError(data.error);
                        return;
                    }            
                    $tr.next().andSelf().remove();
                    
                    reloadFileTab();
                    $(teacss.ui.codeTab.tabs).each( function(key, one_tab) {
                        if (one_tab.options.file==tab.path + '/' + file_status.file) {
                            if (file_status.old_file) {
                                one_tab.options.file = tab.path + '/' + file_status.old_file;
                                var caption = file_status.old_file.split("/").pop(); 
                                one_tab.navElement.find("a").text(caption);
                            }
                            reloadCodeTab(one_tab);
                        }
                    });
                }
            );
        }); 
        
        // переключение view_type
        $(tab.element).on("mousedown",".view_type",function(){
            reloadTab({
                action: tab.element.find(".view_type").data("value")=="working_tree" ? 'history' : 'working_tree'
            },false,function(){
                dayside.editor.saveTabs();
            });
        });         
        
        // переключение branch-а
        $(tab.element).on("mousedown",".branch",function(){
            
            var $this = $(this);
            var view_type = $(".view_type").data("value");
            
            if (view_type=="working_tree") {
                var changed = false;
                $(teacss.ui.codeTab.tabs).each(function(key, one_tab) { changed = changed || one_tab.changed; });
                if (changed) {
                    tab.element.find(".ui-state-error").text("Save changed files before switching branch");
                    tab.element.find(".button-select-panel").removeClass("show");
                    return;
                }
            }            

            reloadTab(
                {
                    action: view_type=='working_tree' ? 'switch_branch' : 'history',
                    selected_branch: $(this).data("value")
                },
                false,
                function (){
                    if (view_type!="working_tree") return;
                    
                    reloadFileTab();
                    $(teacss.ui.codeTab.tabs).each( function(key, one_tab) {
                        reloadCodeTab(one_tab);
                    });
                }
            );
            
           
        });  
        
        // переключение commit-а
        $(tab.element).on("mousedown",".commit",function(){
            if ($(this).is(".selected")) return;
            reloadTab({
                action: 'history',
                selected_branch: tab.element.find(".branch.selected").data("value"),
                selected_commit: $(this).data("value")
            });
        });
        
        // переключение history_depth
        $(tab.element).on("mousedown",".history_depth",function(){
            if ($(this).is(".selected")) return;
            reloadTab({
                action: 'history',
                selected_branch: tab.element.find(".branch.selected").data("value"),
                selected_commit: tab.element.find(".commit.selected").data("value"),
                history_depth: $(this).data("value")
            });
        });
        
        // выполнение amend
        $(tab.element).on("mousedown",".amend",function(){
            reloadTab({
                action: 'amend',
                message: $("input[name=commit_message]").val()
            });
        });
        
        // cкрытие выпадающего меню клику в другом месте
        $(document).mousedown(function(){
            tab.element.find(".button-select-panel.show").removeClass("show");
        });
        
        // скрытие по выбору элемента
        $(tab.element).on('mousedown','.button-select-panel.show .combo-item',function(e) {
            tab.element.find(".button-select-panel.show").removeClass("show");
        });
        
        // но не скрывать по клику по другим частям уже открытой панель (например, полосе прокрутки)
        $(tab.element).on('mousedown','.button-select-panel.show',function(e) {
            e.stopPropagation();
        });
        
        // показывать/скрывать выпадающее меню для branch-ей и commit-ов
        $(tab.element).on("mousedown",".branch_list, .commit_list, .commit_select_menu",function(e){
            var panel = $(this).next(".button-select-panel");
            var show = panel.hasClass("show");
            tab.element.find(".button-select-panel.show").removeClass("show");
            panel.toggleClass("show",!show);
            panel.css({maxHeight:$(window).height() - $(this).offset().top - 30});
            e.stopPropagation();
        });
        
        // hover на кнопке делает её более контрастной
        $(tab.element).on("hover",".ui-button:not(.active)",function(){            
            $(this).toggleClass("ui-state-hover");
        });
        
        // показывать/скрывать diff 
        $(tab.element).on("click","td.filename",function(){  
            
            var $tr = $(this).parent('tr.file')
            var $diff_html = $tr.next().children(".diff_html");
            
            var commit_sha1 = '',commit_sha2 = '';
            
            if ($(".view_type").data("value")=='history') {
                commit_sha2 = tab.element.find("input[name=selected_commit]").val();
                commit_sha1 = commit_sha2+"~"+tab.element.find("input[name=history_depth]").val();
            }           
            
            $diff_html.toggle();

            if ($diff_html.is(":empty")) {
                $diff_html.addClass('load_diff');
                reloadTab(
                    {
                        ajax_action: 'diff',
                        one_status: $tr.attr("data-status"),
                        commit_sha1: commit_sha1,
                        commit_sha2: commit_sha2
                    },
                    'json',
                    function(data) { 
                        if(data.error){
                            $diff_html.removeClass('load_diff').toggle();
                            showError(data.error);
                            return;
                        } else {
                            $diff_html.removeClass('load_diff').html(data.diff_html);                            
                        }
                    }
                );
            }  
            $tr.toggleClass('active');
        });
                
    }    
});
    
    
})(teacss.jQuery,teacss.ui);