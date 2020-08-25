window._ = require('lodash');
window.Popper = require('popper.js').default;
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// try {
//     window.$ = window.jQuery = require('jquery');

//     require('bootstrap');
// } catch (e) {}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// let JwtToken1 = ''
// const JwtToken = JSON.parse(localStorage.getItem('user'));
// if(JwtToken!==null){
//     JwtToken1 = JSON.parse(localStorage.getItem('user')).token
// }

// console.log(localStorage);

window.axios.defaults.headers.common['Authorization'] = JSON.parse(localStorage.getItem('user')).token;

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: "78628710210a2fcca97b",
//     cluster: "ap1",
//     forceTLS: false,
//     encrypted: true,
//     auth:
//     {
//         headers:
//         {
//             'Authorization': 'Bearer ' + JwtToken1
//         }
//     }
// });

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '78628710210a2fcca97b',
    cluster: 'ap1',
    encrypted: true,
    auth: {
        headers: {
            Authorization: 'Bearer ' + JSON.parse(localStorage.getItem('user')).token
        },
    },
    
});
