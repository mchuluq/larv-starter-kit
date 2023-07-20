import axios from 'axios'
import Cookies from 'js-cookie'
import { Crypton,Encrypter } from 'laravel-crypton';

var key = import.meta.env.VITE_CRYPTER_KEY;

const xhttp = axios.create({
    baseURL: window.location.origin,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
    }
});

const xhttps = axios.create({
    baseURL: window.location.origin,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
    }
})

if(import.meta.env.VITE_CRYPTER_STATUS == '1'){
    Crypton(key).response().encrypt(xhttps);
    Crypton(key).request().encrypt(xhttps);
}

const crypter = new Encrypter(key);

export {xhttps, xhttp, crypter};