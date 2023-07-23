/***************
 * AJAX SUBMIT *
 ***************/

import {xhttps, xhttp, crypter } from "./axios";
import { toastr } from "./toastr";

var formSubmit = {
    options : {
        callback : null,
        disable_submit : false,
        secure : false
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
        var formdata = new FormData(form);
        var formurl = form.getAttribute("action");
        
        this.animate_start(form);
        this.disable_submit(form);

        var xapi = (options.secure) ? xhttps : xhttp;
        if(options.secure){
            formdata = this.serialize(formdata)
        }        
        xapi.post(formurl,formdata,{
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
            // console.log(err);
            if(err.response?.data?.secure == true && err.response.data.payload){
                err.response.data = crypter.decrypt(err.response.data.payload)
            }
            // console.log(err);
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
    },
    serialize (data) {
        let obj = {};
        for (let [key, value] of data) {
            if (obj[key] !== undefined) {
                if (!Array.isArray(obj[key])) {
                    obj[key] = [obj[key]];
                }
                obj[key].push(value);
            } else {
                obj[key] = value;
            }
        }
        return obj;
    }
}

export {formSubmit}