import { Toast } from "bootstrap";

const toastr = {
    options : {
        animation : true,
        autohide : true,
        delay : 60000,
        position : 'bottom-left', // top-center, top-left, top-right, middle-center, middle-left, middle-right, bottom-center, bottom-left, bottom-right
        id : 'toastr',

        header : null,
        body : null,
        image : null,
        type : ''
    },
    classess : {
        'top-left' : 'top-0 start-0',
        'top-center' : 'top-0 start-50 translate-middle-x',
        'top-right' : 'top-0 end-0',
        'middle-left' : 'top-50 start-0 translate-middle-y',
        'middle-center' : 'top-50 start-50 translate-middle',
        'middle-right' : 'top-50 end-0 translate-middle-y',
        'bottom-left' : 'bottom-0 start-0',
        'bottom-center' : 'bottom-0 start-50 translate-middle-x',
        'bottom-right' : 'bottom-0 end-0',
    },
    color_schemes : {
        'primary' : 'text-bg-primary',
        'secondary' : 'text-bg-secondary',
        'success' : 'text-bg-success',
        'danger' : 'text-bg-danger',
        'error' : 'text-bg-danger',
        'warning' : 'text-bg-warning',
        'info' : 'text-bg-info',
        'light' : 'text-bg-light',
        'dark' : 'text-bg-dark',
    },
    build_container_ : function(id,classes){
        var body = document.querySelector(body);
        var container = document.createElement("div")
        container.setAttribute('id',`${this.options.id}-${id}`)
        container.className = 'toast-container fixed-bottom p-3 '+classes;
        document.body.insertBefore(container,body)
    },
    build_container : function(options){
        var opt = (!options) ? this.options : options;
        if(!document.getElementById(`${opt.id}-${opt.position}`)){
            var classess = this.classess[opt.position];
            this.build_container_(opt.position,classess);
        }        
    },
    toast : function(options){
        options = {...this.options,...options}

        var container, elem, header, headertext, body, image, closer, flex;
        var container_id = `${options.id}-${options.position}`
        elem = document.createElement('div');
        elem.className = `toast ${this.color_schemes[options.type]}`;

        closer = document.createElement('button')
        closer.setAttribute('type','button')
        closer.setAttribute('data-bs-dismiss','toast')
        closer.setAttribute('aria-label','close')
        closer.className = 'btn-close m-auto'

        body = document.createElement('div')
        body.className = 'toast-body'
        body.appendChild(document.createTextNode(options.body))

        if(options.header){
            header = document.createElement('div');
            header.className = 'toast-header';
            
            // set image
            if(options.image){
                image = document.createElement('img');
                image.className = 'rounded me-2'
                image.setAttribute('src',options.image)
                image.setAttribute('width',20)
                image.setAttribute('height',20)
                header.appendChild(image);
            }

            // set header text
            headertext = document.createElement('strong');
            headertext.appendChild(document.createTextNode(options.header));
            header.appendChild(headertext);

            // set close 
            closer.classList.add('me-0')
            header.appendChild(closer);
        
            elem.appendChild(header);

            elem.appendChild(body)
        }else{
            flex = document.createElement('div')
            flex.className = 'd-flex'
             
            flex.appendChild(body)

            closer.classList.add('me-2')
            flex.appendChild(closer)

            elem.appendChild(flex)
        }
        this.build_container(options);

        console.log(container_id);
        container = document.getElementById(container_id)
        container.appendChild(elem);

        return new Toast(elem,options);
    },
}

document.body.onload = toastr.build_container();

export {toastr}