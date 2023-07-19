/***************
 * AJAX SUBMIT *
 ***************/

import {api} from "./axios";
import { toastr } from "./toastr";

var formSubmit = {
    options : {
        callback : null,
        disable_submit : false,
    },
    init : function(elem,options){
        options = {...this.options,...options}

        var form = document.querySelector(elem);
        form.addEventListener("submit",(e) => {
            e.preventDefault();
            this.send(form,options);
        })
        form.addEventListener("reset",(e) => {
            this.enable_submit(form);
        })
    },
    send : function(form,options){
        const formData = new FormData(form);
        const formurl = form.getAttribute("action");
        
        this.animate_start(form);
        this.disable_submit(form);
        api.post(formurl,formData,{
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        }).then((res) => {
            if(res.data?.message){
                this.showToast(res.data?.message,false);
            }
            this.animate_stop(form);
            if(!options.disable_submit){
                this.enable_submit(form);
            }
            form.classList.remove("form-validated");
            if(options.callback){
                options.callback(res);
            }
        }).catch((err) => {
            if(err.response?.data?.message){
                this.showToast(err.response?.data?.message,true);
            }
            this.showValidationMessage(err,form);
            this.animate_stop(form);
            this.enable_submit(form);
            form.classList.add("form-validated");
        });
    },
    showValidationMessage(err,form){
        if(err.response?.data?.errors){
            Object.keys(err.response.data.errors).forEach(key => {
                var msgfield = form.querySelector(`[data-field="${key}"]`);
                if(msgfield){
                    msgfield.innerHTML = err.response.data.errors[key].join('<br>') 
                };
            });
        }
    },
    showToast(body,iserror){
        toastr.toast({
            body : body,
            type : (iserror) ? 'danger' : 'success',
        }).show();
    },
    enable_submit : function(form){
        form.querySelector("[type=submit]").removeAttribute('disabled')
        form.querySelector("[type=submit]").classList.remove("btn-success")
        form.querySelector("[type=submit]").classList.add("btn-primary")
    },
    disable_submit : function(form){
        form.querySelector("[type=submit]").setAttribute('disabled',true)
        form.querySelector("[type=submit]").classList.add("btn-success")
        form.querySelector("[type=submit]").classList.remove("btn-primary")
    },
    animate_start : function(form){
        form.querySelector("[type=submit]").classList.add('progress-bar-striped','progress-bar-animated')
    },
    animate_stop : function(form){
        form.querySelector("[type=submit]").classList.remove('progress-bar-striped','progress-bar-animated')
    }
}

export {formSubmit}

// (function (window, $) {
//     var AjaxSubmit = function (elem, options) {
//         this.elem = elem;
//         this.$elem = $(elem);
//         this.options = options;
//         this.message = '';
//     };

//     AjaxSubmit.prototype = {
//         defaults: {
//             url: '#',
//             type: 'GET',
//             response: 'json',
//             animate: true,
//             disable_submit: true,
//             callback: false,
//             growl: true,
//             response_elem: undefined,
//             before_send: undefined,
//             done: undefined,
//             complete: undefined,
//             confirm : null,
//             additional_data : undefined
//         },
//         init: function () {
//             var that = this;
//             this.defaults.url = $(that.elem).attr('action');
//             this.defaults.type = $(that.elem).attr('method');
//             this.config = $.extend({}, this.defaults, this.options);

//             $(that.elem).find("input, select, textarea").on("invalid", function (event) {
//                 that.showErrorMessage();
//                 event.preventDefault();
//             });

//             $(that.elem).on('submit',function (event) {
//                 event.preventDefault();
//                 if(that.config.confirm != null){
//                     let bboptions = $.extend({},that.config.confirm,{
//                         callback : function(result){
//                             if(result){
//                                 that.send();
//                             }
//                         }
//                     });
//                     bootbox.confirm(bboptions);                    
//                 }else{
//                     that.send();
//                 }
//             });
//             $(that.elem).find("[type=reset]").on('click',function (e) {
//                 that._enable_submit();
//             });
//             return this;
//         },

//         _enable_submit: function () {
//             $(this.elem).find("[type=submit]").removeClass("btn-success").addClass("btn-primary").removeAttr('disabled');
//         },
//         _disable_submit: function () {
//             $(this.elem).find("button[type=submit]").removeClass("btn-primary").addClass("btn-success").attr('disabled', 'disabled');
//         },
//         _animate_start: function () {
//             $(this.elem).find("[type=submit]").addClass('progress-bar-striped progress-bar-animated');
//         },
//         _animate_stop: function () {
//             $(this.elem).find("[type=submit]").removeClass('progress-bar-striped progress-bar-animated');
//         },

