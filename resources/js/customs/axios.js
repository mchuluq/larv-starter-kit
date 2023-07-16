import axios from 'axios'
import Cookies from 'js-cookie'

const api = axios.create({
    baseURL: window.location.origin,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
    }
});

export {api};