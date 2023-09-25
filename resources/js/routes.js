import Vue from 'vue'
import VueRouter from 'vue-router'
import Main from '@/js/views/Main'
import LinkContact from '@/js/views/LinkContact'
import History from '@/js/views/History'

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'main',
            component: Main,
        },
        {
            path: '/link-contact/:id',
            name: 'linkContact',
            component: LinkContact,
            props: true,
        },
        {
            path: '/history',
            name: 'history',
            component: History,
        },
    ]
});

export default router;
