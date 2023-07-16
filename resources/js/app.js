import { passwordToggle } from './customs/password-toggle.js';
import { api } from './customs/axios.js';
import {toastr} from './customs/toastr.js';
import {app_helper} from './customs/helper.js';
import moment from 'moment';

window.passwordToggle = passwordToggle;
window.moment = moment;
window.api_axios = api; 
window.toastr = toastr;
window.app_helper = app_helper;

import './bootstrap';