//         get_data: function (formdata) {
//             var use_fd = formdata || false,
//                 data;
//             if (use_fd) {
//                 data = new FormData($(this.elem)[0]);
//             } else {
//                 var new_val = {};
//                 $.each($(this.elem).serializeArray(), function (i, val) {
//                     new_val[val.name] = val.value;
//                 });
//                 data = new_val;
//             }
//             return data;
//         },
//         send: function () {
//             var that = this;
//             var formData = that.get_data(true);
//             if(that.config.additional_data != undefined){
//                 formData = that.config.additional_data(formData);
//                 if(!formData){
//                     return false;
//                 }
//             }
//             $.ajax({
//                 url: this.config.url,
//                 type: this.config.type,
//                 data: formData,
//                 dataType: this.config.response,
//                 async: true,
//                 success: function (resp) {
//                     that.showErrorMessage();
//                     that.showResponse(resp,that,false)
//                 },
//                 error : function(resp,status,error){
//                     var response = resp.responseJSON;
//                     response.type = 'error';
//                     that.showResponse(response,that,true);
//                     that._enable_submit();
//                 },
//                 contentType: false,
//                 processData: false,
//                 beforeSend: function (xhr, settings) {
//                     that._animate_start();
//                     if (that.config.before_send != undefined) {
//                         that._disable_submit();
//                         var call_ = that.config.before_send(xhr, settings);
//                         if(call_ == false){
//                             that._animate_stop();
//                             that._enable_submit();
//                         }
//                         return call_;
//                     }else{
//                         that._disable_submit();
//                     }
//                 },
//                 complete: function () {
//                     if (that.config.complete != undefined) {
//                         that.config.complete();
//                     }
//                     that._animate_stop();
//                     if(!that.config.disable_submit){
//                         that._enable_submit();
//                     }
//                 }
//             }).done(function (response, textStatus, jqXHR) {
//                 if (that.config.done != undefined) {
//                     that.config.done(response, textStatus, jqXHR);
//                 }
//             });
//         },
//         showErrorMessage: function () {
//             $(this.elem).find(":not(fieldset):invalid").each(function (index, node) {
//                 var message = node.validationMessage || 'Invalid value.';
//                 $(this).parents('.form-group').find('.invalid-feedback').text(message);
//             });
//             if ($(this.elem).find(":not(fieldset):invalid").length > 0) {
//                 $(this.elem).addClass('was-validated');
//             } else {
//                 $(this.elem).removeClass('was-validated');
//             }
//         },
//         showBackendErrorResponse: function (resp) {
//             var that = this;
//             if (typeof resp.errors == 'object') {
//                 $(that.elem).find('.invalid-feedback').text(null);
//                 for (var field in resp.errors) {
//                     if (!resp.errors.hasOwnProperty(field)) continue;
//                     var msgs = resp.errors[field];
//                     for (var prop in msgs) {
//                         if (!msgs.hasOwnProperty(prop)) continue;
//                         let msg = (typeof msgs == 'object') ? msgs.join('<br>') : msgs;
//                         $(that.elem).find('.invalid-feedback[data-field=' + field + ']').html(msg);
//                     }
//                 }
//                 $(this.elem).addClass('backend-validated');
//             } else {
//                 $(this.elem).removeClass('backend-validated');
//             }
//         },
//         showResponse : function(resp,that,error){
//             if(typeof resp == 'object'){
//                 that.showBackendErrorResponse(resp);
//                 if (typeof resp.message == 'object') {
//                     resp.message = resp.message.join('<br>');
//                 }
//                 if(that.config.growl == true){
//                     new Noty({
//                         text : resp.message,
//                         type : resp.type ? resp.type : 'info'
//                     }).show();
//                 }
              
//                 if (typeof that.config.response_elem !== 'undefined') {
//                     let text = (that.config.response === 'json') ? resp.message : resp;
//                     let type = (that.config.response === 'json') ? resp.type : 'info';
//                     // ubah error jadi danger, sesuai bootstrap
//                     if(type == 'error'){
//                         type = 'danger'
//                     }
//                     $(that.config.response_elem).html('<div class="alert alert-' + type + '">' + text + '</div>');
//                 }
//                 if (that.config.disable_submit == true && resp.type == 'success') {
//                     that._disable_submit();
//                 }
//             }else{
//                 if(that.config.growl == true){
//                     new Noty({
//                         text : "error response parsing",
//                         type : resp.type ? resp.type : 'warning'
//                     }).show();
//                 }
//             }

//             if (that.config.callback) {
//                 that.config.callback(resp,error);
//             }
//         }
//     };
//     AjaxSubmit.defaults = AjaxSubmit.prototype.defaults;
//     $.fn.ajaxsubmit = function (options) {
//         return this.each(function () {
//             new AjaxSubmit(this, options).init();
//         });
//     };
//     window.ajaxsubmit = AjaxSubmit;
// })(window, jQuery);