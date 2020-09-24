import Echo from 'laravel-echo';
import Pusher from "pusher-js";
export function PusherAuth() {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '572d881e38a6dda074d1',
        cluster: 'ap1',
        encrypted: true,
        auth: {
            headers: {
                Authorization: 'Bearer ' + JSON.parse(localStorage.getItem('user')).token
            },
        },

    });
}