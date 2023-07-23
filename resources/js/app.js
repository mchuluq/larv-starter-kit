import { passwordToggle } from './customs/password-toggle.js';
import { xhttp,xhttps } from './customs/axios.js';
import {toastr} from './customs/toastr.js';
import {app_helper} from './customs/helper.js';
import moment from 'moment';
import { formSubmit } from './customs/form-submit.js';

window.passwordToggle = passwordToggle;
window.moment = moment;
window.xhttps = xhttps;
window.xhttp = xhttp;
window.toastr = toastr;
window.app_helper = app_helper;
window.formSubmit = formSubmit;

import './bootstrap';

